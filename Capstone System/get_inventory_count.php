<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection settings
$servername = "localhost"; // Change to your server name
$username = "root"; // Change to your database username
$password = ""; // Change to your database password
$dbname = "emg_database"; // Change to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['item_name'])) {
    echo json_encode(['error' => 'item_name parameter is missing']);
    exit;
}

$item_name = $_GET['item_name'];
$query = "SELECT COUNT(*) as count FROM item_inventory WHERE item_name = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode(['error' => 'Failed to prepare statement: ' . $conn->error]);
    exit;
}

$stmt->bind_param("s", $item_name); // Bind item name as a string
if (!$stmt->execute()) {
    echo json_encode(['error' => 'Query execution failed: ' . $stmt->error]);
    exit;
}

$result = $stmt->get_result();
$data = $result->fetch_assoc();

echo json_encode($data);

// Close the connection
$conn->close();
?>
