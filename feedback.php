<?php
// Include database connection
include 'includes/session.php';

// Destroy session when 'exit' is clicked
if (isset($_POST['exit'])) {
    session_start();
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

// Feedback submission logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['feedback_submit'])) {
    // Sanitize and retrieve form input
    $feedback = $conn->real_escape_string($_POST['feedback']);

    // Check if election_id is set in the session
    if (!isset($_SESSION['election_id'])) {
        $_SESSION['error'] = 'Election ID is not set.';
        header("Location: index.php");
        exit();
    }

    $election_id = $_SESSION['election_id']; // Get the election ID from the session

    // Insert feedback into the database
    $sql = "INSERT INTO feedback (election_id, feedback) VALUES ('$election_id', '$feedback')";
    if ($conn->query($sql)) {
        // Feedback submitted successfully
    } else {
        $_SESSION['error'] = $conn->error;
    }

    // Redirect to index.php after submission
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Submit Feedback</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#70C237',
                        'primary-dark': '#5AA12E',
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-green-50 to-green-100 min-h-screen flex flex-col items-center justify-center p-4">
    <!-- Success animation -->
    <div class="mb-8 text-center">
        <div class="text-6xl text-green-500 float-animation">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-800 mt-4">Thank You For Voting!</h1>
        <p class="text-gray-600">Your vote has been successfully recorded</p>
    </div>

    <div class="bg-white rounded-xl shadow-lg w-full max-w-md overflow-hidden transition-all duration-300 transform hover:shadow-xl">
        <div class="bg-primary p-5 text-white">
            <h2 class="text-xl font-bold text-center">We Value Your Feedback</h2>
            <p class="text-center text-sm opacity-90">Help us improve the voting experience</p>
        </div>
        
        <form action="feedback.php" method="POST" class="p-6">
            <div class="mb-4">
                <label for="feedback" class="block text-gray-700 font-medium mb-2">Share your thoughts:</label>
                <textarea 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                    id="feedback" 
                    name="feedback" 
                    rows="4" 
                    placeholder="Was the voting process easy? Any suggestions for improvement?"
                    required
                ></textarea>
            </div>
            
            <div class="mt-6 flex flex-col md:flex-row gap-3 justify-between">
                <a href="index.php" class="inline-flex items-center justify-center px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-full font-medium transition-colors duration-200">
                    <i class="fas fa-home mr-2"></i> Return Home
                </a>
                <button 
                    type="submit" 
                    name="feedback_submit" 
                    class="inline-flex items-center justify-center px-6 py-3 bg-primary hover:bg-primary-dark text-white rounded-full font-medium transition-colors duration-200"
                >
                    <i class="fas fa-paper-plane mr-2"></i> Submit Feedback
                </button>
            </div>
        </form>

        <div class="bg-gray-50 p-4 text-center border-t border-gray-100">
            <form action="feedback.php" method="POST" class="inline-block">
                <button 
                    type="submit" 
                    name="exit" 
                    class="text-gray-500 hover:text-gray-700 text-sm font-medium"
                >
                    <i class="fas fa-sign-out-alt mr-1"></i> Sign Out
                </button>
            </form>
        </div>
    </div>

    <!-- Additional instructions -->
    <div class="mt-6 text-center text-gray-500 text-sm">
        <p>If you have any issues, please contact the election administrator</p>
    </div>
</body>
</html>