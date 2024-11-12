<?php
// delete_transaction.php
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

// Get the posted data
$data = json_decode(file_get_contents('php://input'), true);
$transactionId = $data['id'];

// Prepare SQL statements to delete from both tables
$deleteUniqueTransaction = $conn->prepare("DELETE FROM unique_transaction WHERE transaction_id = ?");
$deleteItemTransaction = $conn->prepare("DELETE FROM item_transaction WHERE transaction_id = ?");

// Check if prepare() was successful
if ($deleteUniqueTransaction === false || $deleteItemTransaction === false) {
    echo json_encode(['success' => false, 'message' => 'Error preparing SQL statement']);
    exit;
}

// Bind parameters
$deleteUniqueTransaction->bind_param("i", $transactionId);
$deleteItemTransaction->bind_param("i", $transactionId);

// Execute the deletion
if ($deleteUniqueTransaction->execute() && $deleteItemTransaction->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error deleting transaction']);
}

// Close connections
$deleteUniqueTransaction->close();
$deleteItemTransaction->close();
$conn->close();
?>
