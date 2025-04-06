<?php
session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];

    // Validate the password
    if ($password === "kian1103") {
        // Redirect to history.php if the password is correct
        header("Location: history.php");
        exit();
    } else {
        $error_message = "Invalid password. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login to View History</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gradient-to-br from-green-400 to-green-700 text-white font-sans min-h-screen flex items-center justify-center">
    <div class="bg-white text-green-900 shadow-lg rounded-lg p-8 max-w-lg w-full transform transition duration-500 hover:scale-105">
        <div class="text-center">
            <i class="fas fa-lock text-6xl text-green-500 mb-4 animate-bounce"></i>
            <h2 class="text-4xl font-bold mb-4">Secure Login</h2>
            <p class="text-lg mb-6">Enter the password to access the history page.</p>
        </div>
        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4 text-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="" class="space-y-4">
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-gray-900" placeholder="Enter password" required>
            </div>
            <button type="submit" class="w-full bg-green-700 hover:bg-green-800 text-white px-6 py-3 rounded-lg font-semibold text-lg transform transition duration-300 hover:scale-105">
                <i class="fas fa-sign-in-alt mr-2"></i> Login
            </button>
        </form>
    </div>
</body>
</html>