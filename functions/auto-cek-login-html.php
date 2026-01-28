<?php 
// Cek apakah user sudah login
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
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
            <p id="loadingText">Checking credentials...</p>
        </div>

        <script>
            // Auto-login via API
            window.addEventListener('DOMContentLoaded', async function() {
                const savedEmail = localStorage.getItem('remember_email');
                const savedPassword = localStorage.getItem('remember_password');

                if (savedEmail && savedPassword) {
                    try {
                        // Hit API untuk auto-login
                        const response = await fetch('actions/loginauto.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                email: savedEmail,
                                password: savedPassword
                            })
                        });

                        const result = await response.json();

                        if (result.success) {
                            // Login berhasil, reload halaman untuk load dashboard
                            document.getElementById('loadingText').textContent = 'Login successful! Redirecting...';
                            window.location.reload();
                        } else {
                            // Login gagal
                            if (result.clear_storage) {
                                // Clear localStorage jika kredensial salah
                                localStorage.removeItem('remember_email');
                                localStorage.removeItem('remember_password');
                            }
                            // Redirect ke login page
                            window.location.href = 'login.php';
                        }
                    } catch (error) {
                        console.error('Auto-login error:', error);
                        // Error saat fetch, redirect ke login
                        window.location.href = 'login.php';
                    }
                } else {
                    // Tidak ada data di localStorage, redirect ke login
                    window.location.href = 'login.php';
                }
            });
        </script>
    </body>

    </html>

    <?php exit; ?>
<?php }?>