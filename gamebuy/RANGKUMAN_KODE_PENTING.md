# DOKUMENTASI TEKNIS SISTEM GAMERENT
**Mata Kuliah:** Pemrograman Web Lanjut  
**Topik:** Sistem Penyewaan & Pembelian Game Digital  

---

## DAFTAR ISI DAN PEMBAGIAN TUGAS
Dokumen ini disusun sebagai panduan teknis logika pemrograman yang digunakan dalam aplikasi.

1.  **Helper & Konfigurasi Dasar** (Tim Bersama)
2.  **Modul Autentikasi Pengguna** (Mahasiswa B)
3.  **Modul Manajemen Inventori Game** (Mahasiswa A)
4.  **Modul Keuangan Digital & Voucher** (Mahasiswa C)
5.  **Modul Transaksi & Sirkulasi Barang** (Mahasiswa D)

---

## BAGIAN 1: KONFIGURASI DASAR & HELPER
**Status:** Fondasi Aplikasi | **Lokasi:** Root & Folder `config/`

### 1.1 Koneksi Database (`config/database.php`)
Ini adalah jembatan utama antara PHP dan MySQL. Script ini akan mematikan proses jika koneksi gagal agar error tidak terekspos ke pengguna.

```php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "datagame";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}
```

### 1.2 Helper Saldo Real-time (`config/getdata.php`)
Fungsi kecil namun krusial untuk mengambil saldo tekini pengguna di setiap halaman tanpa perlu menulis ulang query SQL berulang kali.

```php
function getUserSaldo($userId, $connection) {
    $query = "SELECT saldo FROM users WHERE id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    // ... pengambilan hasil ...
    return $user ? $user['saldo'] : 0;
}
```

### 1.3 Routing Sederhana (`index.php`)
Berfungsi sebagai gerbang logika. File ini tidak memiliki tampilan, tugasnya hanya mengecek status login dan mengarahkan pengguna ke folder yang benar.

```php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
} else {
    // Arahkan admin ke folder admin, user ke folder user
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: user/dashboard.php");
    }
}
```

---

## BAGIAN 2: MODUL AUTENTIKASI (MAHASISWA B)
**Tanggung Jawab:** Keamanan Akun, Login, Register, Manajemen Profil.

### 2.1 Enkripsi Saat Pendaftaran (`register.php`)
Fitur keamanan utama: Password tidak boleh disimpan mentah. Kita menggunakan algoritma `BCRYPT` standar industri.

```php
// Enkripsi password sebelum Insert ke database
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$query = "INSERT INTO users (username, password, role, saldo) 
          VALUES ('$username', '$hashed_password', 'user', 0)";
```

### 2.2 Verifikasi Login (`login.php`)
Proses pencocokan password inputan user dengan hash acak di database menggunakan fungsi `password_verify()`.

```php
$query = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 1) {
    $row = mysqli_fetch_assoc($result);
    
    // VERIFIKASI HASH (Penting)
    if (password_verify($password, $row['password'])) {
        // Set sesi server
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['role']    = $row['role']; 
    }
}
```

### 2.3 Ganti Password Aman (`proses_ubah_password.php`)
Mencegah pembajakan akun dengan mewajibkan user memasukkan password lama sebelum membuat password baru.

```php
// 1. Validasi Password Lama
if (password_verify($password_lama, $data_user['password'])) {
    
    // 2. Hash Password Baru
    $hash_baru = password_hash($password_baru, PASSWORD_DEFAULT);
    
    // 3. Update Database
    mysqli_query($conn, "UPDATE users SET password='$hash_baru' WHERE id='$id'");
}
```

---

## BAGIAN 3: MODUL INVENTORI GAME (MAHASISWA A)
**Tanggung Jawab:** Master Data, Upload Gambar, Etalase Produk.

### 3.1 Upload Cover Game (`admin/games.php`)
Fungsi PHP untuk menangani file upload, mengecek ekstensi jahat, dan mengganti nama file agar unik (menghindari nama file kembar).

```php
function uploadGameImage($fieldName, $uploadDir) {
    // Validasi Ekstensi (Hanya File Gambar)
    $ext = strtolower(pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION));
    
    // Generate Nama Unik (Timestamp + Random Number)
    $new_name = 'game_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
    
    // Pindahkan file dari Temp Storage ke Folder Public
    move_uploaded_file($_FILES[$fieldName]['tmp_name'], $uploadDir . $new_name);
}
```

### 3.2 Etalase Acak (`user/dashboard.php`)
Menampilkan game secara acak (`RAND()`) agar user tidak bosan melihat game yang sama terus menerus di halaman depan. Hanya menampilkan game yang stoknya tersedia.

```php
// Query menampilkan game yang stok > 0
$query = "SELECT * FROM games WHERE stok > 0 ORDER BY RAND()";
$result = mysqli_query($conn, $query);
```

---

## BAGIAN 4: MODUL KEUANGAN & VOUCHER (MAHASISWA C)
**Tanggung Jawab:** Top Up Saldo, Logika Voucher Diskon, API Perhitungan.

### 4.1 Cek Voucher Tanpa Reload / AJAX (`user/topup.php`)
Menggunakan Javascript `Fetch API` untuk berkomunikasi dengan server di latar belakang. Memberikan pengalaman pengguna yang mulus (Seamless).

```javascript
// Request asinkron ke server
fetch('../api_cek_voucher.php?kode=' + inputKode)
    .then(response => response.json())
    .then(data => {
        // Update UI secara instan jika sukses
        if (data.status === 'success') {
            tampilkanHargaDiskon(data.potongan);
        } else {
            tampilkanError(data.message);
        }
    });
```

### 4.2 API Logika Diskon (`api_cek_voucher.php`)
Backend yang memproses request AJAX di atas. Menangani logika matematika Persen vs Potongan Tetap.

```php
// Logika Backend
if ($voucher['tipe'] == 'fixed') {
    // Potongan tetap (Cth: Potongan Rp 10.000)
    $potongan = $voucher['potongan'];
} else {
    // Potongan persen (Cth: 20% dari Nominal)
    $hitung = $nominal * ($voucher['potongan'] / 100);
    
    // Capping: Jika hasil diskon melebihi batas maksimal, batasi.
    $potongan = ($hitung > $max_potongan) ? $max_potongan : $hitung;
}
```

---

## BAGIAN 5: MODUL TRANSAKSI & SIRKULASI (MAHASISWA D)
**Tanggung Jawab:** Proses Sewa/Beli, Pengurangan Stok, Pencatatan Riwayat.

### 5.1 Transaksi Tunggal Atomik (`proses.php`)
Menggunakan fitur **Database Transaction (ACID)**. Ini adalah bagian paling kritis untuk menjaga konsistensi data uang dan barang.

```php
// Mulai Transaksi (Lock Database)
mysqli_begin_transaction($conn);

try {
    // Langkah 1: Potong Saldo User
    mysqli_query($conn, "UPDATE users SET saldo = saldo - $harga ...");

    // Langkah 2: Kurangi Stok Game
    mysqli_query($conn, "UPDATE games SET stok = stok - 1 ...");

    // Langkah 3: Catat Invoice
    mysqli_query($conn, "INSERT INTO transactions (...) VALUES (...)");

    // Jika semua langkah sukses, simpan permanen (Commit)
    mysqli_commit($conn);

} catch (Exception $e) {
    // Jika ada satu langkah gagal, batalkan SEMUA (Rollback)
    // Uang user kembali, stok tidak berkurang.
    mysqli_rollback($conn);
    die("Transaksi Dibatalkan: " . $e->getMessage());
}
```

### 5.2 Menampilkan Library User (`user/library.php`)
Teknik menggabungkan tabel `transactions` dan tabel `games` menggunakan `JOIN` agar user bisa melihat detail game yang pernah dia beli.

```php
// JOIN Query
$query = "SELECT t.*, g.judul, g.gambar 
          FROM transactions t 
          JOIN games g ON t.game_id = g.id 
          WHERE t.user_id = '$id_user_login'";
```
