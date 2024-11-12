<?php
// Database connection settings
$host = 'localhost';
$db = 'emg_database';
$user = 'root';
$pass = '';

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get total quantity and sales per item
$sql = "SELECT item_name, SUM(quantity) AS total_quantity, SUM(total_price) AS total_price 
        FROM item_transaction
        GROUP BY item_name";
$result = $conn->query($sql);

$items = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $items[] = [
            'item_name' => $row['item_name'],
            'quantity' => $row['total_quantity'],  // Renamed to match expected JS keys
            'total_price' => $row['total_price']
        ];
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($items);
?>
