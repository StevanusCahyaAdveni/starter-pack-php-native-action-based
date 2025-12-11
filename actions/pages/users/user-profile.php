<?php
/**
 * Action: users/user-profile
 * Created: 2025-12-11 10:10:51
 */

session_start();
include '../../functions/sanitasi.php';
include '../../config.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    // $data = sani($_POST['field_name']);
    
    // Process your logic here
    
    // Redirect back with message
    $_SESSION['message'] = 'Action completed successfully!';
    $_SESSION['message_type'] = 'success';
    
    header('Location: ../../index.php?hal=users_user-profile');
    exit;
} else {
    // If accessed directly, redirect to homepage
    header('Location: ../../index.php');
    exit;
}