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

// Check if POST data is set
if (isset($_POST['sku'], $_POST['cost_price'], $_POST['date_added'], $_POST['expiration_date'], $_POST['manufacturer'])) {

    // Prepare and bind
    $stmt = $conn->prepare("UPDATE item_inventory SET cost_price = ?, date_added = ?, expiration_date = ?, manufacturer = ? WHERE sku = ?");
    $stmt->bind_param("dssss", $cost_price, $date_added, $expiry_date, $manufacturer, $sku);

    // Set parameters and execute
    $cost_price = $_POST['cost_price'];
    $date_added = $_POST['date_added'];
    $expiry_date = $_POST['expiration_date'];
    $manufacturer = $_POST['manufacturer'];
    $sku = $_POST['sku'];

    if ($stmt->execute()) {
        echo "success"; // Send success response
    } else {
        echo "Error: " . $stmt->error; // Send error response
    }

    $stmt->close();
} else {
    echo "Missing form data"; // Handle case where form data is missing
}

$conn->close();
?>
