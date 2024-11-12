<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Manila');

// Database connection settings
$host = 'localhost';
$db = 'emg_database';
$user = 'root';
$pass = '';

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the input data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['items']) && !empty($data['items'])) {
        foreach ($data['items'] as $itemName) {
            // Find and delete items with the closest expiration date
            // Loop through the quantity to delete items
            $stmt = $pdo->prepare("SELECT id FROM item_inventory 
                                    WHERE item_name = :itemName 
                                    ORDER BY expiration_date ASC 
                                    LIMIT 1");
            $stmt->bindParam(':itemName', $itemName);
            $stmt->execute();
            $itemToDelete = $stmt->fetch(PDO::FETCH_ASSOC);

            // If an item is found, delete it
            if ($itemToDelete) {
                // Prepare delete statement
                $deleteStmt = $pdo->prepare("DELETE FROM item_inventory WHERE id = :id");
                $deleteStmt->bindParam(':id', $itemToDelete['id']);
                $deleteStmt->execute();
            } else {
                // Log if no items are found to delete
                error_log("No items found for deletion for item: " . $itemName);
                // You may want to stop further processing or continue
                continue; // Skip to the next item name
            }
        }

        // Return success response
        echo json_encode(['success' => true]);
    } else {
        // Handle error if items are not set
        echo json_encode(['success' => false, 'error' => 'No items found']);
    }
} catch (PDOException $e) {
    // Handle database connection errors
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
