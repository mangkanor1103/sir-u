<?php
session_start();
require 'conn.php';

if (!isset($_SESSION['election_id'])) {
    header("Location: index.php");
    exit();
}
if (isset($_POST['back'])) {
    unset($_SESSION['election_id']);
    header("Location: ../index.php");
    exit();
}
$election_id = $_SESSION['election_id'];

// Fetch election details
$election_query = "SELECT name, status, end_time FROM elections WHERE id = ?";
$stmt = $conn->prepare($election_query);
$stmt->bind_param("i", $election_id);
$stmt->execute();
$election = $stmt->get_result()->fetch_assoc();
$election_name = $election['name'] ?? 'Election not found';
$election_status = $election['status'] ?? 0;
$election_end_time = $election['end_time'] ?? null;

// Fetch total voters
$total_voters_query = "SELECT COUNT(*) AS total_voters FROM voters WHERE election_id = ?";
$stmt = $conn->prepare($total_voters_query);
$stmt->bind_param("i", $election_id);
$stmt->execute();
$total_voters_result = $stmt->get_result()->fetch_assoc();
$total_voters = $total_voters_result['total_voters'] ?? 0;

// Calculate the threshold for winning (50% + 1)
$winning_threshold = ceil(($total_voters / 2) + 1);

// Handle ending an election
if (isset($_POST['end_election']) || ($election_status == 1 && $election_end_time && strtotime($election_end_time) <= time())) {
    $conn->begin_transaction();

    try {
        $result = $conn->query("SELECT name FROM elections WHERE id = '$election_id'");
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $election_title = $row['name'];

            $history_sql = "INSERT INTO history (election_title, deleted_at, candidates, voters, votes, positions, partylists)
                            VALUES ('$election_title', NOW(),
                                (SELECT GROUP_CONCAT(CONCAT(id, '|', position_id, '|', firstname, '|', lastname, '|', photo, '|', platform, '|', partylist_id) SEPARATOR ';') FROM candidates WHERE election_id = '$election_id'),
                                (SELECT GROUP_CONCAT(CONCAT(id, '|', voters_id) SEPARATOR ';') FROM voters WHERE election_id = '$election_id'),
                                (SELECT GROUP_CONCAT(CONCAT(id, '|', voters_id, '|', candidate_id, '|', position_id, '|', timestamp) SEPARATOR ';') FROM votes WHERE election_id = '$election_id'),
                                (SELECT GROUP_CONCAT(CONCAT(position_id, '|', description, '|', max_vote) SEPARATOR ';') FROM positions WHERE election_id = '$election_id'),
                                (SELECT GROUP_CONCAT(CONCAT(partylist_id, '|', name) SEPARATOR ';') FROM partylists WHERE election_id = '$election_id'))";
            $conn->query($history_sql) or die($conn->error);
        }

        $conn->query("DELETE FROM votes WHERE election_id = '$election_id'") or die($conn->error);
        $conn->query("DELETE FROM candidates WHERE election_id = '$election_id'") or die($conn->error);
        $conn->query("DELETE FROM voters WHERE election_id = '$election_id'") or die($conn->error);
        $conn->query("DELETE FROM positions WHERE election_id = '$election_id'") or die($conn->error);
        $conn->query("DELETE FROM partylists WHERE election_id = '$election_id'") or die($conn->error);
        $conn->query("DELETE FROM elections WHERE id = '$election_id'") or die($conn->error);

        $conn->commit();
        $_SESSION['success'] = 'Election has ended and all related records have been archived.';

        // Display SweetAlert thank you message
        echo "<script>
            Swal.fire({
                title: 'Thank You!',
                text: 'Thank you for using the election system. You will now be redirected.',
                icon: 'success',
                confirmButtonText: 'OK',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then(() => {
                window.location.href = '../index.php';
            });
        </script>";
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Failed to end election: " . $e->getMessage();
    }
}

// Handle extending election time
if (isset($_POST['extend_time'])) {
    $additional_hours = intval($_POST['hours']);
    $additional_minutes = intval($_POST['minutes']);

    if ($additional_hours > 0 || $additional_minutes > 0) {
        $new_end_time = date("Y-m-d H:i:s", strtotime("+$additional_hours hours +$additional_minutes minutes", strtotime($election_end_time)));

        $update_query = "UPDATE elections SET end_time = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("si", $new_end_time, $election_id);
        $update_stmt->execute();

        $_SESSION['success'] = "Election time has been successfully extended.";
        header("Location: votes.php");
        exit();
    } else {
        $_SESSION['error'] = "Please specify a valid time to extend.";
    }
}

// Calculate remaining time
$remaining_time = null;
if ($election_status == 1 && $election_end_time) {
    $remaining_time = strtotime($election_end_time) - time();
    if ($remaining_time <= 0) {
        $remaining_time = 0;
        $election_status = 0; // Automatically end the election
    }
}

// Fetch votes by position
function getVotesByPosition($election_id) {
    global $conn;
    $sql = "
        SELECT p.description AS position,
               CONCAT(c.firstname, ' ', c.lastname) AS candidate,
               COALESCE(COUNT(v.candidate_id), 0) AS total_votes,
               p.position_id,
               p.max_vote,
               (SELECT COUNT(*) FROM candidates WHERE position_id = p.position_id AND election_id = ?) AS candidate_count
        FROM candidates c
        JOIN positions p ON c.position_id = p.position_id
        LEFT JOIN votes v ON c.id = v.candidate_id AND v.election_id = ?
        WHERE c.election_id = ?
        GROUP BY p.position_id, c.id
        ORDER BY p.position_id, total_votes DESC;
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $election_id, $election_id, $election_id);
    $stmt->execute();
    return $stmt->get_result();
}

$results = getVotesByPosition($election_id);
$positionsData = [];
while ($row = $results->fetch_assoc()) {
    $candidate_count = $row['candidate_count'];
    $max_vote = $row['max_vote'];

    // Determine if the candidate is a winner
    $is_winner = false;

    // For positions with max_vote = 1, only the top candidate is the winner
    if ($max_vote == 1) {
        $is_winner = count($positionsData[$row['position']] ?? []) < $max_vote;
    } else {
        // For positions with max_vote > 1, select up to max_vote candidates
        $current_winners = $positionsData[$row['position']] ?? [];
        if (count($current_winners) < $max_vote) {
            $is_winner = true;
        }
    }

    $positionsData[$row['position']][] = [
        'candidate' => $row['candidate'],
        'total_votes' => $row['total_votes'],
        'is_winner' => $is_winner
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Results</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-50 text-gray-900 font-sans">

    <!-- Navigation Bar -->
    <nav class="bg-green-700 text-white shadow-lg">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <img src="../pics/logo.png" alt="Logo" class="h-10 w-10">
                <a href="home.php" class="text-2xl font-bold flex items-center space-x-2">
                    <i class="fas fa-poll"></i>
                    <span>Election Dashboard</span>
                </a>
            </div>
            <ul class="flex space-x-6">
                <li>
                    <button onclick="openLogoutModal()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded flex items-center space-x-2">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Log Out</span>
                    </button>
                </li>
                <li>
                    <button onclick="openExtendTimeModal()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded flex items-center space-x-2">
                        <i class="fas fa-clock"></i>
                        <span>Extend Time</span>
                    </button>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto mt-10">
        <h1 class="text-4xl font-bold text-center mb-6 text-green-700">Election Results</h1>

        <!-- Remaining Time -->
        <?php if ($remaining_time !== null): ?>
            <div class="bg-green-700 text-white text-center py-3 rounded mb-6 flex items-center justify-center space-x-2">
                <i class="fas fa-clock"></i>
                <span>Remaining Time:</span>
                <span id="remaining-time" class="font-bold"><?php echo gmdate("H:i:s", $remaining_time); ?></span>
            </div>
        <?php endif; ?>

        <!-- Results Table -->
        <?php foreach ($positionsData as $position => $candidates): ?>
            <h2 class="text-2xl font-bold mt-6 text-gray-700"><?php echo htmlspecialchars($position); ?></h2>
            <div class="bg-white shadow-md rounded-lg p-6 mt-4">
                <table class="table-auto w-full border-collapse border border-gray-300">
                    <thead class="bg-green-700 text-white">
                        <tr>
                            <th class="border border-gray-300 px-4 py-2">Candidate</th>
                            <th class="border border-gray-300 px-4 py-2">Total Votes</th>
                            <th class="border border-gray-300 px-4 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($candidates as $candidate): ?>
                            <tr class="hover:bg-green-100">
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($candidate['candidate']); ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($candidate['total_votes']); ?></td>
                                <td class="border border-gray-300 px-4 py-2">
                                    <?php if ($candidate['is_winner']): ?>
                                        <span class="text-green-700 font-bold">Winner</span>
                                    <?php else: ?>
                                        <span class="text-gray-500">Not Qualified</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h2 class="text-2xl font-bold text-green-700 mb-4 flex items-center space-x-2">
                <i class="fas fa-sign-out-alt"></i>
                <span>Confirm Logout</span>
            </h2>
            <p class="text-gray-700 mb-6">Are you sure you want to logout?</p>
            <div class="flex justify-end space-x-4">
                <button onclick="closeLogoutModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Cancel</button>
                <a href="../logout.php" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded">Logout</a>
            </div>
        </div>
    </div>

    <!-- Extend Time Modal -->
    <div id="extendTimeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h2 class="text-2xl font-bold text-green-700 mb-4 flex items-center space-x-2">
                <i class="fas fa-clock"></i>
                <span>Extend Election Time</span>
            </h2>
            <form method="POST" action="">
                <div class="mb-4">
                    <label for="hours" class="block text-sm font-medium text-gray-700">Hours</label>
                    <input type="number" id="hours" name="hours" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" min="0" value="0">
                </div>
                <div class="mb-4">
                    <label for="minutes" class="block text-sm font-medium text-gray-700">Minutes</label>
                    <input type="number" id="minutes" name="minutes" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" min="0" value="0">
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeExtendTimeModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Cancel</button>
                    <button type="submit" name="extend_time" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded">Extend</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Real-time countdown for remaining time
        let remainingTime = <?php echo $remaining_time; ?>;

        function updateRemainingTime() {
            if (remainingTime > 0) {
                remainingTime--;
                const hours = Math.floor(remainingTime / 3600);
                const minutes = Math.floor((remainingTime % 3600) / 60);
                const seconds = remainingTime % 60;

                document.getElementById('remaining-time').textContent =
                    `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            } else {
                document.getElementById('remaining-time').textContent = "00:00:00";
            }
        }

        setInterval(updateRemainingTime, 1000); // Update every second

        // Auto-refresh the page every 10 seconds
        setTimeout(() => {
            location.reload();
        }, 10000);

        // Open the logout confirmation modal
        function openLogoutModal() {
            document.getElementById('logoutModal').classList.remove('hidden');
        }

        // Close the logout confirmation modal
        function closeLogoutModal() {
            document.getElementById('logoutModal').classList.add('hidden');
        }

        // Open the extend time modal
        function openExtendTimeModal() {
            document.getElementById('extendTimeModal').classList.remove('hidden');
        }

        // Close the extend time modal
        function closeExtendTimeModal() {
            document.getElementById('extendTimeModal').classList.add('hidden');
        }
    </script>
</body>
</html>