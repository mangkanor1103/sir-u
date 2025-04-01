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

// Function to create a new position
function createPosition($election_id, $description, $max_vote) {
    global $conn;
    $sql = "INSERT INTO positions (election_id, description, max_vote) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $election_id, $description, $max_vote);
    return $stmt->execute();
}

// Function to update an existing position
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
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
            color: #333;
        }
        .navbar {
            background-color: black;
        }
        .navbar-nav .nav-link {
            color: #e0e0e0;
            font-size: 16px;
            transition: color 0.3s ease, transform 0.3s ease;
            padding: 10px 15px;
        }
        .navbar-nav .nav-link:hover {
            color: #00ffcc;
            transform: translateY(-2px);
        }
        .header {
            background-color: #28a745;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
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
            transition: background 0.3s;
        }
        .btn-custom:hover {
            background: #218838;
        }
        .table thead {
            background: #28a745;
            color: white;
        }
        .table tbody tr:hover {
            background: #f1f1f1;
        }
        .actions button {
            transition: transform 0.2s;
        }
        .actions button:hover {
            transform: scale(1.1);
        }
        .navbar-nav .nav-link.active {
            color: #00ffcc;
            font-weight: bold;
            text-shadow: 0px 0px 8px rgba(0, 255, 204, 0.8);
        }
        .btn-group-custom {
            display: flex;
            justify-content: flex-end; /* Align buttons to the right */
            gap: 10px; /* Add spacing between buttons */
        }
        .btn-group-custom .btn {
            width: auto; /* Allow buttons to take only necessary width */
            white-space: nowrap; /* Prevent text from wrapping */
        }
    </style>
</head>
<body>
    <!-- Navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
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
                            <i class="fas fa-id-card"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="header text-center mb-4">
            <h1>Positions</h1>
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
                        <th>Description</th>
                        <th>Max Vote</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $positions->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td><?php echo htmlspecialchars($row['max_vote']); ?></td>
                        <td class="actions">
                            <button class="btn btn-info btn-sm" onclick="editPosition(<?php echo $row['position_id']; ?>)"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $row['position_id']; ?>"><i class="fas fa-trash-alt"></i> Delete</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Buttons Section -->
        <div class="btn-group-custom mt-4">
            <a href="partylist.php" class="btn btn-success"><i class="fas fa-home"></i> Back to Partylists</a>
            <a href="candidates.php" class="btn btn-success">Next: Set Up Candidates <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap JS (Offline) -->
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>

    <script>
        // Function to handle position deletion with SweetAlert2
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const positionId = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#dc3545',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Submit the form to delete the position
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '';

                        const actionInput = document.createElement('input');
                        actionInput.type = 'hidden';
                        actionInput.name = 'action';
                        actionInput.value = 'delete_position';
                        form.appendChild(actionInput);

                        const idInput = document.createElement('input');
                        idInput.type = 'hidden';
                        idInput.name = 'id';
                        idInput.value = positionId;
                        form.appendChild(idInput);

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });

        // Function to handle position editing
        function editPosition(id) {
            window.location.href = "edit_position.php?id=" + id; // Redirect to edit page
        }
    </script>
</body>
</html>