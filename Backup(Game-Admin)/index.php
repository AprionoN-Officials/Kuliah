<?php
session_start(); // Wajib paling atas
include 'config/database.php';
include 'config/getdata.php';

// 1. Ambil Data Game dari Database
$query = "SELECT * FROM games WHERE stok > 0";
$result = mysqli_query($conn, $query);

// 2. Cek Status Login dari Session
$is_logged_in = isset($_SESSION['user_id']);
$nama_user = $is_logged_in ? $_SESSION['username'] : '';
$saldo = isset($_SESSION['user_id']) ? "Saldo: Rp " . number_format(getUserSaldo($_SESSION['user_id'], $conn)) : "";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - GameRent</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="aset/style.css"> 
</head>
<body>

    <?php include 'aset/sidebar.php'; ?>

    <main class="main-content">
        
        <header class="top-bar">
            <div class="welcome-text">
                <?php if ($is_logged_in): ?>
                    <h2>Selamat Datang, <b><?= htmlspecialchars($nama_user); ?>!</b></h2>
                <?php else: ?>
                    <h2>Anda Belum Login! Silahkan Login atau Buat Akun Terlebih Dahulu!</h2>
                <?php endif; ?>
            </div>
            
            <div class="user-action">
                <?php if ($is_logged_in): ?>
                    <div class="user-dropdown">
                        
                        <div class="profile-trigger">
                            <div style="text-align: right; font-size: 13px;">
                                <span style="display: block; color: var(--text-grey);">Halo,</span>
                                <span style="font-weight: bold;"><?= htmlspecialchars($nama_user); ?></span><br>
                                <span style="font-weight: bold;"><?= htmlspecialchars($saldo) ?></span>
                            </div>
                            <div class="profile-pic-box">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>

                        <div class="dropdown-menu">
                            <a href="akun.php">
                                <i class="fas fa-user-circle" style="margin-right: 8px;"></i> Akun Saya
                            </a>
                            
                            <a href="library.php">
                                <i class="fas fa-gamepad" style="margin-right: 8px;"></i> Library Saya
                            </a>

                            <a href="logout.php" class="logout-btn" onclick="return confirm('Yakin ingin keluar?');">
                                <i class="fas fa-sign-out-alt" style="margin-right: 8px;"></i> Keluar
                            </a>
                        </div>

                    </div>

                <?php else: ?>
                    <a href="login.php" class="btn btn-primary">
                        Masuk / Daftar
                    </a>
                <?php endif; ?>
            </div>
        </header>

        <section>
            <h3 class="section-title">Promo Spesial</h3>
            
            <div class="game-grid">
                <?php while($game = mysqli_fetch_assoc($result)): ?>
                
                <div class="game-card">
                    <?php 
                        // Logika Gambar (Menggunakan folder 'aset')
                        $nama_file_gambar = strtolower(str_replace(' ', '_', $game['judul'])) . ".jpg";
                        $path_gambar = "aset/images/" . $nama_file_gambar;

                        if (file_exists($path_gambar)) {
                            $imgSrc = $path_gambar;
                        } else {
                            // Gambar default jika file tidak ditemukan
                            $imgSrc = "aset/images/tes.png"; 
                        }
                    ?>
                    
                    <img src="<?= $imgSrc; ?>" class="game-img" alt="<?= htmlspecialchars($game['judul']); ?>">
                    
                    <div class="card-body">
                        <div class="game-title"><?= htmlspecialchars($game['judul']); ?></div>
                        <span class="game-genre"><?= htmlspecialchars($game['genre']); ?></span>
                        
                        <div class="price-tag">
                            Sewa: Rp <?= number_format($game['harga_sewa']); ?>/hari
                        </div>

                        <a href="detail.php?id=<?= $game['id']; ?>" class="btn btn-primary btn-block">
                            Lihat Detail
                        </a>
                    </div>
                </div>

                <?php endwhile; ?>
            </div>
        </section>

    </main>
</body>
</html>