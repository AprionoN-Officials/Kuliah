<?php
session_start();
include 'config/database.php';

// Jika sudah login, lempar ke index
if (isset($_SESSION['user_id'])) {
    header("Location: user/dashboard.php");
    exit;
}

$message = "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Cari user berdasarkan username
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        // Cek Password (Hash Verification)
        if (password_verify($password, $row['password'])) {
            // SUKSES LOGIN: Set Session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role']; // 'admin' atau 'user'

            // Redirect sesuai role
            if ($row['role'] === 'admin') {
                echo "<script>alert('Login Berhasil! Selamat datang Admin'); window.location='admin/dashboard.php';</script>";
            } else {
                echo "<script>alert('Login Berhasil!'); window.location='user/dashboard.php';</script>";
            }
            exit;
        }
    }
    
    $message = "Username atau Password salah!";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GameRental</title>
    <link rel="stylesheet" href="aset/style.css">
</head>
<body class="auth-body">

    <div class="auth-box">
        <h2 class="auth-title">Masuk GameRental</h2>

        <?php if($message): ?>
            <p style="color: red; margin-bottom: 10px; font-size: 14px;"><?= $message; ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            
            <button type="submit" name="login" class="btn btn-primary btn-block">Masuk</button>
        </form>

        <span class="link-alt">
            Belum punya akun? <a href="register.php">Daftar sekarang</a>
        </span>
    </div>

</body>
</html>