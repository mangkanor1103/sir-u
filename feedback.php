<?php
// Include session management
include 'includes/session.php';

// Logic to destroy session when 'exit' is clicked
if (isset($_POST['exit'])) {
    session_start();
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session
    header("Location: index.php"); // Redirect to the login page
    exit();
}

// Feedback submission logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['feedback_submit'])) {
    // Sanitize and retrieve form input
    $feedback = $conn->real_escape_string($_POST['feedback']);
    $voter_id = $voter['id']; // Assuming voter info is stored in session
    $election_id = $_POST['election_id']; // Pass the election_id from the feedback form

    // Insert feedback into the database
    $sql = "INSERT INTO feedback (voter_id, election_id, feedback) VALUES ('$voter_id', '$election_id', '$feedback')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success'>Feedback submitted successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Feedback</title>
    <!-- Offline Bootstrap CSS -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Submit Feedback</h2>

        <!-- Feedback form -->
        <form action="feedback.php" method="POST">
            <div class="form-group">
                <label for="feedback">Your Feedback:</label>
                <textarea class="form-control" id="feedback" name="feedback" rows="4" required></textarea>
            </div>

            <!-- Pass the election ID via a hidden input field -->
            <input type="hidden" name="election_id" value="1"> <!-- Set election ID dynamically if needed -->

            <!-- Submit Feedback Button -->
            <button type="submit" name="feedback_submit" class="btn btn-primary">Submit Feedback</button>

            <!-- Exit Button -->
            <button type="submit" name="exit" class="btn btn-danger">Exit</button>
        </form>

        <!-- Success/Error messages will be echoed by the PHP script -->
    </div>

    <!-- Offline Bootstrap JS -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
