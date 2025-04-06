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

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "votingsystem5";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

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
    <title>Dashboard</title>
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
                       onclick="confirmLogout(event);">
                       Logout
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main content -->
    <div class="container mx-auto mt-10">
        <div class="text-center">
            <h1 class="text-4xl font-bold">Current Election: <span class="font-semibold"><?php echo htmlspecialchars($election_name); ?></span></h1>
            <p class="text-lg mt-2">Welcome to the Election Dashboard.</p>
            <p class="text-gray-600 mt-4">Manage your election process step-by-step. Follow the instructions below to set up and start your election.</p>
        </div>

        <!-- Step-by-step horizontal layout -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mt-10">
            <div class="bg-white shadow-md rounded-lg p-6 text-center hover:shadow-lg transition">
                <div class="text-green-700 text-4xl mb-4">📋</div>
                <h5 class="text-xl font-bold mb-2">Step 1: Partylists</h5>
                <p class="text-gray-600">Create and manage the partylists for your election. Ensure all partylists are added before proceeding.</p>
                <a href="partylist.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded mt-4 inline-block">Set Up Partylists</a>
            </div>
            <div class="bg-white shadow-md rounded-lg p-6 text-center hover:shadow-lg transition">
                <div class="text-green-700 text-4xl mb-4">🏛️</div>
                <h5 class="text-xl font-bold mb-2">Step 2: Positions</h5>
                <p class="text-gray-600">Define the positions available in the election. Specify the maximum votes allowed per position.</p>
                <a href="positions.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded mt-4 inline-block">Set Up Positions</a>
            </div>
            <div class="bg-white shadow-md rounded-lg p-6 text-center hover:shadow-lg transition">
                <div class="text-green-700 text-4xl mb-4">👤</div>
                <h5 class="text-xl font-bold mb-2">Step 3: Candidates</h5>
                <p class="text-gray-600">Add candidates for each position. Ensure all candidate details are accurate and complete.</p>
                <a href="candidates.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded mt-4 inline-block">Add Candidates</a>
            </div>
            <div class="bg-white shadow-md rounded-lg p-6 text-center hover:shadow-lg transition">
                <div class="text-green-700 text-4xl mb-4">🗳️</div>
                <h5 class="text-xl font-bold mb-2">Step 4: Voters</h5>
                <p class="text-gray-600">Register voters for the election. Ensure all eligible voters are added to the system.</p>
                <a href="voters.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded mt-4 inline-block">Register Voters</a>
            </div>
            <div class="bg-green-700 text-white shadow-md rounded-lg p-6 text-center hover:shadow-lg transition">
                <div class="text-white text-4xl mb-4">🚀</div>
                <h5 class="text-xl font-bold mb-2">Final Step: Start</h5>
                <p>Start the election process and monitor the results in real-time.</p>
                <a href="start.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded mt-4 inline-block">Start Election</a>
            </div>
        </div>
    </div>

    <script>
        // Toggle the mobile menu
        const menuToggle = document.getElementById('menu-toggle');
        const menu = document.getElementById('menu');

        menuToggle.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });

        // SweetAlert confirmation for logging out
        function confirmLogout(event) {
            event.preventDefault(); // Prevent the default form submission

            Swal.fire({
                title: 'Are you sure?',
                text: "You will be logged out and redirected to the login page.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, log out!',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Logging Out...',
                        text: 'Please wait while you are being logged out.',
                        icon: 'info',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        timer: 2000
                    }).then(() => {
                        // Redirect to ../index.php after logging out
                        window.location.href = '../index.php';
                    });
                }
            });
        }
    </script>

</body>
</html>