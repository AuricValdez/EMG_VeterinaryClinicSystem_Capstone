<?php
session_start([
    'cookie_lifetime' => 0,
    'cookie_httponly' => true,
    'cookie_secure' => true, // Set this to true if using HTTPS
    'use_strict_mode' => true
]);

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db.php'; // Include the database connection file

// Include the PHPMailer classes
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Define the sendMfaCode function
function sendMfaCode($email, $mfaCode)
{
    $mail = new PHPMailer(true); // Create a new PHPMailer instance
    try {
        // Server settings
        $mail->isSMTP(); // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->Username = 'emglipaclinic@gmail.com'; // SMTP username
        $mail->Password = 'yacy yhdu vuwg dmxs'; // SMTP password or App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
        $mail->Port = 587; // TCP port to connect to

        // Add this section for SSL options
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Recipients
        $mail->setFrom('emglipaclinic@gmail.com', 'EMG Support');
        $mail->addAddress($email); // Add a recipient

        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = 'Your MFA Code';
        $mail->Body = "Your multi-factor authentication code is: <strong>$mfaCode</strong>";
        $mail->AltBody = "Your multi-factor authentication code is: $mfaCode";

        $mail->send(); // Send the email
        return "MFA code sent successfully! Please check your email."; // Return success message
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        // Display the error temporarily for debugging
        return "There was an issue sending your MFA code. Please try again. Error: " . htmlspecialchars($mail->ErrorInfo); // Show error on the page
    }
}

// Check if the form was submitted
if (isset($_POST['login'])) {
    $email = $_POST['username']; // Use email input
    $password = $_POST['password'];

    // Prepare a statement to prevent SQL injection
    $stmt = $pdo->prepare('SELECT id, password, role, is_locked FROM admin_users WHERE email = ?'); // Check against email
    $stmt->execute([$email]); // Execute with the provided email

    // Fetch the user record
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the user exists
    if ($user) {
        // Check if account is locked
        if ($user['is_locked']) {
            $error = 'Your account is locked. Please contact support.'; // Set error message if account is locked
        } else {
            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Store the user's role in the session
                $_SESSION['role'] = $user['role']; // 'admin' or 'staff'

                // Correct credentials; generate MFA code
                $mfaCode = random_int(100000, 999999); // Generate a 6-digit code
                $expiration = date('Y-m-d H:i:s', strtotime('+5 minutes')); // Set code expiration

                // Store the MFA code and expiration in the database
                $stmt = $pdo->prepare('UPDATE admin_users SET mfa_code = ?, mfa_code_expiration = ? WHERE id = ?');
                $stmt->execute([$mfaCode, $expiration, $user['id']]);

                // Send the MFA code to the user's email
                $emailResult = sendMfaCode($email, $mfaCode); // Use $email for sending MFA code

                // Set session data for MFA
                $_SESSION['pending_mfa_username'] = $email; // Store email in session
                $_SESSION['mfa_expiration'] = time() + 300; // Set a 5-minute expiration for MFA

                // Store the result of the email sending in the session
                $_SESSION['email_result'] = $emailResult; // Store email result message in session

                // Redirect to MFA verification page
                header('Location: verify_mfa.php');
                exit();
            } else {
                // Incorrect password
                $error = 'Invalid credentials. Please try again.'; // Set error message for incorrect password
            }
        }
    } else {
        // User not found
        $error = 'No user found with that email address.'; // Set error message if user not found
    }
}

// Include the HTML form at the end
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
    <script src="scripts.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
</head>

<body class="login-body">
    <?php if (!empty($error)) : ?>
        <div class="error-popup"><?= $error ?></div>
    <?php endif; ?>
    <?php if (isset($successMessage)) : ?>
        <div class="success-popup"><?= $successMessage ?></div>
    <?php endif; ?>
    <div class="login-container">
        <div class="login-left">
            <!-- Background image with gradient -->
            <div class="login-image"></div>
        </div>
        <div class="login-right">
            <h2>Login</h2>
            <form method="POST">
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="email" name="username" placeholder="Email" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" name="login" class="login-button">
                    <i class="fa fa-sign-in"></i>
                </button>
            </form>
        </div>
    </div>
</body>

</html>
