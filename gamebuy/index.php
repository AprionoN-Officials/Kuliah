<?php
session_start();

// 1. Cek apakah user sudah login atau belum
if (!isset($_SESSION['user_id'])) {
    // Kalau belum login, lempar ke halaman login
    header("Location: login.php");
    exit;
}

// 2. Kalau sudah login, cek role-nya (Admin atau User)
if ($_SESSION['role'] === 'admin') {
    // Kalau Admin, lempar ke dashboard admin di dalam folder
    header("Location: admin/dashboard.php");
} else {
    // Kalau User biasa, lempar ke dashboard user di dalam folder
    header("Location: user/dashboard.php");
}
exit;
?>