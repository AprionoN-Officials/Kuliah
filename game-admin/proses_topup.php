<?php
session_start();
include 'config/database.php';

if (!isset($_POST['konfirmasi'])) {
    header("Location: topup.php");
    exit;
}

$nominal = intval($_POST['nominal']);
$target_username = mysqli_real_escape_string($conn, $_POST['target_username']);
$kode_diskon = isset($_POST['kode_diskon']) ? mysqli_real_escape_string($conn, $_POST['kode_diskon']) : '';

// 1. Cek User Tujuan
$cek_user = mysqli_query($conn, "SELECT id FROM users WHERE username = '$target_username'");
if (mysqli_num_rows($cek_user) == 0) {
    echo "<script>alert('Username tidak ditemukan!'); window.location='topup.php';</script>";
    exit;
}

// 2. HITUNG DISKON DI SERVER (Agar tidak bisa dicurangi)
// ... (Kode atas sama)

// 2. HITUNG DISKON DI SERVER
$potongan = 0;

if (!empty($kode_diskon)) {
    $cek_voucher = mysqli_query($conn, "SELECT * FROM vouchers WHERE kode = '$kode_diskon' AND aktif = 1");
    if (mysqli_num_rows($cek_voucher) > 0) {
        $data_voucher = mysqli_fetch_assoc($cek_voucher);
        
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
            // --------------------------------------
        }
    }
}

// Pastikan potongan tidak minus / melebihi nominal
if ($potongan >= $nominal) { $potongan = 0; }

// 3. Proses Update Saldo (Saldo Masuk = Nominal Full, User Bayar = Nominal - Diskon)
// Di sistem topup biasanya: Saldo bertambah sesuai nominal, tapi user bayar lebih murah.
// Atau saldo bertambah hanya sesuai yang dibayar? 
// -> Biasanya: Saldo masuk FULL (misal 10rb), user bayar diskon (misal 5rb). Sistem 'rugi' demi promo.

$query_topup = "UPDATE users SET saldo = saldo + $nominal WHERE username = '$target_username'";

if (mysqli_query($conn, $query_topup)) {
    $total_bayar = $nominal - $potongan;
    echo "<script>
            alert('Top Up Berhasil!\\nSaldo Masuk: Rp ".number_format($nominal)."\\nAnda Bayar: Rp ".number_format($total_bayar)."');
            window.location='topup.php';
          </script>";
} else {
    echo "<script>alert('Gagal sistem database.'); window.location='topup.php';</script>";
}
?>