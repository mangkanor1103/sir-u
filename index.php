<?php
session_start();
if (isset($_SESSION['admin'])) {
    header('location: admin/home.php');
}
?>
<?php include 'includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mindoro State University Online Voting System</title>
    <style>
        body {
            background-image: url('pics/bg.jpg'); /* Replace with your actual image path */
            background-size: cover; /* Ensures the image covers the whole screen */
            background-position: center; /* Centers the image */
            background-repeat: no-repeat;
            color: #fff;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            height: 100vh;
            margin: 0;
            padding-top: 100px; /* Adjusted to push the login box down */
        }
        .login-box {
            background: rgba(0, 0, 0, 0.7);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }
        .logo-left, .logo-right {
            position: absolute;
            top: 10px;
        }
        .logo-left {
            left: 10px;
        }
        .logo-right {
            right: 10px;
        }
        .logo-left img, .logo-right img {
            max-width: 120px;
            border-radius: 50%;
        }
        .login-logo {
            font-size: 1.5em;
            margin-bottom: 20px;
        }
        .login-box-body {
            margin-top: 20px;
        }
        .login-box-msg {
            margin: 0;
            padding: 10px 0;
            font-size: 1.2em;
        }
        .form-group {
            position: relative;
            margin-bottom: 15px;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
        }
        .form-control-feedback {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }
        .btn {
            width: 100%; /* Make buttons full width */
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
        }
        .btn-signin {
            background-color: #28a745; /* Green for Sign In */
            color: #fff;
            width: calc(50% - 8px); /* Change from 100% to make room for the gap */
        }
        .btn-result {
            background-color: #007bff; /* Blue for Result */
            color: #fff;
            width: calc(50% - 8px); /* Change from 100% to make room for the gap */
        }
        .admin-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .btn-admin {
            background-color: #dc3545; /* Red for Admin */
            color: #fff;
            width: 48%; /* Adjust width to fit two buttons */
        }
        .btn-subadmin {
            background-color: #ffc107; /* Yellow for Sub Admin */
            color: #fff;
            width: 48%; /* Adjust width to fit two buttons */
        }
        .button-container {
            display: flex;
            justify-content: space-between; /* Space between Sign In and Result buttons */
            margin-bottom: 20px; /* Space below the button container */
            gap: 15px; /* Add this line to create space between buttons */
        }
        .steps {
            margin-top: 20px;
            text-align: left; /* Align text to the left */
            color: #fff; /* Ensure text is white for visibility */
            background: rgba(0, 0, 0, 0.5); /* Semi-transparent background for contrast */
            padding: 10px; /* Padding around the steps */
            border-radius: 5px; /* Rounded corners */
        }
        .steps h4 {
            margin-bottom: 10px;
        }
        .steps ul {
            list-style-type: disc; /* Use bullet points */
            padding-left: 20px; /* Indent the list */
        }
        @media (max-width: 767px) {
            .logo-left img, .logo-right img {
                max-width: 80px;
            }
            .login-logo {
                font-size: 1.2em;
            }
            body {
                padding-top: 80px; /* Adjusted to push the login box down */
            }
        }
        @media (max-width: 480px) {
            .logo-left img, .logo-right img {
                max-width: 60px;
            }
            .login-logo {
                font-size: 1em;
            }
            .login-box {
                padding: 10px;
            }
            body {
                padding-top: 60px; /* Adjusted to push the login box down */
            }
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo-left">
            <img src="pics/Picture5.jpg" alt="University Logo 1">
        </div>
        <div class="logo-right">
            <img src="pics/logo.png" alt="University Logo 2">
        </div>
        <div class="login-logo">
            <b>Mindoro State University Online Voting System</b>
        </div>
        <div class="login-box-body">
            <p class="login-box-msg">Sign in to start your session</p>
            <form action="process.php" method="POST">
                <div class="form-group has-feedback">
                    <input type="text" class="form-control" name="voter" placeholder="Enter Voter's ID" required>
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                </div>
                <div class="button-container">
                    <button type="submit" class="btn btn-signin" name="login">
                        <i class="fa fa-sign-in"></i> Sign In
                    </button>
                    <button type="submit" class="btn btn-result" name="result">
                        <i class="fa fa-bar-chart"></i> Result
                    </button>
                </div>
            </form>

            <?php
                if (isset($_SESSION['error'])) {
                    echo "
                        <div class='callout callout-danger text-center mt20' style='background-color: #e74c3c;'> <!-- Change callout background color to red -->
                            <p>" . $_SESSION['error'] . "</p>
                        </div>
                    ";
                    unset($_SESSION['error']);
                }
            ?>

            <p class="mt20">If you want to create your own election, please <a href="https://www.facebook.com/kianr873" style="color: green; text-decoration: underline;">contact us</a>.</p>

            <!-- Steps for Voters -->
            <div class="steps">
                <h4>Steps for Voters:</h4>
                <ul>
                    <li>Input the given Voter's ID code.</li>
                    <li>Click "Sign In" to verify your identity.</li>
                    <li>After voting, you can see the real-time results.</li>
                    <li>Re-enter your Voter's ID and click "Result" to view the results.</li>
                </ul>
            </div>

            <p class="mt20">Manage an Election <a href="admin.php" style="color: green; text-decoration: underline;">Click here.</a>.</p>

        </div>
    </div>

    <?php include 'includes/scripts.php' ?>
</body>
</html>