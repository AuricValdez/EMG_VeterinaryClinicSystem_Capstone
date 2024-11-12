<?php
// inventory_data.php

// Database connection details
$host = 'localhost'; // your database host
$dbname = 'emg_database'; // your database name
$username = 'root'; // your database username
$password = ''; // your database password

try {
    // Establish connection to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare and execute the query
    $stmt = $pdo->prepare('SELECT id, name, category, description, price, image FROM inventory');
    $stmt->execute();

    // Fetch all items as an associative array
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Set the response header to JSON
    header('Content-Type: application/json');
    
    // Return the items as JSON
    echo json_encode($items);

} catch (PDOException $e) {
    // Log the error for debugging (optional)
    error_log("Database Error: " . $e->getMessage());

    // Return the error as JSON
    header('Content-Type: application/json', true, 500); // Set 500 internal server error
    echo json_encode(['error' => 'Database connection failed.']);
}
?> 
