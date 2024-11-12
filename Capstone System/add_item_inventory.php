<?php
// Connect to the database
$servername = "localhost"; // Replace with your DB server name
$username = "root"; // Replace with your DB username
$password = ""; // Replace with your DB password
$dbname = "emg_database"; // Replace with your DB name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if required keys are set in the POST request
    $item_name = isset($_POST['item_name']) ? $_POST['item_name'] : '';
    $sku = isset($_POST['sku']) ? $_POST['sku'] : '';
    $cost_price = isset($_POST['cost_price']) ? $_POST['cost_price'] : 0;
    $date_added = isset($_POST['date_added']) ? $_POST['date_added'] : '';
    $expiration_date = isset($_POST['expiration_date']) ? $_POST['expiration_date'] : null; // Ensure this matches the form name
    $manufacturer = isset($_POST['manufacturer']) ? $_POST['manufacturer'] : '';

    // Check if SKU already exists
    $check_sku_sql = "SELECT * FROM item_inventory WHERE sku = ?";
    $stmt = $conn->prepare($check_sku_sql);
    $stmt->bind_param("s", $sku);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // SKU exists, do not insert
        echo "Error: SKU already exists. Please use a different SKU.";
        $stmt->close();
        $conn->close();
        exit; // Exit early
    }

    // Check if expiration_date is empty or invalid
    if (empty($expiration_date) || !DateTime::createFromFormat('Y-m-d', $expiration_date)) {
        $expiration_date = null; // Set to null if it's empty or invalid
    }

    // Prepare SQL query to insert data into item_inventory table
    $sql = "INSERT INTO item_inventory (item_name, sku, cost_price, date_added, expiration_date, manufacturer) 
            VALUES (?, ?, ?, ?, ?, ?)";

    // Prepare the statement
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Error preparing the statement: " . $conn->error);
    }

    // Bind parameters to the SQL query
    $stmt->bind_param("ssdsss", $item_name, $sku, $cost_price, $date_added, $expiration_date, $manufacturer);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Success: Inventory added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>
