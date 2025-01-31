<?php
session_start();
require 'conn.php';

// Ensure the function is available
if (!function_exists('getPositions')) {
    function getPositions($election_id) {
        global $conn;
        $sql = "SELECT * FROM positions WHERE election_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $election_id);
        $stmt->execute();
        return $stmt->get_result();
    }
}

if (!isset($_SESSION['election_id'])) {
    header("Location: index.php");
    exit();
}

$election_id = $_SESSION['election_id'];

// Function to fetch a candidate by ID
function getCandidate($id) {
    global $conn;
    $sql = "SELECT * FROM candidates WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Function to update a candidate
function updateCandidate($id, $position_id, $firstname, $lastname, $photo, $platform) {
    global $conn;
    $sql = "UPDATE candidates SET position_id = ?, firstname = ?, lastname = ?, photo = ?, platform = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssi", $position_id, $firstname, $lastname, $photo, $platform, $id);
    return $stmt->execute();
}

// Function to handle photo uploads
function uploadPhoto($file) {
    $targetDir = "uploads/"; // Directory where the photo will be stored
    $targetFile = $targetDir . basename($file["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if the file is an image
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return false; // Not an image
    }

    // Allow certain file formats
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowedTypes)) {
        return false; // Invalid file type
    }

    // Attempt to move the uploaded file to the target directory
    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        return $targetFile; // Return the path of the uploaded file
    } else {
        return false; // Error uploading the file
    }
}

// Handle form submissions for updating a candidate
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $position_id = $_POST['position_id'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $platform = $_POST['platform'];

    // Handle photo upload
    $photo = uploadPhoto($_FILES['photo']);
    if ($photo !== false || empty($_FILES['photo']['name'])) {
        // If no new photo is uploaded, keep the existing photo
        if ($photo === false) {
            $photo = getCandidate($id)['photo'];
        }
        updateCandidate($id, $position_id, $firstname, $lastname, $photo, $platform);
        header("Location: candidates.php"); // Redirect after successful update
        exit();
    } else {
        echo "Error uploading photo!";
    }
}

// Get candidate data to pre-fill the form
$candidate = getCandidate($_GET['id']);
$positions = getPositions($election_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Candidate</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8fafc;
        }
        .photo-preview {
            max-width: 150px; /* Set a maximum width for the photo preview */
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <!-- Navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Election Dashboard</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="candidates.php">Back to Candidates</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Edit Candidate</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $candidate['id']; ?>">
            
            <!-- Position Selection Dropdown -->
            <div class="form-group mb-3">
                <label for="position">Position</label>
                <select class="form-control" name="position_id" required>
                    <option value="">Select Position</option>
                    <?php while ($position = $positions->fetch_assoc()): ?>
                        <option value="<?php echo $position['position_id']; ?>" <?php echo ($position['position_id'] == $candidate['position_id']) ? 'selected' : ''; ?>>
                            <?php echo $position['description']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="firstname">First Name</label>
                <input type="text" class="form-control" name="firstname" value="<?php echo $candidate['firstname']; ?>" required>
            </div>

            <div class="form-group mb-3">
                <label for="lastname">Last Name</label>
                <input type="text" class="form-control" name="lastname" value="<?php echo $candidate['lastname']; ?>" required>
            </div>

            <div class="form-group mb-3">
                <label for="photo">Photo</label>
                <div>
                    <img src="<?php echo $candidate['photo']; ?>" alt="Current Photo" class="photo-preview">
                </div>
                <input type="file" class="form-control" name="photo" accept="image/*">
                <small class="form-text text-muted">Leave blank to keep the current photo.</small>
            </div>

            <div class="form-group mb-3">
                <label for="platform">Platform</label>
                <textarea class="form-control" name="platform" required><?php echo $candidate['platform']; ?></textarea>
            </div>

            <button type="submit" class="btn btn-success">Update Candidate</button>
        </form>
    </div>
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
