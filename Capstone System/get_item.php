<?php
header('Content-Type: application/json');

// Database connection (adjust parameters as needed)
$mysqli = new mysqli("localhost", "root", "", "emg_database");

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Get the item ID from the request
$itemId = intval($_GET['id']);

// Fetch item data
$sql = "SELECT id, name, category, description, price, image FROM inventory WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $itemId);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

// Output item data as JSON
echo json_encode($item);

// Close connection
$stmt->close();
$mysqli->close();
?>
