<?php
$host = "localhost"; 
$user = "root";      
$password = "";      
$database = "pengguna";

$koneksi = mysqli_connect($host, $user, $password, $database);

if (!$koneksi) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}

$username = $_POST['username'];
$password_input = $_POST['password']; 

$username_bersih = mysqli_real_escape_string($koneksi, $username);
$password_bersih = mysqli_real_escape_string($koneksi, $password_input);

$sql = "SELECT * FROM admin WHERE username='$username_bersih' AND password='$password_bersih'";

$result = mysqli_query($koneksi, $sql);

if (mysqli_num_rows($result) > 0) {
    
    session_start();
    $_SESSION['username'] = $username;
    
    echo "<h1>Login Berhasil!</h1>";
    echo "<p>Selamat datang, <b>" . htmlspecialchars($username) . "</b>. Anda telah berhasil masuk!</p>";
    echo "<a href='login.html'>Logout</a> (Biasanya akan diarahkan ke halaman dashboard)";
    
} else {
    echo "<h1>Login Gagal!</h1>";
    echo "<p>Username atau Password Anda salah. Silakan coba lagi.</p>";
    echo "<a href='login.html'>Kembali ke Halaman Login</a>";
}

mysqli_close($koneksi);
?>