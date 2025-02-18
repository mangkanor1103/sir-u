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

if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $year_section = trim($_POST['year_section']);
    $course = trim($_POST['course']);

    // Check if voter is already registered
    $stmt = $conn->prepare("SELECT * FROM students WHERE voters_id = ?");
    $stmt->bind_param("s", $voter_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = 'You are already registered!';
    } else {
        // Insert voter details into students table
        $stmt = $conn->prepare("INSERT INTO students (voters_id, name, year_section, course) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $voter_id, $name, $year_section, $course);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Registration successful!';
            header('Location: home.php'); // Redirect to home or voting page
            exit();
        } else {
            $_SESSION['error'] = 'Registration failed!';
        }
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
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #000;
            color: #fff;
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            width: 350px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
        }
        .btn {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .disabled {
            background: #ccc;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Verification</h2>
        <?php if (isset($_SESSION['error'])): ?>
            <p style="color: red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <p style="color: green;"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label>Voter Code:</label>
                <input type="text" class="form-control disabled" value="<?php echo htmlspecialchars($voter_id); ?>" disabled>
            </div>
            <div class="form-group">
                <label>Name:</label>
                <input type="text" class="form-control" name="name" required>
            </div>
            <div class="form-group">
                <label>Year & Section:</label>
                <input type="text" class="form-control" name="year_section" required>
            </div>
            <div class="form-group">
                <label>Course:</label>
                <input type="text" class="form-control" name="course" required>
            </div>
            <button type="submit" class="btn" name="register">Register</button>
        </form>
    </div>
</body>
</html>
