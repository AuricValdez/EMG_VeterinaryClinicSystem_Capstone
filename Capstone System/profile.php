<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Database connection settings
$host = 'localhost';
$db = 'emg_database';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all admin users
    $stmt = $pdo->query("SELECT id, email, role, is_locked FROM admin_users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Management</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
    <style>
        html {
            background-color: white;
        }
        .header {
            background-color: #c55900;
            color: white;
            padding: 15px 20px;
            text-align: center;
            font-size: 1em;
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            width: calc(100% - 160px);
            z-index: 3;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            margin-left: -20px;
            height: 50px;
        }

        .profile-title {
            text-align: center;
            color: white;
            font-size: 20px;
            position: relative;
        }

        .profile-title i {
            margin-right: 10px;
            font-size: 25px;
            color: white;
        }

        .main-content {
            padding-top: 70px;
        }

        .section-title {
            font-size: 24px;
            color: orange;
            margin-top: 30px;
            margin-bottom: 20px;
            text-align: left;
            font-family: 'Roboto', sans-serif;
        }

        .table-container {
            overflow-x: hidden;
            margin-top: 20px;
            border-radius: 8px;
            background-color: #f9f9f9;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: #c55900;
            color: white;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        th {
            padding: 12px;
            text-align: left;
            font-weight: 700;
            white-space: nowrap;
            transition: background-color 0.3s;
        }

        th:hover {
            background-color: #b24c00;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #e0e0e0;
        }

        tr {
            transition: all 0.3s;
        }

        tr:hover {
            transform: scale(1.01);
        }

        .table-input,
        .table-select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #fff;
            font-size: 14px;
            box-sizing: border-box;
            margin: 0;
        }

        .table-input:focus,
        .table-select:focus {
            border-color: #c55900;
            box-shadow: 0 0 5px rgba(197, 89, 0, 0.5);
            outline: none;
        }

        .update-btn {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
            font-size: 14px;
        }

        .update-btn:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        .update-btn i {
            margin-right: 5px;
        }

        /* Alert Button Styling */
        .alert-modal {
            display: flex;
            align-items: center;
            justify-content: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
        }

        .alert-modal-content {
            background: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            max-width: 300px;
            width: 80%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .alert-title {
            text-align: center;
            color: orange !important;
            font-size: 1.5em;
            text-transform: none;
        }

        .alert-title i {
            margin-right: 10px;
            font-size: 1em;
            color: orange;
        }

        .alert-close-button {
            padding: 8px 16px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            display: block;
            margin: 15px auto 0;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .alert-close-button:hover {
            background-color: #c0392b;
            transform: scale(1.05);
        }
    </style>
</head>

<body>
    <div class="sidebar minimized">
        <div class="toggle-btn" onclick="toggleSidebar()">
            <i class="fas fa-chevron-right"></i>
        </div>
        <div class="logo">
            <img src="images/EMG logo.png" alt="Logo" style="width: 80%; margin: 20px;">
        </div>
        <ul class="nav-links">
            <li><a onclick="goDashboard()"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
            <li><a onclick="goAppointment()"><i class="fas fa-calendar-check"></i> <span>Appointments</span></a></li>
            <li><a onclick="goPatientRecords()"><i class="fas fa-paw"></i> <span>Records</span></a></li>
            <li><a onclick="goInventory()"><i class="fas fa-boxes"></i> <span>Inventory</span></a></li>
            <li><a onclick="goBills()"><i class="fas fa-file-invoice-dollar"></i> <span>Bills</span></a></li>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <li><a onclick="goSystemLogs()"><i class="fas fa-book"></i> <span>Logs</span></a></li>
                <li><a onclick="" class="active"><i class="fas fa-user"></i> <span>Profile</span></a></li>
            <?php endif; ?>
        </ul>
        <div class="logout">
            <a onclick="goLogout()" class="logout-button">
                <i class="fa fa-sign-out-alt"></i> <!-- Font Awesome icon (use the appropriate class for your icon) -->
                <span>Logout</span> <!-- Text to be revealed on hover -->
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <h1 class="profile-title">
                <i class="fas fa-user"></i> Profile Management
            </h1>
        </div>

        <h2 class="section-title">Manage Admin Users</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Password</th>
                        <th>Is Locked</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <input type="email" id="email-<?= $user['id'] ?>" value="<?= htmlspecialchars($user['email']) ?>" class="table-input">
                            </td>
                            <td>
                                <select id="role-<?= $user['id'] ?>" class="table-select">
                                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    <option value="staff" <?= $user['role'] === 'staff' ? 'selected' : '' ?>>Staff</option>
                                </select>
                            </td>
                            <td>
                                <input type="password" id="password-<?= $user['id'] ?>" placeholder="New Password" class="table-input">
                            </td>
                            <td>
                                <select id="is_locked-<?= $user['id'] ?>" class="table-select">
                                    <option value="1" <?= $user['is_locked'] ? 'selected' : '' ?>>True</option>
                                    <option value="0" <?= !$user['is_locked'] ? 'selected' : '' ?>>False</option>
                                </select>
                            </td>
                            <td>
                                <button class="update-btn" onclick="updateUser(<?= $user['id'] ?>)">
                                    <i class="fas fa-save"></i> Update
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Alert Modal Structure -->
    <div id="alertModal" class="alert-modal" style="display: none;">
        <div class="alert-modal-content">
            <h3 class="alert-title"><i class="fas fa-info-circle"></i>Alert</h3>
            <span id="alertMessage"></span>
            <button id="alertCloseButton" class="alert-close-button">Close</button>
        </div>
    </div>
    <script src="scripts.js"></script>
    <script>
        async function updateUser(id) {
            const email = document.getElementById(`email-${id}`).value;
            const role = document.getElementById(`role-${id}`).value;
            const password = document.getElementById(`password-${id}`).value;
            const isLocked = document.getElementById(`is_locked-${id}`).value;

            const response = await fetch('update_admin_user.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id,
                    email,
                    role,
                    password,
                    is_locked: isLocked
                })
            });
            const result = await response.json();

            if (result.success) {
                showModal('User updated successfully');
            } else {
                showModal('Error: ' + result.error);
            }
        }

        function showModal(message) {
            const modal = document.getElementById("alertModal");
            const alertMessage = document.getElementById("alertMessage");
            alertMessage.innerText = message;
            modal.style.display = "flex";

            // Close modal when OK button is clicked
            document.getElementById("alertCloseButton").onclick = function() {
                modal.style.display = "none";
            };
        }
    </script>
</body>

</html>