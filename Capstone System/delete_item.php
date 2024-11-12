<?php
header('Content-Type: application/json');

// Database connection (adjust parameters as needed)
$mysqli = new mysqli("localhost", "root", "", "emg_database");

// Check connection
if ($mysqli->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// Get data from the request
$data = json_decode(file_get_contents("php://input"), true);
$itemId = intval($data['id']);

// Check for valid item ID
if ($itemId <= 0) {
    die(json_encode(['success' => false, 'message' => 'Invalid item ID']));
}

// Fetch the current item details
$sql = "SELECT name, image FROM inventory WHERE id = ?"; // Use 'name' instead of 'item_name'
$stmt = $mysqli->prepare($sql);

// Check if the statement was prepared successfully
if (!$stmt) {
    die(json_encode(['success' => false, 'message' => 'Failed to prepare statement: ' . $mysqli->error]));
}

$stmt->bind_param("i", $itemId);
$stmt->execute();
$result = $stmt->get_result();
$currentItem = $result->fetch_assoc();

if (!$currentItem) {
    // Item not found
    die(json_encode(['success' => false, 'message' => 'Item not found']));
}

$itemName = $currentItem['name']; // Changed from 'item_name' to 'name'
$currentImageFileName = $currentItem['image'];

// Delete the item from the inventory
$sql = "DELETE FROM inventory WHERE id = ?";
$stmt = $mysqli->prepare($sql);

// Check if the statement was prepared successfully
if (!$stmt) {
    die(json_encode(['success' => false, 'message' => 'Failed to prepare delete statement: ' . $mysqli->error]));
}

$stmt->bind_param("i", $itemId);

if ($stmt->execute()) {
    // Delete all rows in item_inventory where item_name matches
    $sql = "DELETE FROM item_inventory WHERE item_name = ?";
    $stmt = $mysqli->prepare($sql);

    // Check if the statement was prepared successfully
    if (!$stmt) {
        die(json_encode(['success' => false, 'message' => 'Failed to prepare delete from item_inventory statement: ' . $mysqli->error]));
    }

    $stmt->bind_param("s", $itemName);
    $stmt->execute();

    // If an image file exists, delete it
    if ($currentImageFileName) {
        $filePath = "uploaded_files/" . $currentImageFileName;
        if (file_exists($filePath)) {
            unlink($filePath); // Delete the old image
        }
    }

    // Delete unused images
    deleteUnusedImages($mysqli);
    
    echo json_encode(['success' => true, 'message' => 'Item deleted successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Deletion failed!']);
}

// Function to delete unused images
function deleteUnusedImages($mysqli) {
    $usedImages = [];
    $sql = "SELECT image FROM inventory";
    $result = $mysqli->query($sql);

    while ($row = $result->fetch_assoc()) {
        $usedImages[] = $row['image'];
    }

    $targetDir = "uploaded_files/";
    $files = scandir($targetDir);
    
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $filePath = $targetDir . $file;
            if (!in_array($filePath, $usedImages) && file_exists($filePath)) {
                unlink($filePath); // Delete unused image
            }
        }
    }
}

// Close connection
$stmt->close();
$mysqli->close();
?>
