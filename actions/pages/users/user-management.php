<?php

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['addUser'])) {
        include '../functions/upload_file.php';
        $id = generate_uuid();
        $fullname = sani($_POST['fullname']);
        $username = sani($_POST['username']);
        $email = sani($_POST['email']);
        $password = sani($_POST['password']);
        // $photo_profile = $_FILES['photo_profile'] ?? null;

        $result = uploadFile($_FILES['photo_profile'], '../assets/images/photo_profile/', 5 * 1024 * 1024);

        if ($result['success']) {
            echo "Upload berhasil: " . $result['file_path'];
            $photo_profile = str_replace('../', '', $result['file_path']);
        } else {
            echo "Upload gagal: " . $result['message'];
        }

        $query = "INSERT INTO users (id, fullname, username, email, password, photo_profile) VALUES (?, ?, ?, ?, ?, ?)";
        $params = [$id, $fullname, $username, $email, password_hash($password, PASSWORD_DEFAULT), $photo_profile];
        $types = 'ssssss';
        $insertResult = executeSecure($con, $query, $params, $types);

        $_SESSION['message'] = 'Data berhasil ditambahkan!';
        $_SESSION['message_type'] = 'success';
        createLog($con, $_SESSION['admin']['email'], 'Successful user addition '.$fullname);

        echo "
            <script>
                window.location.href = '../?hal=users_user-management';
            </script>
        ";
    }

    if (isset($_POST['updateUser'])) {
        include '../functions/upload_file.php';
        $id = sani($_POST['id']);
        $fullname = sani($_POST['fullname']);
        $username = sani($_POST['username']);
        $email = sani($_POST['email']);
        $password = sani($_POST['password']);
        $password_old = sani($_POST['password_old']);

        $resultGetSingleUser = querySecure($con, "SELECT photo_profile FROM users WHERE id = ?", [$id], 's');
        $singleUser = mysqli_fetch_assoc($resultGetSingleUser);

        $photo_profile_old = $singleUser['photo_profile'];

        // Default: gunakan foto lama
        $photo_profile = $photo_profile_old;

        // Cek apakah ada file yang diupload
        if (isset($_FILES['photo_profile']) && $_FILES['photo_profile']['error'] === UPLOAD_ERR_OK && !empty($_FILES['photo_profile']['name']) && $_FILES['photo_profile']['size'] > 0 && isset($_FILES['photo_profile']['name'])) {
            $result = uploadFile($_FILES['photo_profile'], '../assets/images/photo_profile/', 5 * 1024 * 1024);

            if ($result['success']) {
                // Hapus foto lama jika ada dan berbeda
                if (!empty($photo_profile_old) && file_exists('../' . $photo_profile_old)) {
                    unlink('../' . $photo_profile_old);
                }
                $photo_profile = str_replace('../', '', $result['file_path']);
            } else {
                $_SESSION['message'] = 'Upload gagal: ' . $result['message'];
                $_SESSION['message_type'] = 'error';
            }
        }
        $photo_profile == 'undefined' ? $photo_profile = $photo_profile_old : '';

        // Handle password
        if (!empty($password) && $password != '') {
            $password_hashed = password_hash($password, PASSWORD_DEFAULT);
        } else {
            $password_hashed = $password_old;
        }

        $query = "UPDATE users SET fullname = ?, username = ?, email = ?, password = ?, photo_profile = ? WHERE id = ?";
        $params = [$fullname, $username, $email, $password_hashed, $photo_profile, $id];
        $types = 'ssssss';
        $updateResult = executeSecure($con, $query, $params, $types);

        $_SESSION['message'] = 'Data berhasil diperbarui!';
        $_SESSION['message_type'] = 'success';
        createLog($con, $_SESSION['admin']['email'], 'Successful user update '.$fullname);
        echo "
            <script>
                window.location.href = '../?hal=users_user-management';
            </script>
        ";
    }
    exit;
} elseif (isset($_GET['deleteUser'])) {
    $id = sani($_GET['deleteUser']);

    // Dapatkan data user untuk menghapus foto profile
    $resultGetUser = querySecure($con, "SELECT photo_profile FROM users WHERE id = ?", [$id], 's');
    $user = mysqli_fetch_assoc($resultGetUser);
    $photo_profile = $user['photo_profile'];

    // Hapus data user
    $deleteResult = executeSecure($con, "DELETE FROM users WHERE id = ?", [$id], 's');

    createLog($con, $_SESSION['admin']['email'], 'Successful user deletion id '.$user['fullname']);
    // Hapus foto profile jika ada
    if (!empty($photo_profile) && file_exists('../' . $photo_profile)) {
        unlink('../' . $photo_profile);
    }
    $_SESSION['message'] = 'User berhasil dihapus!';
    $_SESSION['message_type'] = 'success';
   
    echo "
            <script>
                window.location.href = '../?hal=users_user-management';
            </script>
        ";
    exit;
} else {
    // If accessed directly, redirect to homepage
    header('Location: ../../index.php');
    exit;
}
