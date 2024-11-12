<?php
$host = 'localhost';
$db = 'emg_database';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Retrieve data from POST
    $data = json_decode(file_get_contents("php://input"), true);
    $items = $data['items'];

    // Insert into unique_transaction to create a new transaction record
    $stmt = $pdo->prepare("INSERT INTO unique_transaction (transaction_date, transaction_time) VALUES (CURDATE(), CURTIME())");
    $stmt->execute();

    // Get the transaction ID of the newly created transaction
    $transactionId = $pdo->lastInsertId();

    // Prepare the item transaction insertion statement
    $stmt = $pdo->prepare("INSERT INTO item_transaction (transaction_id, item_name, quantity, total_price) VALUES (?, ?, ?, ?)");

    // Insert each item in the item_transaction table
    foreach ($items as $item) {
        $stmt->execute([$transactionId, $item['name'], $item['quantity'], $item['totalPrice']]);
    }

    // Return success response
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    // Return error response
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
