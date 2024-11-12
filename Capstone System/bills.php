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
    <title>Transaction Management</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
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

        .bills-title {
            text-align: center;
            color: white;
            font-size: 20px;
            position: relative;
        }

        .bills-title i {
            margin-right: 10px;
            font-size: 25px;
            color: white;
        }

        .content {
            margin-top: 70px;
            display: flex;
            height: 665px;
            overflow: hidden;
        }

        .item-list {
            flex: 1;
            padding: 20px;
            padding-left: 28px;
            overflow-y: auto;
            background-color: white;
            display: flex;
            flex-direction: column;
        }

        .item-list::-webkit-scrollbar {
            width: 8px;
        }

        .item-list::-webkit-scrollbar-track {
            background: white;
            border-radius: 10px;
        }

        .item-list::-webkit-scrollbar-thumb {
            background: #c0c0c0;
            border-radius: 10px;
            transition: background 0.3s ease;
        }

        .item-list::-webkit-scrollbar-thumb:hover {
            background: #a0a0a0;
        }

        .search-bar {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-bar input {
            width: 60%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            margin-right: 10px;
        }

        .search-bar select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        #search {
            border-radius: 8px;
            border: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid #ccc;
        }

        #search:focus {
            outline: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            border: 1px solid #c55900;
        }

        #category-select {
            border-radius: 8px;
            border: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid #ccc;
        }

        #category-select:focus {
            outline: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            border: 1px solid #c55900;
        }

        #search::placeholder {
            color: #aaa;
            font-style: italic;
        }

        #category-select:hover,
        #search:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .item-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .item-card {
            background-color: #333;
            border-radius: 8px;
            flex: 1 1 calc(25% - 8px);
            max-width: 206px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            transition: all 0.5s ease;
        }

        .item-card:hover {
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.6);
            transform: scale(1.05);
        }

        .item-image {
            width: 100%;
            height: 175px;
            object-fit: cover;
            border-radius: 8px 8px 0 0;
        }

        .item-card h3 {
            color: orange;
            font-size: 1.5rem;
            margin: 10px 0;
            text-shadow: 3px 3px 5px rgba(0, 0, 0, 0.5);
            text-align: center;
        }

        .item-card p {
            margin: 0 0 5px 0;
        }

        .price-stock {
            display: flex;
            align-items: center;
            position: relative;
            margin-bottom: 20px;
        }

        .stock-icon {
            position: absolute;
            right: 40px;
        }

        .price-stock .price {
            color: white;
            font-size: 1rem;
            margin-left: 10px;
        }

        .stock-count {
            position: absolute;
            right: 20px;
        }

        .price-stock .price-icon {
            margin-left: 15px;
        }

        .quantity-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 10px 0;
        }

        .quantity-input {
            width: 50px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            text-align: center;
            margin: 0 5px;
            background-color: #fff;
        }

        .quantity-input::-webkit-inner-spin-button,
        .quantity-input::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .decrease-btn,
        .increase-btn {
            background-color: transparent;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            transition: all 0.5s ease;
        }

        .decrease-btn:hover,
        .increase-btn:hover {
            transform: scale(1.05);
            color: orange;
        }

        .add-to-cart-btn {
            width: 40%;
            background-color: orange;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            margin: 15px auto;
            margin-bottom: 0px;
            display: block;
            transition: transform 0.2s;
        }

        .add-to-cart-btn i {
            font-size: 1rem;
        }

        .add-to-cart-btn:hover {
            background-color: #c55900;
            transform: scale(1.05);
        }

        .card-content {
            color: white;
            padding: 10px;
        }

        .cart {
            width: 400px;
            padding: 0;
            background-color: #f9f9f9;
            border-left: 1px solid #ddd;
            height: 100vh;
            overflow-y: auto;
            position: relative;
            padding-left: 35px;
        }

        .cart h2 {
            margin: 0;
            text-align: center;
            font-size: 24px;
            color: white;
            background-color: orange;
            padding: 20px 0;
            width: 100%;
            box-sizing: border-box;
            position: absolute;
            top: 0;
            left: 0;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .cart-content {
            margin-top: 80px;
            padding: 20px;
        }

        .cart h2 i {
            font-size: 36px;
            color: white;
        }

        .cart h3 {
            margin-top: 100px;
            font-size: 18px;
            color: #333;
        }

        .cart-buttons {
            position: absolute;
            bottom: 90px;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 10px;
        }

        .cart-buttons button {
            width: 100%;
            height: 50px;
            padding: 10px 0;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 20px;
        }

        .cart-buttons button i {
            margin-right: 8px;
        }

        .cart-buttons button:hover {
            background-color: #c55900;
            transform: scale(1.01);
        }

        #cart-items {
            flex-grow: 1;
            width: 390px;
            margin-top: 90px;
            margin-left: -25px;
            padding: 10px;
            list-style-type: none;
            overflow-y: auto;
            max-height: 265px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        #cart-items li {
            margin-bottom: 8px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            font-size: 15px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        #cart-items li:hover {
            background-color: #f0f0f0;
            transform: scale(1.03);
        }

        .cart-item-content {
            display: flex;
            width: 100%;
        }

        .cart-item-image {
            width: 100px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 12px;
        }

        .cart-item-details {
            flex-grow: 1;
            margin-top: 10px;
        }

        .cart-item-name {
            display: block;
            font-weight: bold;
        }

        .cart-item-price {
            color: #555;
        }

        .delete-item-btn {
            border: none;
            background: none;
            color: #dc3545;
            cursor: pointer;
            margin-left: auto;
            margin-right: 5px;
            transition: color 0.3s, transform 0.3s;
        }

        .delete-item-btn:hover {
            color: #c82333;
            transform: scale(1.05);
        }

        .delete-item-btn i {
            font-size: 20px;
            /* Adjust size if necessary */
        }


        #cart-items li:not(:last-child) {
            border-bottom: 1px solid #e9ecef;
        }

        #cart-items::-webkit-scrollbar {
            width: 8px;
        }

        #cart-items::-webkit-scrollbar-track {
            background: white;
            border-radius: 10px;
        }

        #cart-items::-webkit-scrollbar-thumb {
            background: #c0c0c0;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        #cart-items::-webkit-scrollbar-thumb:hover {
            background: #a0a0a0;
        }

        .cart-total {
            position: absolute;
            bottom: 220px;
            text-align: center;
            background-color: #333;
            padding: 10px;
            border-radius: 10px;
            margin-left: -25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            width: 395px;
        }

        .cart-total h3 {
            font-size: 20px;
            font-weight: bold;
            color: white;
            display: flex;
            align-items: center;
            margin: 0;
        }

        .cart-total h3 i {
            margin-right: 8px;
            font-size: 20px;
            color: white;
        }

        .cart-total .price-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        #payment-input {
            padding: 10px;
            width: 95%;
            margin-top: 12px;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f4f4f4;
            font-size: 16px;
            outline: none;
            transition: all 0.3s ease;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        #payment-input:focus {
            background-color: #fff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
        }

        .calculate-change-btn {
            background-color: orange;
            color: white;
            border: none;
            padding: 10px;
            font-size: 16px;
            border-radius: 10px;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }

        .calculate-change-btn i {
            font-size: 1rem;
        }

        .calculate-change-btn:hover {
            background-color: #c55900;
            transform: scale(1.01);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }

        .transaction-button {
            text-align: center;
            margin-top: 10px;
        }

        .confirm-transaction-btn {
            position: absolute;
            background-color: #28a745;
            color: white;
            padding: 10px 0;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            height: 50px;
            width: 95.5%;
            margin-left: -225px;
            bottom: 160px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .confirm-transaction-btn:hover {
            background-color: #218838;
            transform: scale(1.01);
        }

        .view-transaction-btn {
            background-color: #0073e6 !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }

        .view-transaction-btn:hover {
            background-color: #005bb5 !important;
            transform: scale(1.01);
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

        .button-container {
            display: flex;
            margin-top: 15px;
        }

        /* Modal Styles */
        .transaction-history-modal {
            display: none;
            justify-content: center;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            width: 100%;
            max-width: 600px;
            border-radius: 8px;
            position: relative;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .transaction-close-button {
            position: absolute;
            right: 10px;
            font-size: 24px;
            cursor: pointer;
            color: orange;
            transition: all 0.3s ease;
        }

        .transaction-close-button:hover {
            color: black;
        }

        .transaction-list {
            max-height: 300px;
            overflow-y: auto;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 10px 0;
        }

        .transaction-list::-webkit-scrollbar {
            width: 10px;
        }

        .transaction-list::-webkit-scrollbar-track {
            background: #eaeaea;
            border-radius: 10px;
        }

        .transaction-list::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
            transition: background 0.3s ease;
        }

        .transaction-list::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .transaction-card {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            transition: box-shadow 0.3s, background-color 0.3s, transform 0.3s;
            cursor: pointer;
            position: relative;
        }

        .transaction-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 8px;
            background-color: #FFB84D;
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }

        .transaction-card:hover {
            background-color: #FFEDCC;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            transform: scale(1.02);
        }

        @media (max-width: 768px) {
            .transaction-list {
                padding: 15px;
            }

            .transaction-card {
                padding: 10px;
            }
        }


        .transaction-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: bold;
            color: #333;
        }

        .transaction-time {
            font-size: 0.85em;
            color: #555;
        }

        .transaction-items {
            display: none;
            margin-top: 10px;
        }

        .transaction-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }

        .transaction-item:last-child {
            border-bottom: none;
        }

        .transaction-total {
            display: flex;
            align-items: center;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
            margin-top: 10px;
            border: 1px solid #e0e0e0;
        }

        .transaction-total .delete-icon {
            margin-right: 10px;
            transition: all 0.3s ease;
        }

        .transaction-total .delete-icon:hover {
            margin-right: 10px;
            transform: scale(1.25);
        }

        .transaction-total span {
            margin-left: 10px;
            margin-right: 10px;
        }

        .transaction-total .total-price {
            margin-left: auto;
            font-weight: bold;
        }

        .transaction-modal-content {
            width: 600px;
            max-width: 90%;
            margin: auto;
            margin-top: 100px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
        }

        .transaction-modal-header {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            position: relative;
            margin-bottom: 20px;
            font-size: 1.5em;
        }

        .transaction-modal-header i {
            margin-right: 10px;
            font-size: 1 em;
        }

        .no-transactions-message {
            text-align: center;
            color: #999;
            font-size: 1.2em;
            padding: 20px;
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
            <li><a onclick="" class="active"><i class="fas fa-file-invoice-dollar"></i> <span>Bills</span></a></li>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <li><a onclick="goSystemLogs()"><i class="fas fa-book"></i> <span>Logs</span></a></li>
                <li><a onclick="goUserProfile()"><i class="fas fa-user"></i> <span>Profile</span></a></li>
            <?php endif; ?>
        </ul>
        <div class="logout">
            <a onclick="goLogout()" class="logout-button">
                <i class="fa fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <h1 class="bills-title">
                <i class="fas fa-file-invoice-dollar"></i> Transaction Management
            </h1>
        </div>

        <div class="content">
            <div class="item-list">
                <div class="search-bar">
                    <input type="text" id="search" placeholder="Search items..." oninput="filterItems()">
                    <select id="category-select" onchange="filterItems()">
                        <option value="" selected>All Categories</option>
                        <option value="Medications">Medications</option>
                        <option value="Medical Supplies">Medical Supplies</option>
                        <option value="Surgical Equipment">Surgical Equipment</option>
                        <option value="Diagnostic Tools">Diagnostic Tools</option>
                        <option value="Food and Supplements">Food and Supplements</option>
                        <option value="Grooming Supplies">Grooming Supplies</option>
                        <option value="Pet Products">Pet Products</option>
                        <option value="Personal Protective Equipment (PPE)">Personal Protective Equipment (PPE)</option>
                        <option value="Office Supplies">Office Supplies</option>
                        <option value="Vaccines">Vaccines</option>
                        <option value="Cleaning Supplies">Cleaning Supplies</option>
                        <option value="Consumables">Consumables</option>
                    </select>
                </div>
                <div class="item-cards">
                    <!-- Item cards will be dynamically loaded here -->
                </div>
            </div>

            <div class="cart">
                <h2><i class="fas fa-shopping-cart"></i></h2> <!-- Cart icon -->

                <!-- Cart Items -->
                <ul id="cart-items"></ul>

                <!-- Total and Change -->
                <div class="cart-total">
                    <div class="price-info">
                        <h3><i class="fas fa-tag"></i> <span id="total-price">₱0.00</span></h3>
                        <h3><i class="fas fa-coins"></i> <span id="change-amount">₱0.00</span></h3>
                    </div>
                    <div>
                        <input type="number" id="payment-input" placeholder="Enter amount" />
                    </div>
                    <div>
                        <button class="calculate-change-btn" onclick="calculateChange()">
                            <i class="fas fa-cash-register"></i> Calculate
                        </button>
                    </div>
                </div>

                <!-- Confirm/Add Transaction Button -->
                <div class="transaction-button">
                    <button class="confirm-transaction-btn" onclick="confirmTransaction()">
                        <i class="fas fa-check-circle"></i> Confirm Transaction
                    </button>
                </div>

                <!-- Bottom Buttons -->
                <div class="cart-buttons">
                    <button class="view-transaction-btn" onclick="showTransactionHistory()">
                        <i class="fas fa-book"></i> View
                    </button>
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

            <!-- Confirm Transaction Modal -->
            <div id="confirmTransactionModal" class="alert-modal" style="display: none;">
                <div class="alert-modal-content">
                    <h3 class="alert-title">
                        <i class="fas fa-info-circle"></i> <span>Confirm Transaction</span>
                    </h3>
                    <p>Are you sure you want to confirm this transaction?</p>
                    <div class="button-container">
                        <button id="confirmTransactionButton" class="alert-close-button green-button">Confirm</button>
                        <button id="cancelTransactionButton" class="alert-close-button">Cancel</button>
                    </div>
                </div>
            </div>

            <!-- Transaction History Modal -->
            <div id="transaction-history-modal" class="transaction-history-modal hidden">
                <div class="transaction-modal-content">
                    <div class="transaction-modal-header">
                        <i class="fas fa-book" style="color: orange;"></i>
                        <h3 style="color: orange; margin: 0;"> Transaction History</h3>
                        <span class="transaction-close-button" onclick="closeTransactionHistory()">&times;</span>
                    </div>

                    <!-- Transaction List -->
                    <div id="transaction-list" class="transaction-list">
                        <!-- Transactions will be dynamically loaded here -->
                    </div>
                </div>
            </div>

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
            <script src="scripts.js"></script>
            <script>
                let cart = [];
                let totalPrice = 0;

                function addToCart(name, price, image, button) {
                    const quantityInput = button.parentElement.querySelector('.quantity-input');
                    const quantityToAdd = parseInt(quantityInput.value);

                    // Get the available stock for the item
                    const itemStock = parseInt(button.parentElement.querySelector('.stock-count').dataset.stock);

                    // Check if the available stock is 0
                    if (itemStock === 0) {
                        showModal("No available stock.");
                        return; // Exit the function if no stock is available
                    }

                    // Validate the quantity to add
                    if (isNaN(quantityToAdd) || quantityToAdd <= 0) {
                        showModal("Please enter a valid quantity.");
                        return;
                    }

                    // Check for existing item in the cart
                    const existingItemIndex = cart.findIndex(item => item.name === name);
                    let totalQuantityInCart = existingItemIndex !== -1 ? cart[existingItemIndex].quantity : 0;

                    // Calculate the total quantity if we add the new quantity
                    const totalQuantityAfterAdding = totalQuantityInCart + quantityToAdd;

                    // Check if the total quantity exceeds available stock
                    if (totalQuantityAfterAdding > itemStock) {
                        showModal(`Cannot add more than available stock. Available stock: ${itemStock}`);
                        return; // Exit the function if exceeds stock
                    }

                    // If the item exists in the cart, update its quantity
                    if (existingItemIndex !== -1) {
                        cart[existingItemIndex].quantity += quantityToAdd;
                    } else {
                        // If it's a new item, create the item object
                        const item = {
                            name,
                            price,
                            image,
                            quantity: quantityToAdd
                        };
                        cart.push(item); // Add new item to the cart
                    }

                    // Update total price
                    totalPrice += price * quantityToAdd;

                    // Update cart and total price display
                    updateCart();
                    updateTotalPrice();
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

                // Function to update the total price displayed in the HTML
                function updateTotalPrice() {
                    document.getElementById('total-price').innerText = `₱${totalPrice.toFixed(2)}`;
                }

                function updateCart() {
                    const cartItemsContainer = document.getElementById('cart-items');
                    cartItemsContainer.innerHTML = ''; // Clear previous items

                    cart.forEach((item, index) => { // Added index for delete functionality
                        const li = document.createElement('li');
                        li.classList.add('cart-item'); // Add a class for styling

                        // Create item layout
                        li.innerHTML = `
            <div class="cart-item-content">
                <img src="${item.image}" alt="${item.name}" class="cart-item-image" />
                <div class="cart-item-details">
                    <span class="cart-item-name">${item.name}</span>
                    <span class="cart-item-price">₱${(item.price * item.quantity).toFixed(2)} (${item.quantity})</span>
                </div>
                <button class="delete-item-btn" onclick="deleteFromCart(${index})">
                    <i class="fas fa-times" style="color: red;"></i>
                </button>
            </div>
        `;
                        cartItemsContainer.appendChild(li);
                    });

                    updateTotalPrice(); // Call this function to update the displayed total price
                }

                function deleteFromCart(index) {
                    const item = cart[index]; // Get the item to be removed
                    if (item) {
                        // Subtract the item's total price from the total price
                        totalPrice -= item.price * item.quantity;
                        cart.splice(index, 1); // Remove item from cart
                        updateCart(); // Update cart display
                        updateTotalPrice(); // Update total price display
                    }
                }

                function showTransactionHistory() {
                    document.getElementById('transaction-history-modal').style.display = 'flex';
                    fetchTodayTransactions(); // Fetch today's transactions when the modal is shown
                }

                function closeTransactionHistory() {
                    document.getElementById('transaction-history-modal').style.display = 'none';
                }

                function fetchTodayTransactions() {
                    // Get today's date in GMT+8 time zone
                    const now = new Date();
                    const gmt8Offset = now.getTimezoneOffset() + (8 * 60); // 8 hours ahead
                    const gmt8Date = new Date(now.getTime() + gmt8Offset * 60 * 1000);
                    const todayGMT8 = gmt8Date.toISOString().split('T')[0]; // Format to YYYY-MM-DD

                    fetch(`get_transactions.php?date=${todayGMT8}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                const formattedTransactions = data.transactions.map(transaction => {
                                    transaction.time = formatTime(transaction.time); // Adjust time format for display
                                    return transaction;
                                });
                                displayTransactions(formattedTransactions);
                            } else {
                                alert("No transactions found for today.");
                            }
                        })
                        .catch(error => {
                            console.error("Error fetching transactions:", error);
                        });
                }

                function displayTransactions(transactions) {
                    const transactionList = document.getElementById('transaction-list');
                    transactionList.innerHTML = ''; // Clear previous transactions

                    // Check if there are no transactions
                    if (transactions.length === 0) {
                        const noTransactionsMessage = document.createElement('div');
                        noTransactionsMessage.classList.add('no-transactions-message');
                        noTransactionsMessage.innerHTML = '<span>No Transactions</span>';
                        transactionList.appendChild(noTransactionsMessage);
                        return; // Exit the function
                    }

                    transactions.forEach(transaction => {
                        // Create transaction card
                        const transactionCard = document.createElement('div');
                        transactionCard.classList.add('transaction-card');

                        // Header with transaction ID and time
                        const transactionHeader = document.createElement('div');
                        transactionHeader.classList.add('transaction-header');
                        transactionHeader.innerHTML = `
            <span>Transaction ID: ${transaction.id}</span>
            <span class="transaction-time">${formatTime(transaction.time)}</span>
        `;
                        transactionHeader.onclick = () => toggleTransactionItems(transaction.id);
                        transactionCard.appendChild(transactionHeader);

                        // Container for transaction items (initially hidden)
                        const transactionItems = document.createElement('div');
                        transactionItems.classList.add('transaction-items');
                        transactionItems.id = `items-${transaction.id}`;

                        let totalTransactionPrice = 0; // Initialize total price for the transaction

                        // Add each item in the transaction
                        transaction.items.forEach(item => {
                            const transactionItem = document.createElement('div');
                            transactionItem.classList.add('transaction-item');
                            transactionItem.innerHTML = `
                <span>${item.name}</span>
                <span>${item.quantity} x ₱${item.totalPrice}</span>
            `;
                            transactionItems.appendChild(transactionItem);

                            // Ensure totalPrice is a number and sum it
                            const itemTotalPrice = parseFloat(item.totalPrice); // Convert to number
                            if (!isNaN(itemTotalPrice)) {
                                totalTransactionPrice += itemTotalPrice; // Add to total if valid
                            } else {
                                console.error(`Invalid price for item ${item.name}: ${item.totalPrice}`);
                            }
                        });

                        // Add a row for the total price of the transaction with a delete icon on the far left
                        const totalRow = document.createElement('div');
                        totalRow.classList.add('transaction-total');
                        totalRow.innerHTML = `
                                    <?php if ($_SESSION['role'] === 'admin'): ?>
    <i class="fas fa-trash delete-icon" style="color: red; cursor: pointer;" onclick="deleteTransaction(${transaction.id})"></i>
                <?php endif; ?>
    <div style="flex-grow: 1;"></div> <!-- This div pushes the Total Price to the right -->
    <span>Total Price:</span>
    <span class="total-price">₱${totalTransactionPrice.toFixed(2)}</span>
`;
                        transactionItems.appendChild(totalRow);


                        transactionCard.appendChild(transactionItems);
                        transactionList.appendChild(transactionCard);
                    });
                }

                let currentTransactionId; // Store the ID of the transaction to delete

                function deleteTransaction(transactionId) {
                    currentTransactionId = transactionId; // Set the current transaction ID
                    document.getElementById('delete-confirmation-modal').style.display = 'flex'; // Show the confirmation modal
                }

                function closeDeleteConfirmation() {
                    document.getElementById('delete-confirmation-modal').style.display = 'none'; // Hide the confirmation modal
                }

                document.getElementById('confirmDeleteButton').onclick = function() {
                    // Make AJAX call to delete the transaction
                    fetch('delete_transaction.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                id: currentTransactionId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showModal("Transaction deleted successfully."); // Use showModal instead of alert
                                fetchTransactions(); // Refresh the transaction list after deletion
                            } else {
                                showModal("Error deleting transaction: " + data.message); // Use showModal instead of alert
                            }
                        })
                        .catch(error => console.error("Error:", error));

                    closeDeleteConfirmation(); // Close the modal after confirming deletion
                };

                function toggleTransactionItems(transactionId) {
                    const items = document.getElementById(`items-${transactionId}`);
                    if (items.style.display === "none" || items.style.display === "") {
                        items.style.display = "block"; // Show items
                    } else {
                        items.style.display = "none"; // Hide items
                    }
                }


                function formatTime(timeString) {
                    // Check if the timeString is valid
                    if (!timeString || typeof timeString !== 'string') {
                        console.error("Invalid time string:", timeString);
                        return "Invalid Time"; // Fallback text for invalid time
                    }

                    // Attempt to split by space for AM/PM
                    const timeParts = timeString.split(' ');

                    let hours, minutes;

                    // Check if the time includes an AM/PM part
                    if (timeParts.length === 2) {
                        // This is likely a 12-hour format
                        const [hoursMinutes, period] = timeParts;
                        [hours, minutes] = hoursMinutes.split(':');

                        // Convert to 24-hour format
                        hours = parseInt(hours, 10);
                        if (period === 'PM' && hours < 12) {
                            hours += 12; // Convert PM hours to 24-hour format
                        } else if (period === 'AM' && hours === 12) {
                            hours = 0; // Convert 12 AM to 0 hours
                        }
                    } else if (timeParts.length === 1) {
                        // This is likely a 24-hour format
                        [hours, minutes] = timeString.split(':');
                        hours = parseInt(hours, 10); // Ensure hours is a number
                        minutes = parseInt(minutes, 10); // Ensure minutes is a number
                    } else {
                        console.error("Unexpected time format:", timeString);
                        return "Invalid Time"; // Fallback text for unexpected format
                    }

                    // Create a date object to format the time
                    const time = new Date();
                    time.setHours(hours, minutes);

                    // Format the time to 12-hour format for display
                    return time.toLocaleTimeString('en-US', {
                        hour: 'numeric',
                        minute: 'numeric',
                        hour12: true
                    });
                }

                function filterItems() {
                    const searchTerm = document.getElementById('search').value.toLowerCase();
                    const selectedCategory = document.getElementById('category-select').value; // Get the selected category
                    const itemCards = document.querySelectorAll('.item-card');

                    itemCards.forEach(card => {
                        const itemName = card.querySelector('h3').textContent.toLowerCase();
                        const itemCategory = card.dataset.category; // Assuming each card has a data-category attribute

                        // Check if the item matches the search term and the selected category
                        const matchesSearch = itemName.includes(searchTerm);
                        const matchesCategory = selectedCategory === "" || itemCategory === selectedCategory; // Check if "All Categories" is selected

                        // Display the item if it matches both criteria
                        if (matchesSearch && matchesCategory) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                }


                document.addEventListener("DOMContentLoaded", () => {
                    loadItems();
                });

                function loadItems() {
                    fetch('get_items_bills.php')
                        .then(response => response.json())
                        .then(items => {
                            const itemCardsContainer = document.querySelector('.item-cards');
                            itemCardsContainer.innerHTML = ''; // Clear existing items

                            items.forEach(item => {
                                console.log(item); // Log the item to see its structure

                                // Validate price, stock, and category
                                if (typeof item.price !== 'number' || typeof item.stock !== 'number') {
                                    console.error(`Invalid data for item ${item.name}:`, item);
                                    return; // Skip this item if the price or stock is not a valid number
                                }

                                const card = document.createElement('div');
                                card.className = 'item-card';
                                card.setAttribute('data-category', item.category); // Set data-category attribute
                                card.innerHTML = `
<img src="${item.image}" alt="${item.name}" class="item-image">
<h3>${item.name}</h3>
<div class="card-content">
    <div class="item-info">
        <div class="price-stock">
            <span class="icon price-icon"><i class="fas fa-tags"></i></span>
            <span class="price">₱${item.price.toFixed(2)}</span>
            <span class="icon stock-icon"><i class="fas fa-box"></i></span>
            <span class="stock-count" data-stock="${item.stock}">${item.stock}</span>
        </div>
    </div>
    <div class="quantity-container">
    <button class="decrease-btn" onclick="adjustQuantity(this, -1)">
        <i class="fas fa-minus"></i>
    </button>
    <input type="number" class="quantity-input" value="0" min="0" max="${item.stock}" oninput="validateQuantityInput(this)"/>
    <button class="increase-btn" onclick="adjustQuantity(this, 1)">
        <i class="fas fa-plus"></i>
    </button>
</div>
    <button class="add-to-cart-btn" 
        data-name="${item.name}" 
        data-price="${item.price}" 
        data-image="${item.image}"
        onclick="addToCart('${item.name}', ${item.price}, '${item.image}', this)">
            <i class="fas fa-shopping-cart"></i>
</button>
</button>
</div>
                `;
                                itemCardsContainer.appendChild(card);
                            });
                        })
                        .catch(error => {
                            console.error('Error fetching items:', error);
                            // Optionally display an error message to the user
                            const itemCardsContainer = document.querySelector('.item-cards');
                            itemCardsContainer.innerHTML = '<p>Error loading items. Please try again later.</p>';
                        });
                }

                function adjustQuantity(button, change) {
                    // Get the quantity input
                    const quantityInput = button.parentElement.querySelector('.quantity-input');
                    let currentQuantity = parseInt(quantityInput.value);

                    // Find the stock-count element properly
                    const stockCountElement = button.closest('.card-content').querySelector('.stock-count');

                    // If the stock count element is found, retrieve the stock value
                    if (stockCountElement) {
                        const stockCount = parseInt(stockCountElement.dataset.stock);

                        // Adjust the quantity based on button click
                        currentQuantity += change;

                        // Ensure quantity does not go below 0
                        if (currentQuantity < 0) {
                            currentQuantity = 0;
                        } else if (currentQuantity > stockCount) {
                            currentQuantity = stockCount; // Limit to available stock
                        }

                        quantityInput.value = currentQuantity;

                        // Disable "Add to Cart" if quantity is 0
                        button.parentElement.querySelector('button:last-of-type').disabled = (currentQuantity === 0);
                    } else {
                        console.error('Stock count element not found.');
                    }
                }

                // New function to handle input changes directly
                function validateQuantityInput(input) {
                    const stockCountElement = input.parentElement.closest('.card-content').querySelector('.stock-count');
                    if (stockCountElement) {
                        const stockCount = parseInt(stockCountElement.dataset.stock);
                        let currentQuantity = input.value.trim(); // Get the input value as a string

                        // Prevent input if the first character is 0 and not just "0"
                        if (currentQuantity.length > 1 && currentQuantity.startsWith('0')) {
                            input.value = '0'; // Set to 0 if leading zero is present
                            return;
                        }

                        // Parse the current quantity as an integer
                        currentQuantity = parseInt(currentQuantity);

                        // Check if the currentQuantity is NaN or negative
                        if (isNaN(currentQuantity) || currentQuantity < 0) {
                            input.value = '0'; // Set to 0 if not a valid number or negative
                        } else if (currentQuantity > stockCount) {
                            input.value = stockCount; // Set to max stock if exceeded
                        }
                    }
                }

                function calculateChange() {
                    // Get the total price from the displayed element
                    const totalPriceText = document.getElementById('total-price').innerText;
                    const totalPrice = parseFloat(totalPriceText.replace('₱', '').replace(',', ''));

                    // Get the payment input value
                    const paymentInput = document.getElementById('payment-input');
                    const paymentAmount = parseFloat(paymentInput.value);

                    // Check if the total price is valid and greater than 0
                    if (isNaN(totalPrice) || totalPrice <= 0) {
                        showModal('The total amount must be valid and greater than zero.');
                        document.getElementById('change-amount').innerText = '₱0.00'; // Set the change amount to 0
                        return; // Exit the function if the total is invalid
                    }

                    // Validate the payment amount
                    if (paymentAmount && paymentAmount >= totalPrice) {
                        const change = paymentAmount - totalPrice;
                        document.getElementById('change-amount').innerText = `₱${change.toFixed(2)}`;
                    } else {
                        showModal('The payment amount must be greater than or equal to the total.');
                        document.getElementById('change-amount').innerText = '₱0.00'; // Set the change amount to 0
                    }
                }

                function addTransactionToDB() {
                    // Ensure the cart has items
                    if (cart.length === 0) {
                        showModal("Cart is empty");
                        return; // Exit if the cart is empty
                    }

                    // Prepare the data for the transaction
                    const transactionData = {
                        items: cart.map(item => ({
                            name: item.name,
                            quantity: item.quantity,
                            totalPrice: item.price * item.quantity
                        }))
                    };

                    // Log the transaction data being sent to the server
                    console.log("Transaction Data:", JSON.stringify(transactionData));

                    // Send the transaction data to the server via AJAX (or fetch)
                    fetch('add_transaction.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(transactionData)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showModal("Transaction added successfully.");

                                // Call the function to delete items from inventory
                                deleteItemsFromInventory(transactionData.items);

                                // Reset the cart and total values
                                cart = []; // Clear the cart after successful transaction
                                updateCart(); // Update the cart display
                                totalPrice = 0; // Reset total price
                                updateTotalPrice(); // Update the total price display
                                loadItems();
                            } else {
                                showModal(`Error: ${data.error}`);
                            }
                        })
                        .catch(error => {
                            showModal("An error occurred while processing the transaction.");
                            console.error("Transaction error:", error);
                        });
                }

                // Function to delete items from item_inventory based on the cart
                function deleteItemsFromInventory(items) {
                    const itemsToDelete = [];

                    items.forEach(item => {
                        // Loop to find the items in item_inventory to delete
                        for (let i = 0; i < item.quantity; i++) {
                            itemsToDelete.push(item.name); // Collect item names to delete
                        }
                    });

                    // Prepare a request to the server to delete items
                    const deleteData = {
                        items: itemsToDelete
                    }; // Correct structure for PHP script

                    fetch('delete_items_transaction.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(deleteData) // Send structured data
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                console.log("Items deleted successfully.");
                            } else {
                                console.error(`Error deleting items: ${data.error}`);
                            }
                        })
                        .catch(error => {
                            console.error("Error while deleting items:", error);
                        });
                }

                // Function to display the confirm transaction modal
                function confirmTransaction() {
                    // Check if the cart is empty before showing the modal
                    if (cart.length === 0) {
                        showModal("Cart is empty");
                        return; // Exit if the cart is empty
                    }

                    // Show the confirm transaction modal
                    document.getElementById('confirmTransactionModal').style.display = 'flex';
                }

                // Event listeners for Confirm and Cancel buttons in the confirmation modal
                document.getElementById('confirmTransactionButton').addEventListener('click', function() {
                    // Hide the confirmation modal
                    document.getElementById('confirmTransactionModal').style.display = 'none';
                    // Proceed to add the transaction to the database
                    addTransactionToDB(); // This should only run after modal closure
                });

                document.getElementById('cancelTransactionButton').addEventListener('click', function() {
                    // Hide the confirmation modal without adding the transaction
                    document.getElementById('confirmTransactionModal').style.display = 'none';
                });

                // Modify the Confirm Transaction button to trigger the confirmTransaction function
                document.querySelector('.confirm-transaction-btn').addEventListener('click', confirmTransaction);
            </script>
</body>

</html>