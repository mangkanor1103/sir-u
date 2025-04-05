<?php
require 'conn.php';
session_start();

// Fetch current election ID from session
$election_id  = $_SESSION['election_id'];
$current_page = basename($_SERVER['PHP_SELF']);

// Fetch election name
$election_query = "SELECT name FROM elections WHERE id = ?";
$stmt           = $conn->prepare($election_query);
$stmt->bind_param("i", $election_id);
$stmt->execute();
$result        = $stmt->get_result();
$election      = $result->fetch_assoc();
$election_name = $election ? $election['name'] : 'Election not found';

// Fetch Partylists for this election
$stmt = $conn->prepare("SELECT * FROM partylists WHERE election_id = ?");
$stmt->bind_param("i", $election_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Partylists</title>
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
            <!-- Navigation Links -->
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

    <!-- Main content -->
    <div class="container mx-auto mt-10">
        <h2 class="text-3xl font-bold text-center mb-6">Manage Partylists for <?php echo $election_name; ?></h2>
        <p class="text-center text-lg mb-8">Here you can manage the partylists for the current election. You can add new partylists, edit existing ones, or delete them as needed.</p>

        <!-- Add Partylist Button -->
        <div class="flex justify-end mb-4">
            <button onclick="openAddModal()" class="bg-green-700 hover:bg-green-800 text-white px-6 py-3 rounded-lg">
                + Add Partylist
            </button>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="table-auto w-full bg-white shadow-md rounded-lg">
                <thead class="bg-green-700 text-white">
                    <tr>
                        <th class="px-4 py-2">Partylist Name</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="hover:bg-green-100">
                        <td class="border px-4 py-2"><?php echo $row['name']; ?></td>
                        <td class="border px-4 py-2">
                            <button onclick="openEditModal(<?php echo $row['partylist_id']; ?>, '<?php echo $row['name']; ?>')" class="bg-green-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                                Edit
                            </button>
                            <button onclick="openDeleteModal(<?php echo $row['partylist_id']; ?>)" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                                Delete
                            </button>
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
            <h2 class="text-2xl font-bold text-green-700 mb-4">Add Partylist</h2>
            <form method="POST" action="add_partylist.php">
                <div class="mb-4">
                    <label for="partylistName" class="block text-sm font-medium text-gray-700">Partylist Name</label>
                    <input type="text" id="partylistName" name="partylistName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
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
            <h2 class="text-2xl font-bold text-green-700 mb-4">Edit Partylist</h2>
            <form method="POST" action="edit_partylist.php">
                <input type="hidden" id="editPartylistId" name="id">
                <div class="mb-4">
                    <label for="editPartylistName" class="block text-sm font-medium text-gray-700">Partylist Name</label>
                    <input type="text" id="editPartylistName" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>
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
            <p class="text-gray-700 mb-6">Are you sure you want to delete this partylist?</p>
            <div class="flex justify-end space-x-4">
                <button type="button" onclick="closeDeleteModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Cancel</button>
                <a id="deleteConfirmLink" href="#" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Delete</a>
            </div>
        </div>
    </div>

    <script>
        // Open Add Modal
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
        }

        // Close Add Modal
        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }

        // Open Edit Modal
        function openEditModal(id, name) {
            document.getElementById('editPartylistId').value = id;
            document.getElementById('editPartylistName').value = name;
            document.getElementById('editModal').classList.remove('hidden');
        }

        // Close Edit Modal
        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // Open Delete Modal
        function openDeleteModal(id) {
            document.getElementById('deleteConfirmLink').href = `delete_partylist.php?id=${id}`;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        // Close Delete Modal
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
</body>
</html>