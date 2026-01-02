<?php
session_start();
include 'config/database.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username_saya = $_SESSION['username'];

// Ambil Saldo Terkini
$query_user = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($query_user);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Isi Saldo - GameRent</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="aset/style.css">
</head>
<body>

    <?php include 'aset/sidebar.php'; ?>

    <main class="main-content">
        
        <header class="top-bar">
            <h2><i class="fas fa-wallet"></i> Isi Saldo</h2>
        </header>

        <div class="saldo-card">
            <div>Saldo Kamu Saat Ini</div>
            <div class="saldo-amount">Rp <?= number_format($user['saldo']); ?></div>
        </div>

        <section>
            <h3 class="topup-title">Pilih Nominal Top Up</h3>
            
            <div class="nominal-grid">
                <button type="button" class="nominal-btn" onclick="bukaModal(10000)">10.000</button>
                <button type="button" class="nominal-btn" onclick="bukaModal(20000)">20.000</button>
                <button type="button" class="nominal-btn" onclick="bukaModal(50000)">50.000</button>
                <button type="button" class="nominal-btn" onclick="bukaModal(100000)">100.000</button>
                <button type="button" class="nominal-btn" onclick="bukaModal(250000)">250.000</button>
                <button type="button" class="nominal-btn" onclick="bukaModal(500000)">500.000</button>
            </div>
        </section>

    </main>

    <div class="modal-overlay" id="topupModal">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-title">Konfirmasi Top Up</div>
                <div class="close-modal" onclick="tutupModal()">&times;</div>
            </div>

            <form action="proses_topup.php" method="POST">
                
                <form action="proses_topup.php" method="POST">
    
    <div class="form-group">
        <label style="font-size: 13px; color: #888;">Top Up Untuk (Username):</label>
        <input type="text" name="target_username" class="form-control" value="<?= $username_saya; ?>" required readonly style="background-color: #eee;">
    </div>

    <div class="form-group">
        <label style="font-size: 13px; color: #888;">Kode Voucher:</label>
        <div style="display: flex; gap: 5px;">
            <input type="text" name="kode_diskon" id="inputKode" class="form-control" placeholder="Contoh: HEMAT10">
            <button type="button" class="btn btn-primary" onclick="cekVoucher()">Gunakan</button>
        </div>
        <small id="pesanVoucher" style="display:block; margin-top:5px; font-weight:bold;"></small>
    </div>

    <div style="background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <div class="detail-row">
            <span>Nominal</span>
            <span id="displayNominal">Rp 0</span>
        </div>
        <div class="detail-row" style="color: green;">
            <span>Diskon</span>
            <span id="displayDiskon">- Rp 0</span>
        </div>
        <div class="total-row">
            <span>Total Bayar</span>
            <span id="displayTotal">Rp 0</span>
        </div>
    </div>

    <input type="hidden" name="nominal" id="inputNominal" value="">
    <input type="hidden" name="konfirmasi" value="yes">

    <button type="submit" class="btn btn-primary btn-block">
        <i class="fas fa-check-circle"></i> Bayar Sekarang
    </button>
</form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('topupModal');
        const displayNominal = document.getElementById('displayNominal');
        const displayDiskon = document.getElementById('displayDiskon');
        const displayTotal = document.getElementById('displayTotal');
        const inputNominal = document.getElementById('inputNominal');
        const inputKode = document.getElementById('inputKode');
        const pesanVoucher = document.getElementById('pesanVoucher');

        // Variabel Global
        let currentNominal = 0;
        let currentDiskon = 0;

        function formatRupiah(angka) {
            return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        // 1. Buka Modal
        function bukaModal(nominal) {
            currentNominal = nominal;
            currentDiskon = 0; 
            
            // Reset Input & Pesan
            inputKode.value = "";
            pesanVoucher.innerText = "";
            
            updateTampilan(); 
            inputNominal.value = nominal;
            modal.classList.add('active');
        }

        // 2. LOGIKA BARU: Cek Voucher via Database (AJAX Fetch)
        function cekVoucher() {
            let kode = inputKode.value.toUpperCase().trim();

            if (kode === "") {
                pesanVoucher.innerHTML = "<span style='color:red;'>Masukan kode dulu!</span>";
                return;
            }

            // Tampilkan status "Loading..."
            pesanVoucher.innerHTML = "<span style='color:grey;'>Mengecek kode...</span>";

            // Panggil file PHP API yang kita buat tadi
            fetch('api_cek_voucher.php?kode=' + kode + '&nominal=' + currentNominal)
                .then(response => response.json())
                .then(data => {
                    // Logic setelah mendapat jawaban dari database
                    if (data.status === 'success') {
                        // Jika Sukses
                        currentDiskon = data.potongan;
                        
                        // Validasi Keamanan Frontend (Diskon gaboleh > Nominal)
                        if (currentDiskon >= currentNominal) {
                            currentDiskon = 0;
                            pesanVoucher.innerHTML = "<span style='color:red;'>Potongan melebihi harga bayar!</span>";
                        } else {
                            pesanVoucher.innerHTML = "<span style='color:green;'>" + data.message + " Hemat " + formatRupiah(currentDiskon) + "</span>";
                        }
                    } else {
                        // Jika Gagal (Kode salah / Kurang nominal)
                        currentDiskon = 0;
                        pesanVoucher.innerHTML = "<span style='color:red;'>" + data.message + "</span>";
                    }
                    updateTampilan();
                })
                .catch(error => {
                    console.error('Error:', error);
                    pesanVoucher.innerHTML = "<span style='color:red;'>Terjadi kesalahan server.</span>";
                });
        }

        // 3. Update Tampilan Angka
        function updateTampilan() {
            let totalBayar = currentNominal - currentDiskon;

            displayNominal.innerText = formatRupiah(currentNominal);
            displayDiskon.innerText = "- " + formatRupiah(currentDiskon);
            displayTotal.innerText = formatRupiah(totalBayar);
        }

        function tutupModal() {
            modal.classList.remove('active');
        }

        window.onclick = function(event) {
            if (event.target == modal) tutupModal();
        }
    </script>
</body>
</html>