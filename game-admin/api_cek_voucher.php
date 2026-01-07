<?php
header('Content-Type: application/json');
include 'config/database.php';
// Pastikan tabel vouchers ada untuk menghindari error saat pertama kali
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
## tees
$kode = isset($_GET['kode']) ? mysqli_real_escape_string($conn, $_GET['kode']) : '';
$nominal = isset($_GET['nominal']) ? intval($_GET['nominal']) : 0;

$response = [
    'status' => 'error',
    'message' => 'Kode tidak valid',
    'potongan' => 0,
    'stok' => 0
];

if ($kode) {
    $query = "SELECT * FROM vouchers WHERE kode = '$kode' AND aktif = 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $voucher = mysqli_fetch_assoc($result);

        if ((int)$voucher['stok'] <= 0) {
            $response['message'] = 'Stok voucher habis.';
            echo json_encode($response);
            exit;
        }
        
        // 1. Cek Syarat Minimal Transaksi
        if ($nominal >= $voucher['min_transaksi']) {
            
            $nilai_diskon = 0;

            // 2. LOGIKA BARU: Cek Tipe Diskon
            if ($voucher['tipe'] == 'fixed') {
                $nilai_diskon = $voucher['potongan'];
            } else if ($voucher['tipe'] == 'percent') {
                $hitung_persen = $nominal * ($voucher['potongan'] / 100);
                $nilai_diskon = ($voucher['max_potongan'] > 0 && $hitung_persen > $voucher['max_potongan']) ? $voucher['max_potongan'] : $hitung_persen;
            }

            // Batasi agar tidak melebihi nominal (izinkan 100% = gratis)
            if ($nilai_diskon > $nominal) {
                $nilai_diskon = $nominal;
            }

            // Kirim Hasil ke Javascript
            $response['status'] = 'success';
            $response['message'] = 'Voucher diterapkan!';
            $response['potongan'] = intval($nilai_diskon); // Pastikan bulat
            $response['stok'] = (int)$voucher['stok'];

        } else {
            $response['message'] = 'Minimal transaksi Rp ' . number_format($voucher['min_transaksi']);
        }
    } else {
        $response['message'] = 'Kode voucher tidak ditemukan.';
    }
}

echo json_encode($response);
?>