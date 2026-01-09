<?php
session_start();
include 'config/database.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['submit_password'])) {
    $id = $_SESSION['user_id'];
    $lama = $_POST['pass_lama'];
    $baru = $_POST['pass_baru'];
    $konf = $_POST['pass_konf'];

    // 1. Cek Password Baru & Konfirmasi
    if ($baru !== $konf) {
        echo "<script>alert('Konfirmasi password baru tidak cocok!'); window.location='user/akun.php';</script>";
        exit;
    }

    // 2. Ambil Password Lama dari Database
    $query = mysqli_query($conn, "SELECT password FROM users WHERE id = '$id'");
    $data = mysqli_fetch_assoc($query);

    // 3. Verifikasi Password Lama (PERBAIKAN DISINI)
    // Kita MENGGUNAKAN password_verify karena register.php Anda memakai password_hash
    if (password_verify($lama, $data['password'])) {
        
        // 4. Enkripsi Password Baru Sebelum Disimpan (PENTING!)
        // Jika tidak di-hash, nanti Anda tidak bisa login lagi
        $password_baru_hash = password_hash($baru, PASSWORD_DEFAULT);

        // 5. Update Database
        $update = mysqli_query($conn, "UPDATE users SET password = '$password_baru_hash' WHERE id = '$id'");
        
        if ($update) {
            echo "<script>alert('Berhasil! Password telah diganti.'); window.location='user/akun.php';</script>";
        } else {
            echo "<script>alert('Gagal menyimpan ke database.'); window.location='user/akun.php';</script>";
        }

    } else {
        // Jika password lama tidak cocok dengan hash di database
        echo "<script>alert('Password lama salah!'); window.location='user/akun.php';</script>";
    }
}
?>