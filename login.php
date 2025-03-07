<?php
session_start();
include 'includes/conn.php';

if (isset($_POST['login'])) {
    $voter = $_POST['voter'];

    // First check if the voter ID is associated with an active election
    $stmt = $conn->prepare("SELECT v.*, e.status, e.election_code
                           FROM voters v
                           JOIN elections e ON v.election_id = e.id
                           WHERE v.voters_id = ?");
    $stmt->bind_param("s", $voter);
    $stmt->execute();
    $query = $stmt->get_result();

    if ($query->num_rows < 1) {
        $_SESSION['error'] = 'Cannot find voter with the provided ID';
    } else {
        $row = $query->fetch_assoc();

        // Check if the election is active (status = 1)
        if ($row['status'] != 1) {
            $_SESSION['error'] = 'This election has not started yet or has ended';
        } else {
            $_SESSION['voter'] = $row['id']; // Store the voter's unique identifier
            $_SESSION['election_id'] = $row['election_id']; // Store the election ID

            // Redirect to verification page
            header('Location: verification.php');
            exit();
        }
    }

    $stmt->close();
} else {
    $_SESSION['error'] = 'Input voter credentials first';
}

// Redirect back to the login page in case of error
header('Location: index.php');
exit();
?>
