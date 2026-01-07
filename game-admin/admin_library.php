<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$message = "";
$message_type = "";

$users_q = mysqli_query($conn, "SELECT id, username FROM users WHERE role='user' ORDER BY username");
$users = [];
while ($row = mysqli_fetch_assoc($users_q)) {
    $users[] = $row;
}

$games = [];
$games_q = mysqli_query($conn, "SELECT id, judul, genre, harga_beli, harga_sewa, stok FROM games ORDER BY judul");
while ($row = mysqli_fetch_assoc($games_q)) {
    $games[] = $row;
}

$selected_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
if ($selected_user_id === 0 && count($users) > 0) {
    $selected_user_id = $users[0]['id'];
}

if (isset($_POST['delete_tx'])) {
    $tx_id = intval($_POST['tx_id'] ?? 0);
    $selected_user_id = intval($_POST['user_id_filter'] ?? $selected_user_id);
    $tx = mysqli_query($conn, "SELECT id, user_id, game_id FROM transactions WHERE id=$tx_id LIMIT 1");
    $tx_data = mysqli_fetch_assoc($tx);

    if (!$tx_data) {
        $message = "Transaksi tidak ditemukan.";
        $message_type = "error";
    } else {
        mysqli_begin_transaction($conn);
        try {
            mysqli_query($conn, "DELETE FROM transactions WHERE id={$tx_data['id']}");
            mysqli_query($conn, "UPDATE games SET stok = stok + 1 WHERE id={$tx_data['game_id']}");
            mysqli_commit($conn);
            $message = "Game dihapus dari library user.";
            $message_type = "success";
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $message = "Gagal menghapus data.";
            $message_type = "error";
        }
    }
}

if (isset($_POST['add_tx'])) {
    $selected_user_id = intval($_POST['user_id_filter'] ?? $selected_user_id);
    $game_id = intval($_POST['game_id'] ?? 0);
    $tipe = $_POST['tipe_transaksi'] === 'sewa' ? 'sewa' : 'beli';
    $durasi = $tipe === 'sewa' ? max(1, intval($_POST['durasi_hari'] ?? 1)) : 0;

    $game_q = mysqli_query($conn, "SELECT id, judul, harga_beli, harga_sewa, stok FROM games WHERE id=$game_id LIMIT 1");
    $game = mysqli_fetch_assoc($game_q);

    if (!$game) {
        $message = "Game tidak ditemukan.";
        $message_type = "error";
    } elseif ($game['stok'] <= 0) {
        $message = "Stok game habis.";
        $message_type = "error";
    } else {
        $total = $tipe === 'sewa' ? ($game['harga_sewa'] * $durasi) : $game['harga_beli'];
        $status = $tipe === 'sewa' ? 'dipinjam' : 'permanent';
        $tanggal_kembali_sql = $tipe === 'sewa' ? "DATE_ADD(NOW(), INTERVAL $durasi DAY)" : "NULL";

        mysqli_begin_transaction($conn);
        try {
            if ($tipe === 'sewa') {
                $insert = "INSERT INTO transactions (user_id, game_id, tipe_transaksi, durasi_hari, tanggal_kembali, total_bayar, status) 
                           VALUES ($selected_user_id, $game_id, '$tipe', $durasi, $tanggal_kembali_sql, $total, '$status')";
            } else {
                $insert = "INSERT INTO transactions (user_id, game_id, tipe_transaksi, durasi_hari, tanggal_kembali, total_bayar, status) 
                           VALUES ($selected_user_id, $game_id, '$tipe', 0, NULL, $total, '$status')";
            }
            mysqli_query($conn, $insert);
            mysqli_query($conn, "UPDATE games SET stok = stok - 1 WHERE id=$game_id");
            mysqli_commit($conn);
            $message = "Game ditambahkan ke library user.";
            $message_type = "success";
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $message = "Gagal menambah game.";
            $message_type = "error";
        }
    }
}

$transactions = [];
if ($selected_user_id > 0) {
    $tx_q = "SELECT t.id, t.tipe_transaksi, t.durasi_hari, t.tanggal_pinjam, t.tanggal_kembali, t.total_bayar, t.status, g.judul, g.genre 
             FROM transactions t 
             JOIN games g ON t.game_id = g.id 
             WHERE t.user_id = $selected_user_id 
             ORDER BY t.tanggal_pinjam DESC";
    $tx_res = mysqli_query($conn, $tx_q);
    while ($row = mysqli_fetch_assoc($tx_res)) {
        $transactions[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library User - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="aset/style.css">
    <style>
        .table-container { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #4facfe; color: white; font-weight: 600; }
        tr:hover { background-color: #f5f5f5; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .filter-bar { display:flex; gap:12px; align-items:center; margin-bottom:16px; flex-wrap:wrap; }
    </style>
</head>
<body>

<?php include 'aset/admin_sidebar.php'; ?>

<main class="main-content">
    <header class="top-bar">
        <div class="welcome-text">
            <h2>Library User</h2>
        </div>
    </header>

    <section>
        <?php if($message): ?>
            <div class="alert alert-<?= $message_type ?>"><?= $message ?></div>
        <?php endif; ?>

        <div class="table-container">
            <div class="filter-bar">
                <form method="GET" style="display:flex; gap:10px; align-items:center;">
                    <label>Pilih User</label>
                    <select name="user_id" class="form-control" onchange="this.form.submit()">
                        <?php foreach ($users as $u): ?>
                            <option value="<?= $u['id'] ?>" <?= $selected_user_id == $u['id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['username']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <?php if ($selected_user_id > 0 && count($games) > 0): ?>
            <form method="POST" style="margin-bottom:16px; display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap;">
                <input type="hidden" name="user_id_filter" value="<?= $selected_user_id ?>">
                <div>
                    <label>Game</label>
                    <select name="game_id" class="form-control" required>
                        <option value="">-- Pilih Game --</option>
                        <?php foreach ($games as $g): ?>
                            <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['judul']) ?> (Stok: <?= $g['stok'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Tipe</label>
                    <select name="tipe_transaksi" class="form-control" onchange="toggleDurasi(this)" required>
                        <option value="beli">Beli (permanen)</option>
                        <option value="sewa">Sewa</option>
                    </select>
                </div>
                <div id="durasi_wrapper" style="display:none;">
                    <label>Durasi Sewa (hari)</label>
                    <input type="number" name="durasi_hari" class="form-control" min="1" value="3">
                </div>
                <button type="submit" name="add_tx" class="btn btn-primary" style="padding:10px 14px;">Tambah ke Library</button>
            </form>
            <?php endif; ?>

            <?php if ($selected_user_id === 0 || count($users) === 0): ?>
                <p>Belum ada user.</p>
            <?php elseif (count($transactions) === 0): ?>
                <p>Library user ini kosong.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Game</th>
                            <th>Tipe</th>
                            <th>Durasi</th>
                            <th>Tgl Pinjam</th>
                            <th>Tgl Kembali</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $tx): ?>
                            <tr>
                                <td>#<?= $tx['id'] ?></td>
                                <td><?= htmlspecialchars($tx['judul']) ?> <div style="font-size:12px;color:#888;"><?= htmlspecialchars($tx['genre']) ?></div></td>
                                <td><?= strtoupper($tx['tipe_transaksi']) ?></td>
                                <td><?= $tx['tipe_transaksi']==='sewa' ? ($tx['durasi_hari'] . ' hari') : '-' ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($tx['tanggal_pinjam'])) ?></td>
                                <td><?= $tx['tanggal_kembali'] ? date('d/m/Y H:i', strtotime($tx['tanggal_kembali'])) : '-' ?></td>
                                <td><?= htmlspecialchars($tx['status']) ?></td>
                                <td>
                                    <form method="POST" onsubmit="return confirm('Hapus game ini dari library user?')">
                                        <input type="hidden" name="tx_id" value="<?= $tx['id'] ?>">
                                        <input type="hidden" name="user_id_filter" value="<?= $selected_user_id ?>">
                                        <button type="submit" name="delete_tx" class="btn" style="background:#f5576c;color:white;padding:6px 10px;">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </section>
</main>

<script>
    function toggleDurasi(sel) {
        var wrap = document.getElementById('durasi_wrapper');
        wrap.style.display = sel.value === 'sewa' ? 'block' : 'none';
    }
</script>

</body>
</html>
