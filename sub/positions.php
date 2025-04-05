<?php
session_start();
require 'conn.php';

if (!isset($_SESSION['election_id'])) {
    header("Location: index.php");
    exit();
}

$election_id = $_SESSION['election_id'];
$current_page = basename($_SERVER['PHP_SELF']);

// Function to fetch positions
function getPositions($election_id) {
    global $conn;
    $sql = "SELECT position_id, description, max_vote FROM positions WHERE election_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $election_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Function to create a new position
function createPosition($election_id, $description, $max_vote) {
    global $conn;
    $sql = "INSERT INTO positions (election_id, description, max_vote) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $election_id, $description, $max_vote);
    return $stmt->execute();
}

// Function to update an existing position
function updatePosition($id, $description, $max_vote) {
    global $conn;
    $sql = "UPDATE positions SET description = ?, max_vote = ? WHERE position_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $description, $max_vote, $id);
    return $stmt->execute();
}

// Function to delete a position
function deletePosition($id) {
    global $conn;
    $sql = "DELETE FROM positions WHERE position_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Handle form submissions for positions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    $id = isset($_POST['id']) ? $_POST['id'] : null;

    if ($action == "create_position") {
        $description = $_POST['description'];
        $max_vote = $_POST['max_vote'];
        createPosition($election_id, $description, $max_vote);
    } elseif ($action == "update_position") {
        $description = $_POST['description'];
        $max_vote = $_POST['max_vote'];
        updatePosition($id, $description, $max_vote);
    } elseif ($action == "delete_position") {
        deletePosition($id);
    }
}

$positions = getPositions($election_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Positions</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-green-50 text-green-900 font-sans">

    <!-- Navigation bar -->
    <nav class="bg-green-700 text-white shadow-lg">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="home.php" class="text-2xl font-bold">Election Dashboard</a>
            <ul class="flex space-x-6">
                <li><a href="home.php" class="hover:text-green-300 <?php echo $current_page == 'home.php' ? 'font-bold underline' : ''; ?>">Home</a></li>
                <li><a href="partylist.php" class="hover:text-green-300 <?php echo $current_page == 'partylist.php' ? 'font-bold underline' : ''; ?>">Partylist</a></li>
                <li><a href="positions.php" class="hover:text-green-300 <?php echo $current_page == 'positions.php' ? 'font-bold underline' : ''; ?>">Positions</a></li>
                <li><a href="candidates.php" class="hover:text-green-300 <?php echo $current_page == 'candidates.php' ? 'font-bold underline' : ''; ?>">Candidates</a></li>
                <li><a href="voters.php" class="hover:text-green-300 <?php echo $current_page == 'voters.php' ? 'font-bold underline' : ''; ?>">Voters</a></li>
                <li><a href="start.php" class="hover:text-green-300 <?php echo $current_page == 'start.php' ? 'font-bold underline' : ''; ?>">Start</a></li>
                <li>
                    <a href="#" 
                       class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded" 
                       onclick="openLogoutModal(event);">
                       Logout
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h2 class="text-2xl font-bold text-green-700 mb-4">Confirm Logout</h2>
            <p class="text-gray-700 mb-6">Are you sure you want to logout?</p>
            <div class="flex justify-end space-x-4">
                <button onclick="closeLogoutModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Cancel</button>
                <a href="../index.php" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded">Logout</a>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <div class="container mx-auto mt-10">
        <h2 class="text-3xl font-bold text-center mb-6">Manage Positions</h2>
        <p class="text-center text-lg mb-8">Here you can manage the positions for the current election. Add, update, or delete positions as needed.</p>

        <!-- Create Position Form -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
            <h3 class="text-2xl font-bold mb-4">Create Position</h3>
            <form method="POST" action="">
                <input type="hidden" name="action" value="create_position">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Position Name -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Position Name</label>
                        <input type="text" id="description" name="description" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-green-50" 
                               placeholder="Enter position name" required>
                    </div>
                    <!-- Max Vote -->
                    <div>
                        <label for="max_vote" class="block text-sm font-medium text-gray-700">Max Vote</label>
                        <input type="number" id="max_vote" name="max_vote" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-green-50" 
                               placeholder="Enter max vote" required>
                    </div>
                </div>
                <div class="mt-6">
                    <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-6 py-2 rounded-lg">Create Position</button>
                </div>
            </form>
        </div>

        <!-- Positions List -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="text-2xl font-bold mb-4">Positions List</h3>
            <table class="table-auto w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-green-100">
                        <th class="border border-gray-300 px-4 py-2">Description</th>
                        <th class="border border-gray-300 px-4 py-2">Max Vote</th>
                        <th class="border border-gray-300 px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $positions->fetch_assoc()): ?>
                    <tr class="hover:bg-green-100">
                        <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['description']); ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['max_vote']); ?></td>
                        <td class="border border-gray-300 px-4 py-2">
                            <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded" onclick="editPosition(<?php echo $row['position_id']; ?>)">Edit</button>
                            <button class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded delete-btn" data-id="<?php echo $row['position_id']; ?>">Delete</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Function to handle position deletion with SweetAlert2
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const positionId = this.getAttribute('data-id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#dc3545',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Submit the form to delete the position
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '';
                        const actionInput = document.createElement('input');
                        actionInput.type = 'hidden';
                        actionInput.name = 'action';
                        actionInput.value = 'delete_position';
                        form.appendChild(actionInput);
                        const idInput = document.createElement('input');
                        idInput.type = 'hidden';
                        idInput.name = 'id';
                        idInput.value = positionId;
                        form.appendChild(idInput);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });

        // Function to handle position editing
        function editPosition(id) {
            window.location.href = "edit_position.php?id=" + id; // Redirect to edit page
        }

        // Function to open logout modal
        function openLogoutModal(event) {
            event.preventDefault();
            document.getElementById('logoutModal').classList.remove('hidden');
        }

        // Function to close logout modal
        function closeLogoutModal() {
            document.getElementById('logoutModal').classList.add('hidden');
        }
    </script>
</body>
</html>