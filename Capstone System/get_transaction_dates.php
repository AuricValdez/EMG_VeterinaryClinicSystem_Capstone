<?php
// get_transaction_dates.php
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

// Query to get unique transaction dates
$sql = "SELECT DISTINCT transaction_date FROM unique_transaction";
$result = $conn->query($sql);

$transactionDates = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $transactionDates[] = $row['transaction_date']; // Assuming this is already in Y-m-d format
    }
}

// Return as JSON
header('Content-Type: application/json');
echo json_encode($transactionDates);

// Close connection
$conn->close();
?>
