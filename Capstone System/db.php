<?php
// Database connection settings
$host = 'localhost'; // Database host
$dbname = 'emg_database'; // Database name
$user = 'root'; // Database username
$pass = ''; // Database password

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    
    // Set error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Optionally, set the default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Optional: Set the timezone
    $pdo->exec("SET time_zone = '+00:00'"); // Adjust the timezone as needed
    
    // Optional: Enable PDO emulation
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    // Handle connection errors
    echo 'Connection failed: ' . $e->getMessage();
    exit();
}
?>