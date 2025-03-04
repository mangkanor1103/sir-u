<?php
require 'conn.php';
session_start();

$election_id = $_SESSION['election_id']; // Get the election ID from session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $stmt = $conn->prepare("INSERT INTO partylists (name, election_id) VALUES (?, ?)");
    $stmt->bind_param("si", $name, $election_id);
    $stmt->execute();
    header("Location: partylist.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Partylist</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Add Partylist for Election</h2>
        <form method="POST" action="">
            <div class="mb-3">
                <label>Partylist Name</label>
                <input type="text" class="form-control" name="name" required>
            </div>
            <button type="submit" class="btn btn-success">Add</button>
            <a href="partylist.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
