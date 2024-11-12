<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection settings
$host = 'localhost';
$db = 'emg_database';
$user = 'root';
$pass = '';

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get today's date in GMT+8
    $dateTime = new DateTime('now', new DateTimeZone('Asia/Manila'));
    $date = $dateTime->format('Y-m-d');

    // Prepare the SQL query to fetch only today's transactions
    $stmt = $pdo->prepare("
        SELECT ut.transaction_id, ut.transaction_date, ut.transaction_time, it.item_name, it.quantity, it.total_price
        FROM unique_transaction ut
        JOIN item_transaction it ON ut.transaction_id = it.transaction_id
        WHERE ut.transaction_date = :date
        ORDER BY ut.transaction_time
    ");
    $stmt->execute(['date' => $date]);

    // Fetch and organize results by transaction ID
    $transactions = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Initialize transaction if it hasn't been added yet
        if (!isset($transactions[$row['transaction_id']])) {
            $transactions[$row['transaction_id']] = [
                'id' => $row['transaction_id'],
                'date' => $row['transaction_date'],
                'time' => $row['transaction_time'],
                'items' => []
            ];
        }

        // Add item to the transaction's items array
        $transactions[$row['transaction_id']]['items'][] = [
            'name' => $row['item_name'],
            'quantity' => $row['quantity'],
            'totalPrice' => $row['total_price']
        ];
    }

    // Send the results as JSON with success status
    echo json_encode(['success' => true, 'transactions' => array_values($transactions)]);

} catch (PDOException $e) {
    // Handle and return any errors
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
