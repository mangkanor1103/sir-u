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
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Orbitron', sans-serif;
            margin: 0;
            padding: 0;
            background: radial-gradient(circle, #141e30, #243b55);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }
        .container {
            width: 350px;
            padding: 25px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 15px;
            box-shadow: 0 0 15px #00ffcc;
            text-align: center;
            animation: fadeIn 1s ease-in-out;
        }
        h1 {
            color: #00ffcc;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            color: #00ffcc;
            margin-bottom: 5px;
        }
        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            font-size: 1em;
            text-align: center;
            outline: none;
        }
        input[type="text"]:focus {
            box-shadow: 0 0 10px #00ffcc;
        }
        button {
            padding: 12px;
            margin-top: 15px;
            background: linear-gradient(45deg, #00ffcc, #008080);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        button:hover {
            transform: scale(1.05);
        }
        .home-button {
            margin-top: 20px;
        }
        .home-button a {
            color: #00ffcc;
            text-decoration: none;
            font-weight: bold;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
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
        <h1>Access Election</h1>
        <form method="POST" action="">
            <label>Election Code:</label>
            <input type="text" name="election_code" required>
            <button type="submit" name="election_code_submit">Enter</button>
        </form>
        <div class="home-button">
            <a href="../index.php">Return Home</a>
        </div>
    </div>
</body>
</html>
