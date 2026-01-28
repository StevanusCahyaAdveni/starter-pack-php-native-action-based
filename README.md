# PHP Native Action-Based Framework

Framework PHP native sederhana dengan sistem routing otomatis, autentikasi berbasis session + localStorage, secure query dengan prepared statements, dan **CRUD Generator** otomatis.

---

## ✨ Update Terbaru (January 2026)

### 🆕 Fitur Baru
- **CRUD Generator Otomatis**: Generate SQL, Page, dan Action file secara otomatis dengan form builder
- **Modular Auto-Login System**: Auto-login menggunakan API endpoint terpisah (loginauto.php)
- **Variable-based Routing**: Routing menggunakan variable $content untuk include pages
- **Image Preview Modal**: Klik gambar manapun untuk preview fullscreen
- **Session Admin Data**: Akses lengkap data user yang login melalui `$_SESSION['admin']`
- **Dynamic Page Title**: Title browser menggunakan nama user yang sedang login
- **Dynamic App Name**: Logo sidebar menggunakan variable $appName dari config

### 🔄 Perbaikan
- **Auto-Login Refactoring**: Dipisah menjadi 3 file modular (auto-cek-login-html.php, auto-cek-login-action.php, loginauto.php)
- **Security Enhancement**: Auto-clear localStorage saat login gagal dengan flag $_SESSION['clear_remember']
- **Self-Protection**: User tidak bisa delete akun sendiri di user management
- **Routing System**: Perubahan dari function contenByRoute() ke variable $content
- **Database Config**: Update default database name dan tambah $appName variable
- Login page redirect protection (tidak bisa akses jika sudah login)
- Refactoring JavaScript untuk image preview (dipindah ke file terpisah)
- Improved code organization dan struktur file

---

## 📋 Struktur Folder

```
php-native-action-based/
├── actions/                     # Action handlers (Controller)
│   ├── index.php               # Router untuk action (mirror index.php)
│   ├── login.php               # Handler login
│   ├── register.php            # Handler registrasi
│   ├── logout.php              # Handler logout
│   └── pages/                  # Action handlers per module
│       └── users/
│           └── user-management.php
├── assets/                      # Static assets
│   ├── css/                    # Stylesheets
│   ├── js/                     # JavaScript files
│   ├── images/                 # Images & uploads
│   └── vendors/                # Third-party libraries
├── config.php                   # Database configuration
├── database/                    # SQL files
│   └── users.sql               # Table users schema
├── functions/                   # Helper functions
│   ├── sanitasi.php            # Input sanitization
│   ├── secure_query.php        # Prepared statement wrappers
│   ├── generate_uuid.php       # UUID v4 generator
│   ├── pagination.php          # Pagination dengan prepared statement
│   ├── upload_file.php         # File upload dengan kompresi otomatis
│   ├── auto-routing.php        # Variable-based routing system
│   ├── auto-cek-login-html.php # Auto-login check untuk HTML pages
│   └── auto-cek-login-action.php # Auto-login check untuk action files
├── pages/                       # Views (halaman konten)
│   ├── dashboard.php
│   └── users/
│       └── user-management.php
├── generate.php                 # CLI tool untuk generate files
├── index.php                    # Entry point utama + auto-login
├── login.php                    # Halaman login
├── register.php                 # Halaman registrasi
└── sidebar.php                  # Menu navigasi
```

---

## 🚀 Sistem Routing Otomatis

Framework menggunakan sistem routing **tanpa konfigurasi manual**. File di-load otomatis berdasarkan parameter URL dengan konversi underscore ke slash.

### 🔹 Routing Mechanism

**URL Format:** `index.php?hal=folder_subfolder_filename`

**Konversi:**
- Underscore (`_`) → Slash (`/`) untuk path file
- Dash (`-`) → Spasi untuk title (diubah ke title case)

**Contoh:**

| URL                                   | File Loaded                        | Page Title         |
| ------------------------------------- | ---------------------------------- | ------------------ |
| `index.php`                           | `pages/dashboard.php`              | Dashboard          |
| `index.php?hal=users_user-management` | `pages/users/user-management.php`  | User Management    |
| `index.php?hal=admin_settings_config` | `pages/admin/settings/config.php`  | Config             |

### 🔹 Routing di index.php (View)

**Routing sekarang menggunakan file terpisah:** `functions/auto-routing.php`

```php
<?php
session_start();
include 'config.php';
include 'functions/sanitasi.php';
include 'functions/secure_query.php';
include 'functions/auto-routing.php';  // Load routing system
include 'functions/auto-cek-login-html.php';  // Auto-login check
?>

<!-- HTML content -->
<div class="page-content">
    <?php include $content; ?>  <!-- Variable $content dari auto-routing.php -->
</div>
```

**File auto-routing.php:**
```php
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
$content = 'pages/' . $hal . '.php';  // Variable untuk di-include
?>
```

### 🔹 Routing di actions/index.php (Controller)

```php
<?php
session_start();
include '../config.php';
include '../functions/sanitasi.php';
include '../functions/secure_query.php';

$hal = 'dashboard';
if (isset($_GET['hal'])) {
    $getHal = sani($_GET['hal']);
    $hal = str_replace('_', '/', $getHal);
}

include 'pages/' . $hal . '.php';
?>
```

**Struktur Mirror:**
- View: `pages/users/user-management.php`
- Action: `actions/pages/users/user-management.php`
- Akses action: `actions/?hal=users_user-management`

---

## 🔐 Sistem Autentikasi

Framework dilengkapi autentikasi lengkap dengan session management dan localStorage integration.

### 🔹 Login System

**File:** `login.php` dan `actions/login.php`

**Fitur:**
- ✅ Login dengan email & password
- ✅ Password verification dengan `password_verify()`
- ✅ Remember me dengan localStorage
- ✅ Auto-redirect jika sudah login (via localStorage check)
- ✅ Toggle show/hide password
- ✅ Session management
- ✅ Complete user data di `$_SESSION['admin']`

**Flow Login:**
```
1. User input email & password
2. Validasi format email
3. Query user dengan querySecure()
4. Verify password hash
5. Set session variables:
   - user_id, user_fullname, user_username
   - user_email, user_photo, is_logged_in
   - admin (complete user data array)
6. Jika remember me checked:
   - Save email & password ke localStorage via JavaScript
7. Redirect ke dashboard
```

**Session Variables:**
```php
$_SESSION['user_id']        // UUID user
$_SESSION['user_fullname']  // Nama lengkap
$_SESSION['user_username']  // Username
$_SESSION['user_email']     // Email
$_SESSION['user_photo']     // Path foto profil
$_SESSION['is_logged_in']   // Boolean login status
$_SESSION['admin']          // Complete user data (array)
```

**🆕 Login Page Protection:**
```php
// login.php - Redirect jika sudah login
if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

// Cek localStorage saat page load
if (savedEmail && savedPassword) {
    window.location.href = 'index.php'; // Auto-redirect ke index untuk auto-login
}
```

### 🔹 Auto-Login System (Modular Approach)

**Framework menggunakan 3 file terpisah untuk auto-login:**

#### 1. functions/auto-cek-login-html.php

**Untuk:** HTML pages (index.php, dll)

**Flow:**
```
1. Cek session is_logged_in
2. Jika tidak login:
   - Tampilkan loading screen dengan spinner
   - JavaScript cek localStorage
   - Jika ada credentials:
     → Fetch POST ke actions/loginauto.php
     → Jika success: reload page
     → Jika gagal: redirect ke login.php
   - Jika tidak ada:
     → Redirect ke login.php
3. Jika login:
   - Continue load page normal
```

#### 2. functions/auto-cek-login-action.php

**Untuk:** Action handlers (actions/index.php)

**Flow:**
```
1. Cek session is_logged_in
2. Jika tidak login:
   - Cek POST parameter auto_login
   - Jika ada: verify credentials dan set session
   - Jika tidak ada atau gagal: exit
3. Jika login:
   - Continue execute action
```

#### 3. actions/loginauto.php

**API Endpoint untuk auto-login**

**Request:**
```javascript
fetch('actions/loginauto.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        email: savedEmail,
        password: savedPassword
    })
});
```

**Response:**
```json
{
    "success": true/false,
    "message": "Login berhasil!",
    "clear_storage": true/false,  // Flag untuk clear localStorage
    "user": { "id": "...", "fullname": "..." }
}
```

**Integrasi:**

```php
// index.php
include 'functions/auto-cek-login-html.php';

// actions/index.php
include '../functions/auto-cek-login-action.php';

// login.php (check di JavaScript)
if (result.clear_storage) {
    localStorage.removeItem('remember_email');
    localStorage.removeItem('remember_password');
}
```

**Security Features:**
- API menggunakan JSON POST (bukan form data)
- Password tetap di-verify dengan password_verify()
- Auto-clear localStorage jika credentials salah
- Session flag $_SESSION['clear_remember'] untuk trigger clear localStorage
- No bypass authentication - semua tetap melalui verification

### 🔹 Register System

**File:** `register.php` dan `actions/register.php`

**Fitur:**
- ✅ Register dengan fullname, username, email, password
- ✅ Generate UUID untuk primary key
- ✅ Validasi email & username unique
- ✅ Password strength indicator (real-time)
- ✅ Password match validation
- ✅ Password hashing dengan `password_hash()`

**Validasi:**
```php
- Email format valid (filter_var)
- Username: alphanumeric + underscore, 3-50 karakter
- Password minimal 6 karakter
- Password & confirm password match
- Email unique (cek database)
- Username unique (cek database)
```

### 🔹 Logout System

**File:** `actions/logout.php`

```php
<?php
session_start();
session_unset();   // Hapus semua session variables
session_destroy(); // Destroy session

// Redirect ke login
header('Location: ../login.php');
exit;
```

**JavaScript clear localStorage (di sidebar.php):**
```javascript
function handleLogout(event) {
    if (confirm('Are you sure you want to logout?')) {
        // Hapus data dari localStorage
        localStorage.removeItem('remember_email');
        localStorage.removeItem('remember_password');
        return true; // Continue ke logout.php
    } else {
        event.preventDefault();
        return false;
    }
}
```

**HTML (di sidebar.php):**
```html
<li class="sidebar-item">
    <a href="actions/logout.php" class='sidebar-link' onclick="return handleLogout(event)">
        <i class="bi bi-box-arrow-right"></i>
        <span>Logout</span>
    </a>
</li>
```

---

## 🛡️ Security Functions

### 🔹 Input Sanitization

**File:** `functions/sanitasi.php`

```php
function sani($data)
{
    if (is_array($data)) {
        return array_map('sani', $data);
    }
    if (is_string($data)) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
    return $data;
}
```

**Penggunaan:**
```php
$email = sani($_POST['email']);
$name = sani($_POST['name']);
```

### 🔹 Prepared Statements

**File:** `functions/secure_query.php`

#### 1. querySecure() - Untuk SELECT

```php
function querySecure($con, $query, $params = [], $types = '')
```

**Contoh:**
```php
// SELECT single
$email = 'user@example.com';
$result = querySecure($con, 
    "SELECT * FROM users WHERE email = ?", 
    [$email], 
    's'
);
$user = mysqli_fetch_assoc($result);

// SELECT dengan multiple params
$search = '%john%';
$minAge = 18;
$result = querySecure($con,
    "SELECT * FROM users WHERE name LIKE ? AND age >= ?",
    [$search, $minAge],
    'si'  // s=string, i=integer
);
```

#### 2. executeSecure() - Untuk INSERT/UPDATE/DELETE

```php
function executeSecure($con, $query, $params = [], $types = '')
```

**Contoh:**
```php
// INSERT
$id = generate_uuid();
$name = 'John Doe';
$email = 'john@example.com';

$result = executeSecure($con,
    "INSERT INTO users (id, name, email) VALUES (?, ?, ?)",
    [$id, $name, $email],
    'sss'
);

// UPDATE
$newName = 'John Updated';
$userId = 'uuid-123';

$success = executeSecure($con,
    "UPDATE users SET name = ? WHERE id = ?",
    [$newName, $userId],
    'ss'
);

// DELETE
$userId = 'uuid-123';
$success = executeSecure($con,
    "DELETE FROM users WHERE id = ?",
    [$userId],
    's'
);
```

**Type Codes:**
- `s` = string
- `i` = integer
- `d` = double/float
- `b` = blob

---

## 📊 Pagination System

**File:** `functions/pagination.php`

### 🔹 Fungsi makePagination()

```php
function makePagination($con, $query, $params = [], $types = '', $page = 1, $limit = 10)
```

**Return:**
```php
[
    'data' => mysqli_result,    // Result untuk di-fetch
    'total_pages' => int,       // Total halaman
    'current_page' => int,      // Halaman saat ini
    'total_data' => int,        // Total data
    'per_page' => int,          // Data per halaman
    'from' => int,              // Data mulai dari
    'to' => int                 // Data sampai
]
```

**Contoh Penggunaan:**

```php
// Tanpa parameter WHERE
$page = $_GET['page'] ?? 1;
$pagination = makePagination($con, 
    "SELECT * FROM users ORDER BY created_at DESC", 
    [], '', $page, 10
);

// Dengan parameter WHERE (prepared statement)
$search = isset($_GET['search']) ? sani($_GET['search']) : '';
$whereClause = '';
$params = [];
$types = '';

if (!empty($search)) {
    $searchParam = '%' . $search . '%';
    $whereClause = "WHERE fullname LIKE ? OR email LIKE ?";
    $params = [$searchParam, $searchParam];
    $types = 'ss';
}

$query = "SELECT * FROM users $whereClause ORDER BY created_at DESC";
$pagination = makePagination($con, $query, $params, $types, $page, 10);

// Tampilkan data
if ($pagination['data'] && mysqli_num_rows($pagination['data']) > 0) {
    while ($row = mysqli_fetch_assoc($pagination['data'])) {
        echo $row['fullname'];
    }
}
```

### 🔹 Fungsi showPagination()

```php
function showPagination($currentPage, $totalPages, $queryParams = [], $maxLinks = 5)
```

**Contoh:**
```php
// Tampilkan pagination
if ($pagination['total_pages'] > 1) {
    showPagination(
        $pagination['current_page'], 
        $pagination['total_pages'], 
        ['hal' => 'users_user-management', 'search' => $search]
    );
}
```

**Fitur:**
- Max 5 link halaman ditampilkan
- Ellipsis (...) untuk halaman tengah
- First & Last page selalu tampil
- Previous & Next buttons
- Query params preserved (hal, search, dll)

---

## 📤 File Upload System

**File:** `functions/upload_file.php`

### 🔹 Fungsi uploadFile()

```php
function uploadFile($file, $targetDir = 'uploads/', $maxSize = 5242880, $allowedTypes = [])
```

**Return:**
```php
[
    'success' => bool,
    'message' => string,
    'file_path' => string,  // Full path: uploads/filename.jpg
    'file_name' => string   // Filename only
]
```

**Contoh Penggunaan:**

```php
// Basic upload
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $result = uploadFile(
        $_FILES['photo'], 
        'assets/images/photos/', 
        2 * 1024 * 1024  // 2MB
    );
    
    if ($result['success']) {
        $photoPath = $result['file_path'];
        echo "Upload berhasil: " . $photoPath;
    } else {
        echo "Error: " . $result['message'];
    }
}

// Upload dengan validasi tipe
$result = uploadFile(
    $_FILES['document'], 
    'uploads/documents/', 
    5 * 1024 * 1024, 
    ['pdf', 'doc', 'docx']  // Hanya PDF & Word
);

// Update dengan hapus file lama
$oldPhoto = 'assets/images/old_photo.jpg';

if (isset($_FILES['new_photo']) && $_FILES['new_photo']['error'] === UPLOAD_ERR_OK) {
    $result = uploadFile($_FILES['new_photo'], 'assets/images/');
    
    if ($result['success']) {
        // Hapus foto lama
        if (!empty($oldPhoto) && file_exists($oldPhoto)) {
            unlink($oldPhoto);
        }
        $newPhoto = $result['file_path'];
    }
}
```

### 🔹 Fitur Upload

**Auto Compression untuk Gambar:**
- JPG/JPEG: Quality 80%, resize jika > 2000px
- PNG: Preserve transparency, compress
- WebP: Quality 80%
- HEIC/HEIF: Convert ke JPEG (butuh imagick)
- GIF: Preserve animation

**Unique Filename:**
```
Format: filename_YYYYMMDDHHmmss_randomhex.ext
Contoh: photo_20251217120530_a3f2b1c4.jpg
```

**Supported Types:**
- Images: JPG, PNG, GIF, WebP, BMP, HEIC, HEIF
- Documents: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX
- Text: TXT, CSV
- Archives: ZIP, RAR

---

## 🆔 UUID Generator

**File:** `functions/generate_uuid.php`

```php
function generate_uuid()
```

**Return:** String UUID v4 format (36 karakter)

**Contoh:**
```php
$userId = generate_uuid();
// Output: "a3f2b1c4-5d6e-4f7a-8b9c-0d1e2f3a4b5c"

// Insert ke database
$result = executeSecure($con,
    "INSERT INTO users (id, name, email) VALUES (?, ?, ?)",
    [$userId, $name, $email],
    'sss'
);
```

**Keuntungan UUID vs Auto Increment:**
- ✅ Tidak bisa ditebak
- ✅ Unik secara global
- ✅ Aman untuk public API
- ✅ Distributed system friendly

---

## 🎯 Implementasi CRUD Pattern

### 🔹 Struktur File

**View:** `pages/module/feature.php`
**Action:** `actions/pages/module/feature.php`

### 🔹 Contoh: User Management

#### 1. View (pages/users/user-management.php)

```php
<div>
    <!-- Modal Add User -->
    <form action="actions/?hal=users_user-management" method="POST" enctype="multipart/form-data">
        <input type="text" name="fullname" required>
        <input type="email" name="email" required>
        <input type="password" name="password" required>
        <input type="file" name="photo_profile">
        <button type="submit" name="addUser">Simpan</button>
    </form>

    <!-- Modal Edit User -->
    <form action="actions/?hal=users_user-management" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $user['id'] ?>">
        <input type="hidden" name="password_old" value="<?= $user['password'] ?>">
        <input type="text" name="fullname" value="<?= $user['fullname'] ?>">
        <input type="email" name="email" value="<?= $user['email'] ?>">
        <input type="password" name="password" placeholder="Kosongkan jika tidak diubah">
        <input type="file" name="photo_profile">
        <button type="submit" name="updateUser">Update</button>
    </form>

    <!-- Table with Search -->
    <form method="get">
        <input type="hidden" name="hal" value="users_user-management">
        <input type="text" name="search" value="<?= sani($_GET['search'] ?? '') ?>">
        <button type="submit">Cari</button>
    </form>

    <?php
    // Pagination dengan search
    $search = isset($_GET['search']) ? sani($_GET['search']) : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    
    $whereClause = '';
    $params = [];
    $types = '';
    
    if (!empty($search)) {
        $searchParam = '%' . $search . '%';
        $whereClause = "WHERE fullname LIKE ? OR email LIKE ?";
        $params = [$searchParam, $searchParam];
        $types = 'ss';
    }
    
    $query = "SELECT * FROM users $whereClause ORDER BY created_at DESC";
    $pagination = makePagination($con, $query, $params, $types, $page, 10);
    
    // Display data
    while ($row = mysqli_fetch_assoc($pagination['data'])) {
        ?>
        <tr>
            <td><?= $row['fullname'] ?></td>
            <td><?= $row['email'] ?></td>
            <td>
                <button onclick="editUser('<?= $row['id'] ?>')">Edit</button>
                <a href="actions/?hal=users_user-management&deleteUser=<?= $row['id'] ?>" 
                   onclick="return confirm('Yakin hapus?')">Hapus</a>
            </td>
        </tr>
        <?php
    }
    
    // Pagination links
    if ($pagination['total_pages'] > 1) {
        showPagination(
            $pagination['current_page'], 
            $pagination['total_pages'],
            ['hal' => 'users_user-management', 'search' => $search]
        );
    }
    ?>
</div>
```

#### 2. Action Handler (actions/pages/users/user-management.php)

```php
<?php
// Note: Relative path dari actions/pages/users/ ke root adalah ../../../
// Karena: actions/ (1) -> pages/ (2) -> users/ (3)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // ========== CREATE ==========
    if (isset($_POST['addUser'])) {
        include '../functions/upload_file.php';
        
        $id = generate_uuid();
        $fullname = sani($_POST['fullname']);
        $username = sani($_POST['username']);
        $email = sani($_POST['email']);
        $password = password_hash(sani($_POST['password']), PASSWORD_DEFAULT);
        
        // Upload foto
        $photo_profile = null;
        if (isset($_FILES['photo_profile']) && $_FILES['photo_profile']['error'] === UPLOAD_ERR_OK) {
            $result = uploadFile($_FILES['photo_profile'], '../assets/images/photo_profile/', 2 * 1024 * 1024);
            if ($result['success']) {
                $photo_profile = str_replace('../', '', $result['file_path']);
            }
        }
        
        // Insert
        $insertResult = executeSecure($con,
            "INSERT INTO users (id, fullname, username, email, password, photo_profile) VALUES (?, ?, ?, ?, ?, ?)",
            [$id, $fullname, $username, $email, $password, $photo_profile],
            'ssssss'
        );
        
        if ($insertResult) {
            $_SESSION['message'] = 'User berhasil ditambahkan!';
            $_SESSION['message_type'] = 'success';
        }
        
        header('Location: ../?hal=users_user-management');
        exit;
    }
    
    // ========== UPDATE ==========
    if (isset($_POST['updateUser'])) {
        include '../functions/upload_file.php';
        
        $id = sani($_POST['id']);
        $fullname = sani($_POST['fullname']);
        $username = sani($_POST['username']);
        $email = sani($_POST['email']);
        $password_old = sani($_POST['password_old']);
        
        // Get current photo
        $resultUser = querySecure($con, "SELECT photo_profile FROM users WHERE id = ?", [$id], 's');
        $currentUser = mysqli_fetch_assoc($resultUser);
        $photo_profile = $currentUser['photo_profile'];
        
        // Handle upload foto baru
        if (isset($_FILES['photo_profile']) && 
            $_FILES['photo_profile']['error'] === UPLOAD_ERR_OK && 
            !empty($_FILES['photo_profile']['name'])) {
            
            $result = uploadFile($_FILES['photo_profile'], '../assets/images/photo_profile/', 2 * 1024 * 1024);
            
            if ($result['success']) {
                // Hapus foto lama
                if (!empty($photo_profile) && file_exists('../' . $photo_profile)) {
                    unlink('../' . $photo_profile);
                }
                $photo_profile = str_replace('../', '', $result['file_path']);
            }
        }
        
        // Handle password
        $password_new = sani($_POST['password']);
        $password = !empty($password_new) 
            ? password_hash($password_new, PASSWORD_DEFAULT) 
            : $password_old;
        
        // Update
        $updateResult = executeSecure($con,
            "UPDATE users SET fullname = ?, username = ?, email = ?, password = ?, photo_profile = ? WHERE id = ?",
            [$fullname, $username, $email, $password, $photo_profile, $id],
            'ssssss'
        );
        
        if ($updateResult) {
            $_SESSION['message'] = 'User berhasil diupdate!';
            $_SESSION['message_type'] = 'success';
        }
        
        header('Location: ../?hal=users_user-management');
        exit;
    }
    
} 
// ========== DELETE ==========
elseif (isset($_GET['deleteUser'])) {
    $id = sani($_GET['deleteUser']);
    
    // Get user data untuk hapus foto
    $resultUser = querySecure($con, "SELECT photo_profile FROM users WHERE id = ?", [$id], 's');
    $user = mysqli_fetch_assoc($resultUser);
    
    // Delete user
    $deleteResult = executeSecure($con, "DELETE FROM users WHERE id = ?", [$id], 's');
    
    if ($deleteResult) {
        // Hapus foto profil
        if (!empty($user['photo_profile']) && file_exists('../' . $user['photo_profile'])) {
            unlink('../' . $user['photo_profile']);
        }
        
        $_SESSION['message'] = 'User berhasil dihapus!';
        $_SESSION['message_type'] = 'success';
    }
    
    header('Location: ../?hal=users_user-management');
    exit;
}
else {
    header('Location: ../../index.php');
    exit;
}
?>
```

**Relative Path Guide:**
```
actions/pages/users/user-management.php
│
├── ../ → actions/pages/
├── ../../ → actions/
└── ../../../ → root/
    ├── config.php
    ├── functions/
    ├── assets/
    └── index.php
```

---

## 🆕 CRUD Generator (GUI)

**File:** `pages/crud-generate.php` dan `actions/pages/crud-generate.php`

### 🔹 Fitur CRUD Generator

Generator CRUD otomatis dengan **form builder** yang menghasilkan 3 file sekaligus:
1. **SQL File** - Database schema dengan UUID primary key
2. **Page File** - View dengan CRUD interface lengkap
3. **Action File** - Handler untuk Create, Read, Update, Delete

### 🔹 Cara Menggunakan

1. Akses menu **"Generate CRUD"** di sidebar
2. Isi form:
   - **Direktori & File**: Contoh `products/product-list` (akan buat struktur folder)
   - **Nama Table DB**: Contoh `products` (nama table database)
   - **Struktur Kolom**: Tambah kolom dengan button "Tambah Kolom"
     - Nama Kolom: `name`, `price`, `description`, dll
     - Label: Label yang tampil di form
     - Tipe Data: VARCHAR, INT, TEXT, DATE, DATETIME
   - **Opsi Tambahan**: 
     - ✅ Timestamps (created_at, updated_at)
3. Klik **"Generate CRUD"**

### 🔹 Output yang Dihasilkan

**1. SQL File:** `database/{table_name}_{random}_{timestamp}.sql`
```sql
-- Auto-generated table structure
CREATE TABLE IF NOT EXISTS `products` (
  `id` VARCHAR(36) NOT NULL COMMENT 'Primary Key - UUID v4',
  `name` VARCHAR(255) NOT NULL COMMENT 'Product Name',
  `price` INT(11) NOT NULL COMMENT 'Price',
  `description` TEXT NOT NULL COMMENT 'Description',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**2. Page File:** `pages/products/product-list.php`
- ✅ Table dengan pagination
- ✅ Search form dengan query params preserved
- ✅ Add Modal (Bootstrap)
- ✅ Edit Modal (Bootstrap)
- ✅ Delete dengan confirmation
- ✅ Auto-fill edit form via JavaScript

**3. Action File:** `actions/pages/products/product-list.php`
- ✅ CREATE dengan UUID auto-generate
- ✅ UPDATE dengan preserve data
- ✅ DELETE dengan cascade
- ✅ Prepared statements untuk security
- ✅ Session message untuk feedback
- ✅ Auto-calculated relative paths

### 🔹 Keuntungan CRUD Generator

- 🚀 **Cepat**: Generate 3 file dalam 1 klik
- 🔐 **Secure**: Menggunakan prepared statements
- 📦 **Complete**: CRUD lengkap dengan search & pagination
- 🎨 **Bootstrap UI**: Interface modern & responsive
- 📁 **Auto Directory**: Buat struktur folder otomatis
- 🆔 **UUID Ready**: Primary key menggunakan UUID v4

---

## 🛠️ CLI Generator (Legacy)

**File:** `generate.php`

### 🔹 Penggunaan

```bash
# Format
php generate.php module_feature

# Contoh
php generate.php users_user-management
php generate.php admin_settings_config
php generate.php products_product-list
```

### 🔹 Yang Dihasilkan

1. **Page File:** `pages/module/feature.php`
   - Template HTML dengan Bootstrap card
   - Title otomatis dari filename

2. **Action File:** `actions/pages/module/feature.php`
   - Template handler dengan session check
   - Prepared statement ready
   - Redirect setup
   - **Relative path otomatis** berdasarkan kedalaman folder

### 🔹 Output Contoh

```
=== PHP File Generator ===
Input: users_user-management
Generating files for: users/user-management

✓ Folder created: pages/users
✓ Page created: pages/users/user-management.php
✓ Folder created: actions/pages/users
✓ Action created: actions/pages/users/user-management.php

✓ Generation completed!
Page URL: index.php?hal=users_user-management
Action URL: actions/index.php?hal=users_user-management
Note: Use underscore (_) in URL, it will be converted to slash (/) for file path
```

**Template Action yang Dihasilkan:**
```php
<?php
/**
 * Action: users/user-management
 * Created: 2025-12-17 10:30:00
 */

session_start();
include '../../../functions/sanitasi.php';  // Auto-calculated relative path
include '../../../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process your logic here
    
    $_SESSION['message'] = 'Action completed successfully!';
    $_SESSION['message_type'] = 'success';
    
    header('Location: ../../../index.php?hal=users_user-management');
    exit;
} else {
    header('Location: ../../../index.php');
    exit;
}
```

---

## 🎨 UI Features

### 🔹 Dynamic Page Title

Page title di browser menggunakan nama user yang sedang login:

**Di index.php:**
```html
<title><?php echo $textTitle; ?> - <?= $_SESSION['admin']['fullname'] ?></title>
```

**Output:**
```
Dashboard - John Doe
User Management - John Doe
Product List - John Doe
```

### 🔹 Image Preview Modal

Framework include modal otomatis untuk preview gambar. Semua tag `<img>` dapat diklik untuk memperbesar.

**File:** `assets/js/upImage.js`

```javascript
function showImgLink(url) {
    const modalImage = document.getElementById('modalImage');
    modalImage.src = url;
    const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
    imageModal.show();
}

// Auto-attach click event ke semua img (kecuali yang di modal)
document.addEventListener('DOMContentLoaded', function() {
    const allImages = document.querySelectorAll('img');
    allImages.forEach(function(img) {
        const isInsideModal = img.closest('#imageModal');
        if (!isInsideModal) {
            img.style.cursor = 'pointer';
            img.addEventListener('click', function() {
                const imgSrc = this.getAttribute('src');
                if (imgSrc && imgSrc !== '') {
                    showImgLink(imgSrc);
                }
            });
        }
    });
});
```

**Modal HTML (di index.php):**
```html
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-xl">
            <div class="modal-header">
                <h5 class="modal-title">Preview Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Preview" style="max-width: 100%; height: auto;">
            </div>
        </div>
    </div>
</div>
```

**Include di index.php:**
```html
<script src="assets/js/upImage.js"></script>
```

### 🔹 Alert Messages

**Set message di action:**
```php
$_SESSION['message'] = 'Data berhasil disimpan!';
$_SESSION['message_type'] = 'success'; // success, error, warning, info
```

**Display di view:**
```php
<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?= $_SESSION['message_type'] == 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
        <?= $_SESSION['message'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
<?php endif; ?>
```

---

## 📝 Database Schema

### 🔹 Table: users

```sql
CREATE TABLE `users` (
  `id` varchar(36) NOT NULL COMMENT 'UUID primary key',
  `fullname` varchar(500) NOT NULL,
  `username` varchar(500) NOT NULL,
  `email` varchar(250) NOT NULL,
  `password` text NOT NULL,
  `photo_profile` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_username` (`username`),
  UNIQUE KEY `unique_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## ⚙️ Configuration

### 🔹 Database Config (config.php)

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'project_php_action_based');  // Updated default name

$con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
mysqli_set_charset($con, "utf8mb4");
date_default_timezone_set('Asia/Jakarta');

// App Configuration
$appName = "Little PHP Framework";  // Digunakan di sidebar dan title
```

**Penggunaan $appName:**

```php
// sidebar.php
<div class="logo">
    <a href="index.php">
        <h3><?php echo $appName; ?></h3>
    </a>
</div>
```

### 🔹 PHP Requirements

- PHP 7.4+
- MySQLi extension
- GD library (untuk image compression)
- Imagick (optional, untuk HEIC conversion)

### 🔹 PHP.ini Settings

```ini
upload_max_filesize = 10M
post_max_size = 10M
memory_limit = 256M
```

---

## 🔒 Security Features

### ✅ Input Security
- Semua input melalui fungsi `sani()`
- HTML escaping dengan `htmlspecialchars()`
- Type validation (email, username pattern)
- File type validation untuk upload

### ✅ Database Security
- Prepared statements untuk semua query
- Charset `utf8mb4` untuk prevent SQL injection
- UUID sebagai primary key (tidak bisa ditebak)
- Password hashing dengan bcrypt

### ✅ Authentication Security
- Session-based authentication
- Password verification dengan `password_verify()`
- Auto-logout saat session expired
- Remember me menggunakan localStorage (client-side only)
- CSRF protection ready (bisa tambahkan token)

### ✅ File Upload Security
- File size validation
- File type whitelist
- Unique filename generation
- Upload folder outside public (optional)
- Automatic image compression

---

## 🚀 Quick Start

### 1. Setup Database

```sql
CREATE DATABASE php_native_action;
USE php_native_action;

-- Import users table
SOURCE database/users.sql;
```

### 2. Configure Database

Edit `config.php`:
```php
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### 3. Create First User (Manual)

```sql
INSERT INTO users (id, fullname, username, email, password, created_at) 
VALUES (
    UUID(),
    'Admin',
    'admin',
    'admin@example.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    NOW()
);
```

### 4. Generate New Module

```bash
php generate.php products_product-list
```

### 5. Add Menu di Sidebar

Edit `sidebar.php`:
```php
<?php $sidebarPage = "products_product-list"; ?>
<li class="sidebar-item <?= ($getHal == $sidebarPage) ? "active" : "" ?>">
    <a href="?hal=<?= $sidebarPage ?>" class='sidebar-link'>
        <i class="bi bi-box"></i>
        <span>Product List</span>
    </a>
</li>
```

### 6. Develop Your Feature

Edit files yang di-generate:
- `pages/products/product-list.php` → View
- `actions/pages/products/product-list.php` → Handler

---

## 📚 Best Practices

### ✅ DO's

1. **Selalu gunakan prepared statements**
   ```php
   $result = querySecure($con, "SELECT * FROM users WHERE id = ?", [$id], 's');
   ```

2. **Sanitasi semua input**
   ```php
   $name = sani($_POST['name']);
   ```

3. **Validasi sebelum simpan**
   ```php
   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
       // Handle error
   }
   ```

4. **Set session message untuk feedback**
   ```php
   $_SESSION['message'] = 'Success!';
   $_SESSION['message_type'] = 'success';
   ```

5. **Gunakan UUID untuk primary key**
   ```php
   $id = generate_uuid();
   ```

6. **Hapus file lama saat upload baru**
   ```php
   if (file_exists($oldFile)) {
       unlink($oldFile);
   }
   ```

### ❌ DON'Ts

1. **Jangan gunakan query langsung**
   ```php
   // ❌ Bad
   $query = "SELECT * FROM users WHERE id = '$id'";
   ```

2. **Jangan skip sanitasi**
   ```php
   // ❌ Bad
   $name = $_POST['name'];
   ```

3. **Jangan hardcode path**
   ```php
   // ❌ Bad
   include '../../functions/sanitasi.php';
   
   // ✅ Good - gunakan generator untuk auto-calculate
   ```

4. **Jangan expose error detail ke user**
   ```php
   // ❌ Bad
   die(mysqli_error($con));
   
   // ✅ Good
   error_log(mysqli_error($con));
   $_SESSION['message'] = 'Terjadi kesalahan sistem';
   ```

---

## 🤝 Contributing

Untuk development atau customization:

1. Fork this framework
2. Generate new module dengan CLI
3. Follow CRUD pattern yang ada
4. Gunakan security functions yang tersedia
5. Test semua input validation
6. Document perubahan di README

---

## 📄 License

Free to use for personal and commercial projects.

---

## 👨‍💻 Author

**Stevanus Cahya Adveni**

Framework ini dibuat untuk mempercepat development PHP native dengan pattern yang konsisten dan aman.

---

## 📞 Support

Jika ada pertanyaan atau issue, silakan:
- Baca dokumentasi dengan teliti
- Cek contoh implementasi di user-management
- Review security patterns yang digunakan
- Test dengan data dummy dulu

**Happy Coding! 🚀**
