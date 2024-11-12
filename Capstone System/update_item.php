<?php
header('Content-Type: application/json');

// Database connection (adjust parameters as needed)
$mysqli = new mysqli("localhost", "root", "", "emg_database");

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Get the updated item data from the request
$itemId = $_POST['id'];
$description = $_POST['description'];
$price = $_POST['price'];

// Fetch the current image path from the database
$sql = "SELECT image FROM inventory WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $itemId);
$stmt->execute();
$result = $stmt->get_result();
$existingImagePath = $result->fetch_assoc()['image'];
$stmt->close();

// Handle file upload
$imagePath = $existingImagePath; // Default to the existing image if no new file is uploaded

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $imageTmpName = $_FILES['image']['tmp_name'];
    $imageName = basename($_FILES['image']['name']);
    $uploadDir = 'uploads/'; // Directory to store uploaded files
    $imagePath = $uploadDir . $imageName;

    // Move uploaded file to desired directory
    if (move_uploaded_file($imageTmpName, $imagePath)) {
        // Delete the previous image if it exists and is different from the new one
        if ($existingImagePath && file_exists($existingImagePath) && $existingImagePath !== $imagePath) {
            unlink($existingImagePath); // Delete the old image
        }
    } else {
        // Handle file move error
        $imagePath = $existingImagePath; // Keep the old image path if upload failed
    }
}

// Prepare and execute the update query
$sql = "UPDATE inventory SET description = ?, price = ?, image = ? WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("sdsi", $description, $price, $imagePath, $itemId);
$success = $stmt->execute();

// Output success or error message as JSON
echo json_encode(['success' => $success]);

// Close connection
$stmt->close();
$mysqli->close();
