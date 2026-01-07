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
$upload_dir = __DIR__ . '/aset/images/';

// Pastikan kolom durasi_default ada (durasi sewa default per hari)
$col_check = mysqli_query($conn, "SHOW COLUMNS FROM games LIKE 'durasi_default'");
if ($col_check && mysqli_num_rows($col_check) === 0) {
    @mysqli_query($conn, "ALTER TABLE games ADD COLUMN durasi_default INT NOT NULL DEFAULT 3 AFTER stok");
}

// Simple uploader with basic validation
function uploadGameImage($fieldName, $uploadDir) {
    $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return [true, null];
    }

    if ($_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
        return [false, "Upload gambar gagal (error code: " . $_FILES[$fieldName]['error'] . ")."];
    }

    $ext = strtolower(pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_ext)) {
        return [false, "Format gambar harus jpg, jpeg, png, atau webp."];
    }

    $new_name = 'game_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
    $target_path = rtrim($uploadDir, '/\\') . DIRECTORY_SEPARATOR . $new_name;

    if (!move_uploaded_file($_FILES[$fieldName]['tmp_name'], $target_path)) {
        return [false, "Gagal menyimpan file gambar."];
    }

    return [true, $new_name];
}

// HAPUS GAME + bersihkan library user + refund penuh
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Ambil gambar lama untuk dibersihkan jika delete sukses
    $old_image_query = mysqli_query($conn, "SELECT gambar FROM games WHERE id = $id");
    if (!$old_image_query || mysqli_num_rows($old_image_query) === 0) {
        $message = "Game tidak ditemukan.";
        $message_type = "error";
    } else {
        mysqli_begin_transaction($conn);
        try {
            // Ambil semua transaksi terkait game ini
            $tx_res = mysqli_query($conn, "SELECT user_id, total_bayar FROM transactions WHERE game_id = $id");
            if (!$tx_res) {
                throw new Exception('Gagal mengambil transaksi');
            }

            // Refund per user agar saldo konsisten jika ada banyak transaksi
            $refunds = [];
            while ($row = mysqli_fetch_assoc($tx_res)) {
                $uid = (int)$row['user_id'];
                $refunds[$uid] = ($refunds[$uid] ?? 0) + (float)$row['total_bayar'];
            }

            foreach ($refunds as $uid => $amount) {
                if (!mysqli_query($conn, "UPDATE users SET saldo = saldo + $amount WHERE id = $uid")) {
                    throw new Exception('Gagal refund saldo');
                }
            }

            // Hapus transaksi/library yang terkait game
            if (!mysqli_query($conn, "DELETE FROM transactions WHERE game_id = $id")) {
                throw new Exception('Gagal menghapus transaksi');
            }

            // Hapus game dari katalog
            if (!mysqli_query($conn, "DELETE FROM games WHERE id = $id")) {
                throw new Exception('Gagal menghapus game');
            }

            mysqli_commit($conn);

            $message = "Game berhasil dihapus. Library user dibersihkan dan saldo sudah direfund.";
            $message_type = "success";

            // Hapus file gambar setelah commit agar aman dari rollback
            if ($old_image_query && mysqli_num_rows($old_image_query) > 0) {
                $old_image = mysqli_fetch_assoc($old_image_query)['gambar'];
                $old_path = rtrim($upload_dir, '/\\') . DIRECTORY_SEPARATOR . $old_image;
                if ($old_image && $old_image !== 'default.jpg' && file_exists($old_path)) {
                    @unlink($old_path);
                }
            }
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $message = "Gagal menghapus game atau melakukan refund.";
            $message_type = "error";
        }
    }
}

// TAMBAH GAME
if (isset($_POST['tambah'])) {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $genre = mysqli_real_escape_string($conn, $_POST['genre']);
    $harga_sewa = floatval($_POST['harga_sewa']);
    $harga_beli = floatval($_POST['harga_beli']);
    $stok = intval($_POST['stok']);
    $durasi_default = intval($_POST['durasi_default'] ?? 3);

    // Cek judul duplikat (case-insensitive)
    $dup_check = mysqli_query($conn, "SELECT COUNT(*) AS c FROM games WHERE LOWER(judul) = LOWER('$judul')");
    $is_duplicate = $dup_check ? (int)mysqli_fetch_assoc($dup_check)['c'] > 0 : false;

    if ($is_duplicate) {
        $message = "Game dengan judul tersebut sudah ada.";
        $message_type = "error";
    } else {
        list($upload_ok, $uploaded_image) = uploadGameImage('gambar', $upload_dir);
        if (!$upload_ok || !$uploaded_image) {
            $message = $upload_ok ? "Gambar wajib diunggah." : $uploaded_image;
            $message_type = "error";
        } else {
            $query = "INSERT INTO games (judul, deskripsi, genre, harga_sewa, harga_beli, stok, durasi_default, gambar) 
                      VALUES ('$judul', '$deskripsi', '$genre', $harga_sewa, $harga_beli, $stok, $durasi_default, '$uploaded_image')";

            if (mysqli_query($conn, $query)) {
                $message = "Game berhasil ditambahkan!";
                $message_type = "success";
            } else {
                $message = "Gagal menambahkan game!";
                $message_type = "error";
            }
        }
    }
}

// EDIT GAME
if (isset($_POST['edit'])) {
    $id = intval($_POST['id']);
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $genre = mysqli_real_escape_string($conn, $_POST['genre']);
    $harga_sewa = floatval($_POST['harga_sewa']);
    $harga_beli = floatval($_POST['harga_beli']);
    $stok = intval($_POST['stok']);
    $durasi_default = intval($_POST['durasi_default'] ?? 3);
    $old_gambar = $_POST['old_gambar'] ?? 'default.jpg';

    list($upload_ok, $new_image) = uploadGameImage('gambar', $upload_dir);
    if (!$upload_ok) {
        $message = $new_image;
        $message_type = "error";
    } else {
        $gambar_final = $new_image ?: ($old_gambar ?: 'default.jpg');

        if ($new_image && $old_gambar && $old_gambar !== 'default.jpg') {
            $old_path = rtrim($upload_dir, '/\\') . DIRECTORY_SEPARATOR . $old_gambar;
            if (file_exists($old_path)) {
                @unlink($old_path);
            }
        }

        $query = "UPDATE games SET judul='$judul', deskripsi='$deskripsi', genre='$genre', 
              harga_sewa=$harga_sewa, harga_beli=$harga_beli, stok=$stok, durasi_default=$durasi_default, gambar='$gambar_final' WHERE id=$id";

        if (mysqli_query($conn, $query)) {
            $message = "Game berhasil diupdate!";
            $message_type = "success";
        } else {
            $message = "Gagal mengupdate game!";
            $message_type = "error";
        }
    }
}

// Ambil semua data games
$games_query = "SELECT * FROM games ORDER BY id DESC";
$games_result = mysqli_query($conn, $games_query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Game - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="aset/style.css">
    <style>
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
            background-color: #667eea;
            color: white;
            font-weight: 600;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .btn-action {
            padding: 6px 12px;
            margin: 0 3px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 13px;
            display: inline-block;
        }
        .btn-edit {
            background: #4facfe;
            color: white;
        }
        .btn-delete {
            background: #f5576c;
            color: white;
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
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            inset: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-y: auto;
        }
        .modal-content {
            background: white;
            padding: 30px;
            width: 90%;
            max-width: 600px;
            border-radius: 10px;
            position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        .close {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 30px;
            cursor: pointer;
            color: #999;
        }
    </style>
</head>
<body>

    <?php include 'aset/admin_sidebar.php'; ?>

    <main class="main-content">
        <header class="top-bar">
            <div class="welcome-text">
                <h2>Manajemen Game</h2>
            </div>
            <div class="user-action">
                <button onclick="openModal('addModal')" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Game Baru
                </button>
            </div>
        </header>

        <section>
            <?php if($message): ?>
                <div class="alert alert-<?= $message_type ?>">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Judul</th>
                            <th>Genre</th>
                            <th>Harga Sewa</th>
                            <th>Harga Beli</th>
                            <th>Durasi Sewa (hari)</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($game = mysqli_fetch_assoc($games_result)): ?>
                        <tr>
                            <td><?= $game['id'] ?></td>
                            <td><?= htmlspecialchars($game['judul']) ?></td>
                            <td><?= htmlspecialchars($game['genre']) ?></td>
                            <td>Rp <?= number_format($game['harga_sewa']) ?></td>
                            <td>Rp <?= number_format($game['harga_beli']) ?></td>
                            <td><?= $game['durasi_default'] ?? 0 ?></td>
                            <td><?= $game['stok'] ?></td>
                            <td>
                                <a href="javascript:void(0)" onclick='editGame(<?= json_encode($game) ?>)' class="btn-action btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="?delete=<?= $game['id'] ?>" onclick="return confirm('Yakin hapus game ini?')" class="btn-action btn-delete">
                                    <i class="fas fa-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- Modal Tambah Game -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addModal')">&times;</span>
            <h2 style="margin-top: 0;">Tambah Game Baru</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Judul Game</label>
                    <input type="text" name="judul" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Genre</label>
                    <input type="text" name="genre" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Harga Sewa (Rp)</label>
                    <input type="number" name="harga_sewa" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Harga Beli (Rp)</label>
                    <input type="number" name="harga_beli" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Durasi Sewa Default (hari)</label>
                    <input type="number" name="durasi_default" class="form-control" min="1" value="3" required>
                </div>
                <div class="form-group">
                    <label>Stok</label>
                    <input type="number" name="stok" class="form-control" value="1" required>
                </div>
                <div class="form-group">
                    <label>Gambar Game</label>
                    <input type="file" name="gambar" class="form-control" accept=".jpg,.jpeg,.png,.webp" required>
                </div>
                <button type="submit" name="tambah" class="btn btn-primary">Tambah Game</button>
            </form>
        </div>
    </div>

    <!-- Modal Edit Game -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editModal')">&times;</span>
            <h2 style="margin-top: 0;">Edit Game</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="edit_id">
                <input type="hidden" name="old_gambar" id="edit_old_gambar">
                <div class="form-group">
                    <label>Judul Game</label>
                    <input type="text" name="judul" id="edit_judul" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" id="edit_deskripsi" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Genre</label>
                    <input type="text" name="genre" id="edit_genre" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Harga Sewa (Rp)</label>
                    <input type="number" name="harga_sewa" id="edit_harga_sewa" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Harga Beli (Rp)</label>
                    <input type="number" name="harga_beli" id="edit_harga_beli" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Durasi Sewa Default (hari)</label>
                    <input type="number" name="durasi_default" id="edit_durasi_default" class="form-control" min="1" required>
                </div>
                <div class="form-group">
                    <label>Stok</label>
                    <input type="number" name="stok" id="edit_stok" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Ganti Gambar (opsional)</label>
                    <input type="file" name="gambar" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                    <small style="color:#666;">Biarkan kosong jika tidak ingin mengganti.</small>
                </div>
                <button type="submit" name="edit" class="btn btn-primary">Update Game</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function editGame(game) {
            document.getElementById('edit_id').value = game.id;
            document.getElementById('edit_judul').value = game.judul;
            document.getElementById('edit_deskripsi').value = game.deskripsi || '';
            document.getElementById('edit_genre').value = game.genre || '';
            document.getElementById('edit_harga_sewa').value = game.harga_sewa;
            document.getElementById('edit_harga_beli').value = game.harga_beli;
            document.getElementById('edit_durasi_default').value = game.durasi_default || 3;
            document.getElementById('edit_stok').value = game.stok;
            document.getElementById('edit_old_gambar').value = game.gambar || '';
            openModal('editModal');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>

</body>
</html>
