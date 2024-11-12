<?php
header('Content-Type: application/json');

// Database connection (adjust parameters as needed)
$mysqli = new mysqli("localhost", "root", "", "emg_database");

// Check connection
if ($mysqli->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// Get data from the request
$itemId = intval($_POST['id']);
$itemName = $_POST['name'];
$itemCategory = $_POST['category']; // Get the category from the request
$itemDescription = $_POST['description'];
$itemPrice = $_POST['price'];

// Fetch the current image file name
$sql = "SELECT image FROM inventory WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $itemId);
$stmt->execute();
$result = $stmt->get_result();
$currentItem = $result->fetch_assoc();

if (!$currentItem) {
    // Item not found
    die(json_encode(['success' => false, 'message' => 'Item not found']));
}

$currentImageFileName = $currentItem['image'];

// Handle file upload
$imageFileName = null;
if (isset($_FILES['item-image']) && $_FILES['item-image']['error'] == UPLOAD_ERR_OK) {
    $targetDir = "uploaded_files/"; // Directory where files will be stored
    $imageFileName = $targetDir . basename($_FILES['item-image']['name']);

    // Move uploaded file
    if (!move_uploaded_file($_FILES['item-image']['tmp_name'], $imageFileName)) {
        die(json_encode(['success' => false, 'message' => 'File upload failed']));
    }

    // Delete the previous image if it exists
    if ($currentImageFileName) {
        $oldFilePath = $currentImageFileName;
        if (file_exists($oldFilePath)) {
            unlink($oldFilePath); // Delete the old image
        }
    }
}

// Update item data in the database
$sql = "UPDATE inventory SET name = ?, category = ?, description = ?, price = ?" . ($imageFileName ? ", image = ?" : "") . " WHERE id = ?";
$stmt = $mysqli->prepare($sql);

if ($imageFileName) {
    // If an image is uploaded
    $stmt->bind_param("sssssi", $itemName, $itemCategory, $itemDescription, $itemPrice, $imageFileName, $itemId);
} else {
    // If no new image is uploaded
    $stmt->bind_param("ssssi", $itemName, $itemCategory, $itemDescription, $itemPrice, $itemId);
}

if ($stmt->execute()) {
    // Delete images that are not in the database anymore
    deleteUnusedImages($mysqli);
    echo json_encode(['success' => true, 'message' => 'Item updated successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Update failed']);
}

// Function to delete unused images
function deleteUnusedImages($mysqli)
{
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
