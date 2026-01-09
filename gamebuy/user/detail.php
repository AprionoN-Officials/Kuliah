<?php
session_start();
include '../config/database.php';
include '../config/getdata.php';

// Mode preview admin: admin boleh lihat, tapi tidak bisa memesan
$preview_param = (($_GET['preview'] ?? null) === 'user') ? 'user' : null;
$is_admin = isset($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'admin';
$is_preview = $is_admin && $preview_param !== null;

// 1. Cek apakah ada ID di URL?
if (!isset($_GET['id'])) {
    header("Location: ../index.php");
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
$can_order = $is_logged_in && !$is_preview;

// Cek apakah user sudah memiliki permanen
$has_permanent = false;
if ($is_logged_in) {
    $uid = $_SESSION['user_id'];
    $owned_q = mysqli_query($conn, "SELECT id FROM transactions WHERE user_id='$uid' AND game_id='$id_game' AND status='permanent' LIMIT 1");
    if ($owned_q && mysqli_num_rows($owned_q) > 0) {
        $has_permanent = true;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail <?= htmlspecialchars($game['judul']); ?> - GameRent</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../aset/style.css">
    <style>
        .modal-confirm {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1200;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .modal-box {
            background: white;
            border-radius: 10px;
            padding: 20px;
            max-width: 420px;
            width: 100%;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        .modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 18px; }
    </style>
</head>
<body>

    <?php include '../aset/sidebar.php'; ?>

    <main class="main-content">

        <?php if ($is_preview): ?>
        <div class="alert" style="background:#e8f4ff;border:1px solid #b6d7ff;color:#0a3d62;margin-bottom:15px;">
            Mode Preview Admin — pemesanan dinonaktifkan. <a href="../admin/dashboard.php" style="color:#0a3d62;text-decoration:underline;">Kembali ke dashboard admin</a>
        </div>
        <?php endif; ?>
        
        <a href="index.php<?= $is_preview ? '?preview=user' : '' ?>" class="btn" style="margin-bottom: 20px; color: var(--text-grey);">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>

        <div class="detail-container">
            <div class="left-side">
                <?php 
                    $imgSrc = resolveGameImage($game);
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
                    <?php if ($is_preview): ?>
                        <div style="text-align: center; padding: 20px; background: #e8f4ff; border-radius: 5px; color: #0a3d62;">
                            <i class="fas fa-eye"></i> Ini hanya preview, pemesanan dinonaktifkan.
                        </div>
                    <?php elseif ($can_order): ?>
                        <?php if ($game['stok'] > 0): ?>
                            <?php if ($has_permanent): ?>
                                <div style="margin-bottom:12px; padding:12px; background:#fff7e6; border:1px solid #ffd591; border-radius:8px; color:#ad6800;">
                                    Anda sudah memiliki game ini secara permanen. Lanjutkan jika tetap ingin sewa/beli lagi.
                                </div>
                            <?php endif; ?>
                            <form id="trxForm" action="../proses.php" method="POST">
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

                                <button type="button" class="btn btn-primary btn-block" onclick="handleSubmitTransaksi()">
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
                            <a href="../login.php" class="btn btn-primary">Login Sekarang</a>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>

        <?php if ($can_order): ?>
        <div id="warnModal" class="modal-confirm" onclick="if(event.target.id==='warnModal') cancelProceed();">
            <div class="modal-box">
                <h3 style="margin-top:0;">Anda sudah memiliki game ini</h3>
                <p style="color:#555;">Game ini sudah dimiliki secara permanen. Tetap lanjut sewa/beli lagi?</p>
                <div class="modal-actions">
                    <button class="btn" style="background:#e0e0e0;" onclick="cancelProceed()">Batal</button>
                    <button class="btn btn-primary" onclick="confirmProceed()">Lanjut</button>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </main>

    <script>
        const hasPermanent = <?= $has_permanent ? 'true' : 'false' ?>;

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

        function handleSubmitTransaksi() {
            if (hasPermanent) {
                document.getElementById('warnModal').style.display = 'flex';
            } else {
                document.getElementById('trxForm').submit();
            }
        }

        function confirmProceed() {
            document.getElementById('warnModal').style.display = 'none';
            document.getElementById('trxForm').submit();
        }

        function cancelProceed() {
            document.getElementById('warnModal').style.display = 'none';
        }
    </script>
</body>
</html>