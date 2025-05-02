<?php
session_start();
include 'conn.php';

$error_message = ""; // Initialize error message variable
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['election_code_submit'])) {
    $election_code = $_POST['election_code'];
    $sql = "SELECT id, status FROM elections WHERE election_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $election_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $election = $result->fetch_assoc();

    if ($election) {
        if ($election['status'] == 1) {
            $_SESSION['election_id'] = $election['id'];
            header("Location: votes.php");
            exit();
        } else {
            $_SESSION['election_id'] = $election['id'];
            header("Location: home.php");
            exit();
        }
    } else {
        $error_message = "Invalid election code";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Access</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-image: linear-gradient(135deg, #1e9e6a, #4cd382, #67e796);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }
        
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .login-container {
            width: 400px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 40px;
            animation: fadeIn 1s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            color: #105c3e;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }
        
        .login-header p {
            color: #6b7280;
            font-size: 16px;
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-size: 14px;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 14px;
            background: rgba(255, 255, 255, 0.8);
            border: 2px solid rgba(76, 211, 130, 0.3);
            border-radius: 12px;
            font-size: 16px;
            color: #1f2937;
            outline: none;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #2e7d32;
            background: white;
            box-shadow: 0 0 0 4px rgba(76, 211, 130, 0.2);
        }
        
        .submit-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(45deg, #2e7d32, #4caf50);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .submit-btn:hover {
            background: linear-gradient(45deg, #1b5e20, #43a047);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 125, 50, 0.4);
        }
        
        .links {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            font-size: 14px;
        }
        
        .links a {
            color: #2e7d32;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .links a:hover {
            color: #1b5e20;
            text-decoration: underline;
        }
        
        .error-message {
            background-color: #fee2e2;
            border-left: 4px solid #ef4444;
            color: #b91c1c;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        
        .error-message i {
            margin-right: 8px;
            font-size: 16px;
        }
        
        /* Floating shapes animation */
        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            backdrop-filter: blur(5px);
            z-index: -1;
        }
        
        .shape-1 {
            width: 150px;
            height: 150px;
            top: 15%;
            left: 10%;
            animation: float 8s infinite ease-in-out;
        }
        
        .shape-2 {
            width: 80px;
            height: 80px;
            bottom: 20%;
            right: 15%;
            animation: float 6s infinite ease-in-out;
            animation-delay: 1s;
        }
        
        .shape-3 {
            width: 100px;
            height: 100px;
            bottom: 30%;
            left: 25%;
            animation: float 7s infinite ease-in-out;
            animation-delay: 2s;
        }
        
        .shape-4 {
            width: 60px;
            height: 60px;
            top: 30%;
            right: 25%;
            animation: float 5s infinite ease-in-out;
            animation-delay: 1.5s;
        }

        /* Logo animation */
        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .logo img {
            width: 60px;
            height: 60px;
            object-fit: contain;
            transition: all 0.3s ease;
        }
        
        .logo:hover img {
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <!-- Decorative floating shapes -->
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="shape shape-3"></div>
    <div class="shape shape-4"></div>

    <div class="login-container">
        <div class="login-header">
            <div class="logo">
                <img src="../pics/logo.png" alt="Logo">
            </div>
            <h1>Election Access</h1>
            <p>Enter your election code to continue</p>
        </div>
        
        <?php if (!empty($error_message)) : ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="election_code">Election Code</label>
                <input 
                    type="text" 
                    id="election_code" 
                    name="election_code" 
                    class="form-control" 
                    placeholder="Enter your unique code" 
                    required
                    autocomplete="off"
                >
            </div>
            <button type="submit" name="election_code_submit" class="submit-btn">
                <i class="fas fa-arrow-right-to-bracket mr-2"></i>
                Enter Election
            </button>
        </form>
        
        <div class="links">
            <a href="../index.php">
                <i class="fas fa-arrow-left mr-1"></i> Back to Home
            </a>
            <a href="historylogin.php">
                <i class="fas fa-history mr-1"></i> View Election History
            </a>
        </div>
    </div>
</body>
</html>