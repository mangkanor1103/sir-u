<?php
session_start();
include 'includes/conn.php';

// Redirect to login if not logged in
if (!isset($_SESSION['voter'])) {
    header('Location: index.php');
    exit();
}

// Check if the voter has completed the verification
if (isset($_SESSION['verified']) && $_SESSION['verified'] === true) {
    header('Location: home.php');
    exit();
}

$departments = []; // Initialize an array to hold department data

// Fetch departments from the database
$departmentQuery = $conn->query("SELECT DISTINCT department FROM student");
if ($departmentQuery) {
    while ($row = $departmentQuery->fetch_assoc()) {
        $departments[] = $row['department'];
    }
}

if (isset($_POST['verify'])) {
    $student_id = $_POST['student_id'];
    $name = $_POST['name'];
    $department = $_POST['department']; // Get the selected department

    // Prepare SQL statement to verify student information
    $stmt = $conn->prepare("SELECT * FROM student WHERE student_id = ? AND name = ? AND department = ?");
    $stmt->bind_param("sss", $student_id, $name, $department); // Bind the department parameter
    $stmt->execute();
    $query = $stmt->get_result();

    // Check if the student exists
    if ($query->num_rows < 1) {
        $_SESSION['error'] = 'No matching student found. Please check your information.';
    } else {
        // Student is verified
        $_SESSION['verified'] = true; // Set session variable
        header('Location: home.php');
        exit(); // Ensure to exit after redirection
    }

    $stmt->close();
}

// Prevent caching for this page
header("Cache-Control: no-cache, must-revalidate"); // HTTP 1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #d5e8d4; /* Light green background */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            width: 90%;
            max-width: 400px;
            margin: auto;
            background-color: #ffffff; /* White background */
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
        }
        h2 {
            color: #2c3e50; /* Dark gray color */
            margin-bottom: 20px;
        }
        input[type="text"], select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 1em;
            box-sizing: border-box;
            transition: border 0.3s ease;
        }
        input[type="text"]:focus, select:focus {
            border-color: #218838; /* Dark green border on focus */
        }
        button[type="submit"] {
            padding: 12px;
            background-color: #28a745; /* Slightly darker green */
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        button[type="submit"]:hover {
            background-color: #218838; /* Darker green on hover */
            transform: translateY(-2px);
        }
        .text-danger {
            color: #e74c3c; /* Red for errors */
            font-size: 0.9em;
            margin-bottom: 10px;
        }
        .home-button {
            margin-top: 20px;
        }
        .home-button a {
            padding: 10px 20px;
            background-color: #28a745; /* Green background */
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 500;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .home-button a:hover {
            background-color: #218838; /* Darker green on hover */
            transform: translateY(-2px);
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            .container {
                width: 100%;
                padding: 15px;
            }
            input[type="text"], button[type="submit"], .home-button a {
                font-size: 0.9em;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Verify Your Information</h2>
        <form action="verification.php" method="POST">
            <input type="text" name="student_id" placeholder="Student ID" required>
            <input type="text" name="name" placeholder="Full Name" required>
            <select name="department" required>
                <option value="">Select Department</option>
                <?php foreach ($departments as $dept): ?>
                    <option value="<?= htmlspecialchars($dept) ?>"><?= htmlspecialchars($dept) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="verify">Verify</button>
        </form>

        <?php
            if (isset($_SESSION['error'])) {
                echo "<p class='text-danger'>".htmlspecialchars($_SESSION['error'])."</p>";
                unset($_SESSION['error']);
            }
        ?>

        <div class="home-button">
            <a href="index.php">Home</a>
        </div>
    </div>
</body>
</html>
