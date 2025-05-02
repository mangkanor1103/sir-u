<?php
session_start();
require 'conn.php';

if (!isset($_SESSION['election_id'])) {
    header("Location: index.php");
    exit();
}

$election_id = $_SESSION['election_id'];
$current_page = basename($_SERVER['PHP_SELF']);

// Fetch the current election name
$election_query = "SELECT name, status FROM elections WHERE id = ?";
$stmt = $conn->prepare($election_query);
$stmt->bind_param("i", $election_id);
$stmt->execute();
$result = $stmt->get_result();
$election = $result->fetch_assoc();
$election_name = $election ? $election['name'] : 'Election not found';
$election_status = $election ? $election['status'] : 0;

// Check if election is active and redirect to votes.php if it is
if ($election_status == 1) {
    $_SESSION['error'] = "Cannot edit voters because the election has already started.";
    header("Location: votes.php");
    exit();
}

// Function to fetch voters with pagination
function getVoters($election_id, $limit = null, $offset = null) {
    global $conn;

    if ($limit !== null && $offset !== null) {
        $sql = "SELECT * FROM voters WHERE election_id = ? ORDER BY id DESC LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $election_id, $limit, $offset);
    } else {
        $sql = "SELECT * FROM voters WHERE election_id = ? ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $election_id);
    }

    $stmt->execute();
    return $stmt->get_result();
}

// Function to count total voters
function countVoters($election_id) {
    global $conn;
    $sql = "SELECT COUNT(*) as total FROM voters WHERE election_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $election_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'];
}

// Function to generate voter codes with prefix
function generateVoterCodes($election_id, $count, $prefix = '', $length = 6) {
    global $conn;

    // Enforce a maximum of 10 characters for the prefix
    $prefix = substr($prefix, 0, 10);

    // Get the election name for default prefix if none provided
    if (empty($prefix)) {
        $sql = "SELECT name FROM elections WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $election_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $row['name']), 0, 10));
        }
    }

    $codes = array();

    for ($i = 0; $i < $count; $i++) {
        $unique = false;
        while (!$unique) {
            $random_part = generateRandomString($length);
            $code = $prefix . $random_part;

            // Check if code already exists
            $sql = "SELECT COUNT(*) as count FROM voters WHERE voters_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $code);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row['count'] == 0) {
                $unique = true;
            }
        }

        // Insert the unique code with the prefix
        $sql = "INSERT INTO voters (election_id, voters_id, prefix) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $election_id, $code, $prefix);
        $stmt->execute();
        $codes[] = $code;
    }

    return $codes;
}

// Function to generate a random string
function generateRandomString($length = 6) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Handle form submission for clearing all voter codes
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == "clear_voter_codes") {
    // Delete votes
    $sql = "DELETE FROM votes WHERE voters_id IN (SELECT id FROM voters WHERE election_id = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $election_id);
    $stmt->execute();

    // Delete voters
    $sql = "DELETE FROM voters WHERE election_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $election_id);
    $stmt->execute();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle form submission for generating voter codes
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == "generate_voter_codes") {
    $count = $_POST['count'];
    $prefix = isset($_POST['prefix']) ? strtoupper($_POST['prefix']) : '';
    $length = isset($_POST['length']) ? (int)$_POST['length'] : 6;

    // Validate length (minimum 4, maximum 10)
    $length = max(4, min(10, $length));

    generateVoterCodes($election_id, $count, $prefix, $length);

    // Redirect to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF'] . "?generated=true");
    exit();
}

// Get the current prefix for pagination
$sql = "SELECT DISTINCT prefix FROM voters WHERE election_id = ? ORDER BY prefix ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $election_id);
$stmt->execute();
$result = $stmt->get_result();
$prefixes = [];
while ($row = $result->fetch_assoc()) {
    $prefixes[] = $row['prefix'];
}

// Randomly select a prefix if none is provided
if (empty($_GET['prefix']) && count($prefixes) > 0) {
    $currentPrefix = $prefixes[array_rand($prefixes)]; // Randomly select a prefix
} else {
    $currentPrefix = isset($_GET['prefix']) ? $_GET['prefix'] : '';
}

// Get voters for the current prefix
$sql = "SELECT * FROM voters WHERE election_id = ? AND prefix = ? ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $election_id, $currentPrefix);
$stmt->execute();
$voters = $stmt->get_result();

// Count total voters for this election
$totalVoters = countVoters($election_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Voters | SIR-U</title>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/css/font-awesome/6.4.0/css/all.min.css">
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
                    <a href="home.php" class="text-xl font-bold tracking-tight">SIR-U Election System</a>
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
                    <i class="fas fa-users text-teal-600 mr-3"></i>
                    Manage Voters
                </h1>
                <p class="mt-2 text-gray-600 max-w-3xl">
                    Generate, print, and manage voter access codes for <span class="font-medium"><?php echo htmlspecialchars($election_name); ?></span>.
                    These codes will be used by voters to access the election.
                </p>
            </div>

            <!-- Stats Card -->
            <div class="bg-gradient-to-r from-teal-500 to-teal-600 rounded-xl shadow-sm mb-6 overflow-hidden">
                <div class="p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium opacity-90">Total Voter Codes</h3>
                            <p class="text-3xl font-bold mt-1"><?php echo $totalVoters; ?></p>
                            <p class="text-sm opacity-80 mt-1">
                                <?php echo count($prefixes); ?> 
                                batch<?php echo count($prefixes) !== 1 ? 'es' : ''; ?> generated
                            </p>
                        </div>
                        <div class="bg-white/20 rounded-full p-4">
                            <i class="fas fa-ticket-alt text-3xl"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-wrap justify-between items-center mb-6 gap-4">
                <div class="flex space-x-2">
                    <a href="candidates.php" class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Candidates
                    </a>
                </div>
                
                <div class="flex space-x-2">
                    <button onclick="openGenerateModal()" class="flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-colors shadow-sm">
                        <i class="fas fa-plus mr-2"></i> Generate New Codes
                    </button>
                    
                    <a href="start.php" 
                       class="flex items-center px-4 py-2 rounded-lg transition-colors shadow-sm
                              <?php echo ($totalVoters > 0) ? 
                                    'bg-primary-600 hover:bg-primary-700 text-white' : 
                                    'bg-gray-300 text-gray-500 cursor-not-allowed'; ?>"
                       <?php echo ($totalVoters > 0) ? '' : 'onclick="return false;"'; ?>>
                        Start Election <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>

            <!-- Warning Message if no voters -->
            <?php if ($totalVoters == 0): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                You must generate at least one voter code to proceed to the next step.
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Print All Codes Button (if any voters) -->
            <?php if ($voters->num_rows > 0): ?>
                <div class="bg-white rounded-lg shadow-sm p-4 mb-6 flex flex-wrap md:flex-nowrap items-center justify-between gap-4">
                    <div>
                        <h3 class="font-semibold text-gray-800">Codes ready for printing</h3>
                        <p class="text-sm text-gray-600">Print all voter codes for distribution</p>
                    </div>
                    <button onclick="printVoterCodes()" class="flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors shadow-sm whitespace-nowrap">
                        <i class="fas fa-print mr-2"></i> Print All Codes
                    </button>
                </div>
            <?php endif; ?>
            
            <!-- Batch Navigation -->
            <?php if (count($prefixes) > 0): ?>
                <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 mb-6">
                    <h3 class="font-medium text-indigo-700 mb-2 flex items-center">
                        <i class="fas fa-layer-group mr-2"></i> Browse Voter Code Batches
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($prefixes as $prefix): ?>
                            <a href="?prefix=<?php echo $prefix; ?>" 
                               class="px-3 py-1 rounded-full text-sm <?php echo ($prefix === $currentPrefix) ? 
                                     'bg-indigo-600 text-white' : 
                                     'bg-white text-indigo-700 border border-indigo-200 hover:bg-indigo-100'; ?>">
                                <?php echo htmlspecialchars($prefix); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Voter Codes Display -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <?php if ($voters->num_rows > 0): ?>
                    <div class="p-4 bg-teal-50 border-b border-teal-100 flex items-center justify-between">
                        <h2 class="text-lg font-medium text-teal-800">
                            <i class="fas fa-ticket-alt mr-2"></i> 
                            Voter Codes for Batch: <?php echo htmlspecialchars($currentPrefix); ?>
                        </h2>
                        
                        <button onclick="confirmClearCodes()" class="text-red-600 hover:text-red-800 text-sm flex items-center">
                            <i class="fas fa-trash-alt mr-1"></i> Clear All Codes
                        </button>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3" id="voterCodesGrid">
                            <?php 
                            mysqli_data_seek($voters, 0); // Reset pointer
                            while ($row = $voters->fetch_assoc()): 
                            ?>
                                <div class="border border-gray-200 rounded-md px-3 py-2 text-center bg-gray-50 hover:bg-teal-50 hover:border-teal-200 transition-colors font-mono">
                                    <?php echo htmlspecialchars($row['voters_id']); ?>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="p-12 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-teal-100 text-teal-500 mb-4">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">No voter codes yet</h3>
                        <?php if (count($prefixes) > 0): ?>
                            <p class="mt-2 text-sm text-gray-500">This batch has no codes. Try selecting another batch or generate new codes.</p>
                        <?php else: ?>
                            <p class="mt-2 text-sm text-gray-500">Get started by generating voter codes for your election.</p>
                            <div class="mt-6">
                                <button onclick="openGenerateModal()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700">
                                    <i class="fas fa-plus mr-2"></i> Generate Voter Codes
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Footer -->
            <footer class="mt-12 text-center text-gray-500 text-sm">
                <p>© <?php echo date('Y'); ?> SIR-U Election System | All Rights Reserved</p>
            </footer>
        </main>
    </div>

    <!-- Generate Voter Codes Modal -->
    <div id="generateModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full transform transition-all">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-ticket-alt text-teal-500 mr-2"></i>
                    Generate Voter Codes
                </h3>
                <button type="button" onclick="closeGenerateModal()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form method="POST" action="" class="p-6 space-y-4">
                <input type="hidden" name="action" value="generate_voter_codes">
                
                <div>
                    <label for="prefix" class="block text-sm font-medium text-gray-700 mb-1">Batch Prefix</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-tag text-gray-400"></i>
                        </div>
                        <input type="text" name="prefix" id="prefix" 
                               class="focus:ring-teal-500 focus:border-teal-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-3" 
                               placeholder="e.g., VOTE" maxlength="10">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Max 10 characters. Leave blank to use election name.</p>
                </div>
                
                <div>
                    <label for="length" class="block text-sm font-medium text-gray-700 mb-1">Code Length</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-text-width text-gray-400"></i>
                        </div>
                        <select name="length" id="length" 
                                class="focus:ring-teal-500 focus:border-teal-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-3">
                            <option value="4">4 characters</option>
                            <option value="6" selected>6 characters</option>
                            <option value="8">8 characters</option>
                            <option value="10">10 characters</option>
                        </select>
                        <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 pointer-events-none">
                            <i class="fas fa-chevron-down"></i>
                        </span>
                    </div>
                </div>
                
                <div>
                    <label for="count" class="block text-sm font-medium text-gray-700 mb-1">Number of Codes</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-hashtag text-gray-400"></i>
                        </div>
                        <input type="number" name="count" id="count" min="1" max="1000" value="20"
                               class="focus:ring-teal-500 focus:border-teal-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-3" 
                               placeholder="Enter number of codes" required>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Between 1 and 1000 codes</p>
                </div>
                
                <div class="flex flex-col space-y-2 bg-yellow-50 border border-yellow-200 rounded-md p-3">
                    <div class="flex items-center text-yellow-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span class="font-medium text-sm">Important Notes:</span>
                    </div>
                    <p class="text-xs text-yellow-700">
                        • Generated codes will be automatically saved to the database<br>
                        • After generation, you can print codes using the print button<br>
                        • Keep voter codes confidential and distribute securely
                    </p>
                </div>
                
                <div class="pt-2 flex justify-end space-x-3">
                    <button type="button" onclick="closeGenerateModal()" 
                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                        <i class="fas fa-plus mr-2"></i> Generate Codes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Clear Codes Confirmation Modal -->
    <div id="clearModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full transform transition-all">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                    Clear All Voter Codes
                </h3>
                <button type="button" onclick="closeClearModal()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="p-6">
                <div class="flex items-center justify-center bg-red-100 rounded-full w-16 h-16 mx-auto mb-4">
                    <i class="fas fa-trash-alt text-2xl text-red-600"></i>
                </div>
                
                <p class="text-center text-gray-800 font-medium">Are you sure you want to delete all voter codes?</p>
                <p class="text-center text-red-600 text-sm mt-4">This will delete ALL voter codes for <strong><?php echo htmlspecialchars($election_name); ?></strong>.</p>
                <p class="text-center text-red-600 text-sm mt-2">This action cannot be undone.</p>
                
                <form method="POST" action="" class="mt-6">
                    <input type="hidden" name="action" value="clear_voter_codes">
                    
                    <div class="flex justify-center space-x-3">
                        <button type="button" onclick="closeClearModal()" 
                                class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                            Cancel
                        </button>
                        <button type="submit" 
                               class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <i class="fas fa-trash-alt mr-2"></i> Delete All Codes
                        </button>
                    </div>
                </form>
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
        function openGenerateModal() {
            document.getElementById('generateModal').classList.remove('hidden');
            document.getElementById('prefix').focus();
        }

        function closeGenerateModal() {
            document.getElementById('generateModal').classList.add('hidden');
        }

        function confirmClearCodes() {
            document.getElementById('clearModal').classList.remove('hidden');
        }

        function closeClearModal() {
            document.getElementById('clearModal').classList.add('hidden');
        }

        // Function to print voter codes
        function printVoterCodes() {
            const voterCodes = document.getElementById('voterCodesGrid');
            const prefix = "<?php echo $currentPrefix; ?>";
            const electionName = "<?php echo addslashes($election_name); ?>";

            const printWindow = window.open('', '', 'width=800,height=600');
            printWindow.document.write('<html><head><title>Print Voter Codes</title>');
            printWindow.document.write('<style>');
            printWindow.document.write(`
                body { 
                    font-family: Arial, sans-serif; 
                    margin: 20px; 
                    color: #064e3b; 
                }
                .grid { 
                    display: grid; 
                    grid-template-columns: repeat(5, 1fr); 
                    gap: 8px; 
                }
                .grid div { 
                    border: 1px solid #d1d5db; 
                    padding: 8px 5px; 
                    text-align: center; 
                    font-size: 13px; 
                    border-radius: 4px;
                    font-family: monospace;
                    background-color: #f9fafb;
                }
                h1 {
                    text-align: center;
                    font-size: 22px;
                    color: #065f46;
                    margin-bottom: 4px;
                }
                h2 {
                    text-align: center;
                    font-size: 16px;
                    color: #047857;
                    margin-bottom: 20px;
                }
                .logo {
                    display: block;
                    margin: 0 auto 10px auto;
                    height: 60px;
                }
                .warning-box {
                    border: 2px solid #dc2626;
                    background-color: #fee2e2;
                    color: #b91c1c;
                    padding: 12px;
                    font-weight: bold;
                    font-size: 14px;
                    margin-bottom: 20px;
                    text-align: center;
                    border-radius: 8px;
                }
                .date {
                    text-align: center;
                    font-size: 12px;
                    color: #6b7280;
                    margin: 30px 0 10px 0;
                }
                .footer {
                    text-align: center;
                    font-size: 11px;
                    color: #9ca3af;
                    margin-top: 20px;
                }
            `);
            printWindow.document.write('</style></head><body>');

            // Replace with the actual path of your logo image
            printWindow.document.write('<img src="../pics/logo.png" class="logo" alt="Logo">');
            printWindow.document.write(`<h1>SIR-U Election System</h1>`);
            printWindow.document.write(`<h2>Voter Codes for: ${electionName}</h2>`);

            printWindow.document.write(`
                <div class="warning-box">
                    ⚠️ CONFIDENTIAL: AUTHORIZED ACCESS ONLY<br>
                    These voter codes are strictly confidential and intended for the election administrator only.<br>
                    Distribution must follow proper protocol to maintain election integrity.<br>
                    Unauthorized sharing or misuse may compromise the election.
                </div>
            `);

            // Clone the voter codes grid and modify it for printing
            const gridClone = voterCodes.cloneNode(true);
            gridClone.classList.remove('grid-cols-2', 'sm:grid-cols-3', 'md:grid-cols-4', 'lg:grid-cols-5');
            gridClone.classList.add('grid');

            // Clean up the elements for printing
            const divs = gridClone.querySelectorAll('div');
            divs.forEach(div => {
                div.classList.remove('hover:bg-teal-50', 'hover:border-teal-200', 'transition-colors');
                div.classList.add('border-gray-300');
            });

            printWindow.document.write(gridClone.outerHTML);
            
            // Add date and footer
            const today = new Date();
            const dateString = today.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            
            printWindow.document.write(`<div class="date">Generated on ${dateString}</div>`);
            printWindow.document.write(`<div class="footer">SIR-U Election System - Batch: ${prefix}</div>`);
            
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
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

        // Show success message when codes are generated
        <?php if (isset($_GET['generated'])): ?>
        Swal.fire({
            title: 'Success!',
            text: 'Voter codes have been generated successfully.',
            icon: 'success',
            confirmButtonColor: '#16a34a',
            customClass: {
                popup: 'rounded-lg'
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>
