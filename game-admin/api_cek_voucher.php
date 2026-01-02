<?php
header('Content-Type: application/json');
include 'config/database.php';

$kode = isset($_GET['kode']) ? mysqli_real_escape_string($conn, $_GET['kode']) : '';
$nominal = isset($_GET['nominal']) ? intval($_GET['nominal']) : 0;

$response = [
    'status' => 'error',
    'message' => 'Kode tidak valid',
    'potongan' => 0
];

if ($kode) {
    $query = "SELECT * FROM vouchers WHERE kode = '$kode' AND aktif = 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $voucher = mysqli_fetch_assoc($result);
        
        // 1. Cek Syarat Minimal Transaksi
        if ($nominal >= $voucher['min_transaksi']) {
            
            $nilai_diskon = 0;

            // 2. LOGIKA BARU: Cek Tipe Diskon
            if ($voucher['tipe'] == 'fixed') {
                // Tipe Potongan Tetap (Rp)
                $nilai_diskon = $voucher['potongan'];
            
            } else if ($voucher['tipe'] == 'percent') {
                // Tipe Persen (%)
                // Rumus: (Nominal * Persen) / 100
                $hitung_persen = $nominal * ($voucher['potongan'] / 100);
                
                // Cek Max Cap (Mentok di angka berapa?)
                // Jika hasil hitung > max_potongan, maka pakai max_potongan
                if ($voucher['max_potongan'] > 0 && $hitung_persen > $voucher['max_potongan']) {
                    $nilai_diskon = $voucher['max_potongan'];
                } else {
                    $nilai_diskon = $hitung_persen;
                }
            }

            // Kirim Hasil ke Javascript
            $response['status'] = 'success';
            $response['message'] = 'Voucher diterapkan!';
            $response['potongan'] = intval($nilai_diskon); // Pastikan bulat

        } else {
            $response['message'] = 'Minimal transaksi Rp ' . number_format($voucher['min_transaksi']);
        }
    } else {
        $response['message'] = 'Kode voucher tidak ditemukan.';
    }
}

echo json_encode($response);
?>