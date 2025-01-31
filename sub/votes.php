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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                        <a class="nav-link" href="voters.php">Manage Voters</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="votes.php">Election Results</a>
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
            <h1>Election Results</h1>
            <a href="home.php" class="btn btn-success"><i class="fas fa-home"></i> Back to Dashboard</a>
        </div>

        <!-- Results table -->
        <h2>Results - Most Votes per Position</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Position</th>
                        <th>Candidate</th>
                        <th>Total Votes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Resetting the result pointer to loop through it again
                    foreach ($positionsData as $position => $candidates) {
                        foreach ($candidates as $candidate) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($position) . "</td>
                                    <td>" . htmlspecialchars($candidate['candidate']) . "</td>
                                    <td>" . htmlspecialchars($candidate['total_votes']) . "</td>
                                  </tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Chart Section for each position -->
        <h2 class="mt-5">Results Bar Charts by Position</h2>
        <?php foreach ($positionsData as $position => $candidates): ?>
            <div class="position-chart mb-5">
                <h3><?php echo htmlspecialchars($position); ?></h3>
                <canvas id="chart-<?php echo htmlspecialchars(str_replace(' ', '-', $position)); ?>" width="400" height="200"></canvas>
                <script>
                    // Prepare data for the chart
                    const labels = <?php echo json_encode(array_column($candidates, 'candidate')); ?>;
                    const votesData = <?php echo json_encode(array_column($candidates, 'total_votes')); ?>;

                    // Ensure all candidates are represented in the chart data
                    const chartData = {
                        labels: labels,
                        datasets: [{
                            label: 'Total Votes',
                            data: votesData,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)',
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)',
                            ],
                            borderWidth: 1
                        }]
                    };

                    // Chart configuration
                    const config = {
                        type: 'bar', // Changed to bar graph
                        data: chartData,
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                title: {
                                    display: true,
                                    text: 'Votes for <?php echo htmlspecialchars($position); ?>'
                                }
                            }
                        }
                    };

                    // Creating the chart
                    new Chart(
                        document.getElementById('chart-<?php echo htmlspecialchars(str_replace(' ', '-', $position)); ?>'),
                        config
                    );
                </script>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Bootstrap JS (Offline) -->
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
