
<?php
session_start();
include 'includes/conn.php';

// Check if voter session is set
if (!isset($_SESSION['voter']) || !isset($_SESSION['election_id'])) {
    $_SESSION['error'] = 'Unauthorized access!';
    header('Location: index.php');
    exit();
}

$voter_id = $_SESSION['voter'];
$election_id = $_SESSION['election_id'];

// Check if voter is already registered for this specific election
$stmt = $conn->prepare("SELECT * FROM students WHERE voters_id = ? AND election_id = ?");
$stmt->bind_param("si", $voter_id, $election_id);
$stmt->execute();
$result = $stmt->get_result();

// If voter is found in current election, redirect to home.php
if ($result->num_rows > 0) {
    header('Location: home.php');
    exit();
}

// Fetch courses for dropdown
$courses_query = "SELECT * FROM courses"; // Adjust this query if needed
$courses_result = $conn->query($courses_query);

// If form is submitted
if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $year_section = trim($_POST['year_section']);
    $course = trim($_POST['course']);

    // Handle photo upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $targetDir = "pics/students/"; // Directory to save uploaded photos
        $fileName = basename($_FILES['photo']['name']);
        $targetFilePath = $targetDir . $fileName;

        // Check if the uploads directory exists, if not create it
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Move the uploaded file to the target directory
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $targetFilePath)) {
            $_SESSION['error'] = 'Photo upload failed!';
            header('Location: verification.php');
            exit();
        }
    } else {
        $_SESSION['error'] = 'No photo uploaded or there was an error.';
        header('Location: verification.php');
        exit();
    }

    // Check if the student has already voted in this specific election
    $stmt = $conn->prepare("SELECT * FROM students WHERE name = ? AND election_id = ?");
    $stmt->bind_param("si", $name, $election_id);
    $stmt->execute();
    $name_result = $stmt->get_result();

    if ($name_result->num_rows > 0) {
        $_SESSION['error'] = 'This student has already registered for this election.';
        header('Location: verification.php');
        exit();
    }

    // Insert voter details into students table with election_id and photo path
    $stmt = $conn->prepare("INSERT INTO students (voters_id, name, year_section, course, election_id, photo) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $voter_id, $name, $year_section, $course, $election_id, $targetFilePath);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Registration successful!';
        header('Location: home.php');
        exit();
    } else {
        $_SESSION['error'] = 'Registration failed!';
    }
    $stmt->close();
    header('Location: verification.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification</title>
    <link href="https://fonts.googleapis.com/css2 ?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }
        .verification-container {
            max-width: 400px;
            margin: 80px auto;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .verification-container h2 {
            color: #28a745;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .form-group {
            text-align: left;
        }
        .form-control {
            border-radius: 5px;
            border: 1px solid #28a745;
            padding: 10px;
        }
        .btn-custom {
            background: #28a745;
            color: white;
            font-weight: bold;
            border: none;
            width: 100%;
            padding: 10px;
            margin-top: 15px;
            border-radius: 5px;
            transition: 0.3s;
        }
        .btn-custom:hover {
            background: #218838;
        }
        .alert {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left: 5px solid #28a745;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 5px solid #dc3545;
        }
        .back-button {
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="verification-container">
    <h2>Verification</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="year_section">Year and Section</label>
            <select class="form-control" id="year_section" name="year_section" required>
                <option value="">Select Year and Section</option>
                <?php
                // Fetch year and section from courses
                $courses_query = "SELECT year_section FROM courses"; // Adjust this query if needed
                $courses_result = $conn->query($courses_query);
                while ($row = $courses_result->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($row['year_section']) . "'>" . htmlspecialchars($row['year_section']) . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="course">Course</label>
            <select class="form-control" id="course" name="course" required>
                <option value="">Select Course</option>
                <?php
                // Fetch courses for dropdown
                $courses_query = "SELECT course FROM courses"; // Adjust this query if needed
                $courses_result = $conn->query($courses_query);
                while ($row = $courses_result->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($row['course']) . "'>" . htmlspecialchars($row['course']) . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="cameraInput">Take Photo</label>
            <input type="file" class="form-control" id="cameraInput" name="photo" accept="image/*" capture="user" required>
            <small class="text-muted">Use your camera to take a photo for verification.</small>
        </div>
        <button type="submit" name="register" class="btn btn-custom">Register</button>
    </form>
    <a href="index.php" class="btn btn-secondary back-button">Back to Home</a>
 </div>

<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>