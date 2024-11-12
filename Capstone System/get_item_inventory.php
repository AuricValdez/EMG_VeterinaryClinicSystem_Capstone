<?php
// Turn on error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection settings
$host = 'localhost';
$db = 'emg_database';
$user = 'root';
$pass = '';

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection and return a JSON error if it fails
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Get the item_name from the query string
$item_name = isset($_GET['item_name']) ? $_GET['item_name'] : '';

if (empty($item_name)) {
    echo json_encode(['error' => 'Invalid or missing item name']);
    exit;
}

// Prepare and execute the SQL query based on 'item_name'
$sql = "SELECT sku, cost_price, date_added, expiration_date, manufacturer FROM item_inventory WHERE item_name = ?";
$stmt = $conn->prepare($sql);

// Check if the SQL statement was prepared successfully
if (!$stmt) {
    // Output the SQL error message
    echo json_encode(['error' => 'SQL statement preparation failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("s", $item_name); // Use 's' since item_name is a string
$stmt->execute();
$result = $stmt->get_result();

// Fetch the results into an array
$inventoryItems = [];
while ($row = $result->fetch_assoc()) {
    $inventoryItems[] = $row;
}

// Set the response header to JSON
header('Content-Type: application/json');

// Return the JSON-encoded array
echo json_encode($inventoryItems);

// Close the database connection
$stmt->close();
$conn->close();
?>
