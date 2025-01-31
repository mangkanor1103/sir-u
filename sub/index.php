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
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #d5e8d4; /* Light green background */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            width: 90%;
            max-width: 400px;
            margin: auto;
            background-color: #ffffff; /* White background */
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
        }
        h1 {
            color: #2c3e50; /* Dark gray color */
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            font-size: 1em;
            color: #34495e;
            margin-bottom: 10px;
            text-align: left;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 1em;
            box-sizing: border-box;
            transition: border 0.3s ease;
        }
        input[type="text"]:focus {
            border-color: #218838; /* Dark green border on focus */
        }
        button[type="submit"] {
            padding: 12px;
            background-color: #28a745; /* Slightly darker green */
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        button[type="submit"]:hover {
            background-color: #218838; /* Darker green on hover */
            transform: translateY(-2px);
        }
        .error {
            color: #e74c3c; /* Red for errors */
            font-size: 0.9em;
            margin-bottom: 10px;
        }
        .home-button {
            margin-top: 20px;
        }
        .home-button a {
            padding: 10px 20px;
            background-color: #28a745; /* Green background */
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 500;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .home-button a:hover {
            background-color: #218838; /* Darker green on hover */
            transform: translateY(-2px);
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            .container {
                width: 100%;
                padding: 15px;
            }
            input[type="text"], button[type="submit"], .home-button a {
                font-size: 0.9em;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
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
        <h1>Enter Election Code</h1>
        <form method="POST" action="">
            <label>Election Code:</label>
            <input type="text" name="election_code" required>
            <button type="submit" name="election_code_submit">Submit</button>
        </form>
        <div class="home-button">
            <a href="../index.php">Home</a>
        </div>
    </div>
</body>
</html>
