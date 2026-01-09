<?php
session_start();
include '../config/database.php';

// Proteksi admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$trx_sql = "SELECT t.*, g.judul, u.username FROM transactions t
            LEFT JOIN games g ON g.id = t.game_id
            LEFT JOIN users u ON u.id = t.user_id
            ORDER BY t.id DESC";
$trx_result = mysqli_query($conn, $trx_sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Transaksi - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../aset/style.css">
    <style>
        .table-container { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #4facfe; color: white; font-weight: 600; }
        tr:hover { background-color: #f5f5f5; }
        .badge { padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .badge-sewa { background: #ffd166; color: #5c3b00; }
        .badge-beli { background: #43e97b; color: #0f5a2c; }
        .search-box { display:flex; gap:10px; align-items:center; margin-bottom:15px; }
        .search-box input { padding:10px 12px; border:1px solid #ddd; border-radius:8px; width:260px; }
        .search-box button { padding:10px 14px; border:none; background:#4facfe; color:white; border-radius:8px; cursor:pointer; }
        .search-box button:hover { opacity:0.9; }
    </style>
</head>
<body>
    <?php include '../aset/admin_sidebar.php'; ?>
    <main class="main-content">
        <header class="top-bar">
            <div class="welcome-text"><h2>Cek Transaksi</h2></div>
        </header>
        <section>
            <div class="search-box">
                <input type="text" id="trxSearch" placeholder="Masukkan kode transaksi (contoh: TRX-000123)">
                <button onclick="filterTrx()"><i class="fas fa-search"></i> Cari</button>
                <button onclick="resetTrx()" style="background:#e0e0e0;color:#333;"><i class="fas fa-undo"></i> Reset</button>
            </div>
            <div class="table-container">
                <table id="trxTable">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>User</th>
                            <th>Game</th>
                            <th>Tipe</th>
                            <th>Durasi</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($trx_result && mysqli_num_rows($trx_result) > 0): ?>
                            <?php while($trx = mysqli_fetch_assoc($trx_result)): ?>
                                <?php $kode = 'TRX-' . str_pad($trx['id'], 6, '0', STR_PAD_LEFT); ?>
                                <tr>
                                    <td><?= $kode; ?></td>
                                    <td><?= htmlspecialchars($trx['username'] ?? ''); ?></td>
                                    <td><?= htmlspecialchars($trx['judul'] ?? ''); ?></td>
                                    <td><span class="badge badge-<?= $trx['tipe_transaksi'] ?>"><?= strtoupper($trx['tipe_transaksi']); ?></span></td>
                                    <td><?= $trx['tipe_transaksi'] === 'sewa' ? ($trx['durasi_hari'] . ' hari') : '-'; ?></td>
                                    <td>Rp <?= number_format($trx['total_bayar']); ?></td>
                                    <td><?= htmlspecialchars($trx['status']); ?></td>
                                    <td><?= htmlspecialchars($trx['tanggal_pinjam']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="8" style="text-align:center; padding:15px;">Belum ada transaksi.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    <script>
        function filterTrx() {
            const q = (document.getElementById('trxSearch').value || '').trim().toLowerCase();
            const rows = document.querySelectorAll('#trxTable tbody tr');
            rows.forEach(r => {
                const kode = (r.cells[0]?.textContent || '').toLowerCase();
                r.style.display = q && !kode.includes(q) ? 'none' : '';
            });
        }
        function resetTrx() {
            document.getElementById('trxSearch').value = '';
            filterTrx();
        }
    </script>
</body>
</html>
