<?php
session_start();
require 'conn.php';

if (!isset($_SESSION['election_id'])) {
    header("Location: index.php");
    exit();
}

$election_id = $_SESSION['election_id'];
$current_page = basename($_SERVER['PHP_SELF']);

// Function to fetch positions
function getPositions($election_id) {
    global $conn;
    $sql = "SELECT position_id, description, max_vote FROM positions WHERE election_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $election_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Function to create a new position (Removed priority)
function createPosition($election_id, $description, $max_vote) {
    global $conn;
    $sql = "INSERT INTO positions (election_id, description, max_vote) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $election_id, $description, $max_vote);
    return $stmt->execute();
}

// Function to update an existing position (Removed priority)
function updatePosition($id, $description, $max_vote) {
    global $conn;
    $sql = "UPDATE positions SET description = ?, max_vote = ? WHERE position_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $description, $max_vote, $id);
    return $stmt->execute();
}

// Function to delete a position
function deletePosition($id) {
    global $conn;
    $sql = "DELETE FROM positions WHERE position_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Handle form submissions for positions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    $id = isset($_POST['id']) ? $_POST['id'] : null;

    if ($action == "create_position") {
        $description = $_POST['description'];
        $max_vote = $_POST['max_vote'];
        createPosition($election_id, $description, $max_vote);
    } elseif ($action == "update_position") {
        $description = $_POST['description'];
        $max_vote = $_POST['max_vote'];
        updatePosition($id, $description, $max_vote);
    } elseif ($action == "delete_position") {
        deletePosition($id);
    }
}

$positions = getPositions($election_id);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Positions</title>
    <!-- Link to offline Bootstrap CSS -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">

    <style>
          .btn-primary {
        background-color: #2e7d32;
        border: none;
    }
    .btn-primary:hover {
        background-color: #1b5e20;
    }
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
            color: #333;
        }
        .container {
            max-width: 900px;
            margin-top: 50px;
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 128, 0, 0.5);
        }
        .form-control {
            background: #f0f0f0;
            border: 1px solid #28a745;
            color: #333;
        }
        .btn-custom {
            background: #28a745;
            color: white;
            font-weight: bold;
            border: none;
        }
        .btn-custom:hover {
            background: #218838;
        }
        .table thead {
            background: #28a745;
            color: white;
        }
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
    </li>    <li class="nav-item">
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
    <!-- Voters -->
    <li class="nav-item">
        <a class="nav-link <?php echo $current_page == 'voters.php' ? 'active' : ''; ?>" href="voters.php">
            <i class="fas fa-id-card"></i> Voters
        </a>
    </li>
                        <!-- Back to Login -->
<li class="nav-item">
    <a class="btn btn-danger text-white fw-bold" href="../index.php">
        <i class="fas fa-id-card"></i> Logout
    </a>
</li>

</ul>

                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
    <div class="header text-center mb-4">
    <h1>Positions</h1>
    <div class="d-flex justify-content-between">
        <a href="partylist.php" class="btn btn-success"><i class="fas fa-home"></i> Back to Partylists</a>
        <a href="candidates.php" class="btn btn-success">Next: Set Up Candidates <i class="fas fa-arrow-right"></i></a>
    </div>
</div>


        <div class="create-form mb-4">
            <h2>Create Position</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="create_position">
                <div class="form-group mb-3">
                    <input type="text" class="form-control" name="description" placeholder="Description" required>
                </div>
                <div class="form-group mb-3">
                    <input type="number" class="form-control" name="max_vote" placeholder="Max Vote" required>
                </div>
                <button type="submit" class="btn btn-custom w-100">Create Position</button>
            </form>
        </div>

        <h2>Positions List</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Description</th>
                        <th>Max Vote</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $positions->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['position_id']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td><?php echo $row['max_vote']; ?></td>
                        <td class="actions">
                            <button class="btn btn-info btn-sm" onclick="editPosition(<?php echo $row['position_id']; ?>)"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-danger btn-sm" onclick="deletePosition(<?php echo $row['position_id']; ?>)"><i class="fas fa-trash-alt"></i> Delete</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Bootstrap JS (Offline) -->
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>

    <script>
        function editPosition(id) {
            window.location.href = "edit_position.php?id=" + id; // Redirect to edit page
        }

        function deletePosition(id) {
            if (confirm("Are you sure you want to delete this position?")) {
                var form = document.createElement("form");
                form.method = "POST";
                form.action = "";

                var inputAction = document.createElement("input");
                inputAction.type = "hidden";
                inputAction.name = "action";
                inputAction.value = "delete_position";
                form.appendChild(inputAction);

                var inputId = document.createElement("input");
                inputId.type = "hidden";
                inputId.name = "id";
                inputId.value = id;
                form.appendChild(inputId);

                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
