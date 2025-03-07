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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background-color: #e8f5e9;
            color: #2e7d32;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 500px; /* Set a max width for the form */
            margin-top: 100px; /* Center vertically */
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-bottom: 20px;
            color: #28a745; /* Header color */
        }
        .btn-success {
            background-color: #28a745;
            border: none;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Add Partylist for Election</h2>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="name">Partylist Name</label>
                <input type="text" class="form-control" name="name" id="name" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Add</button>
            <a href="partylist.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
        </form>
    </div>
</body>
</html>
