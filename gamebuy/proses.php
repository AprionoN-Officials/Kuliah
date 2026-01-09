<?php
session_start();
include 'config/database.php';

// 1. Cek Akses: Apakah ada data yang dikirim?
if (!isset($_POST['game_id']) || !isset($_SESSION['user_id'])) {
    // Jika ada orang iseng akses file ini lewat URL langsung
    header("Location: index.php");
    exit;
}

// 2. Tangkap Data Input
$user_id = $_SESSION['user_id'];
$game_id = $_POST['game_id'];
$tipe    = $_POST['tipe']; // 'sewa' atau 'beli'
$durasi  = isset($_POST['durasi']) ? intval($_POST['durasi']) : 0;

// 3. Ambil Data Game & User Terbaru dari Database
// Kita perlu cek harga terbaru dan saldo user saat ini
$query_game = mysqli_query($conn, "SELECT * FROM games WHERE id = '$game_id'");
$game = mysqli_fetch_assoc($query_game);

$query_user = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($query_user);

// 4. Logika Hitung Biaya
$total_bayar = 0;
$tanggal_kembali = "NULL"; // Default jika beli

if ($tipe == 'sewa') {
    $total_bayar = $game['harga_sewa'] * $durasi;
    
    // Hitung Tanggal Kembali (Hari ini + Durasi)
    // Contoh: NOW() + INTERVAL 3 DAY
    $tanggal_kembali = "DATE_ADD(NOW(), INTERVAL $durasi DAY)";
    $status_awal = 'dipinjam';

} else if ($tipe == 'beli') {
    $total_bayar = $game['harga_beli'];
    $durasi = 0; // Tidak ada durasi sewa
    $status_awal = 'permanent';
}

// 5. VALIDASI (Penting!)

// A. Cek Stok
if ($game['stok'] <= 0) {
    echo "<script>alert('Gagal! Stok game sudah habis.'); window.location='user/detail.php?id=$game_id';</script>";
    exit;
}

// B. Cek Saldo
if ($user['saldo'] < $total_bayar) {
    echo "<script>alert('Saldo tidak cukup! Total: Rp ".number_format($total_bayar).". Silakan isi saldo.'); window.location='user/topup.php';</script>";
    exit;
}

// 6. EKSEKUSI TRANSAKSI (Jika lolos validasi)
// Gunakan Transaction SQL biar aman (Semua sukses atau batal semua)
mysqli_begin_transaction($conn);

try {

    
    // A. Kurangi Saldo User
    $update_user = "UPDATE users SET saldo = saldo - $total_bayar WHERE id = '$user_id'";
    mysqli_query($conn, $update_user);

    // B. Kurangi Stok Game
    $update_game = "UPDATE games SET stok = stok - 1 WHERE id = '$game_id'";
    mysqli_query($conn, $update_game);

    // C. Catat di Tabel Transaksi
    // Perhatikan sintaks SQL untuk tanggal_kembali karena dia bisa berupa fungsi SQL atau NULL
    if ($tipe == 'sewa') {
        $sql_transaksi = "INSERT INTO transactions (user_id, game_id, tipe_transaksi, durasi_hari, tanggal_kembali, total_bayar, status) 
                          VALUES ('$user_id', '$game_id', '$tipe', '$durasi', $tanggal_kembali, '$total_bayar', '$status_awal')";
    } else {
        $sql_transaksi = "INSERT INTO transactions (user_id, game_id, tipe_transaksi, durasi_hari, tanggal_kembali, total_bayar, status) 
                          VALUES ('$user_id', '$game_id', '$tipe', '$durasi', NULL, '$total_bayar', '$status_awal')";
    }
    
    mysqli_query($conn, $sql_transaksi);
    $trx_id = mysqli_insert_id($conn);
    $kode_transaksi = 'TRX-' . str_pad($trx_id, 6, '0', STR_PAD_LEFT);

    // Jika semua lancar, simpan perubahan
    mysqli_commit($conn);

    // Tampilkan halaman sukses dengan kode transaksi
    echo "<!DOCTYPE html>\n";
    echo "<html lang='id'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    echo "<link rel='stylesheet' href='aset/style.css'>";
    echo "<title>Transaksi Berhasil</title></head><body style='display:flex;align-items:center;justify-content:center;min-height:100vh;background:#f5f7fb;'>";
    echo "<div style='background:white;padding:24px 28px;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,0.08);max-width:420px;width:100%;text-align:center;font-family:Arial, sans-serif;'>";
    echo "<div style='font-size:40px;color:#43e97b;margin-bottom:8px;'>&#10003;</div>";
    echo "<h2 style='margin:0 0 8px 0;'>Transaksi Berhasil</h2>";
    echo "<p style='margin:0 0 16px 0;color:#555;'>Kode Transaksi Anda:</p>";
    echo "<div style='font-size:20px;font-weight:bold;color:#1f2d3d;margin-bottom:16px;'>$kode_transaksi</div>";
    echo "<p style='margin:0 0 18px 0;color:#666;'>Simpan kode ini untuk pengecekan di admin atau riwayat transaksi.</p>";
    echo "<div style='display:flex;gap:10px;justify-content:center;flex-wrap:wrap;'>";
    echo "<a href='user/library.php' class='btn btn-primary' style='padding:10px 16px;border-radius:8px;text-decoration:none;'>Lihat Library</a>";
    echo "<a href='user/akun.php' class='btn' style='padding:10px 16px;border-radius:8px;border:1px solid #dcdcdc;text-decoration:none;color:#333;'>Riwayat Transaksi</a>";
    echo "</div>";
    echo "</div></body></html>";
    exit;

} catch (Exception $e) {
    // Jika ada error, batalkan semua perubahan
    mysqli_rollback($conn);
    echo "<script>alert('Terjadi kesalahan sistem. Transaksi dibatalkan.'); window.location='index.php';</script>";
}
?>