<?php
session_start();
require 'conn.php';

if (!isset($_SESSION['election_id'])) {
    header("Location: index.php");
    exit();
}
if (isset($_POST['back'])) {
    unset($_SESSION['election_id']);
    header("Location: index.php");
    exit();
}
$election_id = $_SESSION['election_id'];

// Function to fetch voters
function getVoters($election_id) {
    global $conn;
    $sql = "SELECT * FROM voters WHERE election_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $election_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Function to generate voter codes
function generateVoterCodes($election_id, $count) {
    global $conn;
    $codes = array();
    for ($i = 0; $i < $count; $i++) {
        $code = generateRandomString(10); // Change the length as needed
        $sql = "INSERT INTO voters (election_id, voters_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $election_id, $code);
        $stmt->execute();
        $codes[] = $code;
    }
    return $codes;
}

// Function to generate a random string
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Handle form submission for generating voter codes
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == "generate_voter_codes") {
    $count = $_POST['count'];
    generateVoterCodes($election_id, $count);
}

$voters = getVoters($election_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Voter Codes</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <!-- Navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Election Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="candidates.php">Manage Candidates</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="positions.php">Manage Positions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="voters.php">Manage Voters</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="votes.php">Election Results</a>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="">
                            <button type="submit" name="back" class="btn btn-danger">Back to Login</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="header text-center mb-4">
            <h1>Generate Voter Codes</h1>
            <a href="home.php" class="btn btn-success"><i class="fas fa-home"></i> Back to Dashboard</a>
        </div>

        <div class="create-form mb-4">
            <h2>Generate Voter Codes</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="generate_voter_codes">
                <div class="form-group mb-3">
                    <input type="number" class="form-control" name="count" placeholder="Number of Codes to Generate" required>
                </div>
                <button type="submit" class="btn btn-success">Generate Codes</button>
            </form>
        </div>

        <h2>Generated Voter Codes</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Voter Code</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $voters->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['voters_id']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS (Offline) -->
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
