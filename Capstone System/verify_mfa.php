<?php
session_start();
if (!isset($_SESSION['role'])) {
    // Redirect to login if not authenticated
    header("Location: login.php");
    exit();
}

require_once 'db.php';

// Redirect to login if no MFA session exists
if (!isset($_SESSION['pending_mfa_username'])) {
    header('Location: login.php');
    exit();
}

$email = $_SESSION['pending_mfa_username'];
$maxAttempts = 3;
$error = '';

// Initialize attempt counter if not set
if (!isset($_SESSION['mfa_attempts'])) {
    $_SESSION['mfa_attempts'] = 0;
}

// Check if the session has expired
if (time() > $_SESSION['mfa_expiration']) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}

// Fetch user information
$stmt = $pdo->prepare('SELECT is_locked, lockout_until, mfa_code, mfa_code_expiration FROM admin_users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if account is locked
if ($user && $user['is_locked']) {
    $error = 'Your account is locked due to too many failed attempts. Please contact support.';
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Check for maximum attempts
        if ($_SESSION['mfa_attempts'] >= $maxAttempts) {
            // Lock the account (set is_locked to 1 or set lockout_until)
            $stmt = $pdo->prepare('UPDATE admin_users SET is_locked = 1 WHERE email = ?');
            $stmt->execute([$email]);
            session_unset();
            session_destroy();
            header('Location: login.php');
            exit();
        }

        // Get and verify the MFA code
        $mfaCode = $_POST['mfa_code'];

        // Verify MFA code and expiration
        if ($user && $user['mfa_code'] === $mfaCode && strtotime($user['mfa_code_expiration']) > time()) {
            // Successful MFA
            $_SESSION['username'] = $email; // Store email in session
            $_SESSION['mfa_attempts'] = 0;

            // Clear MFA code from database
            $stmt = $pdo->prepare('UPDATE admin_users SET mfa_code = NULL, mfa_code_expiration = NULL WHERE email = ?');
            $stmt->execute([$email]);

            unset($_SESSION['pending_mfa_username']);
            header('Location: dashboard.php');
            exit();
        } else {
            // Increment attempts and set error message
            $_SESSION['mfa_attempts']++;
            $error = 'Invalid or expired MFA code. Please try again.';
        }
    }
}

// Remaining attempts calculation
$remainingAttempts = $maxAttempts - $_SESSION['mfa_attempts'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify MFA</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .verify-button {
            background-color: #f17603;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: transform 0.3s ease, box-shadow 0.3s ease, width 0.3s ease;
            max-width: 100%;
            width: 100px;
            margin-left: 60px;
            display: block;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .verify-button:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            transform: scale(1.2);
        }

        .attempts-info {
            color: #555;
            margin-top: 10px;
            margin-left: 30px;
        }

        .login-right {
            background: none;
        }

        .error-popup {
            color: whie;
            text-align: center;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: #f17603;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            transition: all 0.3s;
        }

        .back-button:hover {
            background-color: #e06500;
            transform: scale(1.05);
        }
    </style>
</head>

<body class="login-body">
    <?php if (!empty($error)) : ?>
        <div class="error-popup"><?= $error ?></div>
    <?php endif; ?>
    <div class="login-container">
        <div class="login-right">
        <a href="login.php?reset_mfa=true" class="back-button">Back</a> <!-- Add this line for the back button -->
            <h2>Multi-Factor Authentication</h2>
            <?php if (isset($_SESSION['email_result'])): ?>
                <div class="<?= strpos($_SESSION['email_result'], 'successfully') !== false ? 'success-popup' : 'error-popup' ?>">
                    <?= $_SESSION['email_result'] ?>
                </div>
                <?php unset($_SESSION['email_result']); // Clear the message after displaying 
                ?>
            <?php endif; ?>
            <p>A code has been sent to your email. Please enter it below:</p>
            <form method="POST">
                <div class="input-group">
                    <input type="text" name="mfa_code" placeholder="Enter MFA code" required>
                </div>
                <button type="submit" class="verify-button">Verify</button>
            </form>
            <?php if ($_SESSION['mfa_attempts'] > 0): ?>
                <div class="attempts-info">
                    You have <?= $remainingAttempts ?> attempts remaining.
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>