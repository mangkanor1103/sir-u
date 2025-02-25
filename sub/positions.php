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

// Function to fetch positions
function getPositions($election_id) {
    global $conn;
    $sql = "SELECT * FROM positions WHERE election_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $election_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Function to create a new position
function createPosition($election_id, $description, $max_vote, $priority) {
    global $conn;
    $sql = "INSERT INTO positions (election_id, description, max_vote, priority) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isii", $election_id, $description, $max_vote, $priority);
    return $stmt->execute();
}

// Function to update an existing position
function updatePosition($id, $description, $max_vote, $priority) {
    global $conn;
    $sql = "UPDATE positions SET description = ?, max_vote = ?, priority = ? WHERE position_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siii", $description, $max_vote, $priority, $id);
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
        $priority = $_POST['priority'];
        createPosition($election_id, $description, $max_vote, $priority);
    } elseif ($action == "update_position") {
        $description = $_POST['description'];
        $max_vote = $_POST['max_vote'];
        $priority = $_POST['priority'];
        updatePosition($id, $description, $max_vote, $priority);
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
    <style>
        body {
            background-color: #f8fafc;
        }
    </style>
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
                        <a class="nav-link" href="candidates.php">Candidates</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="positions.php">Positions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="voters.php">Voters</a>
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
            <h1>Positions</h1>
            <a href="home.php" class="btn btn-success"><i class="fas fa-home"></i> Back to Dashboard</a>
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
                <div class="form-group mb-3">
                    <input type="number" class="form-control" name="priority" placeholder="Priority" required>
                </div>
                <button type="submit" class="btn btn-success">Create Position</button>
            </form>
        </div>

        <h2>Positions List</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Description</th>
                        <th>Max Vote</th>
                        <th>Priority</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $positions->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['position_id']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td><?php echo $row['max_vote']; ?></td>
                        <td><?php echo $row['priority']; ?></td>
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
