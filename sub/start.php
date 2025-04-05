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
$election_query = "SELECT name FROM elections WHERE id = ?";
$stmt = $connection->prepare($election_query);
$stmt->bind_param("i", $election_id);
$stmt->execute();
$result = $stmt->get_result();
$election = $result->fetch_assoc();
$election_name = $election ? $election['name'] : 'Election not found';

// Define an array to store the winners for each position
$winners = array();

// Query to select the candidate with the maximum number of votes for each position
$query = "SELECT position_id, candidate_id, COUNT(*) AS total_votes
          FROM votes
          GROUP BY position_id, candidate_id
          ORDER BY position_id, total_votes DESC";

$result = mysqli_query($connection, $query);

// Loop through the results and store the winner for each position
while ($row = mysqli_fetch_assoc($result)) {
    $position_id = $row['position_id'];
    $candidate_id = $row['candidate_id'];

    // Check if the position already has a winner
    if (!isset($winners[$position_id])) {
        $winners[$position_id] = $candidate_id;
    }
}

// Get the current file name to determine active page
$current_page = basename($_SERVER['PHP_SELF']);

// Handle Start Election
if (isset($_POST['start_election'])) {
    $update_query = "UPDATE elections SET status = 1 WHERE id = ?";
    $update_stmt = $connection->prepare($update_query);
    $update_stmt->bind_param("i", $election_id);
    $update_stmt->execute();

    // Redirect to index.php after updating the status
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Start Election</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-green-50 text-green-900 font-sans">

    <!-- Navigation Bar -->
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

    <!-- Main Content -->
    <div class="container mx-auto mt-10">
        <h2 class="text-3xl font-bold text-center mb-6">Start the Election</h2>
        <p class="text-center text-lg mb-8">Current Election: <span class="font-semibold"><?php echo htmlspecialchars($election_name); ?></span></p>

        <!-- Start Election Button -->
        <div class="flex justify-center">
            <form method="POST" action="">
                <button type="submit" name="start_election" class="bg-green-700 hover:bg-green-800 text-white px-6 py-3 rounded-lg text-lg font-semibold">
                    Start Election
                </button>
            </form>
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
    </script>
</body>
</html>