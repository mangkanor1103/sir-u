<?php
require 'conn.php';
session_start();

// Fetch current election ID from session
$election_id  = $_SESSION['election_id'];
$current_page = basename($_SERVER['PHP_SELF']);

// Fetch election name
$election_query = "SELECT name FROM elections WHERE id = ?";
$stmt           = $conn->prepare($election_query);
$stmt->bind_param("i", $election_id);
$stmt->execute();
$result        = $stmt->get_result();
$election      = $result->fetch_assoc();
$election_name = $election ? $election['name'] : 'Election not found';

// Handle Deletion
if (isset($_GET['delete'])) {
    $id   = $_GET['delete'];
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
    <title>Manage Partylists</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body {
            background-color: #e8f5e9;
            color: #2e7d32;
            font-family: 'Poppins', sans-serif;
        }
        .navbar {
            background-color: #2e7d32;
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
        .table {
            background-color: #ffffff;
        }
        .table thead {
            background-color: #218838;
            color: white;
        }
        .table tbody tr:hover {
            background-color: #f1f1f1;
        }
        .btn-success {
            background-color: #28a745;
            border: none;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .btn-warning {
            background-color: #ffc107;
            border: none;
        }
        .btn-warning:hover {
            background-color: #e0a800;
        }
        .btn-danger {
            background-color: #dc3545;
            border: none;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .navbar-nav .nav-link.active {
            color: #00ffcc;
            font-weight: bold;
            text-shadow: 0px 0px 8px rgba(0, 255, 204, 0.8);
        }
        .btn-lg-custom {
            padding: 10px 20px;
            font-size: 1rem;
            white-space: nowrap; /* Prevent text from wrapping */
        }
        @media (max-width: 767.98px) {
            .btn-lg-custom {
                width: 100%; /* Full width on small screens */
                margin-bottom: 10px; /* Add spacing between stacked buttons */
            }
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

    <div class="container mt-5">
        <h2 class="header text-center">
            Manage Partylists for <?php echo $election_name; ?>
        </h2>

        <p class="text-center">Here you can manage the partylists for the current election. You can add new partylists, edit existing ones, or delete them as needed.</p>

        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>Partylist Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['name']; ?></td>
                        <td>
                            <a href="edit_partylist.php?id=<?php echo $row['partylist_id']; ?>" class="btn btn-warning text-white">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="partylist.php?delete=<?php echo $row['partylist_id']; ?>" class="btn btn-danger delete-btn">
                                <i class="fas fa-trash-alt"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Buttons Section -->
        <div class="d-flex flex-column flex-md-row justify-content-between mt-3">
            <!-- Add Partylist Button (Left) -->
            <a href="add_partylist.php" class="btn btn-success btn-lg-custom mb-2 mb-md-0">
                <i class="fas fa-plus"></i> Add Partylist
            </a>

            <!-- Grouped Buttons (Right) -->
            <div class="d-flex flex-column flex-md-row gap-2">
                <a href="home.php" class="btn btn-success btn-lg-custom">
                    <i class="fas fa-home"></i> Back to Dashboard
                </a>
                <a href="positions.php" class="btn btn-success btn-lg-custom">
                    Next: Set Up Positions <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Add event listener to all delete buttons
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent the default link behavior

                // SweetAlert2 confirmation dialog
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
                        // Redirect to the delete URL if confirmed
                        window.location.href = button.getAttribute('href');
                    }
                });
            });
        });
    </script>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>