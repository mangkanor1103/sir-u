<?php
// home.php
session_start();
if (!isset($_SESSION['election_id'])) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['back'])) {
    unset($_SESSION['election_id']);
    header("Location: ../index.php");
    exit();
}

// Establish database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "votingsystem5";

$connection = mysqli_connect($servername, $username, $password, $database);

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch the current election name based on election_id from the session
$election_id = $_SESSION['election_id'];
$election_query = "SELECT name, status, end_time FROM elections WHERE id = ?";
$stmt = $connection->prepare($election_query);
$stmt->bind_param("i", $election_id);
$stmt->execute();
$result = $stmt->get_result();
$election = $result->fetch_assoc();
$election_name = $election ? $election['name'] : 'Election not found';
$election_status = $election['status'] ?? 0;
$election_end_time = $election['end_time'] ?? null;

// Handle Start Election
if (isset($_POST['start_election'])) {
    $time_limit_hours = intval($_POST['time_limit_hours']); // Time limit in hours
    $time_limit_minutes = intval($_POST['time_limit_minutes']); // Time limit in minutes

    if ($time_limit_hours <= 0 && $time_limit_minutes <= 0) {
        $error_message = "Please set a valid time limit to start the election.";
    } else {
        $end_time = date("Y-m-d H:i:s", strtotime("+$time_limit_hours hours +$time_limit_minutes minutes"));

        $update_query = "UPDATE elections SET status = 1, end_time = ? WHERE id = ?";
        $update_stmt = $connection->prepare($update_query);
        $update_stmt->bind_param("si", $end_time, $election_id);
        $update_stmt->execute();

        // Redirect to votes.php after updating the status
        header("Location: votes.php");
        exit();
    }
}

// Handle Automatic End of Election
if ($election_status == 1 && $election_end_time && strtotime($election_end_time) <= time()) {
    $update_query = "UPDATE elections SET status = 0 WHERE id = ?";
    $update_stmt = $connection->prepare($update_query);
    $update_stmt->bind_param("i", $election_id);
    $update_stmt->execute();
    $election_status = 0; // Update the status locally
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Start Election</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="bg-green-50 text-green-900 font-sans">

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
                <li><a href="home.php" class="hover:text-green-300 <?php echo $current_page == 'home.php' ? 'font-bold underline' : ''; ?>">Home</a></li>
                <li><a href="partylist.php" class="hover:text-green-300 <?php echo $current_page == 'partylist.php' ? 'font-bold underline' : ''; ?>">Partylist</a></li>
                <li><a href="positions.php" class="hover:text-green-300 <?php echo $current_page == 'positions.php' ? 'font-bold underline' : ''; ?>">Positions</a></li>
                <li><a href="candidates.php" class="hover:text-green-300 <?php echo $current_page == 'candidates.php' ? 'font-bold underline' : ''; ?>">Candidates</a></li>
                <li><a href="voters.php" class="hover:text-green-300 <?php echo $current_page == 'voters.php' ? 'font-bold underline' : ''; ?>">Voters</a></li>
                <li><a href="start.php" class="hover:text-green-300 <?php echo $current_page == 'start.php' ? 'font-bold underline' : ''; ?>">Start</a></li>
                <li>
                    <a href="#" 
                       class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded flex items-center space-x-2" 
                       onclick="openLogoutModal(event);">
                       <i class="fas fa-sign-out-alt"></i>
                       <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

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
                <a href="../index.php" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded">Logout</a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto mt-10">
        <h2 class="text-4xl font-bold text-center mb-6 text-green-700">Start the Election</h2>
        <p class="text-center text-lg mb-8">Current Election: <span class="font-semibold"><?php echo htmlspecialchars($election_name); ?></span></p>

        <?php if ($election_status == 1): ?>
            <div class="bg-red-100 text-red-700 text-center py-4 px-6 rounded-lg mb-8">
                <i class="fas fa-exclamation-circle"></i>
                <span>The election is currently ongoing and will end at <strong><?php echo date("F j, Y, g:i A", strtotime($election_end_time)); ?></strong>.</span>
            </div>
        <?php else: ?>
            <div class="flex justify-center">
                <form method="POST" action="" class="bg-white shadow-md rounded-lg p-6 w-full max-w-md">
                    <h3 class="text-2xl font-bold text-green-700 mb-4 flex items-center space-x-2">
                        <i class="fas fa-clock"></i>
                        <span>Set Election Time Limit</span>
                    </h3>
                    <div class="mb-6">
                        <label for="time_limit_hours" class="block text-sm font-medium text-gray-700">Hours</label>
                        <input type="number" id="time_limit_hours" name="time_limit_hours" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-green-50" min="0" value="0" required>
                    </div>
                    <div class="mb-6">
                        <label for="time_limit_minutes" class="block text-sm font-medium text-gray-700">Minutes</label>
                        <input type="number" id="time_limit_minutes" name="time_limit_minutes" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-green-50" min="0" value="0" required>
                    </div>
                    <?php if (isset($error_message)): ?>
                        <p class="text-red-500 text-sm mb-4"><?php echo $error_message; ?></p>
                    <?php endif; ?>
                    <button type="submit" name="start_election" class="bg-green-700 hover:bg-green-800 text-white px-6 py-3 rounded-lg text-lg font-semibold flex items-center space-x-2">
                        <i class="fas fa-play"></i>
                        <span>Start Election</span>
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Function to open the logout confirmation modal
        function openLogoutModal(event) {
            event.preventDefault(); // Prevent the default link behavior
            document.getElementById('logoutModal').classList.remove('hidden');
        }

        // Function to close the logout confirmation modal
        function closeLogoutModal() {
            document.getElementById('logoutModal').classList.add('hidden');
        }
    </script>
</body>
</html>