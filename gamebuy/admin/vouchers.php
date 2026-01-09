<?php
session_start();
include '../config/database.php';

// Proteksi admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Pastikan tabel vouchers ada
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS vouchers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(50) UNIQUE NOT NULL,
    tipe ENUM('fixed','percent') NOT NULL DEFAULT 'percent',
    potongan INT NOT NULL DEFAULT 0,
    max_potongan INT NOT NULL DEFAULT 0,
    min_transaksi INT NOT NULL DEFAULT 0,
    stok INT NOT NULL DEFAULT 0,
    aktif TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Tambah kolom stok jika belum ada
$col_check = mysqli_query($conn, "SHOW COLUMNS FROM vouchers LIKE 'stok'");
if ($col_check && mysqli_num_rows($col_check) === 0) {
    mysqli_query($conn, "ALTER TABLE vouchers ADD COLUMN stok INT NOT NULL DEFAULT 0 AFTER min_transaksi");
}

$message = '';
$message_type = '';

function clean_code($code) {
    $code = strtoupper(trim($code));
    return preg_replace('/[^A-Z0-9_-]/', '', $code);
}

function clean_int($val) {
    // Hapus desimal jika ada (Browser mungkin mengirim 10000.00)
    $val = (string)$val;
    if (strpos($val, '.') !== false) {
        $val = explode('.', $val)[0];
    }
    // Hanya ambil angka
    $num = (int)preg_replace('/[^0-9]/', '', $val);
    return max(0, $num);
}

// Normalisasi persen, dukung koma/titik desimal dan batasi 0-100
function clean_percent($val) {
    $raw = preg_replace('/[^0-9.,]/', '', (string)$val);
    $normalized = str_replace(',', '.', $raw);
    // Jika ada lebih dari satu titik, sisakan yang pertama (hindari thousand separator)
    if (substr_count($normalized, '.') > 1) {
        $parts = explode('.', $normalized, 2);
        $normalized = $parts[0] . '.' . preg_replace('/\./', '', $parts[1]);
    }
    $num = floatval($normalized);
    return max(0, min(100, round($num)));
}

if (isset($_POST['add_voucher'])) {
    $kode = clean_code($_POST['kode'] ?? '');
    $tipe = ($_POST['tipe'] ?? 'percent') === 'fixed' ? 'fixed' : 'percent';
    $potongan = $tipe === 'percent' ? clean_percent($_POST['potongan'] ?? 0) : clean_int($_POST['potongan'] ?? 0);
    $max_potongan = clean_int($_POST['max_potongan'] ?? 0);
    $min_transaksi = clean_int($_POST['min_transaksi'] ?? 0);
    $stok = clean_int($_POST['stok'] ?? 0);
    $aktif = isset($_POST['aktif']) ? 1 : 0;

    if ($kode === '' || $potongan <= 0) {
        $message = 'Kode dan potongan wajib diisi.';
        $message_type = 'error';
    } else {
        $sql = "INSERT INTO vouchers (kode, tipe, potongan, max_potongan, min_transaksi, stok, aktif) 
                VALUES ('$kode', '$tipe', $potongan, $max_potongan, $min_transaksi, $stok, $aktif)";
        if (mysqli_query($conn, $sql)) {
            $message = 'Voucher berhasil ditambahkan.';
            $message_type = 'success';
        } else {
            $message = mysqli_errno($conn) == 1062 ? 'Kode voucher sudah ada.' : 'Gagal menambahkan voucher.';
            $message_type = 'error';
        }
    }
}

if (isset($_POST['update_voucher'])) {
    $id = (int)($_POST['id'] ?? 0);
    $kode = clean_code($_POST['kode'] ?? '');
    $tipe = ($_POST['tipe'] ?? 'percent') === 'fixed' ? 'fixed' : 'percent';
    $potongan = $tipe === 'percent' ? clean_percent($_POST['potongan'] ?? 0) : clean_int($_POST['potongan'] ?? 0);
    $max_potongan = clean_int($_POST['max_potongan'] ?? 0);
    $min_transaksi = clean_int($_POST['min_transaksi'] ?? 0);
    $stok = clean_int($_POST['stok'] ?? 0);
    $aktif = isset($_POST['aktif']) ? 1 : 0;

    if ($id <= 0 || $kode === '' || $potongan <= 0) {
        $message = 'Data tidak valid.';
        $message_type = 'error';
    } else {
        $sql = "UPDATE vouchers 
            SET kode='$kode', tipe='$tipe', potongan=$potongan, max_potongan=$max_potongan, min_transaksi=$min_transaksi, stok=$stok, aktif=$aktif 
            WHERE id=$id";
        if (mysqli_query($conn, $sql)) {
            $message = 'Voucher berhasil diupdate.';
            $message_type = 'success';
        } else {
            $message = mysqli_errno($conn) == 1062 ? 'Kode voucher sudah ada.' : 'Gagal mengupdate voucher.';
            $message_type = 'error';
        }
    }
}

if (isset($_POST['delete_voucher'])) {
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
        $message = 'Data tidak valid.';
        $message_type = 'error';
    } else {
        if (mysqli_query($conn, "DELETE FROM vouchers WHERE id=$id")) {
            $message = 'Voucher dihapus.';
            $message_type = 'success';
        } else {
            $message = 'Gagal menghapus voucher.';
            $message_type = 'error';
        }
    }
}

$list = [];
$res = mysqli_query($conn, "SELECT * FROM vouchers ORDER BY id DESC");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $list[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voucher Diskon - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../aset/style.css">
    <style>
        .table-container { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #4facfe; color: white; font-weight: 600; }
        tr:hover { background-color: #f5f5f5; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .form-inline { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .badge-active { background: #43e97b; color: #0f5a2c; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .badge-inactive { background: #f5576c; color: white; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .btn-sm { padding: 6px 10px; font-size: 13px; }
        .btn-delete { background: #f5576c; color: white; border: none; }
        .edit-row { background: #f9fbff; }
    </style>
</head>
<body>
    <?php include '../aset/admin_sidebar.php'; ?>

    <main class="main-content">
        <header class="top-bar">
            <div class="welcome-text">
                <h2>Voucher Diskon</h2>
            </div>
        </header>

        <section>
            <?php if ($message): ?>
                <div class="alert alert-<?= $message_type ?>"><?= $message ?></div>
            <?php endif; ?>

            <div class="table-container" style="margin-bottom: 20px;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;">
                    <h3 style="margin:0;">Tambah Voucher</h3>
                    <button type="button" class="btn btn-primary" style="padding:8px 12px;" onclick="toggleAdd()">Tambah Baru</button>
                </div>
                <div id="add-form" style="margin-top:12px; display:none;">
                    <form method="POST" class="form-inline">
                        <input type="text" name="kode" class="form-control" placeholder="KODE" maxlength="50" required>
                        <select name="tipe" class="form-control" required>
                            <option value="percent">Persen (%)</option>
                            <option value="fixed">Potongan Rp</option>
                        </select>
                        <input type="number" name="potongan" class="form-control" placeholder="Potongan" min="1" required>
                        <input type="number" name="max_potongan" class="form-control" placeholder="Max Potongan (Rp)" min="0">
                        <input type="number" name="min_transaksi" class="form-control" placeholder="Minimal Pembelian (Rp)" min="0">
                        <input type="number" name="stok" class="form-control" placeholder="Stok" min="0" value="0">
                        <label style="display:flex;align-items:center;gap:6px;"><input type="checkbox" name="aktif" checked> Aktif</label>
                        <button type="submit" name="add_voucher" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-delete" onclick="toggleAdd()">Tutup</button>
                    </form>
                </div>
            </div>

            <div class="table-container">
                <h3 style="margin-top:0;">Daftar Voucher</h3>
                <?php if (empty($list)): ?>
                    <p>Belum ada voucher.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Tipe</th>
                                <th>Potongan</th>
                                <th>Max Potongan</th>
                                <th>Minimal Pembelian</th>
                                <th>Stok</th>
                                <th>Status</th>
                                <th style="width:220px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($list as $v): ?>
                                <tr>
                                    <td><?= htmlspecialchars($v['kode']) ?></td>
                                    <td><?= $v['tipe'] === 'fixed' ? 'Rp' : 'Percent' ?></td>
                                    <td><?= $v['tipe'] === 'fixed' ? 'Rp ' . number_format($v['potongan']) : $v['potongan'] . '%' ?></td>
                                    <td><?= $v['tipe'] === 'percent' ? 'Rp ' . number_format($v['max_potongan']) : '-' ?></td>
                                    <td>Rp <?= number_format($v['min_transaksi']) ?></td>
                                    <td><?= number_format($v['stok']) ?></td>
                                    <td>
                                        <?php if ($v['aktif']): ?>
                                            <span class="badge-active">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge-inactive">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                            <button type="button" class="btn btn-primary btn-sm" onclick="toggleEdit('edit-<?= $v['id'] ?>')">Edit</button>
                                            <form method="POST" onsubmit="return confirm('Hapus voucher ini?')">
                                                <input type="hidden" name="id" value="<?= $v['id'] ?>">
                                                <button type="submit" name="delete_voucher" class="btn btn-delete btn-sm">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <tr id="edit-<?= $v['id'] ?>" class="edit-row" style="display:none;">
                                    <td colspan="7">
                                        <form method="POST" class="form-inline" style="gap:10px;">
                                            <input type="hidden" name="id" value="<?= $v['id'] ?>">
                                            <input type="text" name="kode" class="form-control" value="<?= htmlspecialchars($v['kode']) ?>" maxlength="50" required>
                                            <select name="tipe" class="form-control" required>
                                                <option value="percent" <?= $v['tipe']==='percent' ? 'selected' : '' ?>>Persen (%)</option>
                                                <option value="fixed" <?= $v['tipe']==='fixed' ? 'selected' : '' ?>>Potongan Rp</option>
                                            </select>
                                            <input type="number" name="potongan" class="form-control" value="<?= $v['potongan'] ?>" min="1" required>
                                            <input type="number" name="max_potongan" class="form-control" value="<?= $v['max_potongan'] ?>" min="0" placeholder="Max Potongan (Rp)">
                                            <input type="number" name="min_transaksi" class="form-control" value="<?= $v['min_transaksi'] ?>" min="0" placeholder="Minimal Pembelian (Rp)">
                                            <input type="number" name="stok" class="form-control" value="<?= $v['stok'] ?>" min="0" placeholder="Stok">
                                            <label style="display:flex;align-items:center;gap:6px;"><input type="checkbox" name="aktif" <?= $v['aktif'] ? 'checked' : '' ?>> Aktif</label>
                                            <button type="submit" name="update_voucher" class="btn btn-primary btn-sm">Simpan</button>
                                            <button type="button" class="btn btn-delete btn-sm" onclick="toggleEdit('edit-<?= $v['id'] ?>')">Tutup</button>
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
        function toggleEdit(id) {
            var row = document.getElementById(id);
            if (!row) return;
            row.style.display = row.style.display === 'none' ? '' : 'none';
        }
        function toggleAdd() {
            var form = document.getElementById('add-form');
            if (!form) return;
            form.style.display = form.style.display === 'none' ? '' : 'none';
        }
    </script>
</body>
</html>
