<?php
/**
 * Action: Auto Login API
 * Handle auto-login from localStorage credentials
 * Returns JSON response
 */

session_start();
header('Content-Type: application/json');

include '../functions/sanitasi.php';
include '../functions/secure_query.php';
include '../config.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON data'
    ]);
    exit;
}

// Sanitasi input
$email = isset($input['email']) ? sani($input['email']) : '';
$password = isset($input['password']) ? sani($input['password']) : '';

// Validasi input kosong
if (empty($email) || empty($password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Email dan password harus diisi',
        'clear_storage' => true
    ]);
    exit;
}

// Validasi format email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Format email tidak valid',
        'clear_storage' => true
    ]);
    exit;
}

// Query user berdasarkan email
$result = querySecure(
    $con,
    "SELECT * FROM users WHERE email = ?",
    [$email],
    's'
);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);

    // Verifikasi password
    if (password_verify($password, $user['password'])) {

        // Set session user
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_fullname'] = $user['fullname'];
        $_SESSION['user_username'] = $user['username'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_photo'] = $user['photo_profile'];
        $_SESSION['is_logged_in'] = true;
        $_SESSION['admin'] = $user;

        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Login berhasil! Selamat datang ' . $user['fullname'],
            'user' => [
                'id' => $user['id'],
                'fullname' => $user['fullname'],
                'email' => $user['email']
            ]
        ]);
        exit;

    } else {
        // Password salah - clear localStorage
        echo json_encode([
            'success' => false,
            'message' => 'Email atau password salah',
            'clear_storage' => true
        ]);
        exit;
    }
} else {
    // User tidak ditemukan - clear localStorage
    echo json_encode([
        'success' => false,
        'message' => 'Email atau password salah',
        'clear_storage' => true
    ]);
    exit;
}
?>