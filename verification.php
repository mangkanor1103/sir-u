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
$courses_query = "SELECT * FROM courses";
$courses_result = $conn->query($courses_query);

// If form is submitted
if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $year_section = trim($_POST['year_section']);
    $course = trim($_POST['course']);

    // Check if the student has already registered for this specific election
    $stmt = $conn->prepare("SELECT * FROM students WHERE name = ? AND election_id = ?");
    $stmt->bind_param("si", $name, $election_id);
    $stmt->execute();
    $name_result = $stmt->get_result();

    if ($name_result->num_rows > 0) {
        $_SESSION['error'] = 'This student has already registered for this election.';
        header('Location: verification.php');
        exit();
    }

    // Insert voter details into students table with election_id
    $stmt = $conn->prepare("INSERT INTO students (voters_id, name, year_section, course, election_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $voter_id, $name, $year_section, $course, $election_id);

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
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 font-sans">

<div class="flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md transform transition-all duration-500 hover:scale-105">
        <div class="text-center">
            <i class="fas fa-user-check text-green-500 text-4xl mb-4"></i>
            <h2 class="text-2xl font-bold text-green-600">Verification</h2>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mt-4 rounded">
                <p class="font-bold">Error</p>
                <p><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mt-4 rounded">
                <p class="font-bold">Success</p>
                <p><?= $_SESSION['success']; unset($_SESSION['success']); ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="mt-6">
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-medium">Name</label>
                <input type="text" id="name" name="name" class="w-full px-4 py-2 border border-green-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
            </div>
            <div class="mb-4">
                <label for="year_section" class="block text-gray-700 font-medium">Year and Section</label>
                <select id="year_section" name="year_section" class="w-full px-4 py-2 border border-green-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
                    <option value="">Select Year and Section</option>
                    <?php
                    while ($row = $courses_result->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($row['year_section']) . "'>" . htmlspecialchars($row['year_section']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="course" class="block text-gray-700 font-medium">Course</label>
                <select id="course" name="course" class="w-full px-4 py-2 border border-green-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
                    <option value="">Select Course</option>
                    <?php
                    $courses_result = $conn->query($courses_query); // Re-fetch courses
                    while ($row = $courses_result->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($row['course']) . "'>" . htmlspecialchars($row['course']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" name="register" class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 transition duration-300">
                <i class="fas fa-check-circle"></i> Register
            </button>
        </form>

        <a href="index.php" class="flex items-center justify-center mt-4 text-gray-600 hover:text-green-500 transition duration-300">
            <i class="fas fa-arrow-left mr-2"></i> Back to Home
        </a>
    </div>
</div>

</body>
</html>