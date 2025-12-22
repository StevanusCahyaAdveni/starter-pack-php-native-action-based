<?php
session_start();
include 'functions/sanitasi.php';
include 'functions/secure_query.php';
include 'config.php';

// Cek apakah user sudah login
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    // User belum login, cek localStorage via JavaScript
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Loading...</title>
        <link rel="stylesheet" href="assets/css/bootstrap.css">
        <style>
            body {
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                background: linear-gradient(90deg, #2d499d, #3f5491);
            }

            .loading-container {
                text-align: center;
                color: white;
            }

            .spinner-border {
                width: 3rem;
                height: 3rem;
            }
        </style>
    </head>

    <body>
        <div class="loading-container">
            <div class="spinner-border text-light mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p>Checking credentials...</p>
        </div>

        <!-- Hidden form for auto-login -->
        <form id="autoLoginForm" action="actions/login.php" method="POST" style="display: none;">
            <input type="email" name="email" id="autoEmail">
            <input type="password" name="password" id="autoPassword">
            <input type="checkbox" name="remember" checked>
        </form>

        <script>
            // Cek localStorage saat page load
            window.addEventListener('DOMContentLoaded', function() {
                const savedEmail = localStorage.getItem('remember_email');
                const savedPassword = localStorage.getItem('remember_password');

                if (savedEmail && savedPassword) {
                    // Ada data di localStorage, coba auto-login
                    document.getElementById('autoEmail').value = savedEmail;
                    document.getElementById('autoPassword').value = savedPassword;
                    document.getElementById('autoLoginForm').submit();
                } else {
                    // Tidak ada data di localStorage, redirect ke login
                    window.location.href = 'login.php';
                }
            });
        </script>
    </body>

    </html>
<?php
    exit;
}

// User sudah login, lanjutkan ke dashboard
?>
<?php
$hal = 'dashboard';
$textTitle = 'Dashboard';
if (isset($_GET['hal'])) {
    $getHal = sani($_GET['hal']);
    $hal = str_replace('_', '/', $getHal);
    $lastUnderscore = strrpos($getHal, '_');
    $titlePart = ($lastUnderscore !== false) ? substr($getHal, $lastUnderscore + 1) : $getHal;
    $textTitle = ucwords(str_replace('-', ' ', $titlePart));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $textTitle; ?> - <?= $_SESSION['admin']['fullname'] ?></title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.css">

    <link rel="stylesheet" href="assets/vendors/iconly/bold.css">

    <link rel="stylesheet" href="assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <link rel="shortcut icon" href="assets/images/favicon.svg" type="image/x-icon">
</head>

<body>
    <div id="app">
        <?php include 'sidebar.php'; ?>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block" style="max-width: 100px;">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading mb-0">
                <h3><?php echo $textTitle; ?></h3>
            </div>
            <div class="page-content">
                <?php include 'pages/' . $hal . '.php'; ?>
            </div>

            <footer>
                <div class="footer clearfix mb-0 text-muted">
                    <div class="float-start">
                        <p><?= date('Y'); ?> &copy; Sadewa</p>
                    </div>
                    <div class="float-end">
                        <p>Crafted with <span class="text-danger"><i class="bi bi-heart"></i></span> by <a
                                href="">Stevanus Cahya Adveni</a></p>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Modal untuk menampilkan gambar -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content modal-xl">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Preview Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Preview" style="max-width: 100%; height: auto;">
                </div>
            </div>
        </div>
    </div>



    <script src="assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>

    <script src="assets/vendors/apexcharts/apexcharts.js"></script>
    <script src="assets/js/pages/dashboard.js"></script>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/upImage.js"></script>
</body>

</html>