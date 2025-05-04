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

require 'conn.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the current election name based on election_id from the session
$election_id = $_SESSION['election_id'];
$election_query = "SELECT name FROM elections WHERE id = ?";
$stmt = $conn->prepare($election_query);
$stmt->bind_param("i", $election_id);
$stmt->execute();
$result = $stmt->get_result();
$election = $result->fetch_assoc();
$election_name = $election ? $election['name'] : 'Election not found';

// Get the current file name to determine active page
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Dashboard | SIR-U</title>
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
            
            <!-- Dashboard Header -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-tachometer-alt text-primary-600 mr-3"></i>
                    Election Dashboard
                </h1>
                <p class="mt-2 text-gray-600 max-w-3xl">
                    Welcome to the Votesys Election System dashboard. Follow the setup process below to configure and launch your election successfully.
                </p>
            </div>

            <!-- Setup Process Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
                <!-- Step 1: Partylists -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-all duration-300">
                    <div class="h-2 bg-primary-500"></div>
                    <div class="p-5">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-primary-100 text-primary-500 mb-4">
                            <i class="fas fa-flag text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold mb-1">Step 1: Partylists</h3>
                        <p class="text-gray-600 text-sm mb-4">Create and manage partylists for your election.</p>
                        <a href="partylist.php" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium">
                            Set Up <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Step 2: Positions -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-all duration-300">
                    <div class="h-2 bg-primary-500"></div>
                    <div class="p-5">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-primary-100 text-primary-500 mb-4">
                            <i class="fas fa-sitemap text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold mb-1">Step 2: Positions</h3>
                        <p class="text-gray-600 text-sm mb-4">Define the available positions and vote limits.</p>
                        <a href="positions.php" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium">
                            Set Up <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Step 3: Candidates -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-all duration-300">
                    <div class="h-2 bg-primary-500"></div>
                    <div class="p-5">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-primary-100 text-primary-500 mb-4">
                            <i class="fas fa-user-tie text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold mb-1">Step 3: Candidates</h3>
                        <p class="text-gray-600 text-sm mb-4">Add and manage candidates for each position.</p>
                        <a href="candidates.php" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium">
                            Set Up <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Step 4: Voters -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-all duration-300">
                    <div class="h-2 bg-primary-500"></div>
                    <div class="p-5">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-primary-100 text-primary-500 mb-4">
                            <i class="fas fa-users text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold mb-1">Step 4: Voters</h3>
                        <p class="text-gray-600 text-sm mb-4">Register and manage eligible voters.</p>
                        <a href="voters.php" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium">
                            Set Up <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Step 5: Start Election -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-all duration-300">
                    <div class="h-2 bg-primary-500"></div>
                    <div class="p-5">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-primary-100 text-primary-500 mb-4">
                            <i class="fas fa-play text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold mb-1">Final Step: Launch</h3>
                        <p class="text-gray-600 text-sm mb-4">Start the election and monitor results.</p>
                        <a href="start.php" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium">
                            Launch <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Quick Stats Section -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-sm flex items-center">
                    <div class="rounded-full bg-primary-100 p-3 mr-4">
                        <i class="fas fa-flag text-primary-600"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500 font-medium">Total Partylists</div>
                        <div class="text-2xl font-bold">
                            <?php
                            // Count partylists for this election
                            $count_query = "SELECT COUNT(*) as count FROM partylists WHERE election_id = ?";
                            $stmt = $conn->prepare($count_query);
                            $stmt->bind_param("i", $election_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $row = $result->fetch_assoc();
                            echo $row['count'];
                            ?>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow-sm flex items-center">
                    <div class="rounded-full bg-primary-100 p-3 mr-4">
                        <i class="fas fa-sitemap text-primary-600"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500 font-medium">Total Positions</div>
                        <div class="text-2xl font-bold">
                            <?php
                            // Count positions for this election
                            $count_query = "SELECT COUNT(*) as count FROM positions WHERE election_id = ?";
                            $stmt = $conn->prepare($count_query);
                            $stmt->bind_param("i", $election_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $row = $result->fetch_assoc();
                            echo $row['count'];
                            ?>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow-sm flex items-center">
                    <div class="rounded-full bg-primary-100 p-3 mr-4">
                        <i class="fas fa-user-tie text-primary-600"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500 font-medium">Total Candidates</div>
                        <div class="text-2xl font-bold">
                            <?php
                            // Count candidates for this election
                            $count_query = "SELECT COUNT(*) as count FROM candidates WHERE election_id = ?";
                            $stmt = $conn->prepare($count_query);
                            $stmt->bind_param("i", $election_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $row = $result->fetch_assoc();
                            echo $row['count'];
                            ?>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow-sm flex items-center">
                    <div class="rounded-full bg-primary-100 p-3 mr-4">
                        <i class="fas fa-users text-primary-600"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500 font-medium">Registered Voters</div>
                        <div class="text-2xl font-bold">
                            <?php
                            // Count voters for this election
                            $count_query = "SELECT COUNT(*) as count FROM voters WHERE election_id = ?";
                            $stmt = $conn->prepare($count_query);
                            $stmt->bind_param("i", $election_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $row = $result->fetch_assoc();
                            echo $row['count'];
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <footer class="mt-12 text-center text-gray-500 text-sm">
            <p>© <?php echo date('Y'); ?> Votesys Election System | All Rights Reserved</p>
            </footer>
        </main>
    </div>

    <script>
        // Toggle the mobile menu
        const menuToggle = document.getElementById('menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');

        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

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
                    Swal.fire({
                        title: 'Logging Out...',
                        text: 'Please wait',
                        icon: 'info',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        timer: 1500,
                        heightAuto: false,
                        customClass: {
                            popup: 'rounded-lg'
                        }
                    }).then(() => {
                        window.location.href = '../index.php';
                    });
                }
            });
        }
    </script>
</body>
</html>