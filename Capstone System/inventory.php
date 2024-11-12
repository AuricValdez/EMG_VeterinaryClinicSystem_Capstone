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
    <title>Inventory Management</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
    <style>
        body {
            background-color: white;
            /* Ensure the body background is white */
            margin: 0;
            /* Remove default margin */
            padding: 0;
            /* Remove default padding */
        }

        .inventory-title {
            text-align: center;
            color: white;
            font-size: 20px;
            position: relative;
        }

        .inventory-title i {

            margin-right: 10px;
            /* Space between the icon and text */
            font-size: 25px;
            /* Smaller icon size */
            color: white;
        }

        /* Header Styles */
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

        #inventory-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            /* Space between cards */
            justify-content: center;
            /* Center cards horizontally */
            background-color: white;
            margin-top: 30px;
        }

        .card {
            background-color: #333;
            color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            padding: 0;
            text-align: center;
            transition: all 0.5s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            height: 350px;
            width: 200px;
        }

        .card:hover {
            height: 500px;
            background-color: #4f4f4f;
            transform: translateY(-10px);
            filter: drop-shadow(5px 5px 5px rgba(0, 0, 0, 0.6));
            transform: scale(1.05);
        }

        .card-footer {
            background-color: orange;
            color: white;
            display: flex;
            justify-content: space-between;
            padding-top: 10px;
            transition: background-color 0.3s ease, color 0.3s ease;
            margin-bottom: -5px;
            /* Reduced padding to remove extra space */
        }

        /* Hover effect for the card */
        .card:hover .card-footer {
            background-color: white;
            /* Change background color to white on hover */
            color: orange;
            /* Change text color to white on hover */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.0);
        }

        /* Ensure footer sections have the correct colors on hover */
        .card:hover .card-footer .footer-section {
            color: orange;
            /* Ensure text color is white on hover */
        }

        /* Ensure the icons are visible against the white background on hover */
        .card:hover .card-footer .footer-section i {
            color: orange;
            /* Change icon color to white on hover */
            transition: color 0.3s ease;
            /* Smooth transition for icon color */
        }

        .footer-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            /* Equal space for all sections */
            text-align: center;
        }

        .footer-section .value {
            font-size: 15px;
            /* Increase the size of the value */
            /* Make the value more prominent */
        }

        .card .footer-section i {
            font-size: 1.2rem;
            /* Adjust this size as needed */
        }

        .quantity-section,
        .price-section {
            padding: 0.5rem 0;
        }

        .info-icon {
            cursor: pointer;
            font-size: 1.2rem;
            /* Adjust the icon size */
            display: flex;
            justify-content: center;
            /* Center the icon horizontally */
            align-items: center;
            /* Center the icon vertically */
            height: 100%;
            margin-top: -5px;
        }

        .card img {
            width: 100%;
            height: 175px;
            /* Set to 1/3 of the card height */
            object-fit: cover;
            border: none;
            /* Remove border/bezel */
            border-radius: 0 0 20px 20px;
            /* Remove border-radius if any */
            filter: drop-shadow(5px 5px 5px rgba(0, 0, 0, 0.5));
        }

        .card .card-content {
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card h3 {
            color: orange;
            font-size: 1.5rem;
            margin: 0px 0;
            text-shadow: 3px 3px 5px rgba(0, 0, 0, 0.5);
        }

        .card p {
            margin: 10px 0;
        }

        .card .quantity-price {
            margin-top: 10px;
            /* Space above the quantity and price */
            font-size: 1rem;
            /* Font size for quantity and price */
        }

        .card .quantity-price .row {
            display: flex;
            align-items: center;
            /* Align items vertically */
            justify-content: space-between;
            margin-bottom: 2px;
            /* Adjust the space between rows */
            padding: 0;
            /* Remove padding if any */
        }

        .card .quantity-price .label {
            text-align: left;
            font-weight: bold;
            flex: 1;
            display: flex;
            align-items: center;
            /* Center icon and text vertically */
        }

        .card .quantity-price .label i {
            margin-right: 5px;
            /* Space between icon and text */
        }

        .card .quantity-price .value {
            text-align: right;
            flex: 1;
        }

        .card .quantity-price .label,
        .card .quantity-price .value {
            margin: 0;
            /* Remove margin from label and value */
            padding: 0;
            /* Remove padding from label and value */
        }

        /* Actions container */
        .card .actions-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding: 10px;
            position: relative;
            top: 0;
            left: 0;
            right: 0;
        }

        /* Active state color for the info icon */
        .card.active .info-icon {
            color: #ffeb99;
            /* Match the active color */
        }

        .card.active:hover .info-icon {
            color: #e67e22;
        }

        /* Edit and delete icons at the top right */
        .card .edit-delete-icons {
            display: none;
            position: absolute;
            top: 10px;
            right: 10px;
            gap: 15px;
            font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .edit-delete-overlay {
            position: absolute;
            /* Position it absolutely within the card */
            top: 0;
            /* Align to the top */
            left: 0;
            /* Align to the left */
            right: 0;
            /* Align to the right */
            bottom: 0;
            /* Align to the bottom */
            background-color: rgba(0, 0, 0, 0.6);
            /* Black with 0.6 opacity */
            /* Behind the icons */
            display: none;
            /* Initially hidden */
            height: 50px;
        }

        /* Show edit and delete icons when edit mode is active */
        .edit-mode .card .edit-delete-icons {
            display: flex;
        }

        /* Hover effect for the icons */
        .card .info-icon:hover,
        .card .edit-delete-icons i:hover {
            color: #ffeb99;
        }

        /* Style for the scrollbar container */
        .card .info-text {
            max-height: 0;
            /* Hide by default */
            overflow-y: auto;
            /* Enable vertical scrollbar when content overflows */
            padding: 0;
            /* No padding by default */
            margin-top: 0;
            /* No margin by default */
            opacity: 0;
            /* Hide by default */
            transition: max-height 0.5s ease, opacity 0.5s ease, padding 0.5s ease, margin 0.5s ease;
        }

        .card:hover .info-text {
            max-height: 100px;
            /* Adjust based on your content */
            opacity: 1;
            /* Make it visible */
            padding: 10px;
            /* Add padding */
            margin-top: 0px;
            /* Space between content */
        }

        .card .category {
            color: #a9a9a9;
            /* Dark gray color */
            font-style: italic;
            /* Italicize the text */
            padding: 0;
            /* No padding by default */
            margin-top: 0;
            /* No margin by default */
            opacity: 0;
            /* Hide by default */
            transition: max-height 0.5s ease, opacity 0.5s ease, padding 0.5s ease, margin 0.5s ease;
        }

        /* Show category on hover */
        .card:hover .category {
            opacity: 1;
            /* Add padding */
            margin-top: -30px;
            /* Space between content */
        }

        /* Webkit-based browsers (Chrome, Safari) */
        .card .info-text::-webkit-scrollbar {
            width: 8px;
            /* Slightly thicker than before */
        }

        .card .info-text::-webkit-scrollbar-track {
            background: transparent;
            /* Make the track invisible */
        }

        .card .info-text::-webkit-scrollbar-thumb {
            background: orange;
            /* Semi-transparent color of the scrollbar handle */
            border-radius: 4px;
            /* Slightly rounded corners */
            transition: background 0.3s;
        }

        .card .info-text::-webkit-scrollbar-thumb:hover {
            background: darkorange;
            /* Darker color on hover */
        }


        .card .info-header {
            font-weight: bold;
            margin-bottom: 20px;
            font-size: 1rem;
            /* Ensure header font size matches quantity and price */
        }

        .card .info-content {
            margin-top: 5px;
            /* Space between description header and content */
            font-size: 1rem;
            /* Font size for description content to match quantity and price */
        }

        /* Show description and hide quantity/price when active */
        .card.active .info-text {
            max-height: 200px;
            /* Adjust as needed */
            opacity: 1;
        }

        .info-text.visible {
            max-height: 200px;
            /* Adjust based on your content */
            opacity: 1;
            padding: 10px;
            /* Ensure padding to make content visible */
            margin-top: 10px;
            /* Ensure space between content */
        }

        .card.active .quantity-price {
            display: none;
            /* Hide quantity/price on active */
        }

        .card.active {
            height: 500px;
            /* Adjust the height as needed */
        }

        .actions {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
            /* Space below the title */
        }

        .action-item {
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            font-size: 18px;
            /* Adjust text size */
        }

        .action-item i {
            font-size: 20px;
            /* Icon size next to text */
            color: orange;
            /* Icon color */
            transition: color 0.3s ease;
            /* Color transition */
        }

        .action-item span {
            color: orange;
            /* Text color */
            transition: color 0.3s ease;
            /* Color transition */
        }

        .action-item:hover i {
            color: #e67e22;
            /* Slightly darker orange on hover */
        }

        .action-item:hover span {
            color: #e67e22;
            /* Slightly darker orange on hover */
        }

        .divider {
            height: 24px;
            /* Divider height */
            width: 1px;
            /* Divider width */
            background-color: orange;
            /* Divider color */
        }

        .modal,
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 999;
        }

        .modal-content input,
        .modal-content textarea,
        .modal-content button,
        select {
            font-family: 'Roboto', sans-serif;
            /* Set the font to Roboto */
            font-size: 16px;
            /* Adjust font size if needed */
        }

        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            max-width: 100%;
            padding-right: 25px;
            z-index: 1000;
        }

        .modal-title {
            text-align: center;
            color: orange;
            font-size: 1.5em;
            margin-bottom: 20px;
        }

        .modal-title i {
            margin-right: 10px;
            /* Space between the icon and text */
            font-size: 1em;
            /* Icon size */
            color: orange;
        }

        /* General Modal Styles */
        .modal-content input,
        .modal-content textarea,
        select {
            width: calc(100% - 20px);
            margin: 10px 0;
            padding: 10px;
            border: 2px solid orange;
            /* Changed border color to orange */
            border-radius: 4px;
        }

        .modal-content input:focus,
        .modal-content textarea:focus,
        select:focus {
            border-color: #e67e22;
            /* Optional: Slightly darker orange on focus */
            outline: none;
            /* Remove default focus outline */
        }

        #item-category,
        #edit-item-category {
            width: 405px;
        }

        #item-category option.placeholder,
        #edit-item-category option.placeholder {
            color: gray;
        }

        /* Add gray text color for select dropdown if no value is selected */
        #item-category:invalid,
        #edit-item-category:invalid {

            color: gray;
        }

        #edit-item-category select {
            color: black;
        }


        .modal-content textarea {
            height: 100px;
            /* Fixed height */
            resize: none;
            /* Prevent resizing */
        }

        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
            /* Space above buttons */
        }

        .modal-buttons button {
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            color: #fff;
            cursor: pointer;
            background-color: #2c3e50;
            transition: background-color 0.3s ease, transform 0.2s ease;
            /* Added transition for transform */
        }

        .modal-buttons button.close {
            background-color: #e74c3c;
            /* Color for Close button */
        }

        .modal-buttons button:hover {
            background-color: #1e8449;
            /* Darker green for Add Item button */
            transform: scale(1.05);
            /* Slightly enlarges the button */
        }

        .modal-buttons button.close:hover {
            background-color: #c0392b;
            /* Darker red on hover */
            transform: scale(1.05);
            /* Slightly enlarges the button */
        }

        .modal-buttons button[type="submit"] {
            background-color: #27ae60;
            /* Green color */
            /* Darker green on hover */
        }

        .modal-buttons button[type="submit"]:hover {
            background-color: #1e8449;
            /* Darker green color */
        }

        .card.selected {
            border: 10px solid black;
        }

        /* Container for all action buttons */
        .fixed-action-btn-container {
            position: fixed;
            bottom: 20px;
            /* Adjust the spacing from the bottom as needed */
            right: 20px;
            /* Adjust the spacing from the right as needed */
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: transform 0.5s ease;
            /* Smooth transition for sliding */
        }

        .fixed-action-btn {
            background-color: orange;
            /* Background color of buttons */
            border-radius: 50%;
            width: 50px;
            /* Adjust width */
            height: 50px;
            /* Adjust height */
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.5s, background-color 0.5s ease;
            margin: 10px 0;
            /* Spacing between buttons */
            filter: drop-shadow(5px 5px 5px rgba(0, 0, 0, 0.4));
        }

        .fixed-action-btn i {
            font-size: 25px;
            /* Adjust size as needed */
            color: white;
            cursor: pointer;
            transition: color 0.5s ease, transform 0.5s ease, box-shadow 0.5s ease;
        }

        .fixed-action-btn:hover i {
            color: white;
            transform: scale(1.1);
            /* Slightly increase size on hover */
        }

        .fixed-action-btn:hover {
            background: #c55900;
            transform: scale(1.1);
            filter: drop-shadow(5px 5px 5px rgba(0, 0, 0, 0.2));
        }

        .hidden {
            transform: translateY(150px);
            /* Slide down effect */
        }

        .visible {
            transform: translateY(0);
            /* Slide up effect */
        }

        .card .edit-delete-icons {
            display: none;
            /* Hidden by default */
        }

        .edit-mode .card .edit-delete-icons {
            display: flex;
            /* Show icons when edit-mode is active */
        }

        .edit-delete-icons i {
            transition: color 0.3s ease, transform 0.3s ease, filter 0.3s ease;
            cursor: pointer;
        }

        .edit-delete-icons .fa-edit {
            color: #007bff;
            /* Edit icon color */
        }

        .edit-delete-icons .fa-trash {
            color: red;
            /* Trash icon color */
        }

        .edit-delete-icons:hover .fa-edit {
            color: #0056b3 !important;
            /* Edit icon color */
        }

        .edit-delete-icons:hover .fa-trash {
            color: red !important;
            /* Trash icon color */
        }

        .edit-delete-icons i:hover {
            transform: scale(1.2);
            /* Make icons pop out on hover */
            filter: drop-shadow(0 0 10px rgba(0, 0, 0, 0.5));
            /* Add a shadow effect */
        }

        .edit-active {
            background-color: #c55900;
        }

        /* Centering the Edit Item text and adding the icon */
        .edit-modal-title {
            text-align: center;
            color: orange;
            font-size: 1.5em;
            margin-bottom: 20px;
        }

        .edit-modal-title i {
            margin-right: 10px;
            font-size: 1em;
            color: orange;
        }

        /* Center buttons in the edit modal */
        .edit-modal-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        /* Styling the buttons */
        .edit-modal-buttons button {
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            color: #fff;
            cursor: pointer;
            background-color: #27ae60;
            /* Default green for update button */
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        /* Close button red color */
        .edit-modal-buttons button.close {
            background-color: #e74c3c;
        }

        .edit-modal-buttons button:hover {
            background-color: #1e8449;
            transform: scale(1.05);
        }

        .edit-modal-buttons button.close:hover {
            background-color: #c0392b;
        }

        .modal-prompt {
            text-align: center;
            margin-top: 20px;
            font-size: 1em;
            color: green;
            display: none;
        }

        .delete-item-title {
            text-align: center;
            color: orange;
            font-size: 1.5em;
            margin-bottom: 20px;
        }

        .delete-item-title i {
            margin-right: 10px;
            font-size: 1em;
            color: orange;
        }

        .delete-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .delete-modal-content {
            gap: 10px;
            margin-top: 20px;
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            border-radius: 15px;
            width: 350px;
            text-align: center;
        }

        .delete-modal-buttons {
            margin-top: 20px;
        }

        .yes-button,
        .no-button {
            margin-left: 10px;
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            color: #fff;
            cursor: pointer;
            background-color: #27ae60;
            /* Default green for update button */
            transition: background-color 0.3s ease, transform 0.2s ease;

        }

        .no-button {
            background-color: #e74c3c;
            /* Red for No */
        }

        .yes-button:hover {
            background-color: #1e8449;
            transform: scale(1.05);
        }

        .no-button:hover {
            background-color: #c0392b;
            transform: scale(1.05);
        }

        .success-modal {
            display: none;
            position: fixed;
            z-index: 1001;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .success-modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 300px;
            /* Change width as needed */
            border-radius: 10px;
            /* Optional for rounded corners */
            text-align: center;
        }

        .close-success-modal {
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            color: #fff;
            cursor: pointer;
            background-color: #e74c3c;
            /* Default green for update button */
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .close-success-modal:hover {
            background-color: #c0392b;
            transform: scale(1.05);
        }

        .search-category-bar {
            display: flex;
            gap: 10px;
            margin-top: 70px;
            margin-left: -20px;
            justify-content: flex-start;
            padding-left: 20px;
        }

        .search-bar-container {
            display: flex;
            /* Use flexbox for alignment */
            align-items: center;
            /* Center vertically */
            position: relative;
            width: 735px;
            /* Match the search bar width */
            z-index: 1;
        }

        #search-bar {
            flex-grow: 1;
            /* Allow the search bar to grow and fill space */
            height: 30px;
            padding: 10px 15px;
            padding-right: 40px;
            /* Space for search icon */
            border-radius: 8px;
            border: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            font-size: 1em;
            transition: all 0.3s ease;
            border: 1px solid #ccc;
            margin-top: -10px;
            z-index: 1;
        }

        #search-bar:focus {
            outline: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            border: 1px solid #c55900;
        }

        #category-filter {
            width: 300px;
            height: 52px;
            padding: 10px;
            border-radius: 8px;
            border: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            font-size: 1em;
            transition: all 0.3s ease;
            margin-top: -1px;
            margin-left: 15px;
            border: 1px solid #ccc;
        }

        #category-filter:focus {
            outline: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            border: 1px solid #c55900;
        }

        #search-bar::placeholder {
            color: #aaa;
            font-style: italic;
        }

        #category-filter:hover,
        #search-bar:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        /* Style for unclickable search icon */
        .search-icon {
            margin-top: -5px;
            margin-left: -30px;
            /* Adjust this to position it correctly */
            color: gray;
            /* Gray color when unhovered */
            font-size: 18px;
            cursor: default;
            /* Prevent pointer cursor */
        }

        /* Change search icon color on hover/focus of search bar */
        #search-bar:hover~.search-icon,
        #search-bar:focus~.search-icon {
            color: #c55900;
            transition: all 0.3s ease;
            /* Change to orange when hovered or focused */
        }

        /* Style for statistics button */
        .statistics-button {
            background-color: orange;
            /* Same orange color */
            border: none;
            /* Remove border */
            border-radius: 8px;
            /* Rounded corners */
            height: 52px;
            /* Match height with category filter */
            width: 300px;
            /* Increased width */
            display: flex;
            /* Center icon */
            align-items: center;
            /* Center vertically */
            justify-content: center;
            /* Center horizontally */
            cursor: pointer;
            /* Pointer cursor for button */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-top: -1px;
            transition: all 0.3s ease;
            /* Added transition */
        }

        .statistics-button:hover {
            background-color: #c55900;
            /* Darker shade on hover */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            transform: scale(1.05);
            /* Slightly grow the button on hover */
        }

        /* Statistics icon */
        .statistics-icon {
            color: white;
            /* White color for the icon */
            font-size: 20px;
            /* Adjust size as needed */
        }

        .search-hidden {
            display: none;
        }

        .inventory-modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 1000;
            /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: hidden;
            /* Enable scroll if needed */
            background-color: rgba(0, 0, 0, 0.5);
            /* Black with opacity for a softer backdrop */
            transition: opacity 0.3s ease;
            /* Smooth transition for the modal */
        }

        .inventory-header {
            display: flex;
            align-items: center;
            /* Center icon and text vertically */
            justify-content: center;
            /* Center content horizontally */
            color: orange;
            /* Set the text color to orange */
            font-size: 1.5rem;
            /* Increase the font size for the header */
        }

        .inventory-header i {
            margin-right: 8px;
            /* Space between the icon and text */
            font-size: 1.5rem;
            /* Increase icon size */
            color: orange;
            /* Ensure the icon color matches the header */
        }

        .inventory-modal-content {
            background-color: #fff;
            /* White background for modal content */
            margin: 10% auto;
            /* Centered with margin from the top */
            padding: 20px;
            /* Inner spacing */
            border-radius: 10px;
            /* Rounded corners */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            /* Subtle shadow for depth */
            width: 90%;
            /* Responsive width */
            max-width: 1000px;
            /* Max width for larger screens */
        }

        .inventory-modal-hidden {
            display: none;
            /* Keep it hidden */
        }

        .inventory-modal:not(.inventory-modal-hidden) {
            display: flex;
            /* Use flexbox to center the modal */
            justify-content: center;
            /* Center horizontally */
            align-items: center;
            /* Center vertically */
        }

        .modal-close-footer {
            display: flex;
            justify-content: center;
            /* Center the button */
            margin-top: 20px;
            /* Space above the button */
        }

        .close-button {
            font-size: 16px;
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            color: #fff;
            cursor: pointer;
            background-color: #e74c3c;
            /* Default green for update button */
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .close-button:hover {
            background-color: #c0392b;
            transform: scale(1.05);
        }

        /*add,update,delete buttons*/
        .add-inventory-section {
            display: flex;
            justify-content: center;
            /* Center the buttons horizontally */
            margin-top: 20px;
            /* Add some space above the buttons */
        }

        .add-inventory-button,
        .edit-inventory-button,
        .delete-inventory-button {
            /* Apply styles to all buttons */
            padding: 10px;
            border: none;
            border-radius: 50%;
            background-color: orange;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin: 0 10px;
            /* Add horizontal padding between buttons */
        }

        .add-inventory-button:hover,
        .edit-inventory-button:hover,
        .delete-inventory-button:hover {
            /* Add hover effect for all buttons */
            transform: scale(1.1);
            background-color: #c55900;
        }

        .inventory-form {
            margin-top: 10px;
        }

        .inventory-form.hidden {
            display: none;
        }

        .hidden-button {
            display: none;
            /* Hides the button */
        }

        /*Add Inventory From Styling*/
        .inventory-form {
            background-color: #f9f9f9;
            /* Light background for contrast */
            padding: 20px;
            padding-right: 40px;
            /* Inner spacing */
            border-radius: 8px;
            /* Rounded corners */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            /* Soft shadow for depth */
            margin-top: 15px;
            /* Space above the form */
        }

        /* Label Styles */
        .inventory-form label {
            display: block;
            /* Stack labels and inputs */
            margin-bottom: 8px;
            /* Space between labels and inputs */
            font-weight: bold;
            /* Bold labels for better readability */
            color: #333;
            /* Darker text color */
        }

        /* Input Styles */
        .inventory-form input {
            width: 100%;
            /* Full width for inputs */
            padding: 10px;
            /* Inner spacing */
            margin-bottom: 15px;
            /* Space between inputs */
            border: 1px solid #ccc;
            /* Light border */
            border-radius: 4px;
            /* Rounded corners */
            font-size: 1rem;
            /* Font size for inputs */
            transition: border-color 0.3s;
            /* Transition for border color */
        }

        /* Input Focus Styles */
        .inventory-form input:focus {
            border-color: #007bff;
            /* Change border color on focus */
            outline: none;
            /* Remove default outline */
        }

        /* Button Styles */
        .add-inventory-submit,
        .back-button {
            padding: 10px 20px;
            /* Button padding */
            border: none;
            /* No border */
            border-radius: 4px;
            /* Rounded corners */
            color: #fff;
            /* White text */
            cursor: pointer;
            /* Pointer on hover */
            transition: background-color 0.3s ease, transform 0.2s ease;
            /* Smooth transitions */
        }

        /* Submit Button Styles */
        .add-inventory-submit {
            font-size: 16px;
            border-radius: 20px;
            background-color: #28a745;
            /* Green for add inventory */
        }

        .add-inventory-submit:hover {
            background-color: #218838;
            /* Darker green on hover */
            transform: scale(1.05);
            /* Slightly larger on hover */
        }

        /* Back Button Styles */
        .back-button {
            margin-right: -20px;
            font-size: 16px;
            border-radius: 20px;
            background-color: #e74c3c;
            ;
            /* Blue for back button */
        }

        .back-button:hover {
            background-color: #c0392b;
            /* Darker blue on hover */
            transform: scale(1.05);
            /* Slightly larger on hover */
        }

        /* Footer Styles */
        .modal-footer {
            display: flex;
            /* Use flexbox for layout */
            justify-content: space-between;
            /* Space between buttons */
        }

        /* Item Inventory Table */
        /* Table Wrapper */
        .table-wrapper {
            max-height: 500px;
            /* Adjust height as needed */
            overflow-y: auto;
            /* Enable vertical scrolling */
            overflow-x: auto;
            /* Enable horizontal scrolling */
            border: 1px solid #ddd;
            /* Add border around the table */
            border-radius: 5px;
            /* Rounded corners */
            width: 100%;
            /* Full width of the parent container */
        }

        /* Inventory Table Styles */
        .inventory-table {
            width: 100%;
            /* Set to a larger width than the parent */
            min-width: 800px;
            /* Set a minimum width if needed */
            border-collapse: collapse;
            /* Collapse borders */
            font-family: 'Roboto', sans-serif;
            /* Change font family */
        }

        .inventory-table th,
        .inventory-table td {
            padding: 12px;
            /* Add padding */
            text-align: left;
            /* Align text to the left */
            border-bottom: 1px solid #ddd;
            /* Add bottom border to rows */
        }

        .inventory-table th {
            background-color: #f4f4f4;
            /* Light grey background for header */
            color: #333;
            /* Darker text color */
            font-weight: bold;
            /* Bold header text */
        }

        .inventory-table tr:hover {
            background-color: #f1f1f1;
            /* Change background on hover */
        }

        /* Responsive Design */
        @media (max-width: 600px) {

            .inventory-table th,
            .inventory-table td {
                padding: 8px;
                /* Adjust padding for smaller screens */
            }
        }

        /* General Modern Button Style */
        .modern-button {
            background-color: #f0f0f0;
            /* Light background */
            border: none;
            /* No border */
            border-radius: 4px;
            /* Rounded corners */
            padding: 8px 12px;
            /* Spacing */
            font-size: 14px;
            /* Font size */
            font-weight: bold;
            /* Bold text */
            display: inline-flex;
            /* Flex for icon and text alignment */
            align-items: center;
            /* Center align icon and text */
            gap: 6px;
            /* Spacing between icon and text */
            cursor: pointer;
            /* Pointer on hover */
            transition: background-color 0.3s ease;
            /* Smooth hover transition */
        }

        /* Edit Button */
        .edit-button {
            color: #fff;
            /* White text */
            background-color: #007bff;
            /* Primary color for edit */
        }

        .edit-button:hover {
            transition: all 0.5s ease;
            transform: scale(1.05);
            background-color: #0056b3;
            /* Darker on hover */
        }

        /* Delete Button */
        .delete-button {
            color: #fff;
            /* White text */
            background-color: #dc3545;
            /* Danger color for delete */
        }

        .delete-button:hover {
            transition: all 0.5s ease;
            transform: scale(1.05);
            background-color: #c82333;
            /* Darker on hover */
        }

        /* Icon Styling */
        .modern-button i {
            font-size: 16px;
            /* Slightly larger icon */
        }


        /* Optional: Table and Cell Styling for Modern Look */
        #inventory-table td {
            padding: 12px;
            /* Add padding for better readability */
            text-align: center;
            /* Center-align text for cleaner look */
        }

        #inventory-table th {
            text-align: center;
            background-color: #f8f9fa;
            /* Light background for header */
            font-weight: bold;
            /* Bold header */
            padding: 12px;
            /* Header padding */
        }

        .red-text {
            color: red;
        }

        .green-text {
            color: green;
        }

        .orange-text {
            color: orange;
        }

        .unique-confirmation-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            /* Semi-transparent background */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            /* Ensure it's above other content */
        }

        .unique-confirmation-dialog {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            /* Center text inside the dialog */
        }

        .confirmation-title {
            color: orange;
            /* Set title color to orange */
            font-size: 20px;
            /* Increase font size for the title */
            margin-bottom: 10px;
            /* Space below the title */
        }

        .delete-info-icon {
            color: orange;
            /* Set icon color to orange */
            margin-right: 8px;
            /* Space between icon and title */
            font-size: 24px;
            /* Adjust size of the icon */
        }

        .confirmation-message {
            color: black;
            /* Set message color to black */
            font-size: 16px;
            /* Set font size for the message */
            margin-bottom: 20px;
            /* Space below the message */
        }

        .unique-confirmation-buttons {
            display: flex;
            justify-content: center;
            /* Center buttons */
            gap: 15px;
            /* Add space between buttons */
        }

        .unique-confirm-button,
        .unique-cancel-button {
            margin-left: 10px;
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            color: #fff;
            cursor: pointer;
            background-color: #27ae60;
            /* Default green for update button */
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .unique-confirm-button:hover {
            background-color: #1e8449;
            transform: scale(1.05);
        }

        .unique-cancel-button {
            background-color: #e74c3c;
        }

        .unique-cancel-button:hover {
            background-color: #c0392b;
            transform: scale(1.05);
        }

        .delete-confirmation-hidden {
            display: none;
        }

        /* Modal Styles */
        .stats-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .stats-modal-content {
            position: relative;
            background-color: #fff;
            margin: 5% auto;
            padding: 0;
            width: 80%;
            max-width: 1000px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .stats-modal-header {
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: orange;
        }

        .stats-close {
            color: white;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .stats-close:hover {
            transition: all 0.5s ease;
            transform: scale;
            color: #333;
        }

        /* Centered header with icon and orange color */
        .stats-modal-title {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            /* Orange text */
            margin: 0 auto;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .stats-icon {
            margin-right: 10px;
            color: white;
            /* Orange icon */
        }

        /* Constrain modal body height and enable scroll */
        .stats-modal-body {
            padding: 20px;
            max-height: 60vh;
            /* Limit the modal body height */
            overflow-y: auto;
            /* Enable vertical scroll if content exceeds */
        }

        /* Table Styles */
        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .stats-table th,
        .stats-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        .stats-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .stats-table tr:hover {
            transition: all 0.3s ease;
            background-color: #FFEDCC;
        }

        /* Status Badge Styles */
        .status-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Class for expired stock with warnings */
        .expired-stock-warning {
            background-color: #fee2e2;
            /* Light red background */
            color: #dc2626;
            /* Darker red text */
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Class for expired stock with no issues */
        .expired-stock-none {
            background-color: #dcfce7;
            /* Light green background */
            color: #16a34a;
            /* Darker green text */
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Responsive Styles */
        @media screen and (max-width: 768px) {
            .modal-content {
                width: 95%;
                margin: 2% auto;
            }

            .stats-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }

        /* Status Badge Styles */
        .status-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-critical {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .status-low {
            background-color: #fef3c7;
            color: #d97706;
        }

        .status-medium {
            background-color: #dbeafe;
            color: #2563eb;
        }

        .status-good {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .main-content {
            overflow: hidden;
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
            <li><a onclick="" class="active"><i class="fas fa-boxes"></i> <span>Inventory</span></a></li>
            <li><a onclick="goBills()"><i class="fas fa-file-invoice-dollar"></i> <span>Bills</span></a></li>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <li><a onclick="goSystemLogs()"><i class="fas fa-book"></i> <span>Logs</span></a></li>
                <li><a onclick="goUserProfile()"><i class="fas fa-user"></i> <span>Profile</span></a></li>
            <?php endif; ?>
        </ul>
        <div class="logout">
            <a onclick="goLogout()" class="logout-button">
                <i class="fa fa-sign-out-alt"></i> <span>Logout</span>
            </a>
        </div>
    </div>
    <div class="main-content">
        <div class="header">
            <h1 class="inventory-title">
                <i class="fas fa-boxes"></i> Inventory Management
            </h1>
        </div>

        <!-- Search bar and category dropdown -->
        <div class="search-category-bar">
            <div class="search-bar-container">
                <input type="text" id="search-bar" placeholder="Search items..." onkeyup="filterItems()" />
                <i class="fa fa-search search-icon"></i>
            </div>
            <select id="category-filter" onchange="filterItems()">
                <option value="">All Categories</option>
                <!-- Dynamically populate with categories from your database -->
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
                <!-- Add more categories as needed -->
            </select>
            <button id="statsButton" class="statistics-button">
                <i class="fa fa-pie-chart statistics-icon"></i> <!-- Different Font Awesome icon for statistics -->
            </button>
        </div>

        <!-- Container for action buttons -->
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <div id="action-buttons-container" class="fixed-action-btn-container">
                <!-- Toggle Chevron Button -->
                <div id="toggle-actions-btn" class="fixed-action-btn" onclick="toggleActionsVisibility()">
                    <i id="chevron-icon" class="fas fa-chevron-down"></i>
                </div>

                <!-- Add Item Button -->
                <div id="add-item-button" class="fixed-action-btn" onclick="showAddItemModal()">
                    <i class="fas fa-plus-circle"></i>
                </div>

                <!-- Edit Item Button -->
                <div id="edit-item-button" class="fixed-action-btn" onclick="toggleEditMode()">
                    <i class="fas fa-edit"></i>
                </div>
            </div>
        <?php endif; ?>

        <div id="inventory-container">
            <!-- Inventory items will be loaded here -->
        </div>
    </div>

    <!-- Add Item Modal -->
    <div id="add-item-modal" class="modal">
        <div class="modal-content">
            <h3 class="modal-title">
                <i class="fas fa-plus-circle"></i> Add New Item
            </h3>
            <form id="add-item-form" enctype="multipart/form-data">
                <input type="text" id="item-name" name="item-name" class="modal-input" placeholder="Item Name" required>

                <!-- Category Dropdown -->
                <select id="item-category" name="item-category" class="modal-input" required>
                    <option value="" disabled selected hidden>Select Category</option>
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

                <textarea id="item-description" name="item-description" class="modal-input" placeholder="Item Description" required></textarea>
                <input type="number" step="0.01" id="item-price" name="item-price" class="modal-input" placeholder="Price" required>
                <input type="file" id="item-image" name="item-image" class="modal-input" accept="image/*" required>

                <div class="modal-buttons">
                    <button type="submit">Add Item</button>
                    <button type="button" class="close" onclick="closeAddItemModal()">Close</button>
                </div>
            </form>

            <!-- Message Area -->
            <div id="confirmation-message" style="display: none; color: green; text-align: center; margin-top: 20px;">
                <!-- Confirmation message will be displayed here -->
            </div>
            <div id="error-message" style="display: none; color: red; text-align: center; margin-top: 20px;">
                <!-- Error message will be displayed here -->
            </div>
        </div>
    </div>

    <!-- Edit Item Modal -->
    <div id="edit-item-modal" class="modal">
        <div class="modal-content">
            <h3 class="edit-modal-title">
                <i class="fas fa-edit"></i>Edit Item
            </h3>
            <form id="edit-item-form" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="edit-item-id" name="id">
                <input type="text" id="edit-item-name" name="name" placeholder="Item Name" readonly style="color: orange;">

                <!-- Category Input -->
                <select id="edit-item-category" name="category" required>
                    <option value="" disabled selected hidden>Select Category</option>
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

                <textarea id="edit-item-description" name="description" placeholder="Item Description" required></textarea>
                <input type="number" step="0.01" id="edit-item-price" name="price" placeholder="Price" required>
                <input type="file" id="edit-item-image" name="item-image" accept="image/*">
                <div class="edit-modal-buttons">
                    <button type="submit">Update Item</button>
                    <button type="button" class="close" onclick="closeEditItemModal()">Close</button>
                </div>
                <div class="modal-prompt" id="edit-modal-prompt"></div>
            </form>
        </div>
    </div>


    <!-- Delete Item Modal -->
    <div id="delete-confirmation-modal" class="delete-modal">
        <div class="delete-modal-content">
            <h3 class="delete-item-title"><i class="fas fa-exclamation-circle"></i>Confirm Deletion</h3>
            <p>Are you sure you want to delete this item and all its contents?</p>
            <p id="item-name-to-delete" style="font-weight: bold;"></p>
            <div class="delete-modal-buttons">
                <button id="confirm-delete" class="yes-button" onclick="confirmDelete()">Yes</button>
                <button id="cancel-delete" class="no-button" onclick="closeDeleteConfirmation()">No</button>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="success-modal" class="success-modal">
        <div class="success-modal-content">
            <h3>Success</h3>
            <p id="success-message">Item deleted successfully!</p>
            <button class="close-success-modal" onclick="closeSuccessModal()">Close</button>
        </div>
    </div>

    <!-- Inventory Modal -->
    <div id="inventory-modal" class="inventory-modal inventory-modal-hidden">
        <div class="inventory-modal-content">
            <h3 class="inventory-header">
                <i class="fas fa-box"></i>
                <span id="inventory-item-name">Item Inventory</span>
            </h3>

            <!-- Inventory Table -->
            <div class="table-wrapper">
                <table id="inventory-table" class="inventory-table">
                    <thead>
                        <tr style="font-size:18px;">
                            <th onclick="sortTable(0)">SKU <span class="sort-icon" id="sort-icon-0"></span></th>
                            <th onclick="sortTable(1)">Cost Price(₱) <span class="sort-icon" id="sort-icon-1"></span></th>
                            <th onclick="sortTable(2)">Date Added <span class="sort-icon" id="sort-icon-2"></span></th>
                            <th onclick="sortTable(3)">Expiration Date <span class="sort-icon" id="sort-icon-3"></span></th>
                            <th onclick="sortTable(4)">Manufacturer <span class="sort-icon" id="sort-icon-4"></span></th>
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <th>Actions</th> <!-- Actions column for Edit/Delete -->
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Rows will be dynamically populated -->
                    </tbody>
                </table>
            </div>

            <?php if ($_SESSION['role'] === 'admin'): ?>
                <div class="add-inventory-section">
                    <button class="add-inventory-button" onclick="toggleInventoryForm(selectedItemId, selectedItemName)">
                        <i class="fas fa-plus"></i> <!-- Add an icon for the button -->
                    </button>
                </div>
            <?php endif; ?>

            <!-- Add Inventory Form -->
            <div id="inventory-form" class="inventory-form hidden">
                <form id="add-inventory-form" onsubmit="addInventory(event)">
                    <input type="hidden" id="inventory-item-id" name="item_id"> <!-- Hidden item ID input -->
                    <label for="inventory-name">Item Name:</label>
                    <input type="text" id="inventory-name" name="item_name" required readonly>

                    <label for="inventory-sku">Stock Keeping Unit (SKU):</label>
                    <input type="text" id="inventory-sku" name="sku" required>

                    <label for="inventory-cost-price">Cost Price(₱):</label>
                    <input type="number" step="0.01" id="inventory-cost-price" name="cost_price" required>

                    <label for="inventory-date-added">Date Added:</label>
                    <input type="date" id="inventory-date-added" name="date_added" required>

                    <label for="inventory-expiry-date">Expiration Date:</label>
                    <input type="date" id="inventory-expiry-date" name="expiration_date" required>

                    <label for="inventory-manufacturer">Manufacturer:</label>
                    <input type="text" id="inventory-manufacturer" name="manufacturer" required>

                    <div class="modal-footer">
                        <button type="submit" class="add-inventory-submit">Add Inventory</button>
                        <button type="button" class="back-button" onclick="toggleInventoryForm(selectedItemId, selectedItemName)">Back</button>
                    </div>

                    <!-- Message placeholder for success or error -->
                    <div id="inventory-message" style="margin-top: 10px; text-align: center;"></div>
                </form>
            </div>

            <!-- New row for the close button -->
            <div class="modal-close-footer">
                <button class="close-button" onclick="closeInventoryModal()">Close</button>
            </div>
        </div>
    </div>

    <!-- Unique Delete Confirmation Modal -->
    <div id="unique-delete-confirmation-modal" class="unique-confirmation-modal delete-confirmation-hidden">
        <div class="unique-confirmation-dialog">
            <div class="confirmation-title">
                <h3>
                    <i class="fas fa-info-circle delete-info-icon"></i>
                    <span>Confirm Deletion</span>
                </h3>
            </div>
            <p class="confirmation-message">Are you sure you want to delete this inventory item?</p>
            <div class="unique-confirmation-buttons">
                <button id="unique-confirm-delete" class="modern-button unique-confirm-button">Yes</button>
                <button id="unique-cancel-delete" class="modern-button unique-cancel-button" onclick="closeUniqueDeleteConfirmationModal()">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Statistics Modal -->
    <div id="statsModal" class="stats-modal">
        <div class="stats-modal-content">
            <div class="stats-modal-header">
                <h2 class="stats-modal-title">
                    <i class="fas fa-chart-bar stats-icon"></i> Inventory Statistics Report
                </h2>
                <span class="stats-close" onclick="closeStatsModal()">&times;</span>
            </div>
            <div class="stats-modal-body">
                <table class="stats-table" id="stats-inventory-table">
                    <thead>
                        <tr>
                            <thead>
                                <tr>
                                    <th onclick="sortStocksTable(0)">Item Name <i id="stock-sort-icon-0" class="stock-sort-icon"></i></th>
                                    <th onclick="sortStocksTable(1)">Current Stock <i id="stock-sort-icon-1" class="stock-sort-icon"></i></th>
                                    <th onclick="sortStocksTable(2)">Stock Status <i id="stock-sort-icon-2" class="stock-sort-icon"></i></th>
                                    <th onclick="sortStocksTable(3)">Expired Stock <i id="stock-sort-icon-3" class="stock-sort-icon"></i></th>
                                    <?php if ($_SESSION['role'] === 'admin'): ?>
                                    <th onclick="sortStocksTable(4)">Total Cost Price <i id="stock-sort-icon-4" class="stock-sort-icon"></i></th>
                                    <th onclick="sortStocksTable(5)">Total Selling Price <i id="stock-sort-icon-5" class="stock-sort-icon"></i></th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                        </tr>
                    </thead>
                    <tbody id="inventoryData">
                        <!-- Data will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="scripts.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Load inventory items
            loadInventory();

            // Attach event listeners to existing cards
            document.addEventListener('mouseover', function(event) {
                const card = event.target.closest('.card');
                if (card) {
                    const infoText = card.querySelector('.info-text');
                    if (infoText) {
                        infoText.classList.add('visible'); // Add a class to trigger the transition
                    }
                }
            });

            document.addEventListener('mouseout', function(event) {
                const card = event.target.closest('.card');
                if (card) {
                    const infoText = card.querySelector('.info-text');
                    if (infoText) {
                        infoText.classList.remove('visible'); // Remove class to revert transition
                    }
                }
            });

            const editBtn = document.getElementById('edit-btn'); // ID of the edit button
            const cards = document.querySelectorAll('.card'); // Select all cards

            editBtn.addEventListener('click', () => {
                // Toggle edit mode on all cards
                cards.forEach(card => {
                    card.classList.toggle('edit-mode');
                });
            });
        });

        function toggleActionsVisibility() {
            const chevronIcon = document.querySelector('#toggle-actions-btn i'); // Use the i tag within the toggle-actions-btn
            const actionButtonsContainer = document.getElementById('action-buttons-container');

            if (actionButtonsContainer.classList.contains('hidden')) {
                // Show the action buttons
                actionButtonsContainer.classList.remove('hidden');
                chevronIcon.classList.remove('fa-chevron-up');
                chevronIcon.classList.add('fa-chevron-down');
            } else {
                // Hide the action buttons
                actionButtonsContainer.classList.add('hidden');
                chevronIcon.classList.remove('fa-chevron-down');
                chevronIcon.classList.add('fa-chevron-up');
            }
        }

        function loadInventory() {
            fetch('inventory_data.php')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('inventory-container');
                    container.innerHTML = '';

                    // Check if the returned data is an array
                    if (!Array.isArray(data)) {
                        console.error("Expected an array, but got:", data);
                        return;
                    }

                    data.forEach(item => {
                        const card = document.createElement('div');
                        card.className = 'card';
                        card.setAttribute('data-name', item.name.toLowerCase()); // Add data-name attribute
                        card.setAttribute('data-category', item.category); // Add data-category attribute

                        // Ensure price is a valid number
                        let price = parseFloat(item.price);
                        let formattedPrice = !isNaN(price) ? price.toFixed(2) : 'N/A';

                        // Fetch the inventory count based on the item name
                        fetch(`get_inventory_count.php?item_name=${encodeURIComponent(item.name)}`)
                            .then(response => response.json())
                            .then(inventoryData => {
                                const inventoryCount = inventoryData.count || 0; // Use the fetched count or 0 if not found

                                card.innerHTML = `
                            <img src="${item.image}" alt="${item.name}">
                            <h3>${item.name}</h3>
                            <div class="category">${item.category}</div>
                            <div class="info-text">
                                <p class="info-content">${item.description}</p>
                            </div>
                            <div class="card-footer">
                                <div class="footer-section">
                                    <i class="fas fa-cube"></i>
                                    <p class="value">${inventoryCount}</p> <!-- Display inventory count -->
                                </div>
                                <div class="footer-section">
                                    <i class="fas fa-tag"></i>
                                    <p class="value">₱${formattedPrice}</p>
                                </div>
                                <div class="footer-section info-icon">
                                    <i class="fas fa-table" onclick="showInventoryModal(${item.id}, '${item.name}')"></i>
                                </div>
                            </div>
                            <div class="edit-delete-overlay"></div>
                            <div class="actions edit-delete-icons">
                                <i class="fas fa-edit" onclick="showEditCurrentItemModal(${item.id})"></i>
                                <i class="fas fa-trash" onclick="deleteItem(${item.id}, '${item.name}')"></i>
                            </div>
                        `;

                                // Append the card to the container
                                container.appendChild(card);
                            })
                            .catch(error => {
                                console.error('Error fetching inventory count:', error);
                            });
                    });
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                });
        }


        let isEditMode = false; // Track if edit mode is active

        function toggleEditMode() {
            isEditMode = !isEditMode; // Toggle the edit mode state
            const editButton = document.getElementById('edit-item-button');

            if (isEditMode) {
                editButton.classList.add('edit-active'); // Add active class
            } else {
                editButton.classList.remove('edit-active'); // Remove active class
            }

            // Call your showEditItemModal function or other actions here
            showEditItemModal();
        }

        function showEditItemModal() {
            // Get all card elements
            const cards = document.querySelectorAll('.card');

            // Toggle the display of the edit/delete icons and overlay
            cards.forEach(card => {
                const editDeleteIcons = card.querySelector('.edit-delete-icons');
                const overlay = card.querySelector('.edit-delete-overlay'); // Select the overlay

                if (editDeleteIcons && overlay) {
                    // Toggle visibility of edit/delete icons
                    editDeleteIcons.style.display = editDeleteIcons.style.display === 'flex' ? 'none' : 'flex';

                    // Show or hide the overlay based on isEditMode
                    overlay.style.display = isEditMode ? 'block' : 'none'; // Show overlay when in edit mode
                }
            });

            // Optionally, you can also handle showing the modal for editing
            // For example:
            // document.getElementById('edit-item-modal').style.display = 'block';
        }

        function closeAddItemModal() {
            document.getElementById('add-item-modal').style.display = 'none';
        }

        function showEditCurrentItemModal(id) {
            fetch(`get_item.php?id=${id}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(item => {
                    // Populate the form fields with the current item's details
                    document.getElementById('edit-item-id').value = item.id;
                    document.getElementById('edit-item-name').value = item.name;
                    document.getElementById('edit-item-category').value = item.category; // Set category
                    document.getElementById('edit-item-description').value = item.description;
                    document.getElementById('edit-item-price').value = item.price;

                    // Show the edit item modal
                    document.getElementById('edit-item-modal').style.display = 'flex';
                })
                .catch(error => {
                    console.error('Error fetching item details:', error);
                    // Optionally, show an error message to the user
                    alert('Failed to fetch item details. Please try again.');
                });
        }


        function closeEditItemModal() {
            const prompt = document.getElementById('edit-modal-prompt');
            prompt.innerText = ''; // Clear the prompt message
            prompt.style.display = 'none'; // Hide the prompt
            loadInventory();
            toggleEditMode(); // Deactivate edit mode
            document.getElementById('edit-item-modal').style.display = 'none';
        }

        document.getElementById('add-item-form').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(this);

            fetch('add_item.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text()) // Read response as text first
                .then(text => {
                    try {
                        const result = JSON.parse(text);
                        if (result.success) {
                            showConfirmation('Item added successfully!');
                            loadInventory();
                            // Clear the form and reset file input
                            this.reset();
                            document.getElementById('item-image').value = '';
                        } else {
                            showError(result.message);
                        }
                    } catch (e) {
                        showError('Failed to parse server response.');
                    }
                })
                .catch(error => showError('An error occurred: ' + error.message));
        });

        function showConfirmation(message) {
            const confirmation = document.getElementById('confirmation-message');
            if (confirmation) {
                confirmation.textContent = message;
                confirmation.style.display = 'block';
            } else {
                console.error('Confirmation element not found');
            }
        }

        function showError(message) {
            const error = document.getElementById('error-message');
            if (error) {
                error.textContent = message;
                error.style.display = 'block';
            } else {
                console.error('Error element not found');
            }
        }

        function showAddItemModal() {
            // Reset confirmation and error messages
            document.getElementById('confirmation-message').style.display = 'none';
            document.getElementById('error-message').style.display = 'none';
            document.getElementById('add-item-modal').style.display = 'flex';
        }

        document.getElementById('edit-item-form').onsubmit = function(event) {
            event.preventDefault(); // Prevent the default form submission

            const formData = new FormData(this); // Create FormData object from the form

            // Log the formData for debugging
            for (let [key, value] of formData.entries()) {
                console.log(key, value);
            }

            fetch('edit_item.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    showEditPrompt(data.message, data.success); // Update the prompt with the message
                })
                .catch(error => {
                    console.error('Error:', error);
                    showEditPrompt('An error occurred. Please try again.', false);
                });
        };

        // Function to show prompt in the modal
        function showEditPrompt(message, success) {
            const prompt = document.getElementById('edit-modal-prompt');
            prompt.innerText = `${message}`;
            prompt.style.color = success ? 'green' : 'red';
            prompt.style.display = 'block'; // Show the prompt
        }

        let itemToDelete = null;

        function deleteItem(id, itemName) {
            itemToDelete = id; // Store the id of the item to be deleted
            document.getElementById('item-name-to-delete').textContent = itemName; // Set the item name in the modal
            document.getElementById('delete-confirmation-modal').style.display = 'block'; // Show the modal
        }

        function confirmDelete() {
            fetch('delete_item.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: itemToDelete
                    })
                })
                .then(response => response.json())
                .then(result => {
                    closeDeleteConfirmation(); // Close the delete confirmation modal

                    if (result.success) {
                        document.getElementById('success-message').textContent = result.message;
                        document.getElementById('success-modal').style.display = 'block'; // Show the success modal
                        loadInventory(); // Refresh inventory
                    } else {
                        alert(result.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
        }

        function closeSuccessModal() {
            loadInventory();
            toggleEditMode();
            document.getElementById('success-modal').style.display = 'none'; // Hide the success modal
        }

        function closeDeleteConfirmation() {
            document.getElementById('delete-confirmation-modal').style.display = 'none'; // Hide the modal
        }

        // Call filterItems function on input change
        document.getElementById('search-bar').addEventListener('input', filterItems);
        document.getElementById('category-filter').addEventListener('change', filterItems);

        function filterItems() {
            const searchValue = document.getElementById('search-bar').value.toLowerCase();
            const categoryValue = document.getElementById('category-filter').value;
            const cards = document.querySelectorAll('.card');

            cards.forEach(card => {
                const itemName = card.getAttribute('data-name').toLowerCase();
                const itemCategory = card.getAttribute('data-category');

                // Check if the item matches the search query and category filter
                const matchesSearch = itemName.includes(searchValue);
                const matchesCategory = categoryValue === "" || itemCategory === categoryValue;

                // Show or hide the card based on the matching conditions
                if (matchesSearch && matchesCategory) {
                    card.classList.remove('search-hidden'); // Show card
                } else {
                    card.classList.add('search-hidden'); // Hide card
                }
            });
        }

        // Function to show the modal
        let selectedItemId; // Declare a global variable to hold the selected item ID
        let selectedItemName; // Declare a global variable to hold the selected item name

        function showInventoryModal(itemId, itemName) {
            const modal = document.getElementById('inventory-modal');
            const inventoryTableBody = document.getElementById('inventory-table').querySelector('tbody');

            inventoryTableBody.innerHTML = ''; // Clear previous rows

            // Set global variables for the selected item ID and name
            selectedItemId = itemId;
            selectedItemName = itemName;

            // Fetch inventory data based on item name
            fetch(`get_item_inventory.php?item_name=${encodeURIComponent(itemName)}`)
                .then(response => response.json())
                .then(data => {
                    if (data && Array.isArray(data) && data.length > 0) {
                        data.forEach(item => {
                            const row = document.createElement('tr');

                            // Parse expiration date and compare with the current date
                            const expirationDate = new Date(item.expiration_date);
                            const currentDate = new Date();
                            let expirationClass = '';

                            // Reset the time components to midnight for accurate date comparison
                            currentDate.setHours(0, 0, 0, 0);
                            expirationDate.setHours(0, 0, 0, 0);

                            // Calculate the difference in days
                            const diffTime = expirationDate - currentDate; // Difference in milliseconds
                            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); // Convert to days

                            // Apply styles based on whether the expiration date is in the future or past
                            if (diffDays > 7) {
                                expirationClass = 'green-text'; // Future expiration
                            } else if (diffDays === 0) {
                                expirationClass = 'red-text'; // Today
                            } else if (diffDays > 0 && diffDays <= 7) {
                                expirationClass = 'orange-text'; // 1-7 days before expiration
                            } else {
                                expirationClass = 'red-text'; // Expired
                            }


                            row.innerHTML = `
    <td>${item.sku || 'N/A'}</td>
    <td>${item.cost_price ? parseFloat(item.cost_price).toFixed(2) : 'N/A'}</td>
    <td>${item.date_added || 'N/A'}</td>
    <td class="${expirationClass}">${item.expiration_date || 'N/A'}</td>
    <td>${item.manufacturer || 'N/A'}</td>
    <td>
        <button class="modern-button edit-button" onclick="editInventory('${item.sku}')">
            <i class="fas fa-pen"></i> Edit
        </button>
        <button class="modern-button delete-button" onclick="deleteInventory('${item.sku}')">
            <i class="fas fa-trash"></i> Delete
        </button>
    </td>
`;
                            inventoryTableBody.appendChild(row);
                        });
                    } else {
                        const row = document.createElement('tr');
                        row.innerHTML = `<td colspan="6">No inventory found for this item.</td>`;
                        inventoryTableBody.appendChild(row);
                    }
                })
                .catch(error => {
                    console.error('Error fetching inventory data:', error);
                });

            // Display the item name in the modal header
            document.getElementById('inventory-item-name').textContent = itemName;

            // Show the modal
            modal.classList.remove('inventory-modal-hidden');
        }

        function editInventory(sku) {
            // Fetch the current inventory item using SKU and populate the form for editing
            fetch(`get_item_by_sku.php?sku=${encodeURIComponent(sku)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(item => {
                    if (item) {
                        // Populate form fields for editing
                        document.getElementById('inventory-sku').value = item.sku; // Ensure this is treated as a string
                        document.getElementById('inventory-cost-price').value = item.cost_price;
                        document.getElementById('inventory-date-added').value = item.date_added;
                        document.getElementById('inventory-expiry-date').value = item.expiration_date;
                        document.getElementById('inventory-manufacturer').value = item.manufacturer;

                        // Set the editing state using the correct SKU
                        const rowIndex = getRowIndexBySku(item.sku); // Use item.sku here
                        console.log(`Editing row index: ${rowIndex}`); // Debugging log
                        document.getElementById('add-inventory-form').setAttribute('data-editing-row', rowIndex);

                        // Show the form to edit the inventory
                        toggleInventoryForm(selectedItemId, selectedItemName);
                    }
                })
                .catch(error => console.error('Error fetching inventory item for editing:', error));
        }

        // Function to find the row index by SKU - you need to implement this logic
        function getRowIndexBySku(sku) {
            const inventoryTable = document.getElementById('inventory-table').querySelector('tbody');
            for (let i = 0; i < inventoryTable.rows.length; i++) {
                const rowSku = inventoryTable.rows[i].cells[0].textContent.trim(); // Assuming SKU is in the first cell
                // Log the SKU comparison for debugging
                console.log(`Comparing row SKU: "${rowSku}" with SKU: "${sku}"`);
                if (rowSku === sku) {
                    return i; // Return the index of the row where SKU matches
                }
            }
            return -1; // Return -1 if SKU not found
        }

        function deleteInventory(sku) {
            // Show the unique delete confirmation modal
            const modal = document.getElementById('unique-delete-confirmation-modal');
            modal.classList.remove('delete-confirmation-hidden'); // Show the modal

            // Set the confirm button to call the delete function with the specific SKU
            document.getElementById('unique-confirm-delete').onclick = function() {
                // Change to POST request
                fetch('delete_item_inventory.php', {
                        method: 'POST', // Ensure this is POST
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded' // Set content type
                        },
                        body: `sku=${encodeURIComponent(sku)}` // Pass the SKU as form data
                    })
                    .then(response => response.text())
                    .then(result => {
                        if (result.includes('success')) {
                            // Refresh inventory table after deletion
                            showInventoryModal(selectedItemId, selectedItemName);
                        } else {
                            alert('Failed to delete inventory item: ' + result); // Include the error message
                        }
                        // Hide the modal after the action
                        closeUniqueDeleteConfirmationModal();
                    })
                    .catch(error => console.error('Error deleting inventory item:', error));
            };
        }


        // Function to close the unique delete confirmation modal
        function closeUniqueDeleteConfirmationModal() {
            const modal = document.getElementById('unique-delete-confirmation-modal');
            modal.classList.add('delete-confirmation-hidden'); // Hide the modal
        }


        function closeDeleteConfirmationModal() {
            const modal = document.getElementById('delete-confirmation-modal');
            modal.classList.add('hidden'); // Hide the modal
        }

        function closeInventoryModal() {
            loadInventory();
            resetSortIcons();
            const modal = document.getElementById('inventory-modal');
            modal.classList.add('inventory-modal-hidden'); // Hide the modal
        }

        function toggleInventoryForm(itemId, itemName) {
            const form = document.getElementById('inventory-form'); // Select the form by its ID
            const addInventoryButton = document.querySelector('.add-inventory-button'); // Select the add inventory button
            const inventoryTable = document.getElementById('inventory-table'); // Select the inventory table
            const closeButton = document.querySelector('.close-button'); // Select the close button
            const itemNameInput = document.getElementById('inventory-name'); // Select the item name input
            const skuInput = document.getElementById('inventory-sku'); // Select the SKU input
            const messageElement = document.getElementById('inventory-message'); // Select the message element
            const submitButton = document.querySelector('.add-inventory-submit'); // Select the submit button

            // Toggle the visibility of the form and the table
            if (form.classList.contains('hidden')) {
                // Show the form and hide the table
                form.classList.remove('hidden'); // Show the form
                inventoryTable.style.display = 'none'; // Hide the inventory table
                closeButton.style.display = 'none'; // Hide the close button
                addInventoryButton.style.display = 'none'; // Hide the add inventory button

                // Set the item name and ID in the form
                document.getElementById('inventory-item-id').value = itemId; // Set the current item ID
                itemNameInput.value = itemName; // Set the item name

                // Check if we are in editing mode
                const isEditing = document.getElementById('add-inventory-form').getAttribute('data-editing-row');
                if (isEditing) {
                    // Change the button text to 'Edit Inventory' when in edit mode
                    submitButton.textContent = 'Edit Inventory';
                    skuInput.readOnly = true; // Make SKU not editable
                } else {
                    // Change the button text to 'Add Inventory' when not in edit mode
                    submitButton.textContent = 'Add Inventory';
                    skuInput.readOnly = false; // Make SKU editable
                }
            } else {
                // Hide the form and show the table
                form.classList.add('hidden'); // Hide the form
                inventoryTable.style.display = ''; // Show the inventory table
                closeButton.style.display = 'block'; // Show the close button
                addInventoryButton.style.display = 'block'; // Show the add inventory button

                // Clear form fields
                skuInput.value = ''; // Clear SKU field
                document.getElementById('inventory-cost-price').value = ''; // Clear cost price field
                document.getElementById('inventory-date-added').value = ''; // Clear date added field
                document.getElementById('inventory-expiry-date').value = ''; // Clear expiration date field
                document.getElementById('inventory-manufacturer').value = ''; // Clear manufacturer field
                messageElement.textContent = ''; // Clear confirmation message

                // Reset the isEditing state
                document.getElementById('add-inventory-form').removeAttribute('data-editing-row');

                // Reset button text back to 'Add Inventory'
                submitButton.textContent = 'Add Inventory';

                // Reload the inventory table
                showInventoryModal(itemId, itemName); // Reload the table with correct item details
            }
        }


        function resetSortIcons() {
            const icons = document.querySelectorAll('.sort-icon');
            icons.forEach(icon => {
                icon.className = 'sort-icon'; // Reset icon class
            });
        }

        function addInventory(event) {
            event.preventDefault(); // Prevent form from submitting normally

            const formData = new FormData(document.getElementById('add-inventory-form'));
            const itemId = document.getElementById('inventory-item-id').value; // Get the item ID
            const messageDiv = document.getElementById('inventory-message');
            const isEditing = document.getElementById('add-inventory-form').getAttribute('data-editing-row');

            console.log('isEditing:', isEditing); // Debug log

            // Check if we're editing an existing inventory entry or adding a new one
            if (isEditing !== null) {
                // Edit mode: Update the selected row in the table
                const rowIndex = parseInt(isEditing, 10); // Get the row index
                const inventoryTable = document.getElementById('inventory-table').querySelector('tbody');
                const row = inventoryTable.rows[rowIndex]; // Get the selected row

                // Update the row cells with the form data
                row.cells[0].textContent = document.getElementById('inventory-sku').value;
                row.cells[1].textContent = parseFloat(document.getElementById('inventory-cost-price').value).toFixed(2);
                row.cells[2].textContent = document.getElementById('inventory-date-added').value;
                row.cells[3].textContent = document.getElementById('inventory-expiry-date').value;
                row.cells[4].textContent = document.getElementById('inventory-manufacturer').value;

                // Update the server with the edited inventory
                fetch('update_item_inventory.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(result => {
                        if (result.includes('success')) {
                            messageDiv.textContent = "Inventory updated successfully!";
                            messageDiv.style.color = "green";
                        } else {
                            messageDiv.textContent = result;
                            messageDiv.style.color = "red";
                        }
                    })
                    .catch(error => {
                        messageDiv.textContent = "Error updating inventory.";
                        messageDiv.style.color = "red";
                    });

                // Clear editing state
                document.getElementById('add-inventory-form').removeAttribute('data-editing-row');

            } else {
                // Add mode: Add new inventory
                fetch('add_item_inventory.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(result => {
                        if (result.includes('success')) {
                            messageDiv.textContent = "Inventory added successfully!";
                            messageDiv.style.color = "green";

                            // Append the new inventory row to the table dynamically
                            const inventoryTable = document.getElementById('inventory-table').querySelector('tbody');
                            const newRow = document.createElement('tr');
                            newRow.innerHTML = `
                    <td>${document.getElementById('inventory-sku').value}</td>
                    <td>${parseFloat(document.getElementById('inventory-cost-price').value).toFixed(2)}</td>
                    <td>${document.getElementById('inventory-date-added').value}</td>
                    <td>${document.getElementById('inventory-expiry-date').value}</td>
                    <td>${document.getElementById('inventory-manufacturer').value}</td>
                    <td><button class="edit-inventory-button" onclick="editInventoryRow(this)">Edit</button></td>
                `;
                            inventoryTable.appendChild(newRow);
                        } else {
                            messageDiv.textContent = result;
                            messageDiv.style.color = "red";
                        }
                    })
                    .catch(error => {
                        messageDiv.textContent = "Error submitting the form.";
                        messageDiv.style.color = "red";
                    });
            }

            // Clear specific fields, but not the item name
            document.getElementById('inventory-sku').value = '';
            document.getElementById('inventory-cost-price').value = '';
            document.getElementById('inventory-date-added').value = '';
            document.getElementById('inventory-expiry-date').value = '';
            document.getElementById('inventory-manufacturer').value = '';

            // Clear any messages after some delay (optional)
            setTimeout(() => {
                messageDiv.textContent = '';
            }, 3000);
        }

        let sortOrder = true; // true for ascending, false for descending
        let lastSortedColumn = -1; // To track the last sorted column

        function sortTable(columnIndex) {
            const table = document.getElementById('inventory-table');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr')); // Get all rows in the tbody

            // Determine the data type for sorting
            const isNumeric = (columnIndex === 1); // Assuming 'Cost Price' is the numeric column

            rows.sort((a, b) => {
                const cellA = a.children[columnIndex].textContent;
                const cellB = b.children[columnIndex].textContent;

                let comparison = 0;

                if (isNumeric) {
                    comparison = parseFloat(cellA) - parseFloat(cellB); // Numeric comparison
                } else {
                    comparison = cellA.localeCompare(cellB); // String comparison
                }

                return sortOrder ? comparison : -comparison; // Return based on sortOrder
            });

            // Clear the existing rows and append the sorted rows
            tbody.innerHTML = '';
            rows.forEach(row => tbody.appendChild(row));

            // Manage sort icons
            updateSortIcons(columnIndex);

            // Toggle the sort order for next sort
            sortOrder = !sortOrder;
        }

        function updateSortIcons(columnIndex) {
            // Reset all icons
            const icons = document.querySelectorAll('.sort-icon');
            icons.forEach(icon => {
                icon.className = 'sort-icon'; // Reset icon class
            });

            // Set the current icon for the sorted column
            const currentIcon = document.getElementById(`sort-icon-${columnIndex}`);
            if (sortOrder) {
                currentIcon.classList.add('fa', 'fa-sort-up'); // Ascending icon
            } else {
                currentIcon.classList.add('fa', 'fa-sort-down'); // Descending icon
            }

            // Remove icons from the last sorted column
            if (lastSortedColumn !== -1 && lastSortedColumn !== columnIndex) {
                const lastIcon = document.getElementById(`sort-icon-${lastSortedColumn}`);
                lastIcon.className = 'sort-icon'; // Reset last sorted column icon
            }

            // Update last sorted column
            lastSortedColumn = columnIndex;
        }

        // Get modal elements
        const modal = document.getElementById('statsModal');
        const btn = document.getElementById('statsButton');
        const span = document.getElementsByClassName('stats-close')[0];

        // Open modal
        btn.onclick = function() {
            modal.style.display = "block";
            fetchInventoryData();
        };

        // Close modal
        span.onclick = function() {
            modal.style.display = "none";
        };

        // Click outside modal to close
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        };

        // Fetch inventory data
        function fetchInventoryData() {
            fetch('get_inventory.php')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('inventoryData');
                    tbody.innerHTML = '';

                    data.forEach(item => {
                        const row = document.createElement('tr');

                        // Ensure we use the correct properties from the fetched data
                        const totalCostPrice = item.total_cost_price || 0; // Use total_cost_price from the response, default to 0
                        const totalPrice = item.total_price || 0; // Use total_price from the response, default to 0

                        // Add the onclick event to open the inventory modal and close the stats modal
                        row.onclick = function() {
                            closeStatsModal(); // Close the stats modal
                            showInventoryModal(item.id, item.item_name); // Open the inventory modal with item details
                        };

                        const expiredStock = item.expired_stock > 0 ? item.expired_stock : 'None';
                        const expiredClass = item.expired_stock > 0 ? 'expired-stock-warning' : 'expired-stock-none';
                        const warningIcon = item.expired_stock > 0 ? '⚠️' : ''; // Only show icon if expired stock exists

                        // Add `status-badge` class to Total Cost Price and Total Price, using appropriate colors
                        row.innerHTML = `
                    <td>${item.item_name}</td>
                    <td><span class="status-badge ${getStatusClass(item.status)}">${item.quantity}</span></td>
                    <td><span class="status-badge ${getStatusClass(item.status)}">${item.status}</span></td>
                    <td><span class="${expiredClass}">${expiredStock} ${warningIcon}</span></td>
                    <td><span class="status-badge">${totalCostPrice.toFixed(2)}</span></td>
                    <td><span class="status-badge">${totalPrice.toFixed(2)}</span></td>
                `;
                        tbody.appendChild(row);
                    });
                })
                .catch(error => {
                    console.error('Error fetching inventory data:', error);
                });
        }


        // Close the stats modal
        function closeStatsModal() {
            const statsModal = document.getElementById('statsModal');
            statsModal.style.display = "none"; // Hide the stats modal
        }

        // Function to get CSS class based on stock status
        function getStatusClass(status) {
            switch (status) {
                case 'Out of Stock':
                    return 'status-critical';
                case 'Low Stock':
                    return 'status-low';
                case 'Medium Stock':
                    return 'status-medium';
                case 'Well Stocked':
                    return 'status-good';
                default:
                    return '';
            }
        }

        let sortStockOrder = true; // true for ascending, false for descending
        let lastStockSortedColumn = -1; // Keep track of the last sorted column

        // Sort table by column index
        function sortStocksTable(columnIndex) {
            const table = document.getElementById('stats-inventory-table');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr')); // Get all rows in the tbody

            // Check if rows are empty
            if (rows.length === 0) {
                console.warn('No rows to sort');
                return;
            }

            // Determine the data type for sorting
            // Assuming:
            // - Column 1 is Quantity (numeric)
            // - Column 4 is Total Cost Price (numeric)
            // - Column 5 is Total Price (numeric)
            const numericColumns = [1, 4, 5]; // Add indices of numeric columns
            const isNumeric = numericColumns.includes(columnIndex);

            rows.sort((a, b) => {
                const cellA = a.children[columnIndex].textContent.trim();
                const cellB = b.children[columnIndex].textContent.trim();

                let comparison = 0;

                if (isNumeric) {
                    // Parse to float for numeric comparison
                    const numA = parseFloat(cellA.replace(/[^0-9.-]+/g, '')); // Remove peso sign and parse to float
                    const numB = parseFloat(cellB.replace(/[^0-9.-]+/g, '')); // Remove peso sign and parse to float
                    comparison = numA - numB; // Numeric comparison
                } else {
                    // String comparison
                    comparison = cellA.localeCompare(cellB);
                }

                return sortStockOrder ? comparison : -comparison; // Return based on sortOrder
            });

            // Clear the existing rows and append the sorted rows
            tbody.innerHTML = ''; // Clear existing rows
            rows.forEach(row => tbody.appendChild(row)); // Append sorted rows

            // Manage sort icons
            updateStocksSortIcons(columnIndex);

            // Toggle the sort order for next sort
            sortStockOrder = !sortStockOrder;
        }


        // Update stock sort icons
        function updateStocksSortIcons(columnIndex) {
            // Reset all icons
            const icons = document.querySelectorAll('.stock-sort-icon');
            icons.forEach(icon => {
                icon.className = 'stock-sort-icon'; // Reset icon class
            });

            // Set the current icon for the sorted column
            const currentIcon = document.getElementById(`stock-sort-icon-${columnIndex}`);
            if (sortStockOrder) {
                currentIcon.classList.add('fa', 'fa-sort-up'); // Ascending icon
            } else {
                currentIcon.classList.add('fa', 'fa-sort-down'); // Descending icon
            }

            // Remove icons from the last sorted column
            if (lastStockSortedColumn !== -1 && lastStockSortedColumn !== columnIndex) {
                const lastIcon = document.getElementById(`stock-sort-icon-${lastStockSortedColumn}`);
                lastIcon.className = 'stock-sort-icon'; // Reset last sorted column icon
            }

            // Update last sorted column
            lastStockSortedColumn = columnIndex;
        }
    </script>
</body>

</html>