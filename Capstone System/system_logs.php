<?php
session_start();
if (!isset($_SESSION['role'])) {
    // Redirect to login if not authenticated
    header("Location: login.php");
    exit();
}

// Database connection settings
$host = 'localhost';
$db = 'emg_database';
$user = 'root';
$pass = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get total unique transactions
    $stmt = $conn->query("SELECT COUNT(transaction_id) AS total_transactions FROM unique_transaction");
    $totalTransactions = $stmt->fetch(PDO::FETCH_ASSOC)['total_transactions'];

    // Get total items sold
    $stmt = $conn->query("SELECT SUM(quantity) AS total_items_sold FROM item_transaction");
    $totalItemsSold = $stmt->fetch(PDO::FETCH_ASSOC)['total_items_sold'];

    // Get the total gross sales for all transactions
    $stmt = $conn->query("SELECT SUM(total_price) AS total_gross_sales FROM item_transaction");
    $totalGrossSales = $stmt->fetch(PDO::FETCH_ASSOC)['total_gross_sales'];

    // If no transactions, set to 0
    $totalGrossSales = $totalGrossSales ? $totalGrossSales : 0;

    // Get all items and their quantities sold for the chart
    $stmt = $conn->query("SELECT item_name, SUM(quantity) AS total_quantity FROM item_transaction GROUP BY item_name");
    $itemData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Initialize filters from the GET request
    $yearFilter = isset($_GET['year_filter']) ? $_GET['year_filter'] : null;
    $monthFilter = isset($_GET['month_filter']) ? $_GET['month_filter'] : null;
    $dayFilter = isset($_GET['day_filter']) ? $_GET['day_filter'] : null;

    // Prepare the query for Items Sold (adjusted for date filtering)
    $itemsQuery = "
    SELECT 
        it.item_name, 
        SUM(it.quantity) AS total_quantity
    FROM item_transaction it
    JOIN unique_transaction ut ON it.transaction_id = ut.transaction_id
    WHERE 1=1
";

    if ($yearFilter) {
        $itemsQuery .= " AND YEAR(ut.transaction_date) = :year_filter";
    }
    if ($monthFilter) {
        $itemsQuery .= " AND MONTH(ut.transaction_date) = :month_filter";
    }
    if ($dayFilter) {
        $itemsQuery .= " AND DAY(ut.transaction_date) = :day_filter";
    }

    $itemsQuery .= " GROUP BY it.item_name";

    // Prepare and execute the query
    $stmt = $conn->prepare($itemsQuery);

    // Initialize filters from the GET request
    $yearFilter = isset($_GET['year_filter']) ? $_GET['year_filter'] : null;
    $monthFilter = isset($_GET['month_filter']) ? $_GET['month_filter'] : null;
    $dayFilter = isset($_GET['day_filter']) ? $_GET['day_filter'] : null;

    // Prepare the query for Items Sold (adjusted for date filtering)
    $itemsQuery = "
SELECT 
    it.item_name, 
    SUM(it.quantity) AS total_quantity,
    SUM(it.total_price) AS total_sales
FROM item_transaction it
JOIN unique_transaction ut ON it.transaction_id = ut.transaction_id
WHERE 1=1
";

    if ($yearFilter) {
        $itemsQuery .= " AND YEAR(ut.transaction_date) = :year_filter";
    }
    if ($monthFilter) {
        $itemsQuery .= " AND MONTH(ut.transaction_date) = :month_filter";
    }
    if ($dayFilter) {
        $itemsQuery .= " AND DAY(ut.transaction_date) = :day_filter";
    }

    $itemsQuery .= " GROUP BY it.item_name";

    // Prepare and execute the query
    $stmt = $conn->prepare($itemsQuery);

    // Bind parameters if filters are set
    if ($yearFilter) $stmt->bindParam(':year_filter', $yearFilter);
    if ($monthFilter) $stmt->bindParam(':month_filter', $monthFilter);
    if ($dayFilter) $stmt->bindParam(':day_filter', $dayFilter);

    $stmt->execute();
    $itemsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare data for the chart
    $labels = [];
    $quantityData = [];
    $salesData = [];

    foreach ($itemsData as $item) {
        $labels[] = $item['item_name'];
        $quantityData[] = round($item['total_quantity']);
        $salesData[] = round($item['total_sales']);
    }

    /// Fetch the year filter (if set)
    $year = isset($_GET['year_filter']) ? $_GET['year_filter'] : date('Y');

    // Query to get monthly gross sales for the selected year
    $query = "
SELECT 
    MONTH(ut.transaction_date) AS month,
    SUM(it.total_price) AS total_sales
FROM item_transaction it
INNER JOIN unique_transaction ut ON it.transaction_id = ut.transaction_id
WHERE YEAR(ut.transaction_date) = :year
GROUP BY MONTH(ut.transaction_date)
ORDER BY MONTH(ut.transaction_date)
";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
    $stmt->execute();
    $monthlySales = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // PHP code for fetching data
    $months = [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December'
    ];

    // Initialize an array with 12 zeros for total sales for each month
    $totalSales = array_fill(0, 12, 0);

    // Map the fetched data to the corresponding month
    foreach ($monthlySales as $data) {
        $monthIndex = $data['month'] - 1; // Month is 1-based (1 for January, 12 for December)
        $totalSales[$monthIndex] = (float) $data['total_sales']; // Set the total sales for the correct month
    }

    // Convert the months and totalSales arrays to JSON for use in JavaScript
    $monthsJson = json_encode($months);
    $totalSalesJson = json_encode($totalSales);

    // Fetch the month filter (if set)
    $month = isset($_GET['month_filter']) ? $_GET['month_filter'] : date('m');

    // Query to get sales for the selected month
    $query = "
    SELECT 
        it.item_name, 
        SUM(it.total_price) AS total_sales
    FROM item_transaction it
    INNER JOIN unique_transaction ut ON it.transaction_id = ut.transaction_id
    WHERE MONTH(ut.transaction_date) = :month
    GROUP BY it.item_name
";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':month', $month, PDO::PARAM_INT);
    $stmt->execute();
    $itemSales = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare data for the pie chart
    $itemNames = [];
    $itemSalesData = [];
    foreach ($itemSales as $data) {
        $itemNames[] = $data['item_name'];
        $itemSalesData[] = (float) $data['total_sales'];
    }

    $pieLabels = json_encode($itemNames);
    $pieSalesData = json_encode($itemSalesData);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Include Chart.js -->
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

        .system-logs-title {
            text-align: center;
            color: white;
            font-size: 20px;
            position: relative;
        }

        .system-logs-title i {
            margin-right: 10px;
            font-size: 25px;
            color: white;
        }

        .dashboard-card-container {
            display: flex;
            gap: 20px;
            margin: 20px;
            margin-top: 75px;
        }

        .dashboard-card {
            background-color: #fff2cc;
            /* Solid color instead of gradient */
            border: 2px solid #e0a800;
            /* Changed border color to stand out more */
            padding: 20px;
            border-radius: 12px;
            width: 40%;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
            overflow: hidden;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        }

        .dashboard-card h3 {
            margin-bottom: 10px;
            font-size: 18px;
            color: #333;
            font-weight: 600;
        }

        .dashboard-card-value {
            font-size: 32px;
            font-weight: bold;
            color: #333;
        }

        .dashboard-card-value span {
            display: block;
            margin-top: 5px;
            font-size: 16px;
            color: #777;
        }

        .dashboard-card i {
            font-size: 40px;
            color: #ff7a00;
            margin-bottom: 10px;
        }

        /* Add styling for the chart container */
        .chart-container {
            width: 80%;
            height: 300px;
            margin: 30px auto;
        }

        select.year-input,
        select.month-input,
        select.day-input {
            padding: 12px 18px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            width: 300px;
            background-color: #fff;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        select.year-input:focus,
        select.month-input:focus,
        select.day-input:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
            outline: none;
        }

        .filter-button {
            padding: 12px 25px;
            font-size: 16px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
            box-sizing: border-box;
        }

        .filter-button:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        .filter-button:active {
            background-color: #004085;
            transform: translateY(0);
        }


        .card-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin: 20px;
            transition: all 0.3s ease;
        }

        .card {
            background-color: #ffffff;
            border: 2px solid #c55900;
            border-radius: 12px;
            width: 98%;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            max-height: 320px;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            max-height: 600px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            font-size: 18px;
        }

        .transaction-id {
            font-weight: bold;
        }

        .transaction-time {
            font-size: 18px;
            color: #888;
            text-align: right;
            flex-grow: 1;
            text-align: right;
            /* Ensures right alignment */
        }

        .card .transaction-details {
            font-size: 14px;
            color: #777;
            display: none;
        }

        .card:hover .transaction-details {
            display: block;
            font-size: 16px;
            color: #333;
        }

        .card .card-body {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .delete-btn-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 10px;
        }

        .delete-btn {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            font-size: 14px;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .delete-btn:hover {
            transform: scale(1.05);
            background-color: darkred;
        }

        .delete-alert-modal {
            display: none;
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

        .delete-alert-modal-content {
            background: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            max-width: 300px;
            width: 80%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .delete-alert-title {
            text-align: center;
            color: orange !important;
            font-size: 1.5em;
            text-transform: none;
        }

        .delete-alert-title i {
            margin-right: 5px;
            font-size: 1em;
            color: orange;
        }

        .delete-close-button {
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

        .delete-close-button:hover {
            background-color: #c0392b;
            transform: scale(1.05);
        }

        .green-button {
            background-color: #2ecc71;
        }

        .green-button:hover {
            background-color: #27ae60;
        }

        .delete-alert-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .delete-alert-modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .hidden {
            display: none;
        }

        .button-container {
            display: flex;
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

        /* Year filter container to center the form */
        .year-filter-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px 0;
        }

        /* Styling for the select dropdown */
        .year-filter-select,
        .month-filter-select,
        .day-filter-select {
            font-family: 'Arial', sans-serif;
            font-size: 16px;
            padding: 10px 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            color: #333;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }

        /* Adding hover and focus states for better interactivity */
        .year-filter-select:hover,
        .month-filter-select:hover,
        .day-filter-select:hover {
            border-color: #ff7a00;
        }

        .year-filter-select:focus,
        .month-filter-select:focus,
        .day-filter-select:focus {
            outline: none;
            box-shadow: 0 0 5px rgba(255, 122, 0, 0.8);
            /* Focus shadow effect */
            border-color: #ff7a00;
        }

        /* Optional: Customize dropdown arrow */
        .year-filter-select::-ms-expand,
        .month-filter-select::-ms-expand,
        .day-filter-select::-ms-expand {
            display: none;
        }


        .filters-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .filters-container form {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        /* Main container for the transaction cards */
        .transaction-container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
            max-height: 600px;
            overflow-y: auto;
            border: 1px solid #ddd;
        }

        .transaction-header {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
            font-size: 16px;
            font-weight: 600;
        }

        .transaction-header h2 {
            color: #ff7a00;
            margin-left: 10px;
        }

        .history-icon {
            font-size: 28px;
            color: #ff7a00;
        }

        .charts-container {
            display: flex;
            justify-content: space-between;
            gap: 10px; 
            margin-top: 30px;
        }

        .chart-item {
            margin-left: 120px;
            margin-right: 70px;
            flex: 1;
            min-width: 500px;
            max-width: 700px;
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
                <li><a onclick="" class="active"><i class="fas fa-book"></i> <span>Logs</span></a></li>
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
            <h1 class="system-logs-title">
                <i class="fas fa-book"></i> Transaction Logs
            </h1>
        </div>

        <div class="dashboard-card-container">
            <!-- Unique Transactions Card -->
            <div class="dashboard-card">
                <i class="fas fa-exchange-alt"></i>
                <h3>Unique Transactions</h3>
                <div class="dashboard-card-value">
                    <?php echo $totalTransactions; ?>
                    <span>Transactions</span>
                </div>
            </div>

            <!-- Total Items Sold Card -->
            <div class="dashboard-card">
                <i class="fas fa-box-open"></i>
                <h3>Total Items Sold</h3>
                <div class="dashboard-card-value">
                    <?php echo number_format($totalItemsSold); ?>
                    <span>Items</span>
                </div>
            </div>

            <!-- Most Sold Item Card -->
            <div class="dashboard-card">
                <i class="fas fa-cogs"></i>
                <h3>Total Inventory Sales</h3>
                <div class="dashboard-card-value">
                    ₱<?php echo $totalGrossSales; ?>
                    <span>Sales</span>
                </div>
            </div>
        </div>

        <div class="filters-container">
            <form method="GET">
                <!-- Year Filter -->
                <select name="year_filter" onchange="this.form.submit()" class="year-filter-select">
                    <option value="">Select Year</option>
                    <?php for ($year = date("Y"); $year >= 2000; $year--): ?>
                        <option value="<?php echo $year; ?>" <?php echo (isset($_GET['year_filter']) && $_GET['year_filter'] == $year) ? 'selected' : ''; ?>>
                            <?php echo $year; ?>
                        </option>
                    <?php endfor; ?>
                </select>

                <!-- Month Filter -->
                <select name="month_filter" onchange="this.form.submit()" class="month-filter-select">
                    <option value="">Select Month</option>
                    <?php for ($month = 1; $month <= 12; $month++): ?>
                        <option value="<?php echo sprintf("%02d", $month); ?>" <?php echo (isset($_GET['month_filter']) && $_GET['month_filter'] == sprintf("%02d", $month)) ? 'selected' : ''; ?>>
                            <?php echo date("F", mktime(0, 0, 0, $month, 10)); ?>
                        </option>
                    <?php endfor; ?>
                </select>

                <!-- Day Filter -->
                <select name="day_filter" onchange="this.form.submit()" class="day-filter-select">
                    <option value="">Select Day</option>
                    <?php for ($day = 1; $day <= 31; $day++): ?>
                        <option value="<?php echo sprintf("%02d", $day); ?>" <?php echo (isset($_GET['day_filter']) && $_GET['day_filter'] == sprintf("%02d", $day)) ? 'selected' : ''; ?>>
                            <?php echo $day; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </form>
        </div>

        <div class="chart-container">
            <canvas id="combinedChart"></canvas>
        </div>

        <div class="charts-container">
            <div class="chart-item">
                <canvas id="lineChart"></canvas>
            </div>
            <div class="chart-item">
                <canvas id="pieChart"></canvas>
            </div>
        </div>

        <!-- Transaction Cards Section -->
        <div class="transaction-container">
            <div class="transaction-header">
                <i class="fas fa-history history-icon"></i>
                <h2>Transaction History</h2>
            </div>
            <div class="card-container">
                <?php
                try {
                    // Apply filtering if any filter is set
                    $query = "SELECT * FROM unique_transaction";
                    $conditions = [];

                    if (!empty($_GET['year_filter'])) {
                        $conditions[] = "YEAR(transaction_date) = :year_filter";
                    }
                    if (!empty($_GET['month_filter'])) {
                        $conditions[] = "MONTH(transaction_date) = :month_filter";
                    }
                    if (!empty($_GET['day_filter'])) {
                        $conditions[] = "DAY(transaction_date) = :day_filter";
                    }

                    if ($conditions) {
                        $query .= " WHERE " . implode(" AND ", $conditions);
                    }

                    $stmt = $conn->prepare($query);

                    // Bind parameters
                    if (!empty($_GET['year_filter'])) {
                        $stmt->bindParam(':year_filter', $_GET['year_filter'], PDO::PARAM_INT);
                    }
                    if (!empty($_GET['month_filter'])) {
                        $stmt->bindParam(':month_filter', $_GET['month_filter'], PDO::PARAM_INT);
                    }
                    if (!empty($_GET['day_filter'])) {
                        $stmt->bindParam(':day_filter', $_GET['day_filter'], PDO::PARAM_INT);
                    }

                    $stmt->execute();
                    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Display transactions
                    foreach ($transactions as $transaction):
                        // Get total price for this transaction
                        $stmtTotal = $conn->prepare("SELECT SUM(total_price) AS total_price FROM item_transaction WHERE transaction_id = :transaction_id");
                        $stmtTotal->bindParam(':transaction_id', $transaction['transaction_id'], PDO::PARAM_INT);
                        $stmtTotal->execute();
                        $totalTransactionPrice = $stmtTotal->fetchColumn();

                        // Format the total transaction price
                        $formattedTotalPrice = number_format($totalTransactionPrice, 2);
                ?>
                        <div class="card">
                            <div class="card-header">
                                <span class="transaction-id">Transaction ID: <?php echo htmlspecialchars($transaction['transaction_id']); ?></span>
                                <span class="transaction-time"><?php echo htmlspecialchars(date('h:i A', strtotime($transaction['transaction_time']))); ?></span>
                            </div>
                            <div class="card-body">
                                <div class="transaction-details">
                                    <?php
                                    // Fetch the individual items for this transaction
                                    $stmtItems = $conn->prepare("SELECT item_name, quantity FROM item_transaction WHERE transaction_id = :transaction_id");
                                    $stmtItems->bindParam(':transaction_id', $transaction['transaction_id'], PDO::PARAM_INT);
                                    $stmtItems->execute();
                                    $transactionItems = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

                                    // Display the items
                                    foreach ($transactionItems as $item): ?>
                                        <p><strong>Item:</strong> <?php echo htmlspecialchars($item['item_name']); ?> - <strong>Quantity:</strong> <?php echo htmlspecialchars($item['quantity']); ?></p>
                                    <?php endforeach; ?>
                                    <div class="card-footer">
                                        <strong>Total Price:</strong> ₱<?php echo htmlspecialchars($formattedTotalPrice); ?>
                                    </div>
                                    <div class="delete-btn-container">
                                        <!-- Delete Button -->
                                        <button class="delete-btn" data-transaction-id="<?php echo htmlspecialchars($transaction['transaction_id']); ?>">Delete Transaction</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php
                } catch (PDOException $e) {
                    echo "<p>Error fetching transactions: " . htmlspecialchars($e->getMessage()) . "</p>";
                }
                ?>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="delete-confirmation-modal" class="delete-alert-modal hidden">
            <div class="delete-alert-modal-content">
                <h3 class="delete-alert-title">
                    <i class="fas fa-info-circle"></i> <span>Alert</span>
                </h3>
                <p>Are you sure you want to delete this transaction and its related records?</p>
                <div class="button-container">
                    <button id="confirmDeleteButton" class="delete-close-button green-button">Confirm</button>
                    <button class="delete-close-button" onclick="closeDeleteConfirmation()">Cancel</button>
                </div>
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
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Prepare data for Chart.js
            const labels = <?php echo json_encode($labels); ?>;
            const quantityData = <?php echo json_encode($quantityData); ?>;
            const salesData = <?php echo json_encode($salesData); ?>;

            // Combined Chart (Items Sold and Sales Per Item)
            const combinedCtx = document.getElementById('combinedChart').getContext('2d');
            const combinedChart = new Chart(combinedCtx, {
                type: 'bar', // We are using a bar chart for both data sets
                data: {
                    labels: labels, // Item names
                    datasets: [{
                            label: 'Items Sold',
                            data: quantityData,
                            backgroundColor: '#ff7a00',
                            borderColor: '#e0a800',
                            borderWidth: 1,
                            yAxisID: 'y1', // This dataset uses the left Y-axis
                        },
                        {
                            label: 'Sales Per Item',
                            data: salesData,
                            backgroundColor: '#00c4b4',
                            borderColor: '#00b0a1',
                            borderWidth: 1,
                            yAxisID: 'y2', // This dataset uses the right Y-axis
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Items'
                            }
                        },
                        y1: {
                            title: {
                                display: true,
                                text: 'Quantity Sold'
                            },
                            beginAtZero: true,
                            grid: {
                                display: false // Disable horizontal grid lines for the left axis
                            }
                        },
                        y2: {
                            title: {
                                display: true,
                                text: 'Total Sales'
                            },
                            beginAtZero: true,
                            position: 'right', // Place the sales on the right axis
                            grid: {
                                display: false // Disable horizontal grid lines for the right axis
                            },
                            ticks: {
                                callback: function(value) {
                                    // Add the Peso sign before the value and format the number with commas
                                    return '₱' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                // Customizing the tooltip for the Sales Per Item dataset
                                label: function(tooltipItem) {
                                    if (tooltipItem.datasetIndex === 1) { // Sales Per Item dataset
                                        return '₱' + tooltipItem.raw.toLocaleString(); // Add Peso sign and format
                                    }
                                    return tooltipItem.raw; // For other datasets, just return the raw value
                                }
                            }
                        }
                    }
                }
            });

            // Line Chart (Monthly Sales by Year)
            const lineCtx = document.getElementById('lineChart').getContext('2d');
            const lineChart = new Chart(lineCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($months); ?>, // Month names (January to December)
                    datasets: [{
                        label: 'Monthly Gross Sales',
                        data: <?php echo json_encode($totalSales); ?>, // Total sales for each month, including 0s for missing data
                        fill: false,
                        borderColor: '#28a745', // Green color for the line
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Month'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Gross Sales (₱)'
                            },
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₱' + value.toLocaleString(); // Add Peso sign and format the y-axis values
                                }
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Monthly Sales Report', // The title text
                            font: {
                                size: 18, // Font size for the title
                                weight: 'bold' // Make the title bold
                            },
                            padding: {
                                top: 20,
                                bottom: 10
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    // Format the tooltip label with the Peso sign and value
                                    return '₱' + tooltipItem.raw.toLocaleString(); // Adding Peso sign to the tooltip value
                                }
                            }
                        }
                    }
                }
            });

            // Pie Chart (Monthly Sales for Selected Month)
            const pieCtx = document.getElementById('pieChart').getContext('2d');
            const pieChart = new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: <?php echo $pieLabels; ?>, // Item names
                    datasets: [{
                        data: <?php echo $pieSalesData; ?>, // Total sales for each item in the selected month
                        backgroundColor: ['#ff7a00', '#00c4b4', '#ff6347', '#36a2eb', '#ff99ff'], // Random colors
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Monthly Sales', // The title text
                            font: {
                                size: 18, // Font size for the title
                                weight: 'bold' // Make the title bold
                            },
                            padding: {
                                top: 20,
                                bottom: 10
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    // Format the tooltip label with the Peso sign and value
                                    return '₱' + tooltipItem.raw.toLocaleString(); // Add Peso sign to the tooltip value
                                }
                            }
                        }
                    }
                }
            });

            // Get all delete buttons and modal elements
            const deleteButtons = document.querySelectorAll('.delete-btn');
            const deleteConfirmationModal = document.getElementById('delete-confirmation-modal');
            const confirmDeleteButton = document.getElementById('confirmDeleteButton');
            const cancelDeleteButton = document.querySelector('.delete-close-button');
            let currentTransactionId = null; // Variable to store the current transaction ID

            // Add event listener to each delete button
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    currentTransactionId = this.getAttribute('data-transaction-id'); // Get the transaction ID
                    deleteConfirmationModal.classList.remove('hidden'); // Show the confirmation modal
                });
            });

            // Close the confirmation modal
            function closeDeleteConfirmation() {
                deleteConfirmationModal.classList.add('hidden'); // Hide the modal
            }

            // Confirm deletion and proceed
            confirmDeleteButton.addEventListener('click', function() {
                if (currentTransactionId) {
                    // Send a request to delete the transaction using AJAX
                    fetch('delete_transaction.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                id: currentTransactionId
                            }) // Send transaction ID as JSON
                        })
                        .then(response => response.json()) // Ensure to parse the response as JSON
                        .then(data => {
                            if (data.success) {
                                // Show success message
                                showModal('Transaction and related records have been successfully deleted.');
                            } else {
                                // Show error message if the deletion was not successful
                                alert('Error: ' + data.message);
                            }

                            closeDeleteConfirmation(); // Close the modal after confirming
                        })
                        .catch(error => {
                            alert('Error deleting transaction: ' + error); // Handle any errors
                        });
                }
            });


            // Cancel deletion and close the modal
            cancelDeleteButton.addEventListener('click', function() {
                closeDeleteConfirmation(); // Close the modal if the user cancels
            });

            function showModal(message) {
                const modal = document.getElementById("alertModal");
                const alertMessage = document.getElementById("alertMessage");
                alertMessage.innerText = message;
                modal.style.display = "flex";

                // Close modal when OK button is clicked
                document.getElementById("alertCloseButton").onclick = function() {
                    modal.style.display = "none";
                    // Reload the page or remove the transaction from the DOM
                    location.reload(); // Reload the page to reflect the changes
                };
            }
        </script>

        <script src="scripts.js"></script>
</body>

</html>