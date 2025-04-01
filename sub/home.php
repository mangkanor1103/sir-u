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
        body {
            background-color: #e8f5e9;
            color: #2e7d32;
            font-family: 'Poppins', sans-serif;
            transition: background-color 0.5s ease;
        }

        .navbar-nav .nav-link {
            color: #e0e0e0;
            font-size: 16px;
            transition: color 0.3s ease, transform 0.3s ease;
            position: relative;
            padding: 10px 15px;
        }

        .navbar-nav .nav-link:hover {
            color: #00ffcc;
            transform: translateY(-2px);
        }

        .navbar-nav .nav-link.active {
            color: #00ffcc;
            font-weight: bold;
            text-shadow: 0px 0px 8px rgba(0, 255, 204, 0.8);
        }

        .step-container {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 25px;
    flex-wrap: wrap;
    margin: 0 auto;
    max-width: 950px;
}

.card {
    background-color: white;
    border: 2px solid #2e7d32;
    border-radius: 10px;
    padding: 15px;
    text-align: center;
    transition: transform 0.3s ease-in-out, box-shadow 0.3s ease;
    width: 190px;
    height: 160px; /* Setting a fixed height for all cards */
    display: flex;
    flex-direction: column;
    justify-content: center;
    animation: fadeIn 0.5s ease forwards;
}

.final-step {
    background-color: #2e7d32;
    color: white;
    border: none;
    font-weight: bold;
    width: 190px; /* Same width as other cards */
    height: 160px; /* Same height as other cards */
}

        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .card-title {
            font-weight: bold;
            font-size: 1.2em;
            margin-bottom: 10px;
        }

        .btn-primary {
    background-color: #2e7d32;
    border: none;
    transition: background-color 0.3s ease;
    padding: 10px 20px; /* Increased padding */
    font-size: 1.1rem; /* Increased font size */
    font-weight: 500; /* Added some font weight */
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

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
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
                            <i class="fas fa-user-tie"></i> Positions
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
                        <a class="btn btn-danger text-white fw-bold" href="../index.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
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
        <!-- Step-by-step horizontal layout -->
<div class="step-container mt-4">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Step 1: Partylists</h5>
            <p class="card-text">Set up partylists.</p>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Step 2: Positions</h5>
            <p class="card-text">Set up positions.</p>
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

<!-- Buttons below the steps -->
<div class="row mt-5">
    <div class="col-12 text-center">
        <div class="d-flex justify-content-center gap-4">
            <form method="POST" action="">
                <button type="submit" name="start_election" class="btn btn-primary btn-lg">Start Election</button>
            </form>
            <a href="partylist.php" class="btn btn-primary btn-lg">Set Up Partylists</a>
        </div>
    </div>
</div>

    <!-- Link to offline Bootstrap JS -->
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>