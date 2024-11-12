<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

// Database connection settings
$host = 'localhost';
$db = 'emg_database';
$user = 'root';
$pass = '';

$data = json_decode(file_get_contents('php://input'), true);

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare query for updating user
    $query = "UPDATE admin_users SET email = :email, role = :role, is_locked = :is_locked";
    
    // Check if password is provided
    if (!empty($data['password'])) {
        $query .= ", password = :password";
    }
    
    // If is_locked changed from true to false, clear locked_until
    if ($data['is_locked'] == 0) {
        $query .= ", lockout_until = NULL";
    }
    
    $query .= " WHERE id = :id";
    
    $stmt = $pdo->prepare($query);

    // Bind parameters
    $stmt->bindParam(':email', $data['email']);
    $stmt->bindParam(':role', $data['role']);
    $stmt->bindParam(':is_locked', $data['is_locked'], PDO::PARAM_INT);
    $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);

    // Bind hashed password if provided
    if (!empty($data['password'])) {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt->bindParam(':password', $hashedPassword);
    }

    // Execute the update
    $stmt->execute();

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
