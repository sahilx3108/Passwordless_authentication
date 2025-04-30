<?php
session_start();

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Verify the token matches the one stored in session
    if (isset($_SESSION['magic_link_token']) && $token === $_SESSION['magic_link_token']) {
        // Token is valid, log the user in
        $_SESSION['logged_in'] = true;
        $_SESSION['email'] = $_SESSION['magic_link_email'];
        
        // Clear the magic link token
        unset($_SESSION['magic_link_token']);
        
        // Redirect to dashboard or home page
        header("Location: dashboard.php");
        exit();
    } else {
        // Invalid or expired token
        echo "Invalid or expired magic link. Please request a new one.";
    }
} else {
    // No token provided
    echo "Invalid magic link.";
}
?> 
