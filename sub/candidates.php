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

        // Handle photo upload
        $photo = uploadPhoto($_FILES['photo']);
        if ($photo) {
            if (createCandidate($election_id, $position_id, $firstname, $lastname, $photo, $platform)) {
                echo "<div class='alert alert-success'>Candidate created successfully!</div>";
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

// Function to fetch candidates with position descriptions
function getCandidates($election_id) {
    global $conn;
    $sql = "
        SELECT c.id AS candidate_id, c.firstname, c.lastname, c.photo, c.platform, p.description AS position_description
        FROM candidates c
        JOIN positions p ON c.position_id = p.position_id
        WHERE c.election_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $election_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Function to create a new candidate
function createCandidate($election_id, $position_id, $firstname, $lastname, $photo, $platform) {
    global $conn;
    $sql = "INSERT INTO candidates (election_id, position_id, firstname, lastname, photo, platform) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissss", $election_id, $position_id, $firstname, $lastname, $photo, $platform);
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
    if ($file["size"] > 2000000) {
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

// Fetch candidates and positions for the display
$candidates = getCandidates($election_id);
$positions = getPositions($election_id);

// Get the current file name to determine active page
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Candidates</title>
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
                        <a class="nav-link <?php echo $current_page == 'home.php' ? 'active' : ''; ?>" href="home.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'candidates.php' ? 'active' : ''; ?>" href="candidates.php">Manage Candidates</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'positions.php' ? 'active' : ''; ?>" href="positions.php">Manage Positions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'voters.php' ? 'active' : ''; ?>" href="voters.php">Manage Voters</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'votes.php' ? 'active' : ''; ?>" href="votes.php">Manage Votes</a>
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
            <h1>Manage Candidates</h1>
            <a href="home.php" class="btn btn-success"><i class="fas fa-home"></i> Back to Dashboard</a>
        </div>

        <div class="card p-4 mb-4">
            <h2>Create Candidate</h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="action" value="create_candidate">
                
                <!-- Position Selection Dropdown -->
                <div class="form-group mb-3">
                    <label for="position">Position</label>
                    <select class="form-control" name="position_id" required>
                        <option value="">Select Position</option>
                        <?php while ($position = $positions->fetch_assoc()): ?>
                            <option value="<?php echo $position['position_id']; ?>"><?php echo $position['description']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="firstname">First Name</label>
                    <input type="text" class="form-control" name="firstname" placeholder="First Name" required>
                </div>

                <div class="form-group mb-3">
                    <label for="lastname">Last Name</label>
                    <input type="text" class="form-control" name="lastname" placeholder="Last Name" required>
                </div>

                <div class="form-group mb-3">
                    <label for="photo">Photo</label>
                    <input type="file" class="form-control" name="photo" accept="image/*" required>
                </div>

                <div class="form-group mb-3">
                    <label for="platform">Platform</label>
                    <textarea class="form-control" name="platform" rows="4" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Create Candidate</button>
            </form>
        </div>

        <div class="card p-4">
            <h2>Existing Candidates</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Position</th>
                        <th>First Name</th>
                        <th>Last Name</th>
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
                            <td><img src="<?php echo $candidate['photo']; ?>" alt="Candidate Photo" style="width: 50px; height: 50px;"></td>
                            <td><?php echo $candidate['platform']; ?></td>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="id" value="<?php echo $candidate['candidate_id']; ?>">
                                    <input type="hidden" name="action" value="delete_candidate">
                                    <button type="submit" class="btn btn-danger">Delete</button>
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
