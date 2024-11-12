<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection settings
$host = 'localhost';
$db = 'emg_database';
$user = 'root';
$pass = '';

// Create a new database connection
$conn = new mysqli($host, $user, $pass, $db);

// Check for connection errors
if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
}

// Get SKU from the query parameter
$sku = isset($_GET['sku']) ? $_GET['sku'] : '';

// Prepare and execute the SQL query
$stmt = $conn->prepare("SELECT sku, cost_price, date_added, expiration_date, manufacturer FROM item_inventory WHERE sku = ?");
$stmt->bind_param("s", $sku);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the item data
if ($result->num_rows > 0) {
    $item = $result->fetch_assoc();
    echo json_encode($item); // Return item data as JSON
} else {
    echo json_encode(['error' => 'Item not found.']); // Return error message as JSON
}

// Close the database connection
$stmt->close();
$conn->close();
?>
