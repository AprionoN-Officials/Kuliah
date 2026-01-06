<?php
session_start();
include 'config/database.php'; // Pastikan path ini benar

// 1. Cek Login
// Pastikan di file login.php Anda menyimpan session dengan nama 'user_id'
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='login.php';</script>";
    exit;
}

// 2. Ambil Data User
$id_user = $_SESSION['user_id'];

// Mengambil data user. Pastikan tabel Anda bernama 'users' atau sesuaikan (misal: 'admin', 'pelanggan')
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id_user'");
$data = mysqli_fetch_assoc($query);

// Cek jika kolom 'saldo' atau 'email' tidak ada di database agar tidak error
$saldo = isset($data['saldo']) ? $data['saldo'] : 0;
$email = isset($data['email']) ? $data['email'] : 'Email belum diatur';
$username = isset($data['username']) ? $data['username'] : 'User';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Saya</title>
    <link rel="stylesheet" href="aset/style.css"> 
    <script src="https://cdn.tailwindcss.com"></script> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">

<div class="flex min-h-screen">
    
    <div class="w-64 bg-white border-r hidden md:block">
        <?php include 'aset/sidebar.php'; ?>
    </div>

    <div class="flex-1 p-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Detail Akun</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="bg-gradient-to-r from-blue-600 to-blue-400 rounded-xl shadow-lg p-6 text-white">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-blue-100 text-sm font-semibold uppercase">Saldo Anda</p>
                        <h3 class="text-4xl font-bold mt-2">Rp <?php echo number_format($saldo, 0, ',', '.'); ?></h3>
                    </div>
                    <i class="fas fa-wallet text-4xl text-blue-200 opacity-50"></i>
                </div>
                <a href="topup.php" class="mt-6 inline-block bg-white text-blue-600 px-4 py-2 rounded-lg font-bold text-sm hover:bg-gray-100 transition">
                    + Isi Saldo
                </a>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="font-bold text-gray-700 mb-4 border-b pb-2">Info Pengguna</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="text-xs text-gray-400 font-bold uppercase">Username</label>
                        <p class="text-lg font-medium text-gray-800"><?php echo $username; ?></p>
                    </div>
                    
                    <div>
                        <label class="text-xs text-gray-400 font-bold uppercase">Email</label>
                        <p class="text-lg font-medium text-gray-800"><?php echo $email; ?></p>
                    </div>

                    <div>
                        <label class="text-xs text-gray-400 font-bold uppercase">Password</label>
                        <div class="flex justify-between items-center">
                            <p class="text-lg font-medium text-gray-800">••••••••</p>
                            <button onclick="document.getElementById('modalReset').classList.remove('hidden')" 
                                    class="text-blue-500 hover:text-blue-700 text-sm font-semibold">
                                Ubah Password
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modalReset" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-xl shadow-2xl w-96">
        <h3 class="text-xl font-bold mb-4">Ganti Password</h3>
        <form action="proses_ubah_password.php" method="POST">
            <div class="mb-3">
                <label class="block text-sm mb-1">Password Lama</label>
                <input type="password" name="pass_lama" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-3">
                <label class="block text-sm mb-1">Password Baru</label>
                <input type="password" name="pass_baru" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm mb-1">Konfirmasi Password</label>
                <input type="password" name="pass_konf" class="w-full border rounded p-2" required>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalReset').classList.add('hidden')" class="px-4 py-2 text-gray-500">Batal</button>
                <button type="submit" name="submit_password" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>