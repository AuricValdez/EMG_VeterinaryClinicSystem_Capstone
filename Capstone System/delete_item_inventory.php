<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "emg_database"; // Adjust this if your database name is different

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if SKU is provided
    if (isset($_POST['sku'])) {
        $sku = $_POST['sku'];

        // Prepare the SQL statement to delete the inventory item
        $stmt = $conn->prepare("DELETE FROM item_inventory WHERE sku = ?");
        $stmt->bind_param("s", $sku);

        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'Failed to delete inventory item: ' . $stmt->error; // Include error message from statement
        }

        $stmt->close();
    } else {
        echo 'SKU not provided.';
    }
} else {
    echo 'Invalid request method.';
}

$conn->close();
?>
