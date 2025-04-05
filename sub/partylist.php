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

// Handle Deletion
if (isset($_GET['delete'])) {
    $id   = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM partylists WHERE partylist_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: partylist.php");
    exit;
}

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
        <h2 class="text-3xl font-bold text-center mb-6">Manage Partylists for <?php echo $election_name; ?></h2>
        <p class="text-center text-lg mb-8">Here you can manage the partylists for the current election. You can add new partylists, edit existing ones, or delete them as needed.</p>

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
                            <a href="edit_partylist.php?id=<?php echo $row['partylist_id']; ?>" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                                Edit
                            </a>
                            <a href="partylist.php?delete=<?php echo $row['partylist_id']; ?>" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded delete-btn">
                                Delete
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Buttons -->
        <div class="flex justify-between mt-8">
            <a href="add_partylist.php" class="bg-green-700 hover:bg-green-800 text-white px-6 py-3 rounded-lg">Add Partylist</a>
            <div class="flex space-x-4">
                <a href="home.php" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-3 rounded-lg">Back to Dashboard</a>
                <a href="positions.php" class="bg-green-700 hover:bg-green-800 text-white px-6 py-3 rounded-lg">Next: Set Up Positions</a>
            </div>
        </div>
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

        // Add event listener to all delete buttons
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent the default link behavior

                // SweetAlert2 confirmation dialog
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
                        // Redirect to the delete URL if confirmed
                        window.location.href = button.getAttribute('href');
                    }
                });
            });
        });
    </script>
</body>
</html>