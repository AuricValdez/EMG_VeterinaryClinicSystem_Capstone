<?php
session_start();
if (!isset($_SESSION['role'])) {
    // Redirect to login if not authenticated
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
    <style>
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

        .patient-records-title {
            text-align: center;
            color: white;
            font-size: 20px;
            position: relative;
        }

        .patient-records-title i {
            margin-right: 10px;
            font-size: 25px;
            color: white;
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
            <li><a onclick="" class="active"><i class="fas fa-paw"></i> <span>Records</span></a></li>
            <li><a onclick="goInventory()"><i class="fas fa-boxes"></i> <span>Inventory</span></a></li>
            <li><a onclick="goBills()"><i class="fas fa-file-invoice-dollar"></i> <span>Bills</span></a></li>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <li><a onclick="goSystemLogs()"><i class="fas fa-book"></i> <span>Logs</span></a></li>
                <li><a onclick="goUserProfile()"><i class="fas fa-user"></i> <span>Profile</span></a></li>
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
            <h1 class="patient-records-title">
                <i class="fas fa-paw"></i> Pet Records
            </h1>
        </div>

    </div>

    <script src="scripts.js"></script>
</body>

</html>