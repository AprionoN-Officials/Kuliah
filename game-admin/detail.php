<?php
session_start();
include 'config/database.php';

// 1. Cek apakah ada ID di URL?
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id_game = $_GET['id'];

// 2. Ambil data game dari database
$query = "SELECT * FROM games WHERE id = '$id_game'";
$result = mysqli_query($conn, $query);
$game = mysqli_fetch_assoc($result);

// Jika game tidak ditemukan (misal ID ngawur)
if (!$game) {
    echo "Game tidak ditemukan!";
    exit;
}

// Cek Login
$is_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail <?= htmlspecialchars($game['judul']); ?> - GameRent</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="aset/style.css">
</head>
<body>

    <?php include 'aset/sidebar.php'; ?>

    <main class="main-content">
        
        <a href="index.php" class="btn" style="margin-bottom: 20px; color: var(--text-grey);">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>

        <div class="detail-container">
            
            <div class="left-side">
                <?php 
                    $nama_file = strtolower(str_replace(' ', '_', $game['judul'])) . ".jpg";
                    $path = "aset/images/" . $nama_file;
                    $imgSrc = file_exists($path) ? $path : "aset/images/tes.png";
                ?>
                <img src="<?= $imgSrc; ?>" class="detail-img" alt="Cover Game">
                
                <div style="margin-top: 15px; text-align: center;">
                    Status: 
                    <?php if($game['stok'] > 0): ?>
                        <span style="color: green; font-weight: bold;">Tersedia (<?= $game['stok']; ?> unit)</span>
                    <?php else: ?>
                        <span style="color: red; font-weight: bold;">Habis / Sold Out</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="detail-info">
                <h1><?= htmlspecialchars($game['judul']); ?></h1>
                <span class="detail-genre"><?= htmlspecialchars($game['genre']); ?></span>
                
                <p class="detail-desc">
                    <?= nl2br(htmlspecialchars($game['deskripsi'] ?? 'Belum ada deskripsi untuk game ini.')); ?>
                </p>

                <div class="price-box">
                    <div class="price-row">
                        <span>Harga Sewa:</span>
                        <span class="price-nominal">Rp <?= number_format($game['harga_sewa']); ?> / hari</span>
                    </div>
                    <div class="price-row">
                        <span>Harga Beli Permanen:</span>
                        <span class="price-nominal">Rp <?= number_format($game['harga_beli']); ?></span>
                    </div>
                </div>

                <div class="transaksi-form">
                    <?php if ($is_logged_in): ?>
                        
                        <?php if ($game['stok'] > 0): ?>
                            <form action="proses.php" method="POST">
                                <input type="hidden" name="game_id" value="<?= $game['id']; ?>">
                                
                                <div class="form-group">
                                    <label class="form-label">Pilih Jenis Transaksi:</label>
                                    <select name="tipe" id="tipeSelect" class="form-control" onchange="cekTipe()" required>
                                        <option value="sewa">Sewa (Harian)</option>
                                        <option value="beli">Beli Permanen</option>
                                    </select>
                                </div>

                                <div class="form-group" id="durasiBox">
                                    <label class="form-label">Durasi Sewa (Hari):</label>
                                    <input type="number" name="durasi" min="1" max="30" value="1" class="form-control">
                                    <small style="color: #888;">Biaya: Rp <span id="estimasiBiaya"><?= number_format($game['harga_sewa']); ?></span></small>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block" onclick="return confirm('Pastikan saldo Anda cukup. Lanjutkan?')">
                                    <i class="fas fa-shopping-cart"></i> Proses Sekarang
                                </button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-block" disabled style="background: #ccc; cursor: not-allowed;">Stok Habis</button>
                        <?php endif; ?>

                    <?php else: ?>
                        <div style="text-align: center; padding: 20px; background: #fff3cd; border-radius: 5px; color: #856404;">
                            <i class="fas fa-lock"></i> Silakan <b>Login</b> untuk meminjam game ini.
                            <br><br>
                            <a href="login.php" class="btn btn-primary">Login Sekarang</a>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>

    </main>

    <script>
        function cekTipe() {
            var tipe = document.getElementById("tipeSelect").value;
            var durasiBox = document.getElementById("durasiBox");
            
            // Jika pilih beli, sembunyikan input durasi
            if (tipe === "beli") {
                durasiBox.style.display = "none";
            } else {
                durasiBox.style.display = "block";
            }
        }
    </script>
</body>
</html>