<?php
// votes.php
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
$current_page = basename($_SERVER['PHP_SELF']);

// Function to fetch all candidates and their votes for each position
function getVotesByPosition($election_id) {
    global $conn;
    $sql = "
        SELECT p.description AS position,
               CONCAT(c.firstname, ' ', c.lastname) AS candidate,
               COALESCE(COUNT(v.candidate_id), 0) AS total_votes
        FROM candidates c
        JOIN positions p ON c.position_id = p.position_id
        LEFT JOIN votes v ON c.id = v.candidate_id AND v.election_id = ?
        WHERE c.election_id = ?
        GROUP BY p.position_id, c.id
        ORDER BY p.position_id, total_votes DESC;
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $election_id, $election_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Fetching the votes by position
$results = getVotesByPosition($election_id);

// Prepare data for each position
$positionsData = [];
while ($row = $results->fetch_assoc()) {
    $positionsData[$row['position']][] = [
        'candidate' => $row['candidate'],
        'total_votes' => $row['total_votes']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Results</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
    <!-- Home -->
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
        <!-- Positions -->
        <li class="nav-item">
        <a class="nav-link <?php echo $current_page == 'positions.php' ? 'active' : ''; ?>" href="positions.php">
            <i class="fas fa-user-tie"></i> Positions
        </a>
    </li>
    <!-- Candidates -->
    <li class="nav-item">
        <a class="nav-link <?php echo $current_page == 'candidates.php' ? 'active' : ''; ?>" href="candidates.php">
            <i class="fas fa-users"></i> Candidates
        </a>
    </li>

    <!-- Voters -->
    <li class="nav-item">
        <a class="nav-link <?php echo $current_page == 'voters.php' ? 'active' : ''; ?>" href="voters.php">
            <i class="fas fa-id-card"></i> Voters
        </a>
    </li>
    <!-- Election Results -->
    <li class="nav-item">
        <a class="nav-link <?php echo $current_page == 'votes.php' ? 'active' : ''; ?>" href="votes.php">
            <i class="fas fa-chart-bar"></i> Election Results
        </a>
    </li>
    <!-- Back to Login -->
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

    <div class="container mt-5">
    <div class="header text-center mb-4">
        <h1>Election Results</h1>
        <a href="home.php" class="btn btn-success"><i class="fas fa-home"></i> Back to Dashboard</a>
    </div>

    <?php foreach ($positionsData as $position => $candidates): ?>
        <?php
        // Get the winner (first candidate in the sorted list)
        $winner = reset($candidates);
        ?>

        <div class="winner-box p-3 mb-3 text-center text-white bg-success">
            <h3>🏆 The winner for <strong><?php echo htmlspecialchars($position); ?></strong> is:</h3>
            <h2 class="fw-bold"><?php echo htmlspecialchars($winner['candidate']); ?></h2>
            <h4>with a total of <strong><?php echo htmlspecialchars($winner['total_votes']); ?></strong> votes!</h4>
        </div>

        <h2 class="mt-4"><?php echo htmlspecialchars($position); ?></h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Candidate</th>
                        <th>Total Votes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($candidates as $candidate): ?>
                        <tr class="<?php echo ($candidate === $winner) ? 'table-success fw-bold' : ''; ?>">
                            <td><?php echo htmlspecialchars($candidate['candidate']); ?></td>
                            <td><?php echo htmlspecialchars($candidate['total_votes']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endforeach; ?>
</div>

<style>
    .winner-box {
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    }
</style>

</body>
</html>
