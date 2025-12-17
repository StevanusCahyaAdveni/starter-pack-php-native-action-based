<?php

/**
 * Action: Login Authentication
 * Handle user login with email and password
 */

session_start();
include '../functions/sanitasi.php';
include '../functions/secure_query.php';
include '../config.php';

// Cek apakah form di-submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitasi input
    $email = sani($_POST['email']);
    $password = sani($_POST['password']);
    $remember = isset($_POST['remember']) ? true : false;

    // Validasi input kosong
    if (empty($email) || empty($password)) {
        $_SESSION['message'] = 'Email dan password harus diisi!';
        $_SESSION['message_type'] = 'error';
        header('Location: ../login.php');
        exit;
    }

    // Validasi format email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = 'Format email tidak valid!';
        $_SESSION['message_type'] = 'error';
        header('Location: ../login.php');
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

            // Set data untuk localStorage (akan di-handle via JavaScript)
            if ($remember) {
                $_SESSION['remember_email'] = $email;
                $_SESSION['remember_password'] = $password; // Untuk localStorage, bukan password hash
            } else {
                $_SESSION['clear_remember'] = true;
            }

            // Redirect ke dashboard
            $_SESSION['message'] = 'Login berhasil! Selamat datang ' . $user['fullname'];
            $_SESSION['message_type'] = 'success';
            header('Location: ../index.php');
            exit;
        } else {
            // Password salah
            $_SESSION['message'] = 'Email atau password salah!';
            $_SESSION['message_type'] = 'error';
            header('Location: ../login.php');
            exit;
        }
    } else {
        // User tidak ditemukan
        $_SESSION['message'] = 'Email atau password salah!';
        $_SESSION['message_type'] = 'error';
        header('Location: ../login.php');
        exit;
    }
} else {
    // Jika diakses langsung tanpa POST, redirect ke login
    header('Location: ../login.php');
    exit;
}
