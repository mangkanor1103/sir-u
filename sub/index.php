<?php
// index.php
session_start();
require 'conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['election_code_submit'])) {
    $election_code = $_POST['election_code'];
    $sql = "SELECT id FROM elections WHERE election_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $election_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $election = $result->fetch_assoc();

    if ($election) {
        $_SESSION['election_id'] = $election['id'];
        header("Location: home.php");
        exit();
    } else {
        echo "<div class='error'>Invalid election code</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Futuristic Login</title>
    <link rel="stylesheet" href="css/bootstrap.min.css"> <!-- Offline Bootstrap -->
    <style>
        body {
            background-color: #0a0f0d;
            color: #00ff99;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Arial', sans-serif;
            text-shadow: 0 0 10px #00ff99;
        }
        .container {
            background: rgba(10, 15, 13, 0.9);
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 0 20px #00ff99;
            text-align: center;
            max-width: 600px;
            width: 90%;
        }
        .form-control {
            background: transparent;
            border: 2px solid #00ff99;
            color: #00ff99;
        }
        .form-control:focus {
            background: transparent;
            box-shadow: 0 0 15px #00ff99;
            border-color: #00ff99;
        }
        .btn-custom {
            background-color: #00ff99;
            color: #0a0f0d;
            font-weight: bold;
            transition: all 0.3s ease-in-out;
            padding: 12px;
            font-size: 1.2em;
        }
        .btn-custom:hover {
            background-color: #007755;
            box-shadow: 0 0 15px #00ff99;
        }
        .home-button a {
            color: #00ff99;
            text-decoration: none;
            font-size: 1.2em;
            transition: all 0.3s ease;
        }
        .home-button a:hover {
            text-shadow: 0 0 15px #00ff99;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Enter Election Code</h2>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="election_code" class="form-label">Election Code:</label>
                <input type="text" id="election_code" name="election_code" class="form-control" required>
            </div>
            <button type="submit" name="election_code_submit" class="btn btn-custom w-100">Submit</button>
        </form>
        <div class="home-button mt-3">
            <a href="../index.php">Home</a>
        </div>
    </div>
    <script src="js/bootstrap.bundle.min.js"></script> <!-- Offline Bootstrap -->
</body>
</html>