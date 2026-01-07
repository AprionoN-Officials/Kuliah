<?php
session_start();
include 'config/database.php';

// Proteksi admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: login.php");
    exit;
}

$message = '';
$message_type = '';

// Pastikan tabel ada
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS topup_options (id INT AUTO_INCREMENT PRIMARY KEY, nominal INT NOT NULL UNIQUE)");
// Seed default jika kosong
$defaults = [10000,20000,50000,100000,250000,500000];
$check = mysqli_query($conn, "SELECT COUNT(*) AS c FROM topup_options");
$cnt = $check ? (int)mysqli_fetch_assoc($check)['c'] : 0;
if ($cnt === 0) {
    foreach ($defaults as $d) {
        mysqli_query($conn, "INSERT IGNORE INTO topup_options (nominal) VALUES ($d)");
    }
}

function sanitize_nominal($value) {
    // Hanya angka dan tidak boleh negatif
    $clean = preg_replace('/[^0-9]/', '', (string)$value);
    return max(0, (int)$clean);
}

if (isset($_POST['add_nominal'])) {
    $nominal = sanitize_nominal($_POST['nominal'] ?? 0);
    if ($nominal <= 0) {
        $message = "Nominal harus lebih dari 0.";
        $message_type = "error";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO topup_options (nominal) VALUES ($nominal)");
        if ($insert) {
            $message = "Nominal baru berhasil ditambahkan.";
            $message_type = "success";
        } else {
            $message = mysqli_errno($conn) == 1062 ? "Nominal sudah ada." : "Gagal menambahkan nominal.";
            $message_type = "error";
        }
    }
}

if (isset($_POST['update_nominal'])) {
    $id = intval($_POST['id'] ?? 0);
    $nominal = sanitize_nominal($_POST['nominal_edit'] ?? 0);
    if ($id <= 0 || $nominal <= 0) {
        $message = "Data tidak valid.";
        $message_type = "error";
    } else {
        $update = mysqli_query($conn, "UPDATE topup_options SET nominal = $nominal WHERE id = $id");
        if ($update) {
            $message = "Nominal berhasil diupdate.";
            $message_type = "success";
        } else {
            $message = mysqli_errno($conn) == 1062 ? "Nominal sudah ada." : "Gagal mengupdate nominal.";
            $message_type = "error";
        }
    }
}

if (isset($_POST['delete_nominal'])) {
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        $message = "Data tidak valid.";
        $message_type = "error";
    } else {
        $delete = mysqli_query($conn, "DELETE FROM topup_options WHERE id = $id");
        if ($delete) {
            $message = "Nominal berhasil dihapus.";
            $message_type = "success";
        } else {
            $message = "Gagal menghapus nominal.";
            $message_type = "error";
        }
    }
}

$options = [];
$res = mysqli_query($conn, "SELECT id, nominal FROM topup_options ORDER BY nominal");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $options[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Nominal Top Up - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="aset/style.css">
    <style>
        .table-container { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #4facfe; color: white; font-weight: 600; }
        tr:hover { background-color: #f5f5f5; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .form-inline { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .btn-sm { padding: 6px 10px; font-size: 13px; }
        .btn-delete { background: #f5576c; color: white; border: none; }
        .compact-table th, .compact-table td { padding: 8px 10px; }
        .nominal-input { width: 140px; }
    </style>
</head>
<body>
    <?php include 'aset/admin_sidebar.php'; ?>

    <main class="main-content">
        <header class="top-bar">
            <div class="welcome-text">
                <h2>Atur Nominal Top Up</h2>
            </div>
        </header>

        <section>
            <?php if ($message): ?>
                <div class="alert alert-<?= $message_type ?>"><?= $message ?></div>
            <?php endif; ?>

            <div class="table-container" style="margin-bottom: 20px;">
                <h3 style="margin-top:0;">Tambah Nominal Baru</h3>
                <form method="POST" class="form-inline">
                    <input type="number" name="nominal" class="form-control" placeholder="Masukkan nominal" min="1000" step="1000" required>
                    <button type="submit" name="add_nominal" class="btn btn-primary">Tambah</button>
                </form>
            </div>

            <div class="table-container">
                <h3 style="margin-top:0;">Daftar Nominal</h3>
                <?php if (empty($options)): ?>
                    <p>Belum ada nominal. Tambahkan nominal baru.</p>
                <?php else: ?>
                    <table class="compact-table">
                        <thead>
                            <tr>
                                <th>Nominal</th>
                                <th style="width:320px;">Edit / Hapus</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($options as $opt): ?>
                                <tr>
                                    <td>Rp <?= number_format($opt['nominal'],0,',','.') ?></td>
                                    <td>
                                        <form method="POST" class="form-inline" style="gap:8px;">
                                            <input type="hidden" name="id" value="<?= $opt['id'] ?>">
                                            <input type="number" name="nominal_edit" class="form-control nominal-input" value="<?= $opt['nominal'] ?>" min="1000" step="1000" required>
                                            <button type="submit" name="update_nominal" class="btn btn-primary btn-sm">Simpan</button>
                                            <button type="submit" name="delete_nominal" class="btn btn-delete btn-sm" onclick="return confirm('Hapus nominal ini?')">Hapus</button>
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
</body>
</html>
