# PHP Native Action-Based Framework

Framework PHP native sederhana dengan sistem routing otomatis, secure query, dan generator file CLI.

---

## 📋 Struktur Folder

```
php-native-action-based/
├── actions/                 # Folder untuk file handler/action
│   ├── index.php           # Entry point routing action
│   └── pages/              # File-file action handler (CRUD operations)
│       └── users/
│           └── user-management.php
├── assets/                  # Assets statis (CSS, JS, Images)
│   ├── css/
│   ├── js/
│   ├── images/
│   └── vendors/            # Library pihak ketiga (Bootstrap, jQuery, dll)
├── functions/               # Helper functions
│   ├── sanitasi.php        # Fungsi sanitasi input
│   ├── secure_query.php    # Fungsi query aman dengan prepared statement
│   └── pagination.php      # Fungsi pagination otomatis
├── pages/                   # Halaman konten aplikasi (View)
│   └── users/
│       └── user-management.php
├── config.php               # Konfigurasi database dan koneksi
├── index.php                # Entry point utama aplikasi
├── sidebar.php              # Komponen sidebar menu
└── generate.php             # CLI generator untuk membuat file otomatis
```

---

## 🚀 Sistem Routing Otomatis

Framework ini menggunakan sistem routing otomatis **tanpa perlu konfigurasi routing manual**. File akan di-load secara otomatis berdasarkan parameter URL.

### 🔹 Routing di `index.php` (Halaman View)

```php
<?php
$hal = 'dashboard';
$textTitle = 'Dashboard';

if (isset($_GET['hal'])) {
    $getHal = sani($_GET['hal']);
    $hal = str_replace('_', '/', $getHal);

    // Generate title dari bagian terakhir setelah underscore
    $lastUnderscore = strrpos($getHal, '_');
    $titlePart = ($lastUnderscore !== false) ? substr($getHal, $lastUnderscore + 1) : $getHal;
    $textTitle = ucwords(str_replace('-', ' ', $titlePart));
}
?>

<div class="page-content">
    <?php include 'pages/' . $hal . '.php'; ?>
</div>
```

### 🔹 Routing di `actions/index.php` (Handler/Controller)

```php
<?php
include 'functions/secure_query.php';

$hal = 'dashboard';
if (isset($_GET['hal'])) {
    $getHal = sani($_GET['hal']);
    $hal = str_replace('_', '/', $getHal);
}

include 'pages/' . $hal . '.php';
?>
```

### 📌 Penjelasan Routing:

1. **Default Page**: Jika tidak ada parameter `?hal=`, aplikasi akan load `pages/dashboard.php`

2. **Konversi Underscore ke Slash**:

   - URL: `index.php?hal=users_user-management`
   - Dikonversi menjadi: `pages/users/user-management.php`
   - Underscore (`_`) otomatis diganti dengan slash (`/`)

3. **Generate Title Otomatis**:

   - Mengambil bagian setelah underscore terakhir
   - Contoh: `users_user-management` → Title: "User Management"
   - Dash (`-`) diganti spasi dan diubah jadi title case dengan `ucwords()`

4. **Struktur Action Mirror**:

   - Action menggunakan routing yang sama dengan view
   - View: `index.php?hal=users_user-management` → `pages/users/user-management.php`
   - Action: `actions/index.php?hal=users_user-management` → `actions/pages/users/user-management.php`

5. **Include File Otomatis**:
   - File langsung di-include dari folder sesuai path yang sudah dikonversi
   - Tidak perlu routing table atau switch-case

### 📊 Contoh Mapping URL:

| URL View                               | File View                          | URL Action                                     | File Action                                |
| -------------------------------------- | ---------------------------------- | ---------------------------------------------- | ------------------------------------------ |
| `index.php`                            | `pages/dashboard.php`              | `actions/index.php`                            | `actions/pages/dashboard.php`              |
| `index.php?hal=profile`                | `pages/profile.php`                | `actions/index.php?hal=profile`                | `actions/pages/profile.php`                |
| `index.php?hal=users_user-management`  | `pages/users/user-management.php`  | `actions/index.php?hal=users_user-management`  | `actions/pages/users/user-management.php`  |
| `index.php?hal=admin_settings_general` | `pages/admin/settings/general.php` | `actions/index.php?hal=admin_settings_general` | `actions/pages/admin/settings/general.php` |

---

## 📂 Sistem Menu Sidebar

File `sidebar.php` menggunakan sistem variabel untuk memudahkan pengelolaan menu dan menandai menu aktif.

### Struktur Menu di `sidebar.php`

```php
<?php
$getHal = sani($_GET['hal'] ?? 'dashboard');
?>

<!-- Contoh Menu Item -->
<?php
$sidebarPage = "users_user-management";
?>
<li class="sidebar-item <?= ($getHal == $sidebarPage) ? "active" : "" ?>">
    <a href="?hal=<?php echo $sidebarPage; ?>" class='sidebar-link'>
        <i class="bi bi-people-fill"></i>
        <span>User Management</span>
    </a>
</li>
```

### 🎯 Keuntungan Sistem Variabel:

1. **Mudah Dikelola**: Variabel `$sidebarPage` bisa di-reuse untuk class active dan href
2. **Konsisten**: Satu perubahan di variabel, otomatis update semua referensi
3. **Active State Otomatis**: Menu akan otomatis mendapat class `active` jika sedang dibuka
4. **Clean Code**: Tidak perlu duplikasi string URL berkali-kali
5. **Easy Maintenance**: Ganti URL cukup edit 1 tempat

### 📝 Cara Menambah Menu Baru:

```php
<?php
$sidebarPage = "products_product-list";
?>
<li class="sidebar-item <?= ($getHal == $sidebarPage) ? "active" : "" ?>">
    <a href="?hal=<?php echo $sidebarPage; ?>" class='sidebar-link'>
        <i class="bi bi-box-seam"></i>
        <span>Product List</span>
    </a>
</li>
```

---

## 🛠️ Generator File Otomatis (generate.php)

Script CLI untuk membuat file page dan action secara otomatis dengan dukungan nested folder.

### Cara Menggunakan:

```bash
# Format dasar
php generate.php nama-file

# Contoh file tanpa subfolder
php generate.php profile

# Contoh file dengan subfolder
php generate.php users/user-management
php generate.php admin/settings/general
```

### ✅ Apa yang Dilakukan Generator:

1. **Membuat File View di `pages/`**

   - Template HTML dengan Bootstrap card
   - Sudah include title dan struktur dasar
   - Otomatis membuat subfolder jika diperlukan

2. **Membuat File Action di `actions/pages/`**

   - Template action handler dengan session
   - Include sanitasi input dan secure query
   - Redirect otomatis setelah action
   - Otomatis membuat subfolder jika diperlukan

3. **Penanganan Folder Otomatis**

   - Jika nama file mengandung `/` (slash), otomatis membuat folder
   - Contoh: `users/profile` → membuat folder `users/` lalu file `profile.php` di dalamnya

4. **Feedback Visual**
   - Warna hijau untuk sukses
   - Warna merah jika file sudah ada
   - Menampilkan URL akses di akhir

### 📤 Contoh Output:

```bash
$ php generate.php users/user-management

=== PHP File Generator ===
Generating files for: users/user-management

✓ Folder created: pages\users
✓ Page created: pages\users\user-management.php
✓ Folder created: actions\pages\users
✓ Action created: actions\pages\users\user-management.php

✓ Generation completed!
Page URL: index.php?hal=users_user-management
Action URL: actions/index.php?hal=users_user-management
```

### 📄 Template yang Dihasilkan:

#### File Page (`pages/users/user-management.php`):

```php
<?php
/**
 * Page: users/user-management
 * Created: 2025-12-11 10:30:00
 */
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">User Management</h4>
            </div>
            <div class="card-body">
                <p>Content for users/user-management</p>
            </div>
        </div>
    </div>
</div>
```

#### File Action (`actions/pages/users/user-management.php`):

```php
<?php
/**
 * Action: users/user-management
 * Created: 2025-12-11 10:30:00
 */

session_start();
include '../../functions/sanitasi.php';
include '../../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    // $data = sani($_POST['field_name']);

    // Process your logic here

    // Redirect back with message
    $_SESSION['message'] = 'Action completed successfully!';
    $_SESSION['message_type'] = 'success';

    header('Location: ../../index.php?hal=users_user-management');
    exit;
} else {
    header('Location: ../../index.php');
    exit;
}
```

---

## 🔒 Secure Query dengan Prepared Statement

Framework ini menggunakan **prepared statement** untuk mencegah SQL injection dan membuat query lebih aman.

### File: `functions/secure_query.php`

Berisi 2 fungsi utama:

#### 1. `querySecure()` - Untuk SELECT

```php
function querySecure($con, $query, $params = [], $types = '')
```

**Parameter:**

- `$con`: Koneksi database
- `$query`: Query SQL dengan placeholder `?`
- `$params`: Array parameter untuk binding
- `$types`: String tipe data (s=string, i=integer, d=double, b=blob)

**Contoh Penggunaan:**

```php
// SELECT dengan WHERE
$email = 'user@example.com';
$result = querySecure($con,
    "SELECT * FROM users WHERE email = ?",
    [$email],
    's'
);

if ($result) {
    $user = mysqli_fetch_assoc($result);
    echo $user['name'];
}

// SELECT dengan multiple parameters
$search = '%john%';
$minAge = 18;
$maxAge = 50;

$result = querySecure($con,
    "SELECT * FROM users WHERE name LIKE ? AND age BETWEEN ? AND ?",
    [$search, $minAge, $maxAge],
    'sii'  // s=string, i=integer, i=integer
);

while ($row = mysqli_fetch_assoc($result)) {
    echo $row['name'] . ' - ' . $row['age'];
}
```

#### 2. `executeSecure()` - Untuk INSERT/UPDATE/DELETE

```php
function executeSecure($con, $query, $params = [], $types = '')
```

**Return:**

- `int`: Last insert ID untuk INSERT
- `bool`: True/False untuk UPDATE/DELETE

**Contoh Penggunaan:**

```php
// INSERT data
$name = 'John Doe';
$email = 'john@example.com';
$age = 25;

$lastId = executeSecure($con,
    "INSERT INTO users (name, email, age, created_at) VALUES (?, ?, ?, NOW())",
    [$name, $email, $age],
    'ssi'  // s=string, s=string, i=integer
);

if ($lastId) {
    echo "Data berhasil disimpan dengan ID: " . $lastId;
}

// UPDATE data
$name = 'John Updated';
$userId = 1;

$success = executeSecure($con,
    "UPDATE users SET name = ?, updated_at = NOW() WHERE id = ?",
    [$name, $userId],
    'si'
);

if ($success) {
    echo "Data berhasil diupdate";
}

// DELETE data
$userId = 1;

$success = executeSecure($con,
    "DELETE FROM users WHERE id = ?",
    [$userId],
    'i'
);

if ($success) {
    echo "Data berhasil dihapus";
}
```

### 📋 Tipe Data untuk Binding:

| Tipe | Keterangan    |
| ---- | ------------- |
| `s`  | String        |
| `i`  | Integer       |
| `d`  | Double/Float  |
| `b`  | Blob (Binary) |

**Contoh kombinasi:**

- `'ssi'` = string, string, integer
- `'isi'` = integer, string, integer
- `'sssii'` = string, string, string, integer, integer

---

## 🎯 Cara Penggunaan Action yang Benar

Action adalah file handler yang memproses data dari form (POST) dan melakukan operasi CRUD ke database.

### 📍 Struktur Action File

File action terletak di: `actions/pages/{module}/{feature}.php`

### 🔄 Flow Lengkap: GET → POST → UPDATE

#### 1️⃣ Halaman View (GET) - `pages/users/user-form.php`

```php
<?php
// Cek apakah mode edit atau tambah
$userId = isset($_GET['id']) ? sani($_GET['id']) : null;
$userData = null;

if ($userId) {
    // Mode EDIT - ambil data user
    $result = querySecure($con,
        "SELECT * FROM users WHERE id = ?",
        [$userId],
        'i'
    );
    $userData = mysqli_fetch_assoc($result);
}
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4><?= $userId ? 'Edit User' : 'Tambah User' ?></h4>
            </div>
            <div class="card-body">
                <form action="actions/index.php?hal=users_user-form" method="POST">
                    <!-- Hidden input untuk ID (jika edit) -->
                    <?php if ($userId): ?>
                        <input type="hidden" name="id" value="<?= $userData['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label>Nama</label>
                        <input type="text" name="name" class="form-control"
                               value="<?= $userData['name'] ?? '' ?>" required>
                    </div>

                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control"
                               value="<?= $userData['email'] ?? '' ?>" required>
                    </div>

                    <div class="mb-3">
                        <label>Umur</label>
                        <input type="number" name="age" class="form-control"
                               value="<?= $userData['age'] ?? '' ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <?= $userId ? 'Update' : 'Simpan' ?>
                    </button>
                    <a href="?hal=users_user-list" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
```

#### 2️⃣ Action Handler (POST) - `actions/pages/users/user-form.php`

```php
<?php
/**
 * Action: users/user-form
 * Handle INSERT dan UPDATE user
 */

session_start();
include '../../functions/sanitasi.php';
include '../../config.php';

// Cek apakah form di-submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitasi input
    $id = isset($_POST['id']) ? sani($_POST['id']) : null;
    $name = sani($_POST['name']);
    $email = sani($_POST['email']);
    $age = sani($_POST['age']);

    // Validasi input
    if (empty($name) || empty($email) || empty($age)) {
        $_SESSION['message'] = 'Semua field harus diisi!';
        $_SESSION['message_type'] = 'error';
        header('Location: ../../index.php?hal=users_user-form' . ($id ? '&id='.$id : ''));
        exit;
    }

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = 'Format email tidak valid!';
        $_SESSION['message_type'] = 'error';
        header('Location: ../../index.php?hal=users_user-form' . ($id ? '&id='.$id : ''));
        exit;
    }

    if ($id) {
        // MODE UPDATE
        $success = executeSecure($con,
            "UPDATE users SET name = ?, email = ?, age = ?, updated_at = NOW() WHERE id = ?",
            [$name, $email, $age, $id],
            'ssii'  // string, string, integer, integer
        );

        if ($success) {
            $_SESSION['message'] = 'Data user berhasil diupdate!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Gagal mengupdate data user!';
            $_SESSION['message_type'] = 'error';
        }

    } else {
        // MODE INSERT

        // Cek apakah email sudah ada
        $checkEmail = querySecure($con,
            "SELECT id FROM users WHERE email = ?",
            [$email],
            's'
        );

        if (mysqli_num_rows($checkEmail) > 0) {
            $_SESSION['message'] = 'Email sudah terdaftar!';
            $_SESSION['message_type'] = 'error';
            header('Location: ../../index.php?hal=users_user-form');
            exit;
        }

        // Insert data baru
        $lastId = executeSecure($con,
            "INSERT INTO users (name, email, age, created_at) VALUES (?, ?, ?, NOW())",
            [$name, $email, $age],
            'ssi'  // string, string, integer
        );

        if ($lastId) {
            $_SESSION['message'] = 'Data user berhasil ditambahkan!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Gagal menambahkan data user!';
            $_SESSION['message_type'] = 'error';
        }
    }

    // Redirect ke halaman list
    header('Location: ../../index.php?hal=users_user-list');
    exit;

} else {
    // Jika diakses langsung tanpa POST, redirect ke homepage
    header('Location: ../../index.php');
    exit;
}
```

#### 3️⃣ Action Delete - `actions/pages/users/user-delete.php`

```php
<?php
/**
 * Action: users/user-delete
 * Handle DELETE user
 */

session_start();
include '../../functions/sanitasi.php';
include '../../config.php';

// Cek apakah ada ID
if (isset($_GET['id'])) {
    $id = sani($_GET['id']);

    // Cek apakah user ada
    $checkUser = querySecure($con,
        "SELECT id FROM users WHERE id = ?",
        [$id],
        'i'
    );

    if (mysqli_num_rows($checkUser) > 0) {
        // Hapus user
        $success = executeSecure($con,
            "DELETE FROM users WHERE id = ?",
            [$id],
            'i'
        );

        if ($success) {
            $_SESSION['message'] = 'Data user berhasil dihapus!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Gagal menghapus data user!';
            $_SESSION['message_type'] = 'error';
        }
    } else {
        $_SESSION['message'] = 'User tidak ditemukan!';
        $_SESSION['message_type'] = 'error';
    }

} else {
    $_SESSION['message'] = 'ID user tidak valid!';
    $_SESSION['message_type'] = 'error';
}

// Redirect ke halaman list
header('Location: ../../index.php?hal=users_user-list');
exit;
```

#### 4️⃣ Halaman List dengan Tombol Action - `pages/users/user-list.php`

```php
<?php
// Ambil semua data user
$query = "SELECT * FROM users ORDER BY created_at DESC";
$result = querySecure($con, $query);
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Daftar User</h4>
                <a href="?hal=users_user-form" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Tambah User
                </a>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?= $_SESSION['message_type'] == 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
                        <?= $_SESSION['message'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Umur</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            while($row = mysqli_fetch_assoc($result)):
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= $row['age'] ?></td>
                                    <td>
                                        <a href="?hal=users_user-form&id=<?= $row['id'] ?>"
                                           class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <a href="actions/index.php?hal=users_user-delete&id=<?= $row['id'] ?>"
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Yakin ingin menghapus?')">
                                            <i class="bi bi-trash"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
```

### 🔑 Poin Penting Action:

1. **Selalu gunakan `session_start()`** di awal file action
2. **Include file yang diperlukan**:
   - `../../functions/sanitasi.php` - untuk sanitasi input
   - `../../config.php` - untuk koneksi database
3. **Cek REQUEST_METHOD** untuk memastikan form di-submit dengan POST
4. **Sanitasi semua input** dengan fungsi `sani()`
5. **Validasi data** sebelum simpan ke database
6. **Gunakan secure query** dengan `querySecure()` dan `executeSecure()`
7. **Set session message** untuk feedback ke user
8. **Redirect dengan header()** setelah proses selesai
9. **Selalu gunakan `exit`** setelah redirect

---

## 📝 Workflow Pengembangan Lengkap

### 1. Generate File Baru

```bash
php generate.php users/user-management
```

### 2. Tambahkan Menu di Sidebar

Edit `sidebar.php`:

```php
<?php $sidebarPage = "users_user-management"; ?>
<li class="sidebar-item <?= ($getHal == $sidebarPage) ? "active" : "" ?>">
    <a href="?hal=<?php echo $sidebarPage; ?>" class='sidebar-link'>
        <i class="bi bi-people"></i>
        <span>User Management</span>
    </a>
</li>
```

### 3. Kembangkan Halaman View

Edit `pages/users/user-management.php` sesuai kebutuhan

### 4. Implementasi Action Handler

Edit `actions/pages/users/user-management.php` untuk handle CRUD

### 5. Test Aplikasi

- Buka `index.php?hal=users_user-management`
- Test form submit ke `actions/index.php?hal=users_user-management`

---

## ✨ Keunggulan Framework

- ✅ **No Routing Configuration**: Tidak perlu setup routing manual
- ✅ **Auto Title Generation**: Title halaman otomatis dari URL
- ✅ **CLI Generator**: Buat file cepat dengan 1 command
- ✅ **Nested Folder Support**: Organisasi file dengan subfolder
- ✅ **Clean URL Pattern**: URL menggunakan underscore, path menggunakan slash
- ✅ **Variable-Based Menu**: Menu sidebar mudah dikelola dengan variabel
- ✅ **Secure Query**: Prepared statement untuk mencegah SQL injection
- ✅ **Template Ready**: File yang digenerate sudah include template Bootstrap
- ✅ **Mirror Structure**: View dan Action memiliki struktur yang sama

---

## 🔒 Keamanan

- ✅ Semua input dari `$_GET` dan `$_POST` melalui fungsi `sani()`
- ✅ Query database menggunakan prepared statement
- ✅ Session management untuk autentikasi dan flash messages
- ✅ Action files cek request method sebelum proses data
- ✅ Charset `utf8mb4` untuk mencegah SQL injection via charset
- ✅ Validasi input sebelum simpan ke database
- ✅ HTML escaping dengan `htmlspecialchars()`

---

## 📞 Support

Untuk pertanyaan atau issue, silakan hubungi developer.

**Happy Coding! 🚀**
