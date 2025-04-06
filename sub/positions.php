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
            <!-- Logo and Title -->
            <div class="flex items-center space-x-3">
                <img src="../pics/logo.png" alt="Logo" class="h-10 w-10">
                <a href="home.php" class="text-2xl font-bold">Election Dashboard</a>
            </div>

            <!-- Hamburger Menu for Mobile -->
            <button id="menu-toggle" class="block md:hidden focus:outline-none">
                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- Navigation Links -->
            <ul id="menu" class="hidden md:flex space-x-6">
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

        <!-- Navigation Buttons -->
        <div class="flex justify-between items-center mb-6">
            <a href="partylist.php" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-3 rounded-lg">
                &larr; Back to Partylist
            </a>
            <button onclick="openAddModal()" class="bg-green-700 hover:bg-green-800 text-white px-6 py-3 rounded-lg mx-auto">
                + Add Position
            </button>
            <a href="candidates.php" class="bg-green-700 hover:bg-green-800 text-white px-6 py-3 rounded-lg">
                Next Step &rarr;
            </a>
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
                            <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded" onclick="openEditModal(<?php echo $row['position_id']; ?>, '<?php echo htmlspecialchars($row['description']); ?>', <?php echo $row['max_vote']; ?>)">Edit</button>
                            <button class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded" onclick="openDeleteModal(<?php echo $row['position_id']; ?>)">Delete</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Modal -->
    <div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h2 class="text-2xl font-bold text-green-700 mb-4">Add Position</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="create_position">
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700">Position Name</label>
                    <input type="text" id="description" name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>
                </div>
                <div class="mb-4">
                    <label for="max_vote" class="block text-sm font-medium text-gray-700">Max Vote</label>
                    <input type="number" id="max_vote" name="max_vote" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeAddModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Cancel</button>
                    <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded">Add</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h2 class="text-2xl font-bold text-green-700 mb-4">Edit Position</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="update_position">
                <input type="hidden" id="editPositionId" name="id">
                <div class="mb-4">
                    <label for="editDescription" class="block text-sm font-medium text-gray-700">Position Name</label>
                    <input type="text" id="editDescription" name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>
                </div>
                <div class="mb-4">
                    <label for="editMaxVote" class="block text-sm font-medium text-gray-700">Max Vote</label>
                    <input type="number" id="editMaxVote" name="max_vote" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeEditModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Cancel</button>
                    <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h2 class="text-2xl font-bold text-red-700 mb-4">Confirm Deletion</h2>
            <p class="text-gray-700 mb-6">Are you sure you want to delete this position?</p>
            <form method="POST" action="">
                <input type="hidden" name="action" value="delete_position">
                <input type="hidden" id="deletePositionId" name="id">
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeDeleteModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Cancel</button>
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Delete</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Toggle the mobile menu
        const menuToggle = document.getElementById('menu-toggle');
        const menu = document.getElementById('menu');

        menuToggle.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });

        // Open Add Modal
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }

        // Open Edit Modal
        function openEditModal(id, description, maxVote) {
            document.getElementById('editPositionId').value = id;
            document.getElementById('editDescription').value = description;
            document.getElementById('editMaxVote').value = maxVote;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // Open Delete Modal
        function openDeleteModal(id) {
            document.getElementById('deletePositionId').value = id;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
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