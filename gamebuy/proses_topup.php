<?php
session_start();
include 'config/database.php';
// Pastikan tabel vouchers ada agar pengecekan voucher tidak error
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

// Tambah kolom stok jika skema lama belum memilikinya
$col_check = mysqli_query($conn, "SHOW COLUMNS FROM vouchers LIKE 'stok'");
if ($col_check && mysqli_num_rows($col_check) === 0) {
    mysqli_query($conn, "ALTER TABLE vouchers ADD COLUMN stok INT NOT NULL DEFAULT 0 AFTER min_transaksi");
}

if (!isset($_POST['konfirmasi'])) {
    header("Location: user/topup.php");
    exit;
}

$nominal = intval($_POST['nominal']);
$target_username = mysqli_real_escape_string($conn, $_POST['target_username']);
$kode_diskon = isset($_POST['kode_diskon']) ? mysqli_real_escape_string($conn, $_POST['kode_diskon']) : '';

// 1. Cek User Tujuan
$cek_user = mysqli_query($conn, "SELECT id FROM users WHERE username = '$target_username'");
if (mysqli_num_rows($cek_user) == 0) {
    echo "<script>alert('Username tidak ditemukan!'); window.location='user/topup.php';</script>";
    exit;
}

// 2. HITUNG DISKON DI SERVER (Agar tidak bisa dicurangi)
// ... (Kode atas sama)

// 2. HITUNG DISKON DI SERVER
$potongan = 0;
$voucher_dipakai = false;
$voucher_id = null;

if (!empty($kode_diskon)) {
    $cek_voucher = mysqli_query($conn, "SELECT * FROM vouchers WHERE kode = '$kode_diskon' AND aktif = 1");
    if (mysqli_num_rows($cek_voucher) > 0) {
        $data_voucher = mysqli_fetch_assoc($cek_voucher);
        $voucher_id = (int)$data_voucher['id'];

        if ((int)$data_voucher['stok'] <= 0) {
            echo "<script>alert('Stok voucher habis!'); window.location='user/topup.php';</script>";
            exit;
        }
        
        // Cek syarat minimal
        if ($nominal >= $data_voucher['min_transaksi']) {
            
            // --- LOGIKA BARU (Sama seperti API) ---
            if ($data_voucher['tipe'] == 'fixed') {
                $potongan = $data_voucher['potongan'];
            } else {
                // Hitung Persen
                $hitung = $nominal * ($data_voucher['potongan'] / 100);
                // Cek Max Cap
                if ($data_voucher['max_potongan'] > 0 && $hitung > $data_voucher['max_potongan']) {
                    $potongan = $data_voucher['max_potongan'];
                } else {
                    $potongan = $hitung;
                }
            }

            // Batasi agar tidak melebihi nominal (izinkan 100% gratis)
            if ($potongan > $nominal) {
                $potongan = $nominal;
            }

            $voucher_dipakai = $potongan > 0;
        }
    }
}

// Kurangi stok voucher ketika dipakai
if ($voucher_dipakai && $voucher_id !== null) {
    mysqli_query($conn, "UPDATE vouchers SET stok = stok - 1 WHERE id = $voucher_id AND stok > 0");
    // Jika stok tiba-tiba habis karena race condition, batal pakai voucher
    if (mysqli_affected_rows($conn) === 0) {
        $potongan = 0;
        $voucher_dipakai = false;
    }
}

$total_bayar = max(0, $nominal - $potongan);

// 3. Proses Update Saldo (Saldo Masuk = Nominal Full, User Bayar = Nominal - Diskon)
// Di sistem topup biasanya: Saldo bertambah sesuai nominal, tapi user bayar lebih murah.
// Atau saldo bertambah hanya sesuai yang dibayar? 
// -> Biasanya: Saldo masuk FULL (misal 10rb), user bayar diskon (misal 5rb). Sistem 'rugi' demi promo.

$query_topup = "UPDATE users SET saldo = saldo + $nominal WHERE username = '$target_username'";

if (mysqli_query($conn, $query_topup)) {
    echo "<script>
            alert('Top Up Berhasil!\\nSaldo Masuk: Rp ".number_format($nominal)."\\nAnda Bayar: Rp ".number_format($total_bayar)."');
            window.location='user/topup.php';
          </script>";
} else {
    echo "<script>alert('Gagal sistem database.'); window.location='user/topup.php';</script>";
}
?>