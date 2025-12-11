<?php

/**
 * Database Configuration
 * File ini berisi konfigurasi database dan koneksi yang aman
 */

// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'php_native_action');

// Inisialisasi koneksi database
$con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if (!$con) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset untuk keamanan (mencegah SQL injection via charset)
mysqli_set_charset($con, "utf8mb4");

// Set timezone (sesuaikan dengan timezone Anda)
date_default_timezone_set('Asia/Jakarta');