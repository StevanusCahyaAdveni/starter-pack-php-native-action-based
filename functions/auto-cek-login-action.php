<?php 
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    // Try auto-login if credentials in POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['auto_login'])) {
        $email = sani($_POST['email']);
        $password = sani($_POST['password']);

        if (!empty($email) && !empty($password)) {
            $result = querySecure(
                $con,
                "SELECT * FROM users WHERE email = ?",
                [$email],
                's'
            );

            if ($result && mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_assoc($result);

                if (password_verify($password, $user['password'])) {
                    // Set session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_fullname'] = $user['fullname'];
                    $_SESSION['user_username'] = $user['username'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_photo'] = $user['photo_profile'];
                    $_SESSION['is_logged_in'] = true;
                    $_SESSION['admin'] = $user;
                    // Continue to load page
                } else {
                    exit; // Password salah
                }
            } else {
                exit; // User tidak ditemukan
            }
        } else {
            exit; // Input tidak lengkap
        }
    } else {
        exit; // Tidak ada session dan tidak ada auto-login
    }
}
?>