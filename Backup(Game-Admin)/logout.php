<?php
session_start();

// 1. Kosongkan semua variabel session
$_SESSION = [];

// 2. Hapus session dari memori server
session_unset();
session_destroy();

// 3. Tampilkan pesan dan alihkan ke halaman login
echo "<script>
        alert('Anda berhasil keluar (Logout).');
        window.location = 'login.php';
      </script>";
exit;
?>