<?php 
include "config/database.php";
include "config/getdata.php";
session_start();

// Cek login (opsional)
$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Game - GameRent</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="aset/style.css">
</head>
<body>
    <?php include "aset/sidebar.php"; ?>

    <main class="main-content">
        <header class="top-bar">
            <div class="welcome-text">
                <h2>Tambah <b>Game Baru</b></h2>
            </div>
            <div class="user-action">
                <a href="daftargame.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </header>

        <section>
            <div class="auth-box" style="max-width: 600px; margin: 0 auto; text-align: left;">
                <form action="proses_tambah_game.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="judul">Judul Game</label>
                        <input type="text" name="judul" id="judul" class="form-control" placeholder="Contoh: GTA V" required>
                    </div>

                    <div class="form-group">
                        <label for="genre">Genre</label>
                        <input type="text" name="genre" id="genre" class="form-control" placeholder="Contoh: Action, Open World" required>
                    </div>

                    <div class="form-group">
                        <label for="harga_sewa">Harga Sewa (per hari)</label>
                        <input type="number" name="harga_sewa" id="harga_sewa" class="form-control" placeholder="Contoh: 5000" required>
                    </div>

                    <div class="form-group">
                        <label for="harga_beli">Harga Beli</label>
                        <input type="number" name="harga_beli" id="harga_beli" class="form-control" placeholder="Contoh: 500000" required>
                    </div>

                    <div class="form-group">
                        <label for="stok">Stok</label>
                        <input type="number" name="stok" id="stok" class="form-control" placeholder="Contoh: 10" required>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi">Deskripsi Game</label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control" rows="5" placeholder="Masukkan deskripsi lengkap game..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="gambar">Upload Gambar (Opsional)</label>
                        <input type="file" name="gambar" id="gambar" class="form-control" accept="image/*">
                        <small style="color: var(--text-grey);">Jika tidak diupload, sistem akan mencari gambar otomatis berdasarkan judul di folder aset/images/</small>
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary btn-block" style="margin-top: 20px;">
                        <i class="fas fa-save"></i> Simpan Game
                    </button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
