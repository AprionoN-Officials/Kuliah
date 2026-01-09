<?php
session_start();
include 'config/database.php';

// Proteksi: Hanya admin yang boleh akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$message = "";
$message_type = "";

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS topup_options (id INT AUTO_INCREMENT PRIMARY KEY, nominal INT NOT NULL UNIQUE)");
function fetchTopupOptions($conn) {
    $opts = [];
    $res = mysqli_query($conn, "SELECT nominal FROM topup_options ORDER BY nominal");
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $opts[] = intval($row['nominal']);
        }
    }
    if (empty($opts)) {
        $opts = [10000,20000,50000,100000,250000,500000];
    }
    return $opts;
}
$nominal_options = fetchTopupOptions($conn);

// TOPUP SALDO
if (isset($_POST['topup'])) {
    $user_id = intval($_POST['user_id']);
    $nominal = floatval($_POST['nominal']);
    
    // Update saldo user
    $query = "UPDATE users SET saldo = saldo + $nominal WHERE id = $user_id";
    
    if (mysqli_query($conn, $query)) {
        $message = "Saldo berhasil ditambahkan sebesar Rp " . number_format($nominal);
        $message_type = "success";
    } else {
        $message = "Gagal menambahkan saldo!";
        $message_type = "error";
    }
}

// KURANGI SALDO
if (isset($_POST['withdraw'])) {
    $user_id = intval($_POST['user_id']);
    $nominal = floatval($_POST['nominal']);
    
    // Cek saldo mencukupi
    $check_query = "SELECT saldo FROM users WHERE id = $user_id";
    $check_result = mysqli_query($conn, $check_query);
    $user_data = mysqli_fetch_assoc($check_result);
    
    if ($user_data['saldo'] >= $nominal) {
        $query = "UPDATE users SET saldo = saldo - $nominal WHERE id = $user_id";
        if (mysqli_query($conn, $query)) {
            $message = "Saldo berhasil dikurangi sebesar Rp " . number_format($nominal);
            $message_type = "success";
        } else {
            $message = "Gagal mengurangi saldo!";
            $message_type = "error";
        }
    } else {
        $message = "Saldo user tidak mencukupi!";
        $message_type = "error";
    }
}

// Ambil semua user kecuali admin
$users_query = "SELECT id, username, saldo FROM users WHERE role = 'user' ORDER BY username";
$users_result = mysqli_query($conn, $users_query);

// Ambil riwayat topup (dari tabel users untuk ditampilkan)
$history_query = "SELECT u.id, u.username, u.saldo, u.created_at 
                  FROM users u 
                  WHERE u.role = 'user' 
                  ORDER BY u.saldo DESC";
$history_result = mysqli_query($conn, $history_query);

// Kelola pilihan nominal
if (isset($_POST['add_option'])) {
    $new = intval($_POST['option_value']);
    if ($new > 0) {
        $exists = mysqli_query($conn, "SELECT id FROM topup_options WHERE nominal = $new LIMIT 1");
        if ($exists && mysqli_num_rows($exists) === 0) {
            mysqli_query($conn, "INSERT INTO topup_options (nominal) VALUES ($new)");
        }
        $nominal_options = fetchTopupOptions($conn);
        $message = "Pilihan nominal ditambahkan.";
        $message_type = "success";
    }
}

if (isset($_POST['delete_option'])) {
    $del = intval($_POST['option_value']);
    mysqli_query($conn, "DELETE FROM topup_options WHERE nominal = $del LIMIT 1");
    $nominal_options = fetchTopupOptions($conn);
    $message = "Pilihan nominal dihapus.";
    $message_type = "success";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Saldo - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="aset/style.css">
    <style>
        .topup-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .topup-form {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .nominal-buttons {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 10px;
        }
        .nominal-btn {
            padding: 12px;
            border: 2px solid #667eea;
            background: white;
            color: #667eea;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        .nominal-btn:hover {
            background: #667eea;
            color: white;
        }
        .table-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #43e97b;
            color: white;
            font-weight: 600;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        @media (max-width: 768px) {
            .topup-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <?php include 'aset/admin_sidebar.php'; ?>

    <main class="main-content">
        <header class="top-bar">
            <div class="welcome-text">
                <h2>Manajemen Saldo User</h2>
            </div>
        </header>

        <section>
            <?php if($message): ?>
                <div class="alert alert-<?= $message_type ?>">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <div class="topup-container">
                <!-- Form Tambah Saldo -->
                <div class="topup-form">
                    <h3 style="margin-top: 0; color: #43e97b;">
                        <i class="fas fa-plus-circle"></i> Tambah Saldo User
                    </h3>
                    <form method="POST">
                        <div class="form-group">
                            <label>Pilih User</label>
                            <select name="user_id" class="form-control" required>
                                <option value="">-- Pilih User --</option>
                                <?php 
                                mysqli_data_seek($users_result, 0);
                                while($user = mysqli_fetch_assoc($users_result)): 
                                ?>
                                    <option value="<?= $user['id'] ?>">
                                        <?= htmlspecialchars($user['username']) ?> 
                                        (Saldo: Rp <?= number_format($user['saldo']) ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Nominal Top-up (Rp)</label>
                            <input type="number" name="nominal" id="topup_nominal" class="form-control" min="1000" required>
                        </div>

                        <div class="nominal-buttons">
                            <?php foreach ($nominal_options as $opt): ?>
                                <button type="button" class="nominal-btn" onclick="setNominal('topup_nominal', <?= $opt ?>)"><?= number_format($opt,0,',','.') ?></button>
                            <?php endforeach; ?>
                        </div>

                        <button type="submit" name="topup" class="btn btn-primary" style="width: 100%; margin-top: 15px; background: #43e97b;">
                            <i class="fas fa-check"></i> Tambah Saldo
                        </button>
                    </form>
                </div>

                <!-- Form Kurangi Saldo -->
                <div class="topup-form">
                    <h3 style="margin-top: 0; color: #f5576c;">
                        <i class="fas fa-minus-circle"></i> Kurangi Saldo User
                    </h3>
                    <form method="POST">
                        <div class="form-group">
                            <label>Pilih User</label>
                            <select name="user_id" class="form-control" required>
                                <option value="">-- Pilih User --</option>
                                <?php 
                                mysqli_data_seek($users_result, 0);
                                while($user = mysqli_fetch_assoc($users_result)): 
                                ?>
                                    <option value="<?= $user['id'] ?>">
                                        <?= htmlspecialchars($user['username']) ?> 
                                        (Saldo: Rp <?= number_format($user['saldo']) ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Nominal Pengurangan (Rp)</label>
                            <input type="number" name="nominal" id="withdraw_nominal" class="form-control" min="1000" required>
                        </div>

                        <div class="nominal-buttons">
                            <?php foreach ($nominal_options as $opt): ?>
                                <button type="button" class="nominal-btn" onclick="setNominal('withdraw_nominal', <?= $opt ?>)"><?= number_format($opt,0,',','.') ?></button>
                            <?php endforeach; ?>
                        </div>

                        <button type="submit" name="withdraw" class="btn btn-primary" style="width: 100%; margin-top: 15px; background: #f5576c;">
                            <i class="fas fa-minus"></i> Kurangi Saldo
                        </button>
                    </form>
                </div>
            </div>

            <h3 class="section-title">Daftar Saldo User</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Saldo Saat Ini</th>
                            <th>Tanggal Daftar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($user = mysqli_fetch_assoc($history_result)): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><strong>Rp <?= number_format($user['saldo']) ?></strong></td>
                            <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <h3 class="section-title">Kelola Pilihan Nominal</h3>
            <div class="table-container">
                <form method="POST" style="margin-bottom:12px; display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                    <input type="number" name="option_value" class="form-control" placeholder="Nominal baru" min="1000" required>
                    <button type="submit" name="add_option" class="btn btn-primary">Tambah Pilihan</button>
                </form>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <?php foreach ($nominal_options as $opt): ?>
                        <form method="POST" style="display:flex; align-items:center; gap:6px;">
                            <input type="hidden" name="option_value" value="<?= $opt ?>">
                            <span style="padding:8px 12px; border:1px solid #ddd; border-radius:8px; background:#f9f9f9;">Rp <?= number_format($opt) ?></span>
                            <button type="submit" name="delete_option" class="btn" style="background:#f5576c; color:white; padding:6px 10px;">Hapus</button>
                        </form>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <script>
        function setNominal(inputId, value) {
            document.getElementById(inputId).value = value;
        }
    </script>

</body>
</html>
