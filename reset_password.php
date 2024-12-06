<?php
session_start();
include("database.php");
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Handle password reset request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['request_reset'])) {
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);

    // Check if the email exists in the database
    $sql = "SELECT * FROM users WHERE user = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        // User exists, generate OTP
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['reset_email'] = $email;

        // Send OTP via email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'phpkuben@gmail.com';
            $mail->Password = 'srnq cqiy dqzu kyfl'; 
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('from@example.com', 'EroZone');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset OTP';
            $mail->Body = "Your OTP for password reset is <b>$otp</b>";

            $mail->send();

            // Redirect to OTP verification page
            header("Location: reset_password.php?step=verify");
            exit();
        } catch (Exception $e) {
            echo "Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Email not found.";
    }
}

// Handle OTP verification
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verify_otp'])) {
    $entered_otp = filter_input(INPUT_POST, "otp", FILTER_SANITIZE_NUMBER_INT);
    
    if ($entered_otp == $_SESSION['otp']) {
        // OTP is correct, show the password reset form
        $_SESSION['otp_verified'] = true;
    } else {
        echo "Invalid OTP. Please try again.";
    }
}

// Handle password reset
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_password'])) {
    $new_password = filter_input(INPUT_POST, "new_password", FILTER_SANITIZE_STRING);
    $confirm_password = filter_input(INPUT_POST, "confirm_password", FILTER_SANITIZE_STRING);

    if ($new_password === $confirm_password) {

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the user's password in the database
        $sql = "UPDATE users SET password = ? WHERE user = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $hashed_password, $_SESSION['reset_email']);
        mysqli_stmt_execute($stmt);

        $redirect = isset($_SESSION['redirect']) ? $_SESSION['redirect'] : 'hub.php';
        header("Location: " . $redirect);
        exit();
        
        unset($_SESSION['otp']);
        unset($_SESSION['reset_email']);
        unset($_SESSION['otp_verified']);
    } else {
        echo "Passwords do not match.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="body">
<div class="form-container">
    <h2>Reset Password</h2>

    <?php if (!isset($_GET['step']) || $_GET['step'] == 'request'): ?>
        <form action="" method="POST">
            <div>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>
            <button class="registration-button" type="submit" name="request_reset">Request Password Reset</button>
        </form>

    <?php elseif (isset($_SESSION['otp_verified']) && $_SESSION['otp_verified']): ?>
        <form action="" method="POST">
            <div>
                <input type="password" name="new_password" placeholder="New Password" required>
            </div>
            <div>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            </div>
            <button class="registration-button" type="submit" name="reset_password">Reset Password</button>
        </form>

    <?php else: ?>
        <form action="" method="POST">
            <div>
                <input type="text" name="otp" placeholder="Enter OTP" required>
            </div>
            <button class="registration-button" type="submit" name="verify_otp">Verify OTP</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>