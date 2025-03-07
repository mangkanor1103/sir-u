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
    <title>Dashboard</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .navbar-nav .nav-link {
            font-family: 'Orbitron', sans-serif;
            color: #e0e0e0;
            font-size: 16px;
            transition: color 0.3s ease, transform 0.3s ease;
            position: relative;
            padding: 10px 15px;
        }

        /* Hover Effect */
        .navbar-nav .nav-link:hover {
            color: #00ffcc;
            transform: translateY(-2px); /* Slight lift effect */
        }

        /* Active Page Indicator */
        .navbar-nav .nav-link.active {
            color: #00ffcc;
            font-weight: bold;
            text-shadow: 0px 0px 8px rgba(0, 255, 204, 0.8);
        }

        /* Underline Animation */
        .navbar-nav .nav-link::after {
            content: "";
            display: block;
            width: 0;
            height: 2px;
            background: #00ffcc;
            transition: width 0.3s ease;
            margin-top: 3px;
        }

        .navbar-nav .nav-link:hover::after {
            width: 100%;
        }

        /* Icons Styling */
        .navbar-nav .nav-link i {
            margin-right: 8px;
        }

        body {
            background-color: #e8f5e9;
            color: #2e7d32;
            font-family: Arial, sans-serif;
        }

        .step-container {
            display: flex;
            justify-content: space-around;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .card {
            background-color: white;
            border: 2px solid #2e7d32;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            transition: transform 0.3s ease-in-out;
            width: 200px;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card-title {
            font-weight: bold;
        }

        .btn-primary {
            background-color: #2e7d32;
            border: none;
        }

        .btn-primary:hover {
            background-color: #1b5e20;
        }

        .final-step {
            background-color: #2e7d32;
            color: white;
            border: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="home.php">Election Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'home.php' ? 'active' : ''; ?>" href="home.php">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'partylist.php' ? 'active' : ''; ?>" href="partylist.php">
                            <i class="fas fa-users"></i> Partylist
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'positions.php' ? 'active' : ''; ?>" href="positions.php">
                            <i class="fas fa-users"></i> Positions
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'candidates.php' ? 'active' : ''; ?>" href="candidates.php">
                            <i class="fas fa-user-tie"></i> Candidates
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'voters.php' ? 'active' : ''; ?>" href="voters.php">
                            <i class="fas fa-id-card"></i> Voters
                        </a>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="">
                            <button type="submit" name="back" class="btn btn-danger">
                                <i class="fas fa-sign-out-alt"></i> Back to Login
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main content -->
    <div class="container mt-5">
        <div class="row">
            <div class="col-12 text-center">
                <h1>Welcome to the Election Dashboard</h1>
                <p class="lead">Current Election: <?php echo htmlspecialchars($election_name); ?></p>
            </div>
        </div>

        <!-- Step-by-step horizontal layout -->
        <div class="step-container mt-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Step 1: Positions</h5>
                    <p class="card-text">Set up positions.</p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Step 2: Partylists</h5>
                    <p class="card-text">Set up Partylists.</p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Step 3: Candidates</h5>
                    <p class="card-text">Set up candidates.</p>
                </div>
            </div>
            <div class="card final-step">
                <div class="card-body">
                    <h5 class="card-title">Final Step: Voters</h5>
                    <p class="card-text">Set up voters.</p>
                </div>
            </div>
        </div>

        <!-- Start Election Button -->
        <div class="row mt-4">
            <div class="col-12 text-center">
                <form method="POST" action="">
                    <button type="submit" name="start_election" class="btn btn-primary">Start Election</button>
                </form>
            </div>
        </div>

        <!-- Next Button at the Bottom -->
        <div class="row mt-4">
            <div class="col-12 text-center">
                <a href="partylist.php" class="btn btn-primary">Next: Set Up Partylists</a>
            </div>
        </div>
    </div>

    <!-- Link to offline Bootstrap JS -->
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
