<?php
// Database connection settings
$host = 'localhost';
$db = 'emg_database';
$user = 'root';
$pass = '';

header('Content-Type: application/json'); // Ensure the response is always JSON

date_default_timezone_set('Asia/Manila');

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to fetch all items from the 'inventory' table with their prices
    $query = $conn->query("SELECT name, price FROM inventory");
    $items = $query->fetchAll(PDO::FETCH_ASSOC);

    $result = [];

    // Get current date for expiration comparison
    $currentDate = date('Y-m-d');

    // Loop through each item to calculate the quantity (count), stock status, total cost price, and total price
    foreach ($items as $item) {
        $itemName = $item['name'];
        $itemPrice = (float)$item['price']; // Ensure price is a float

        // Count the total number of records in 'item_inventory' where item_name matches
        $stmt = $conn->prepare("
            SELECT 
                COALESCE(COUNT(*), 0) as total_quantity, 
                COALESCE(SUM(cost_price), 0) as total_cost_price 
            FROM item_inventory 
            WHERE item_name = :item_name");
        $stmt->bindParam(':item_name', $itemName);
        $stmt->execute();
        $stockData = $stmt->fetch(PDO::FETCH_ASSOC);

        // Debugging output for stock data
        error_log("Stock Data for $itemName: " . json_encode($stockData));

        $quantity = (int)($stockData['total_quantity']); // Use 0 if no stock is found
        $totalCostPrice = (float)($stockData['total_cost_price'] ?? 0); // Ensure total cost price is a float, default to 0

        // Debugging output for quantity and total cost price
        error_log("Item: $itemName, Quantity: $quantity, Total Cost Price: $totalCostPrice");

        // Debugging output for current date
        error_log("Current Date: " . $currentDate);

        // Count the number of expired items for this item
        $expiredStmt = $conn->prepare("
    SELECT COUNT(*) as expired_stock 
    FROM item_inventory 
    WHERE item_name = :item_name AND expiration_date < DATE_ADD(:current_date, INTERVAL 1 DAY)
");
        $expiredStmt->bindParam(':item_name', $itemName);
        $expiredStmt->bindParam(':current_date', $currentDate);
        $expiredStmt->execute();
        $expiredData = $expiredStmt->fetch(PDO::FETCH_ASSOC);

        $expiredStock = (int)($expiredData['expired_stock'] ?? 0);

        // Debugging output for expired stock
        error_log("Expired Stock for $itemName: " . $expiredStock);


        // Determine the stock status based on the quantity count
        if ($quantity == 0) {
            $stockStatus = 'Out of Stock';
        } elseif ($quantity < 5) {
            $stockStatus = 'Low Stock';
        } elseif ($quantity < 11) {
            $stockStatus = 'Medium Stock';
        } else {
            $stockStatus = 'Well Stocked';
        }

        // Calculate the Total Price
        $totalPrice = $itemPrice * $quantity; // Ensure itemPrice is a float

        // Debugging output for total price
        error_log("Item: $itemName, Total Price: $totalPrice");

        // Add the item details to the result array, including the expired stock
        $result[] = [
            'item_name' => $itemName,
            'quantity' => $quantity,
            'status' => $stockStatus,
            'expired_stock' => $expiredStock,
            'total_cost_price' => $totalCostPrice, // New total cost price field
            'total_price' => $totalPrice // New total price field
        ];
    }

    echo json_encode($result); // Return the result as a JSON array

} catch (PDOException $e) {
    echo json_encode(['error' => 'Connection failed: ' . $e->getMessage()]);
}
