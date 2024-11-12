function navigateTo(url) {
    const sidebar = document.querySelector('.sidebar');

    // Check if the sidebar is currently minimized
    if (!sidebar.classList.contains('minimized')) {
        // Sidebar is maximized, so add the minimizing class and delay the navigation
        sidebar.classList.add('minimizing'); // Trigger transition effect
        
        // Wait for the transition to complete
        setTimeout(() => {
            sidebar.classList.add('minimized'); // Apply minimized state
            sidebar.classList.remove('minimizing'); // Remove minimizing class
            
            // Navigate after the transition is complete
            setTimeout(() => {
                window.location.href = url;
            }, 300); // Short delay to ensure transition is visible before navigation
        }, 50); // Delay matches the CSS transition duration
    } else {
        // Sidebar is already minimized, navigate immediately
        window.location.href = url;
    }
}

function goAppointment() {
    navigateTo("appointments.php");
}

function goBills() {
    navigateTo("bills.php");
}

function goDashboard() {
    navigateTo("dashboard.php");
}

function goInventory() {
    navigateTo("inventory.php");
}

function goPatientRecords() {
    navigateTo("patient_records.php");
}

function goLogout() {
    navigateTo("login.php");
}

function goUserProfile() {
    navigateTo("profile.php");
}

function goSystemLogs() {
    navigateTo("system_logs.php");
}

// Toggle Sidebar
function adjustHeader() {
    const sidebar = document.querySelector('.sidebar');
    const header = document.querySelector('#header');
    const sidebarWidth = sidebar.classList.contains('minimized') ? '80px' : '250px';

    header.style.left = sidebarWidth;
    header.style.width = `calc(100% - ${sidebarWidth})`;
}

// Call this function when the sidebar is toggled
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const toggleIcon = document.querySelector('.toggle-btn i');

    if (sidebar.classList.contains('minimized')) {
        sidebar.classList.remove('minimized');
        toggleIcon.classList.remove('fa-chevron-right');
        toggleIcon.classList.add('fa-chevron-left');
        localStorage.setItem('sidebarState', 'maximized');
    } else {
        sidebar.classList.add('minimized');
        toggleIcon.classList.remove('fa-chevron-left');
        toggleIcon.classList.add('fa-chevron-right');
        localStorage.setItem('sidebarState', 'minimized');
    }

    adjustHeader();
}

// Initialize header adjustment on page load
window.addEventListener('load', adjustHeader);

// Update Sidebar Icon and State on Page Load
function updateSidebarIcon() {
    const sidebar = document.querySelector('.sidebar');
    const toggleIcon = document.querySelector('.toggle-btn i');
    const savedState = localStorage.getItem('sidebarState');

    if (savedState === 'minimized') {
        sidebar.classList.add('minimized');
        toggleIcon.classList.remove('fa-chevron-left');
        toggleIcon.classList.add('fa-chevron-right');
    } else {
        sidebar.classList.remove('minimized');
        toggleIcon.classList.remove('fa-chevron-right');
        toggleIcon.classList.add('fa-chevron-left');
    }
}

// Ensure Sidebar State is Set Correctly on Page Load
window.addEventListener('load', function() {
    // Ensure sidebar is minimized when the page loads
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.add('minimized');
    localStorage.setItem('sidebarState', 'minimized'); // Update the saved state

    // Update the toggle icon based on the saved state
    updateSidebarIcon();

    // Check for fade-in transition
    if (localStorage.getItem("fade-in") === "true") {
        document.body.classList.add("fade-in");
        localStorage.setItem("fade-in", "false"); // Reset the flag
    }
});

// Show the Add Modal
// scripts.js

// Show the Add Modal with slide-in effect
function openAddModal() {
    const modal = document.getElementById('addModal');
    modal.classList.add('show'); // Add 'show' class to make it visible and slide in
}

// Close the Add Modal with slide-out effect
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.remove('show'); // Remove 'show' class to slide it out
}


// Handle form submission for adding items
document.getElementById('submitAddItem').addEventListener('click', function(event) {
    event.preventDefault(); // Prevent default form submission
    
    const formData = new FormData();
    formData.append('itemName', document.getElementById('itemName').value);
    formData.append('itemDescription', document.getElementById('itemDescription').value);
    formData.append('itemQuantity', document.getElementById('itemQuantity').value);
    formData.append('itemPrice', document.getElementById('itemPrice').value);
    formData.append('itemImage', document.getElementById('itemImage').files[0]);
    formData.append('add_item', true); // Add this to identify the action

    fetch('inventory.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Item added successfully');
            closeModal('addModal');
            location.reload(); // Reload page to show the updated inventory
        } else {
            alert('Failed to add item');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
});



document.getElementById('editButton').addEventListener('click', function() {
    document.getElementById('editItemModal').style.display = 'block';
});

function showDescription(id) {
    var description = document.getElementById('description-' + id);
    description.style.display = description.style.display === 'none' ? 'block' : 'none';
}

function deleteItem() {
    var id = document.getElementById('edit-id').value;
    if (confirm('Are you sure you want to delete this item?')) {
        document.querySelector('#editItemModal form').action = 'inventory.php';
        document.querySelector('#editItemModal form').method = 'POST';
        document.querySelector('#editItemModal form').innerHTML += '<input type="hidden" name="delete_item" value="1">';
        document.querySelector('#editItemModal form').submit();
    }
}