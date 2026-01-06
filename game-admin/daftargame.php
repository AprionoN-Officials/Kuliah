<?php 
include "config/database.php";
include "config/getdata.php";
session_start();

// Cek login (opsional, tapi biasanya admin/user harus login)
$is_logged_in = isset($_SESSION['user_id']);

// Proteksi: Redirect admin ke dashboard admin
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
    header("Location: admin_dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Game - GameRent</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="aset/style.css">
</head>
<body>
    <?php include "aset/sidebar.php"; ?>

    <main class="main-content">
        <header class="top-bar">
            <div class="welcome-text">
                <h2>Daftar <b>Game</b></h2>
            </div>
        </header>

        <section>
            <div class="section-title">
                Semua Koleksi Game
            </div>

            <div class="game-grid">
                <?php
                // Menggunakan tabel 'games' sesuai dengan index.php
                $query = "SELECT * FROM games ORDER BY id DESC";
                $result = mysqli_query($conn, $query);
                
                if ($result && mysqli_num_rows($result) > 0) {
                    while($game = mysqli_fetch_assoc($result)):
                        // Logika Gambar (Mendukung berbagai ekstensi)
                        $nama_dasar = strtolower(str_replace(' ', '_', $game['judul']));
                        $ekstensi = ['jpg', 'jpeg', 'png', 'webp'];
                        $path_gambar = "aset/images/tes.png"; // Default

                        foreach ($ekstensi as $ext) {
                            if (file_exists("aset/images/" . $nama_dasar . "." . $ext)) {
                                $path_gambar = "aset/images/" . $nama_dasar . "." . $ext;
                                break;
                            }
                        }
                ?>

                <div class="game-card">
                    <img src="<?= $path_gambar; ?>" class="game-img" alt="<?= htmlspecialchars($game['judul']); ?>">
                    
                    <div class="card-body">
                        <div class="game-title"><?= htmlspecialchars($game['judul']); ?></div>
                        <span class="game-genre"><?= htmlspecialchars($game['genre']); ?></span>
                        
                        <div class="price-tag">
                            Sewa: Rp <?= number_format($game['harga_sewa']); ?> / hari<br>
                            Beli: Rp <?= number_format($game['harga_beli']); ?>
                        </div>

                        <a href="detail.php?id=<?= $game['id']; ?>" class="btn btn-primary btn-block">
                            Lihat Detail
                        </a>
                    </div>
                </div>

                <?php 
                    endwhile; 
                } else {
                    echo "<p style='grid-column: 1/-1; text-align: center; padding: 20px; color: var(--text-grey);'>Belum ada data game atau tabel 'games' tidak ditemukan.</p>";
                }
                ?>
            </div>
        </section>
    </main>
</body>
</html>
