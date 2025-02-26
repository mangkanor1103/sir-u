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
    <title>Election Access</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #2e7d32, #388e3c, #43a047);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }
        .container {
            width: 400px;
            padding: 30px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 0 20px #4caf50;
            text-align: center;
            position: relative;
            animation: fadeIn 1s ease-in-out;
        }
        h1 {
            color: #2e7d32;
            font-size: 1.8em;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        label {
            color: #2e7d32;
            font-size: 1em;
            margin-bottom: 5px;
        }
        input[type="text"] {
            width: 90%;
            padding: 12px;
            border: 2px solid #4caf50;
            border-radius: 8px;
            background: rgba(76, 175, 80, 0.1);
            color: #000;
            font-size: 1.2em;
            text-align: center;
            outline: none;
            transition: 0.3s ease-in-out;
        }
        input[type="text"]:focus {
            box-shadow: 0 0 10px #4caf50;
            background: rgba(76, 175, 80, 0.2);
        }
        button {
            padding: 12px;
            margin-top: 20px;
            width: 100%;
            background: linear-gradient(45deg, #66bb6a, #2e7d32);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1.2em;
            font-weight: bold;
            cursor: pointer;
            text-transform: uppercase;
            transition: 0.3s ease-in-out;
            box-shadow: 0 0 10px #4caf50;
        }
        button:hover {
            transform: scale(1.1);
            box-shadow: 0 0 20px #4caf50;
        }
        .home-button {
            margin-top: 20px;
        }
        .home-button a {
            color: #2e7d32;
            text-decoration: none;
            font-weight: bold;
            font-size: 1em;
            transition: 0.3s ease-in-out;
        }
        .home-button a:hover {
            text-shadow: 0 0 10px #4caf50;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Election Access</h1>
        <form method="POST" action="">
            <label>Enter Election Code:</label>
            <input type="text" name="election_code" required>
            <button type="submit" name="election_code_submit">Enter</button>
        </form>
        <div class="home-button">
            <a href="../index.php">Return Home</a>
        </div>
    </div>
</body>
</html>
