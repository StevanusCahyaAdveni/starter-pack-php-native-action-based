<?php
$getHal = sani($_GET['hal'] ?? 'dashboard');
?>
<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between">
                <div class="logo">
                    <a href="index.html"><img src="assets/images/logo/logo.png" alt="Logo" srcset=""></a>
                </div>
                <div class="toggler">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">Menu</li>

                <?php
                $sidebarPage = "dashboard";
                ?>
                <li class="sidebar-item <?= ($getHal == $sidebarPage) ? "active" : "" ?>">
                    <a href="?hal=<?php echo $sidebarPage; ?>" class='sidebar-link'>
                        <i class="bi bi-file-earmark-medical-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="sidebar-title">Users</li>
                <!-- Example New Menu in Side Bar -->
                <?php
                $sidebarPage = "users_user-management";
                ?>
                <li class="sidebar-item <?= ($getHal == $sidebarPage) ? "active" : "" ?>">
                    <a href="?hal=<?php echo $sidebarPage; ?>" class='sidebar-link'>
                        <i class="bi bi-people-fill"></i>
                        <span>User Management</span>
                    </a>
                </li>

                <li class="sidebar-title">Account</li>
                <li class="sidebar-item">
                    <a href="actions/logout.php" class='sidebar-link' onclick="return handleLogout(event)">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </a>
                </li>
                <!-- End Example New Menu in Side Bar -->
            </ul>
        </div>
        <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
    </div>
</div>

<script>
    function handleLogout(event) {
        if (confirm('Are you sure you want to logout?')) {
            // Hapus data dari localStorage
            localStorage.removeItem('remember_email');
            localStorage.removeItem('remember_password');
            return true;
        } else {
            event.preventDefault();
            return false;
        }
    }
</script>