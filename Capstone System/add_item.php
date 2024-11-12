<?php
header('Content-Type: application/json');

// Database connection setup
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "emg_database";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Define paths
$tempUploadDir = 'uploaded_files/';
$finalUploadDir = 'uploaded_files/';

// Ensure the directory exists
if (!is_dir($tempUploadDir)) {
    if (!mkdir($tempUploadDir, 0755, true)) {
        echo json_encode(['success' => false, 'message' => 'Failed to create upload directory.']);
        exit;
    }
}

// Handle file upload
$imagePath = '';
if (isset($_FILES['item-image']) && $_FILES['item-image']['error'] === UPLOAD_ERR_OK) {
    $tempFilePath = $tempUploadDir . basename($_FILES['item-image']['name']);
    if (move_uploaded_file($_FILES['item-image']['tmp_name'], $tempFilePath)) {
        $imagePath = $tempFilePath;
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file to temporary location.']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error.']);
    exit;
}

// Prepare SQL statement
$itemName = $_POST['item-name'];
$itemCategory = $_POST['item-category']; // Get category from the form
$itemDescription = $_POST['item-description'];
$itemPrice = $_POST['item-price'];

// Validate inputs
if (empty($itemName) || empty($itemCategory) || empty($itemDescription) || empty($itemPrice)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
    exit;
}

$sql = "INSERT INTO inventory (name, category, description, price, image) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Failed to prepare SQL statement: ' . $conn->error]);
    exit;
}

// Convert price to float for binding
$itemPriceFloat = (float)$itemPrice;

// Bind parameters with the correct types
$stmt->bind_param("sssds", $itemName, $itemCategory, $itemDescription, $itemPriceFloat, $imagePath);

$response = ['success' => false, 'message' => 'Failed to add item'];

if ($stmt->execute()) {
    // If item is successfully added, move the file to the final directory
    if ($imagePath) {
        $finalFilePath = $finalUploadDir . basename($_FILES['item-image']['name']);
        if (rename($imagePath, $finalFilePath)) {
            $imagePath = $finalFilePath;
        } else {
            $response['message'] = 'Failed to move the uploaded file.';
        }
    }
    $response['success'] = true;
    $response['message'] = 'Item added successfully';
} else {
    // Rollback file upload if insertion fails
    if ($imagePath && file_exists($imagePath)) {
        unlink($imagePath);
    }
    $response['message'] = 'Failed to add item to the database: ' . $stmt->error;
}

// Close the connection and statement
$stmt->close();
$conn->close();

// Send JSON response
echo json_encode($response);
?>
