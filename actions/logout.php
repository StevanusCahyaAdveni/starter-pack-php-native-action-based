<?php

/**
 * Action: Logout
 * Handle user logout
 */

session_start();

// Hapus semua session
session_unset();
session_destroy();

// Hapus cookie remember me jika ada
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

// Redirect ke login
header('Location: ../login.php');
exit;
