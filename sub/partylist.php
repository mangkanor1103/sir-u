<?php
require 'conn.php';
session_start();

// Fetch current election ID from session
$election_id = $_SESSION['election_id'];
$current_page = basename($_SERVER['PHP_SELF']);

// Fetch election name
$election_query = "SELECT name FROM elections WHERE id = ?";
$stmt = $conn->prepare($election_query);
$stmt->bind_param("i", $election_id);
$stmt->execute();
$result = $stmt->get_result();
$election = $result->fetch_assoc();
$election_name = $election ? $election['name'] : 'Election not found';

// Handle Deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM partylists WHERE partylist_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: partylist.php");
    exit;
}

// Fetch Partylists for this election
$stmt = $conn->prepare("SELECT * FROM partylists WHERE election_id = ?");
$stmt->bind_param("i", $election_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Link to offline Bootstrap CSS -->
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
    <!-- Candidates -->
    <li class="nav-item">
        <a class="nav-link <?php echo $current_page == 'positions.php' ? 'active' : ''; ?>" href="positions.php">
            <i class="fas fa-users"></i> Positions
        </a>
    </li>
    <!-- Positions -->
    <li class="nav-item">
        <a class="nav-link <?php echo $current_page == 'candidates.php' ? 'active' : ''; ?>" href="candidates.php">
            <i class="fas fa-user-tie"></i> Candidates
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

                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
    <h2 class="text-center text-white p-3 rounded" style="background-color: #28a745;">
        Manage Partylists for <?php echo $election_name; ?>
    </h2>

    <div class="d-flex justify-content-between mb-3">
        <a href="add_partylist.php" class="btn btn-success">
            <i class="fas fa-plus"></i> Add Partylist
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered text-center" style="background-color: #ffffff;">
            <thead class="text-white" style="background-color: #218838;">
                <tr>
                    <th>ID</th>
                    <th>Partylist Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['partylist_id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td>
                        <a href="edit_partylist.php?id=<?php echo $row['partylist_id']; ?>" class="btn btn-warning text-white">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="partylist.php?delete=<?php echo $row['partylist_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?');">
                            <i class="fas fa-trash-alt"></i> Delete
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>

</body>
</html>
