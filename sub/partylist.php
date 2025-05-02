<?php
require 'conn.php';
session_start();

// Fetch current election ID from session
$election_id  = $_SESSION['election_id'];
$current_page = basename($_SERVER['PHP_SELF']);

// Fetch election name
$election_query = "SELECT name, status FROM elections WHERE id = ?";
$stmt           = $conn->prepare($election_query);
$stmt->bind_param("i", $election_id);
$stmt->execute();
$result        = $stmt->get_result();
$election      = $result->fetch_assoc();
$election_name = $election ? $election['name'] : 'Election not found';
$election_status = $election ? $election['status'] : 0;

// Check if election is active and redirect to votes.php if it is
if ($election_status == 1) {
    $_SESSION['error'] = "Cannot edit partylists because the election has already started.";
    header("Location: votes.php");
    exit();
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
    <title>Manage Partylists | SIR-U</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 font-sans min-h-screen flex flex-col">

    <!-- Top Navigation Bar -->
    <nav class="bg-gradient-to-r from-primary-700 to-primary-800 text-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-3">
                <!-- Logo and Title -->
                <div class="flex items-center space-x-3">
                    <div class="bg-white p-1.5 rounded-full shadow-md">
                        <img src="../pics/logo.png" alt="Logo" class="h-8 w-8">
                    </div>
                    <a href="home.php" class="text-xl font-bold tracking-tight">Votesys Election System</a>
                </div>

                <!-- Election Name Badge -->
                <div class="hidden md:flex items-center bg-white/20 px-4 py-1.5 rounded-full backdrop-blur-sm">
                    <i class="fas fa-vote-yea mr-2"></i>
                    <span class="font-medium"><?php echo htmlspecialchars($election_name); ?></span>
                </div>

                <!-- Hamburger Menu for Mobile -->
                <button id="menu-toggle" class="md:hidden focus:outline-none">
                    <i class="fas fa-bars text-lg"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Side Navigation & Content Container -->
    <div class="flex flex-col md:flex-row flex-1">
        
        <!-- Side Navigation for Desktop -->
        <aside class="hidden md:block bg-white w-64 shadow-lg h-[calc(100vh-64px)] sticky top-0 overflow-y-auto">
            <div class="p-4 border-b border-gray-100">
                <div class="text-sm font-medium text-gray-400 uppercase">Main Menu</div>
            </div>
            
            <nav class="mt-2 px-2">
                <!-- Dashboard Link -->
                <a href="home.php" class="flex items-center px-4 py-3 mb-1 rounded-lg <?php echo $current_page == 'home.php' ? 'bg-primary-50 text-primary-700 font-medium' : 'hover:bg-gray-50'; ?>">
                    <i class="fas fa-home w-5 h-5 mr-3 <?php echo $current_page == 'home.php' ? 'text-primary-700' : 'text-gray-400'; ?>"></i>
                    <span>Dashboard</span>
                </a>
                
                <!-- Partylist Link -->
                <a href="partylist.php" class="flex items-center px-4 py-3 mb-1 rounded-lg <?php echo $current_page == 'partylist.php' ? 'bg-primary-50 text-primary-700 font-medium' : 'hover:bg-gray-50'; ?>">
                    <i class="fas fa-flag w-5 h-5 mr-3 <?php echo $current_page == 'partylist.php' ? 'text-primary-700' : 'text-gray-400'; ?>"></i>
                    <span>Partylists</span>
                </a>
                
                <!-- Positions Link -->
                <a href="positions.php" class="flex items-center px-4 py-3 mb-1 rounded-lg <?php echo $current_page == 'positions.php' ? 'bg-primary-50 text-primary-700 font-medium' : 'hover:bg-gray-50'; ?>">
                    <i class="fas fa-sitemap w-5 h-5 mr-3 <?php echo $current_page == 'positions.php' ? 'text-primary-700' : 'text-gray-400'; ?>"></i>
                    <span>Positions</span>
                </a>
                
                <!-- Candidates Link -->
                <a href="candidates.php" class="flex items-center px-4 py-3 mb-1 rounded-lg <?php echo $current_page == 'candidates.php' ? 'bg-primary-50 text-primary-700 font-medium' : 'hover:bg-gray-50'; ?>">
                    <i class="fas fa-user-tie w-5 h-5 mr-3 <?php echo $current_page == 'candidates.php' ? 'text-primary-700' : 'text-gray-400'; ?>"></i>
                    <span>Candidates</span>
                </a>
                
                <!-- Voters Link -->
                <a href="voters.php" class="flex items-center px-4 py-3 mb-1 rounded-lg <?php echo $current_page == 'voters.php' ? 'bg-primary-50 text-primary-700 font-medium' : 'hover:bg-gray-50'; ?>">
                    <i class="fas fa-users w-5 h-5 mr-3 <?php echo $current_page == 'voters.php' ? 'text-primary-700' : 'text-gray-400'; ?>"></i>
                    <span>Voters</span>
                </a>
                
                <!-- Start Link -->
                <a href="start.php" class="flex items-center px-4 py-3 mb-1 rounded-lg <?php echo $current_page == 'start.php' ? 'bg-primary-50 text-primary-700 font-medium' : 'hover:bg-gray-50'; ?>">
                    <i class="fas fa-play-circle w-5 h-5 mr-3 <?php echo $current_page == 'start.php' ? 'text-primary-700' : 'text-gray-400'; ?>"></i>
                    <span>Start Election</span>
                </a>
                
                <hr class="my-4 border-gray-100">
                
                <!-- Logout Link -->
                <a href="#" onclick="confirmLogout(event);" class="flex items-center px-4 py-3 mb-1 rounded-lg text-red-600 hover:bg-red-50">
                    <i class="fas fa-sign-out-alt w-5 h-5 mr-3"></i>
                    <span>Logout</span>
                </a>
            </nav>
        </aside>
        
        <!-- Mobile Navigation Menu (Hidden by default) -->
        <div id="mobile-menu" class="md:hidden hidden bg-white w-full shadow-lg absolute z-50 top-16 left-0 right-0 transition-all duration-300 ease-in-out">
            <nav class="py-2">
                <a href="home.php" class="block px-6 py-3 hover:bg-gray-50 <?php echo $current_page == 'home.php' ? 'text-primary-700 font-medium' : ''; ?>">
                    <i class="fas fa-home mr-2"></i> Dashboard
                </a>
                <a href="partylist.php" class="block px-6 py-3 hover:bg-gray-50 <?php echo $current_page == 'partylist.php' ? 'text-primary-700 font-medium' : ''; ?>">
                    <i class="fas fa-flag mr-2"></i> Partylists
                </a>
                <a href="positions.php" class="block px-6 py-3 hover:bg-gray-50 <?php echo $current_page == 'positions.php' ? 'text-primary-700 font-medium' : ''; ?>">
                    <i class="fas fa-sitemap mr-2"></i> Positions
                </a>
                <a href="candidates.php" class="block px-6 py-3 hover:bg-gray-50 <?php echo $current_page == 'candidates.php' ? 'text-primary-700 font-medium' : ''; ?>">
                    <i class="fas fa-user-tie mr-2"></i> Candidates
                </a>
                <a href="voters.php" class="block px-6 py-3 hover:bg-gray-50 <?php echo $current_page == 'voters.php' ? 'text-primary-700 font-medium' : ''; ?>">
                    <i class="fas fa-users mr-2"></i> Voters
                </a>
                <a href="start.php" class="block px-6 py-3 hover:bg-gray-50 <?php echo $current_page == 'start.php' ? 'text-primary-700 font-medium' : ''; ?>">
                    <i class="fas fa-play-circle mr-2"></i> Start Election
                </a>
                <hr class="my-2 border-gray-100">
                <a href="#" onclick="confirmLogout(event);" class="block px-6 py-3 text-red-600 hover:bg-red-50">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <main class="flex-1 p-4 md:p-6 bg-gray-50">
            <!-- Mobile Election Name Badge -->
            <div class="md:hidden bg-white rounded-lg shadow-sm p-3 mb-6 flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-vote-yea text-primary-600 mr-2"></i>
                    <span class="font-medium"><?php echo htmlspecialchars($election_name); ?></span>
                </div>
                <div class="text-xs text-gray-500">Active Election</div>
            </div>
            
            <!-- Page Header -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-flag text-blue-600 mr-3"></i>
                    Manage Partylists
                </h1>
                <p class="mt-2 text-gray-600 max-w-3xl">
                    Create and manage partylists for <span class="font-medium"><?php echo htmlspecialchars($election_name); ?></span>. 
                    Add new partylists, edit existing ones, or delete them as needed.
                </p>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-wrap justify-between items-center mb-6 gap-4">
                <div class="flex space-x-2">
                    <a href="home.php" class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                    </a>
                </div>
                
                <div class="flex space-x-2">
                    <button onclick="openAddModal()" class="flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors shadow-sm">
                        <i class="fas fa-plus mr-2"></i> Add New Partylist
                    </button>
                    
                    <a href="positions.php" 
                       class="flex items-center px-4 py-2 rounded-lg transition-colors shadow-sm
                              <?php echo ($result->num_rows > 0) ? 
                                    'bg-primary-600 hover:bg-primary-700 text-white' : 
                                    'bg-gray-300 text-gray-500 cursor-not-allowed'; ?>"
                       <?php echo ($result->num_rows > 0) ? '' : 'onclick="return false;"'; ?>>
                        Next: Positions <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
            
            <!-- Warning Message if no partylists -->
            <?php if ($result->num_rows == 0): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                You must add at least one partylist to proceed to the next step.
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Partylists Table -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-4 bg-blue-50 border-b border-blue-100">
                    <h2 class="text-lg font-medium text-blue-800">
                        <i class="fas fa-list-ul mr-2"></i> Partylists
                    </h2>
                </div>
                
                <?php if ($result->num_rows > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Partylist Name
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php
                                mysqli_data_seek($result, 0); // Reset pointer
                                while ($row = $result->fetch_assoc()): 
                                ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-flag text-blue-500"></i>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <?php echo htmlspecialchars($row['name']); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button onclick="openEditModal(<?php echo $row['partylist_id']; ?>, '<?php echo addslashes($row['name']); ?>')" 
                                                    class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-md transition-colors mr-2">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </button>
                                            <button onclick="openDeleteModal(<?php echo $row['partylist_id']; ?>, '<?php echo addslashes($row['name']); ?>')" 
                                                    class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition-colors">
                                                <i class="fas fa-trash-alt mr-1"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="py-12 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 text-blue-500 mb-4">
                            <i class="fas fa-flag-checkered text-xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">No partylists yet</h3>
                        <p class="mt-2 text-sm text-gray-500">Get started by creating a new partylist.</p>
                        <div class="mt-6">
                            <button onclick="openAddModal()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                <i class="fas fa-plus mr-2"></i> Add Partylist
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Footer -->
            <footer class="mt-12 text-center text-gray-500 text-sm">
            <p>© <?php echo date('Y'); ?> Votesys Election System | All Rights Reserved</p>
            </footer>
        </main>
    </div>

    <!-- Add Partylist Modal -->
    <div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full transform transition-all">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-plus-circle text-blue-500 mr-2"></i>
                    Add New Partylist
                </h3>
                <button type="button" onclick="closeAddModal()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form method="POST" action="add_partylist.php" class="p-6 space-y-4">
                <div>
                    <label for="partylistName" class="block text-sm font-medium text-gray-700 mb-1">Partylist Name</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-flag text-gray-400"></i>
                        </div>
                        <input type="text" name="partylistName" id="partylistName" 
                               class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-3" 
                               placeholder="Enter partylist name" required>
                    </div>
                </div>
                
                <div class="pt-2 flex justify-end space-x-3">
                    <button type="button" onclick="closeAddModal()" 
                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i> Save Partylist
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Edit Partylist Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full transform transition-all">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-edit text-blue-500 mr-2"></i>
                    Edit Partylist
                </h3>
                <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form method="POST" action="edit_partylist.php" class="p-6 space-y-4">
                <input type="hidden" id="editPartylistId" name="id">
                
                <div>
                    <label for="editPartylistName" class="block text-sm font-medium text-gray-700 mb-1">Partylist Name</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-flag text-gray-400"></i>
                        </div>
                        <input type="text" name="name" id="editPartylistName" 
                               class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-3" 
                               placeholder="Enter partylist name" required>
                    </div>
                </div>
                
                <div class="pt-2 flex justify-end space-x-3">
                    <button type="button" onclick="closeEditModal()" 
                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i> Update Partylist
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full transform transition-all">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-trash-alt text-red-500 mr-2"></i>
                    Delete Partylist
                </h3>
                <button type="button" onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="p-6">
                <div class="flex items-center justify-center bg-red-100 rounded-full w-16 h-16 mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
                </div>
                
                <p class="text-center text-gray-800 font-medium">Are you sure you want to delete this partylist?</p>
                <p id="deletePartylistName" class="text-center text-gray-600 mt-1"></p>
                <p class="text-center text-red-600 text-sm mt-4">This action cannot be undone.</p>
                
                <div class="mt-6 flex justify-center space-x-3">
                    <button type="button" onclick="closeDeleteModal()" 
                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <a id="deleteConfirmLink" href="#" 
                       class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <i class="fas fa-trash-alt mr-2"></i> Delete Permanently
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle the mobile menu
        const menuToggle = document.getElementById('menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');

        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Modal functions
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
            document.getElementById('partylistName').focus();
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }

        function openEditModal(id, name) {
            document.getElementById('editPartylistId').value = id;
            document.getElementById('editPartylistName').value = name;
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editPartylistName').focus();
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function openDeleteModal(id, name) {
            document.getElementById('deleteConfirmLink').href = `delete_partylist.php?id=${id}`;
            document.getElementById('deletePartylistName').textContent = name;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // SweetAlert confirmation for logging out
        function confirmLogout(event) {
            event.preventDefault();

            Swal.fire({
                title: 'Log Out?',
                text: "You will be logged out of the election system.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, log out',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280',
                heightAuto: false,
                customClass: {
                    popup: 'rounded-lg'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../index.php';
                }
            });
        }
    </script>
</body>
</html>