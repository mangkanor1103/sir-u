<?php
session_start();
require 'conn.php';

if (!isset($_SESSION['election_id'])) {
    header("Location: index.php");
    exit();
}

$election_id = $_SESSION['election_id'];

// Function to delete a candidate
function deleteCandidate($id) {
    global $conn;
    $sql = "DELETE FROM candidates WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Handle form submissions for candidates
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['back'])) {
        unset($_SESSION['election_id']);
        header("Location: index.php");
        exit();
    }

    $action = $_POST['action'];

    if ($action == "create_candidate") {
        $position_id = $_POST['position_id'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $platform = $_POST['platform'];
        $partylist_id = $_POST['partylist_id']; // Added partylist_id

        // Handle photo upload
        $photo = uploadPhoto($_FILES['photo']);
        if ($photo) {
            if (createCandidate($election_id, $position_id, $firstname, $lastname, $photo, $platform, $partylist_id)) {
              $_SESSION['message'] = "Candidate created successfully!";
              header("Location: candidates.php");
              exit();

            } else {
                echo "<div class='alert alert-danger'>Error creating candidate!</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Error uploading photo!</div>";
        }
    } elseif ($action == "delete_candidate") {
        $id = $_POST['id'];
        if (deleteCandidate($id)) {
            echo "<div class='alert alert-success'>Candidate deleted successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error deleting candidate!</div>";
        }
    }
}

// Function to fetch positions for a specific election
function getPositions($election_id) {
    global $conn;
    $sql = "SELECT * FROM positions WHERE election_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $election_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Function to fetch partylists for a specific election
function getPartylists($election_id) {
    global $conn;
    $sql = "SELECT * FROM partylists WHERE election_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $election_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Function to fetch candidates with position descriptions and partylist names
function getCandidates($election_id) {
    global $conn;
    $sql = "
        SELECT c.id AS candidate_id, c.firstname, c.lastname, c.photo, c.platform,
               p.description AS position_description, pl.name AS partylist_name
        FROM candidates c
        JOIN positions p ON c.position_id = p.position_id
        LEFT JOIN partylists pl ON c.partylist_id = pl.partylist_id
        WHERE c.election_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $election_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Function to create a new candidate
function createCandidate($election_id, $position_id, $firstname, $lastname, $photo, $platform, $partylist_id) {
    global $conn;
    $sql = "INSERT INTO candidates (election_id, position_id, firstname, lastname, photo, platform, partylist_id)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissssi", $election_id, $position_id, $firstname, $lastname, $photo, $platform, $partylist_id);
    return $stmt->execute();
}

// Function to handle file upload
function uploadPhoto($file) {
    $targetDir = "uploads/"; // Directory where images will be saved
    $targetFile = $targetDir . basename($file["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is a valid image type
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return false; // Not a valid image
    }

    // Check if the file already exists
    if (file_exists($targetFile)) {
        return false; // File already exists
    }

    // Limit the file size (example: 2MB)
    if ($file["size"] > 20000000) {
        return false; // File is too large
    }

    // Allow only certain file formats
    $allowedFormats = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowedFormats)) {
        return false; // Invalid file format
    }

    // Move file to the upload directory
    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        return $targetFile; // Return the file path if uploaded successfully
    } else {
        return false; // Upload failed
    }
}

// Fetch candidates, positions, and partylists for the display
$candidates = getCandidates($election_id);
$positions = getPositions($election_id);
$partylists = getPartylists($election_id); // Fetch partylists

// Get the current file name to determine active page
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidates</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        body {
            background-color: #f8fafc;
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
    </li>
    </li>    <li class="nav-item">
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
    <h1>Positions</h1>
    <div class="d-flex justify-content-between">
        <a href="home.php" class="btn btn-success"><i class="fas fa-home"></i> Back to Dashboard</a>
        <a href="candidates.php" class="btn btn-success">Next: Set Up Candidates <i class="fas fa-arrow-right"></i></a>
    </div>

        <div class="card p-4 mb-4 bg-light border-success">
    <h2 class="text-success">Create Candidate</h2>
    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="action" value="create_candidate">

        <div class="row">
            <!-- Left Side -->
            <div class="col-md-6">
                <!-- Position Selection Dropdown -->
                <div class="form-group mb-3">
                    <label for="position" class="text-success">Position</label>
                    <select class="form-control border-success" name="position_id" required>
                        <option value="">Select Position</option>
                        <?php while ($position = $positions->fetch_assoc()): ?>
                            <option value="<?php echo $position['position_id']; ?>">
                                <?php echo $position['description']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Partylist Selection Dropdown -->
                <div class="form-group mb-3">
                    <label for="partylist" class="text-success">Partylist</label>
                    <select class="form-control border-success" name="partylist_id" required>
                        <option value="">Select Partylist</option>
                        <?php while ($partylist = $partylists->fetch_assoc()): ?>
                            <option value="<?php echo $partylist['partylist_id']; ?>">
                                <?php echo $partylist['name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="firstname" class="text-success">First Name</label>
                    <input type="text" class="form-control border-success" name="firstname" placeholder="First Name" required>
                </div>
            </div>

            <!-- Right Side -->
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="lastname" class="text-success">Last Name</label>
                    <input type="text" class="form-control border-success" name="lastname" placeholder="Last Name" required>
                </div>

                <div class="form-group mb-3">
                    <label for="photo" class="text-success">Photo</label>
                    <input type="file" class="form-control border-success" name="photo" accept="image/*" required>
                </div>

                <div class="form-group mb-3">
                    <label for="platform" class="text-success">Platform</label>
                    <textarea class="form-control border-success" name="platform" rows="4" required></textarea>
                </div>
            </div>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-success">Create Candidate</button>
        </div>
    </form>
</div>

<div class="card p-4 bg-light border-success">
    <h2 class="text-success">Existing Candidates</h2>
    <div style="max-height: 400px; overflow-y: auto;">
        <table class="table table-bordered table-hover">
            <thead class="table-success">
                <tr>
                    <th>Position</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Partylist</th>
                    <th>Photo</th>
                    <th>Platform</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($candidate = $candidates->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $candidate['position_description']; ?></td>
                        <td><?php echo $candidate['firstname']; ?></td>
                        <td><?php echo $candidate['lastname']; ?></td>
                        <td><?php echo $candidate['partylist_name'] ?? 'None'; ?></td>
                        <td>
                            <img src="<?php echo $candidate['photo']; ?>" alt="Candidate Photo" class="rounded-circle border border-success" style="width: 50px; height: 50px; object-fit: cover;">
                        </td>
                        <td><?php echo $candidate['platform']; ?></td>
                        <td class="d-flex gap-1">
                            <a href="edit_candidate.php?id=<?php echo $candidate['candidate_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <form method="POST" action="">
                                <input type="hidden" name="id" value="<?php echo $candidate['candidate_id']; ?>">
                                <input type="hidden" name="action" value="delete_candidate">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
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
