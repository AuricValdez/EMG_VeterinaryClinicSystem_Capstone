<?php
// Database credentials
$host = 'localhost';
$db = 'emg_database'; // Replace with your database name
$user = 'root';     // Replace with your database username
$pass = '';     // Replace with your database password

try {
    // Establish connection to the database
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Set the email and new password
    $email = 'auricvaldez1@gmail.com'; // Replace with the target email
    $newPassword = '!emgstaffLipa!'; // The password you want to hash

    // Hash the password
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    // Update the password in the admin_users table
    $stmt = $pdo->prepare('UPDATE admin_users SET password = ? WHERE email = ?');
    $stmt->execute([$hashedPassword, $email]);

    echo "Password updated successfully for email: " . htmlspecialchars($email);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close the database connection
$pdo = null;
