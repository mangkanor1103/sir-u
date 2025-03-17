<?php
// Include database connection
include 'includes/session.php';

// Destroy session when 'exit' is clicked
if (isset($_POST['exit'])) {
    session_start();
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

// Feedback submission logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['feedback_submit'])) {
    // Sanitize and retrieve form input
    $feedback = $conn->real_escape_string($_POST['feedback']);

    // Check if election_id is set in the session
    if (!isset($_SESSION['election_id'])) {
        $_SESSION['error'] = 'Election ID is not set.';
        header("Location: index.php");
        exit();
    }

    $election_id = $_SESSION['election_id']; // Get the election ID from the session

    // Insert feedback into the database
    $sql = "INSERT INTO feedback (election_id, feedback) VALUES ('$election_id', '$feedback')";
    if ($conn->query($sql)) {
        // Feedback submitted successfully
    } else {
        $_SESSION['error'] = $conn->error;
    }

    // Redirect to index.php after submission
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Feedback</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #d4f8e8, #a0e4b0);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Arial', sans-serif;
        }
        .feedback-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            animation: fadeInUp 0.6s ease-in-out;
            width: 100%;
            max-width: 500px;
        }
        .btn-green {
            background-color: #28a745;
            border: none;
        }
        .btn-green:hover {
            background-color: #218838;
        }
        .btn-red {
            background-color: #dc3545;
            border: none;
        }
        .btn-red:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="feedback-container animate__animated animate__fadeInUp">
        <h2 class="text-center text-success">Submit Feedback</h2>
        <form action="feedback.php" method="POST">
            <div class="form-group mt-3">
                <label for="feedback" class="fw-bold">Your Feedback:</label>
                <textarea class="form-control" id="feedback" name="feedback" rows="4" required></textarea>
            </div>
            <p class="text-center text-success mt-3">Thank you for voting! Your vote has been successfully submitted.</p> <!-- Static thank you message -->
            <div class="d-flex justify-content-between mt-4">
                <button type="submit" name="feedback_submit" class="btn btn-green px-4 py-2 text-white">Submit</button>
            </div>
        </form>
    </div>
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>