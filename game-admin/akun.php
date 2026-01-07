<?php
session_start();
include 'config/database.php'; // Pastikan path ini benar

// 1. Cek Login
// Pastikan di file login.php Anda menyimpan session dengan nama 'user_id'
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='login.php';</script>";
    exit;
}

// 2. Ambil Data User & helper pesan
$id_user = $_SESSION['user_id'];
$message = '';
$message_type = '';

// Proses update username
if (isset($_POST['ganti_username'])) {
    $new_username = mysqli_real_escape_string($conn, trim($_POST['username_baru'] ?? ''));
    if ($new_username === '') {
        $message = 'Username tidak boleh kosong.';
        $message_type = 'error';
    } else {
        $dup = mysqli_query($conn, "SELECT id FROM users WHERE username = '$new_username' AND id <> $id_user LIMIT 1");
        if ($dup && mysqli_num_rows($dup) > 0) {
            $message = 'Username sudah dipakai.';
            $message_type = 'error';
        } else {
            if (mysqli_query($conn, "UPDATE users SET username='$new_username' WHERE id=$id_user")) {
                $_SESSION['username'] = $new_username;
                $message = 'Username berhasil diperbarui.';
                $message_type = 'success';
            } else {
                $message = 'Gagal memperbarui username.';
                $message_type = 'error';
            }
        }
    }
}

// Proses hapus akun
if (isset($_POST['hapus_akun'])) {
    // Hapus transaksi lalu user, lalu logout
    mysqli_begin_transaction($conn);
    try {
        mysqli_query($conn, "DELETE FROM transactions WHERE user_id = $id_user");
        mysqli_query($conn, "DELETE FROM users WHERE id = $id_user");
        mysqli_commit($conn);
        session_destroy();
        header('Location: index.php');
        exit;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $message = 'Gagal menghapus akun.';
        $message_type = 'error';
    }
}

// Mengambil data user. Pastikan tabel Anda bernama 'users' atau sesuaikan (misal: 'admin', 'pelanggan')
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id_user'");
$data = mysqli_fetch_assoc($query);

// Cek jika kolom 'saldo' atau 'email' tidak ada di database agar tidak error
$saldo = isset($data['saldo']) ? $data['saldo'] : 0;
$username = isset($data['username']) ? $data['username'] : 'User';

// Hitung kepemilikan dan sewa aktif
$owned_res = mysqli_query($conn, "SELECT COUNT(*) AS c FROM transactions WHERE user_id=$id_user AND tipe_transaksi='beli'");
$owned_count = $owned_res ? (int)mysqli_fetch_assoc($owned_res)['c'] : 0;
$rent_res = mysqli_query($conn, "SELECT COUNT(*) AS c FROM transactions WHERE user_id=$id_user AND tipe_transaksi='sewa'");
$rent_count = $rent_res ? (int)mysqli_fetch_assoc($rent_res)['c'] : 0;

// Ambil riwayat transaksi user
$trx_sql = "SELECT t.*, g.judul FROM transactions t LEFT JOIN games g ON g.id = t.game_id WHERE t.user_id = '$id_user' ORDER BY t.id DESC";
$trx_result = mysqli_query($conn, $trx_sql);
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

        <?php if ($message): ?>
            <div class="mb-4 px-4 py-3 rounded <?php echo $message_type === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

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
                
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="text-xs text-gray-400 font-bold uppercase">Username</label>
                        <input type="text" name="username_baru" value="<?php echo htmlspecialchars($username); ?>" class="w-full border rounded p-2 mt-1" required>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" name="ganti_username" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Simpan Username</button>
                        <button type="button" onclick="document.getElementById('modalReset').classList.remove('hidden')" 
                                class="text-blue-500 hover:text-blue-700 text-sm font-semibold">
                            Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div class="bg-white border rounded-lg p-4 shadow-sm">
                <div class="text-xs text-gray-500 uppercase">Game Dimiliki</div>
                <div class="text-2xl font-bold text-gray-800"><?php echo $owned_count; ?></div>
            </div>
            <div class="bg-white border rounded-lg p-4 shadow-sm">
                <div class="text-xs text-gray-500 uppercase">Game Disewa</div>
                <div class="text-2xl font-bold text-gray-800"><?php echo $rent_count; ?></div>
            </div>
            <div class="bg-white border rounded-lg p-4 shadow-sm">
                <div class="text-xs text-gray-500 uppercase">Aksi Akun</div>
                <form method="POST" onsubmit="return confirm('Hapus akun beserta seluruh transaksi?')">
                    <button type="submit" name="hapus_akun" class="mt-2 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm w-full">Hapus Akun</button>
                </form>
            </div>
        </div>

        <div class="mt-8 bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-700">Riwayat Transaksi</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2">Kode</th>
                            <th class="py-2">Game</th>
                            <th class="py-2">Tipe</th>
                            <th class="py-2">Durasi</th>
                            <th class="py-2">Total</th>
                            <th class="py-2">Status</th>
                            <th class="py-2">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($trx_result && mysqli_num_rows($trx_result) > 0): ?>
                            <?php while($trx = mysqli_fetch_assoc($trx_result)): ?>
                                <?php $kode = 'TRX-' . str_pad($trx['id'], 6, '0', STR_PAD_LEFT); ?>
                                <tr class="border-b last:border-0">
                                    <td class="py-2 font-semibold text-gray-800"><?= $kode; ?></td>
                                    <td class="py-2"><?= htmlspecialchars($trx['judul'] ?? ''); ?></td>
                                    <td class="py-2 uppercase"><?= htmlspecialchars($trx['tipe_transaksi']); ?></td>
                                    <td class="py-2"><?= $trx['tipe_transaksi'] === 'sewa' ? ($trx['durasi_hari'] . ' hari') : '-'; ?></td>
                                    <td class="py-2">Rp <?= number_format($trx['total_bayar']); ?></td>
                                    <td class="py-2"><?= htmlspecialchars($trx['status']); ?></td>
                                    <td class="py-2"><?= htmlspecialchars($trx['tanggal_pinjam'] ?? ''); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="py-3 text-center text-gray-500">Belum ada transaksi.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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