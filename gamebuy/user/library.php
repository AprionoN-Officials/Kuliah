<?php
session_start();
include '../config/database.php';
include '../config/getdata.php';

// 1. Cek Login (Halaman ini KHUSUS User)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Proteksi: Redirect admin ke dashboard admin
if ($_SESSION['role'] === 'admin') {
    header("Location: ../admin/dashboard.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil transaksi beserta info game (termasuk gambar)
$query = "SELECT t.*, g.judul, g.genre, g.gambar 
          FROM transactions t 
          JOIN games g ON t.game_id = g.id 
          WHERE t.user_id = '$user_id' 
          ORDER BY t.tanggal_pinjam DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Library Saya - GameRent</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../aset/style.css">
    
    <style>
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            color: white;
            margin-top: 5px;
        }
        .bg-success { background-color: #2ecc71; } /* Hijau */
        .bg-warning { background-color: #f1c40f; color: #333; } /* Kuning */
        .bg-danger { background-color: #e74c3c; } /* Merah */
        
        /* Efek visual jika expired */
        .card-expired { opacity: 0.6; filter: grayscale(80%); }
    </style>
</head>
<body>

    <?php include '../aset/sidebar.php'; ?>

    <main class="main-content">
        
        <header class="top-bar">
            <h2><i class="fas fa-book-open"></i> Library Saya</h2>
        </header>

        <section>
            <?php if (mysqli_num_rows($result) == 0): ?>
                <div style="text-align: center; padding: 50px; color: #888;">
                    <i class="fas fa-ghost" style="font-size: 50px; margin-bottom: 20px;"></i>
                    <p>Library kamu masih kosong.</p>
                    <a href="dashboard.php" class="btn btn-primary" style="margin-top: 10px;">Cari Game Yuk!</a>
                </div>
            <?php else: ?>

                <div class="game-grid">
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        
                        <?php
                            $status_label = "";
                            $badge_class = "";
                            $is_expired = false;

                            if ($row['tipe_transaksi'] == 'beli') {
                                $status_label = "Milik Permanen";
                                $badge_class = "bg-success";
                            } else {
                                $tgl_kembali = new DateTime($row['tanggal_kembali']);
                                $tgl_sekarang = new DateTime();
                                
                                if ($tgl_sekarang > $tgl_kembali) {
                                    $status_label = "Masa Sewa Habis";
                                    $badge_class = "bg-danger";
                                    $is_expired = true;
                                } else {
                                    $selisih = $tgl_sekarang->diff($tgl_kembali);
                                    $status_label = "Sisa: " . $selisih->days . " Hari " . $selisih->h . " Jam";
                                    $badge_class = "bg-warning";
                                }
                            }

                            $imgSrc = resolveGameImage($row);
                        ?>

                        <div class="game-card <?= $is_expired ? 'card-expired' : '' ?>">
                            <img src="<?= $imgSrc; ?>" class="game-img" alt="<?= htmlspecialchars($row['judul']); ?>">
                            
                            <div class="card-body">
                                <div class="game-title"><?= htmlspecialchars($row['judul']); ?></div>
                                <span class="game-genre"><?= htmlspecialchars($row['genre']); ?></span>
                                
                                <div style="margin-top: 10px;">
                                    <span class="badge <?= $badge_class ?>">
                                        <?= $status_label; ?>
                                    </span>
                                </div>

                                <?php if (!$is_expired): ?>
                                    <button class="btn btn-primary btn-block" style="margin-top: 15px;">
                                        <i class="fas fa-bookmark"></i> Sudah Dimiliki
                                    </button>
                                <?php else: ?>
                                    <a href="detail.php?id=<?= $row['game_id']; ?>" class="btn btn-block" style="margin-top: 15px; border: 1px solid #ccc;">
                                        Sewa Lagi
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                    <?php endwhile; ?>
                </div>

            <?php endif; ?>
        </section>

    </main>
</body>
</html>