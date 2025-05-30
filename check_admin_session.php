<?php
session_start();

// Function to clear admin session
function clearAdminSession() {
    // Clear admin-specific session variables
    unset($_SESSION['admin']);
    unset($_SESSION['admin_username']);
    unset($_SESSION['admin_login_time']);
    
    // Don't destroy the entire session as it might contain customer data
    // Just remove admin privileges
    
    // Redirect to customer dashboard
    header("Location: ../../views/MainDashboard.php");
    exit;
}

// Check if admin session exists
if (!isset($_SESSION['admin'])) {
    clearAdminSession();
}

// Check if session cookie exists
if (!isset($_COOKIE[session_name()])) {
    clearAdminSession();
}

?>
