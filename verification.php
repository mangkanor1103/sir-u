<?php
session_start();
include 'includes/conn.php';

// Check if voter session is set
if (!isset($_SESSION['voter'])) {
    $_SESSION['error'] = 'Unauthorized access!';
    header('Location: index.php');
    exit();
}

$voter_id = $_SESSION['voter'];

// Check if voter is already registered
$stmt = $conn->prepare("SELECT * FROM students WHERE voters_id = ?");
$stmt->bind_param("s", $voter_id);
$stmt->execute();
$result = $stmt->get_result();

// If voter is found, redirect to home.php
if ($result->num_rows > 0) {
    header('Location: home.php');
    exit();
}

// If form is submitted
if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $year_section = trim($_POST['year_section']);
    $course = trim($_POST['course']);

    // Insert voter details into students table
    $stmt = $conn->prepare("INSERT INTO students (voters_id, name, year_section, course) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $voter_id, $name, $year_section, $course);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Registration successful!';
        header('Location: home.php'); // Redirect to home after successful registration
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
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
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label><strong>Voter Code:</strong></label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($voter_id); ?>" disabled>
        </div>
        <div class="form-group">
            <label><strong>Name:</strong></label>
            <input type="text" class="form-control" name="name" required>
        </div>
        <div class="form-group">
            <label><strong>Year & Section:</strong></label>
            <input type="text" class="form-control" name="year_section" required>
        </div>
        <div class="form-group">
            <label><strong>Course:</strong></label>
            <input type="text" class="form-control" name="course" required>
        </div>
        <button type="submit" class="btn btn-custom" name="register">Register</button>
    </form>

    <div class="back-button">
        <a href="javascript:history.back()" class="btn btn-secondary">Back</a>
    </div>
</div>

</body>
</html>
