<?php
// Include database connection
include 'includes/session.php';

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
        // Destroy session after submission
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['error'] = $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Feedback - SIR-U</title>
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
<body class="bg-gray-100 min-h-screen flex flex-col">
    <div class="flex-grow flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Logo and Title -->
            <div class="text-center mb-6">
                <div class="text-6xl text-green-500 float-animation mb-2">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight bg-gradient-to-r from-green-600 to-green-400 bg-clip-text text-transparent mb-3">Thank You For Voting!</h1>
                <p class="text-gray-600 mt-1">Your vote has been successfully recorded</p>
                <div class="h-2 w-32 bg-green-500 mx-auto mt-3 rounded-full"></div>
            </div>
            
            <!-- Feedback Form -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-green-600 to-green-400 p-4 text-white">
                    <h2 class="text-xl font-semibold text-center">Feedback Required</h2>
                    <p class="text-center text-sm opacity-90">Please share your thoughts about the voting experience</p>
                </div>
                
                <form action="feedback.php" method="POST" class="p-6">
                    <div class="mb-4">
                        <label for="feedback" class="block text-gray-700 font-medium mb-2">Your feedback:</label>
                        <textarea 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                            id="feedback" 
                            name="feedback" 
                            rows="4" 
                            placeholder="Was the voting process easy? Any suggestions for improvement?"
                            required
                        ></textarea>
                    </div>
                    
                    <div class="mt-6">
                        <button 
                            type="submit" 
                            name="feedback_submit" 
                            class="w-full py-3 bg-gradient-to-r from-green-600 to-green-400 hover:from-green-700 hover:to-green-500 text-white rounded-lg font-medium transition-colors duration-200 flex items-center justify-center"
                        >
                            <i class="fas fa-paper-plane mr-2"></i> Submit Feedback & Sign Out
                        </button>
                    </div>
                </form>
                
                <!-- Note about feedback being required -->
                <div class="bg-green-50 p-4 border-t border-green-100">
                    <div class="flex items-center">
                        <div class="text-green-500 mr-3">
                            <i class="fas fa-info-circle text-lg"></i>
                        </div>
                        <p class="text-sm text-gray-600">
                            Your feedback is required to complete the voting process and helps us improve future elections.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="mt-6 text-center">
                <p class="text-gray-500 text-sm">© <?php echo date('Y'); ?> Votesys Election System</p>
            </div>
        </div>
    </div>
</body>
</html>