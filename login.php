<?php
session_start();
include 'includes/conn.php';

if (isset($_POST['login'])) {
    $voter = $_POST['voter'];

    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM voters WHERE voters_id = ?");
    $stmt->bind_param("s", $voter);
    $stmt->execute();
    $query = $stmt->get_result();

    // Check if any rows were returned
    if ($query->num_rows < 1) {
        $_SESSION['error'] = 'Cannot find voter with the provided ID';
    } else {
        $row = $query->fetch_assoc();
        $_SESSION['voter'] = $row['id']; // Store the voter's unique identifier in the session
        
        // Redirect to verification page
        header('Location: verification.php');
        exit(); // Ensure to exit after redirection
    }

    $stmt->close();
} else {
    $_SESSION['error'] = 'Input voter credentials first';
}

// Redirect back to the login page in case of error or if login was not attempted
header('Location: index.php');
exit(); // Ensure to exit after redirection
?>
