<?php
include 'config/database.php';

$message = "";

// Jika tombol daftar ditekan
if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    // Validasi sederhana
    if ($password !== $confirm) {
        $message = "Password konfirmasi tidak cocok!";
    } else {
        // Cek apakah username sudah ada?
        $cek_user = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
        if (mysqli_num_rows($cek_user) > 0) {
            $message = "Username sudah terdaftar, cari yang lain!";
        } else {
            // Enkripsi Password (Wajib untuk keamanan)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Masukkan ke Database (Default saldo 0, role user)
            $query = "INSERT INTO users (username, password, role, saldo) VALUES ('$username', '$hashed_password', 'user', 0)";
            
            if (mysqli_query($conn, $query)) {
                echo "<script>alert('Pendaftaran Berhasil! Silakan Login.'); window.location='login.php';</script>";
            } else {
                $message = "Gagal mendaftar: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - GameRental</title>
    <link rel="stylesheet" href="aset/style.css">
</head>
<body class="auth-body">

    <div class="auth-box">
        <h2 class="auth-title">Buat Akun Baru</h2>
        
        <?php if($message): ?>
            <p style="color: red; margin-bottom: 10px; font-size: 14px;"><?= $message; ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required placeholder="Masukkan username unik">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter">
            </div>
            <div class="form-group">
                <label>Konfirmasi Password</label>
                <input type="password" name="confirm_password" class="form-control" required placeholder="Ulangi password">
            </div>
            
            <button type="submit" name="register" class="btn btn-primary btn-block">Daftar Sekarang</button>
        </form>

        <span class="link-alt">
            Sudah punya akun? <a href="login.php">Masuk di sini</a>
        </span>
    </div>

</body>
</html>