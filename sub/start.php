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
$dbname = "votingsystem5";

$connection = mysqli_connect($servername, $username, $password, $dbname);

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

// Check if there are party lists, positions, candidates, and voters for the current election
$partylist_count = $connection->query("SELECT COUNT(*) as count FROM partylists WHERE election_id = $election_id")->fetch_assoc()['count'];
$positions_count = $connection->query("SELECT COUNT(*) as count FROM positions WHERE election_id = $election_id")->fetch_assoc()['count'];
$candidates_count = $connection->query("SELECT COUNT(*) as count FROM candidates WHERE election_id = $election_id")->fetch_assoc()['count'];
$voters_count = $connection->query("SELECT COUNT(*) as count FROM voters WHERE election_id = $election_id")->fetch_assoc()['count'];

// Determine if the "Start Election" button should be enabled
$can_start_election = $partylist_count > 0 && $positions_count > 0 && $candidates_count > 0 && $voters_count > 0;

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
$current_page = basename($_SERVER['PHP_SELF']);

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

    <!-- Welcome Message -->
    <div class="bg-green-100 text-green-900 py-6">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold">Welcome to the Election Dashboard</h1>
            <p class="text-lg mt-2">Manage your election process efficiently and start the election when all requirements are met.</p>
        </div>
    </div>
                    <!-- Navigation Buttons -->
                    <div class="flex justify-between items-center mb-6">
            <a href="candidates.php" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-3 rounded-lg">
                &larr; Back to Candidates
            </a>
            <a href="home.php" class="bg-green-700 hover:bg-green-800 text-white px-6 py-3 rounded-lg">
                &larr; Back to Home
            </a>
        </div>
    

    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h2 class="text-2xl font-bold text-green-700 mb-4">Confirm Logout</h2>
            <p class="text-gray-700 mb-6">Are you sure you want to logout?</p>
            <div class="flex justify-end space-x-4">
                <button onclick="closeLogoutModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Cancel</button>
                <a href="../logout.php" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded">Logout</a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto mt-10 flex space-x-8">
        <!-- Left Side: Election Requirements -->
        <div class="w-1/2 bg-white shadow-md rounded-lg p-6">
            <h3 class="text-2xl font-bold text-green-700 mb-4">Election Requirements</h3>
            <ul class="space-y-2">
                <li class="flex items-center space-x-2">
                    <?php if ($partylist_count > 0): ?>
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>Partylist is available.</span>
                    <?php else: ?>
                        <i class="fas fa-times-circle text-red-500"></i>
                        <span class="text-red-500">No partylist found. Please add a partylist.</span>
                    <?php endif; ?>
                </li>
                <li class="flex items-center space-x-2">
                    <?php if ($positions_count > 0): ?>
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>Positions are available.</span>
                    <?php else: ?>
                        <i class="fas fa-times-circle text-red-500"></i>
                        <span class="text-red-500">No positions found. Please add positions.</span>
                    <?php endif; ?>
                </li>
                <li class="flex items-center space-x-2">
                    <?php if ($candidates_count > 0): ?>
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>Candidates are available.</span>
                    <?php else: ?>
                        <i class="fas fa-times-circle text-red-500"></i>
                        <span class="text-red-500">No candidates found. Please add candidates.</span>
                    <?php endif; ?>
                </li>
                <li class="flex items-center space-x-2">
                    <?php if ($voters_count > 0): ?>
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>Voters are available.</span>
                    <?php else: ?>
                        <i class="fas fa-times-circle text-red-500"></i>
                        <span class="text-red-500">No voters found. Please add voters.</span>
                    <?php endif; ?>
                </li>
            </ul>
        </div>

        <!-- Right Side: Set Election Time -->
        <div class="w-1/2 bg-white shadow-md rounded-lg p-6">
            <h3 class="text-2xl font-bold text-green-700 mb-4 flex items-center space-x-2">
                <i class="fas fa-clock"></i>
                <span>Set Election Time Limit</span>
            </h3>
            <form method="POST" action="">
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
                <button type="submit" name="start_election" class="bg-<?php echo $can_start_election ? 'green-700 hover:bg-green-800' : 'red-500 cursor-not-allowed'; ?> text-white px-6 py-3 rounded-lg text-lg font-semibold flex items-center space-x-2" <?php echo !$can_start_election ? 'disabled' : ''; ?>>
                    <i class="fas fa-play"></i>
                    <span>Start Election</span>
                </button>
                <?php if (!$can_start_election): ?>
                    <p class="text-red-500 text-sm mt-4">You cannot start the election. Please ensure all requirements are met.</p>
                <?php endif; ?>
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

        // Open Logout Modal
        function openLogoutModal(event) {
            event.preventDefault(); // Prevent default link behavior
            document.getElementById('logoutModal').classList.remove('hidden');
        }

        // Close Logout Modal
        function closeLogoutModal() {
            document.getElementById('logoutModal').classList.add('hidden');
        }
    </script>
</body>
</html>