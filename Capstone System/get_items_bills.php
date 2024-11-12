<?php
$host = 'localhost';
$db = 'emg_database';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update SQL to count stock from item_inventory based on item_name
$sql = "
    SELECT 
        i.name, 
        i.category, 
        i.description, 
        i.price, 
        i.image,
        COUNT(ii.item_name) AS stock
    FROM 
        inventory i
    LEFT JOIN 
        item_inventory ii ON i.name = ii.item_name
    GROUP BY 
        i.name
";
$result = $conn->query($sql);

$items = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['price'] = (float)$row['price']; // Ensure price is a float
        $row['stock'] = (int)$row['stock']; // Ensure stock is an integer
        $items[] = $row;
    }
}
$conn->close();
header('Content-Type: application/json');
echo json_encode($items);
?>
