<?php
session_start();
include 'config/database.php';

// Proteksi: Hanya admin yang boleh akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Statistik Dashboard
$total_games = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM games"))['total'];
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='user'"))['total'];
$total_transactions = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM transactions"))['total'];
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_bayar) as total FROM transactions"))['total'] ?? 0;

$nama_admin = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - GameRent</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="aset/style.css">
</head>
<body>

    <?php include 'aset/admin_sidebar.php'; ?>

    <main class="main-content">
        
        <header class="top-bar">
            <div class="welcome-text">
                <h2>Dashboard Admin - Selamat Datang, <b><?= htmlspecialchars($nama_admin); ?>!</b></h2>
            </div>
            
            <div class="user-action">
                <div class="user-dropdown">
                    <div class="profile-trigger">
                        <div style="text-align: right; font-size: 13px;">
                            <span style="display: block; color: var(--text-grey);">Admin</span>
                            <span style="font-weight: bold;"><?= htmlspecialchars($nama_admin); ?></span>
                        </div>
                        <div class="profile-pic-box">
                            <i class="fas fa-user-shield"></i>
                        </div>
                    </div>

                    <div class="dropdown-menu">
                        <a href="logout.php" class="logout-btn" onclick="return confirm('Yakin ingin keluar?');">
                            <i class="fas fa-sign-out-alt" style="margin-right: 8px;"></i> Keluar
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <section>
            <h3 class="section-title">Statistik Sistem</h3>
            
            <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
                
                <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">Total Game</div>
                            <div style="font-size: 32px; font-weight: bold;"><?= $total_games ?></div>
                        </div>
                        <i class="fas fa-gamepad" style="font-size: 50px; opacity: 0.3;"></i>
                    </div>
                </div>

                <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">Total User</div>
                            <div style="font-size: 32px; font-weight: bold;"><?= $total_users ?></div>
                        </div>
                        <i class="fas fa-users" style="font-size: 50px; opacity: 0.3;"></i>
                    </div>
                </div>

                <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">Total Transaksi</div>
                            <div style="font-size: 32px; font-weight: bold;"><?= $total_transactions ?></div>
                        </div>
                        <i class="fas fa-exchange-alt" style="font-size: 50px; opacity: 0.3;"></i>
                    </div>
                </div>

                <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">Total Pendapatan</div>
                            <div style="font-size: 24px; font-weight: bold;">Rp <?= number_format($total_revenue) ?></div>
                        </div>
                        <i class="fas fa-wallet" style="font-size: 50px; opacity: 0.3;"></i>
                    </div>
                </div>

            </div>

            <h3 class="section-title">Menu Manajemen</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                
                <a href="admin_games.php" style="text-decoration: none;">
                    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: transform 0.2s; cursor: pointer;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <i class="fas fa-gamepad" style="font-size: 40px; color: #667eea;"></i>
                            <div>
                                <h4 style="margin: 0; color: #333; font-size: 18px;">Manajemen Game</h4>
                                <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Tambah, Edit, Hapus Game</p>
                            </div>
                        </div>
                    </div>
                </a>

                <a href="admin_users.php" style="text-decoration: none;">
                    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: transform 0.2s; cursor: pointer;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <i class="fas fa-users" style="font-size: 40px; color: #f5576c;"></i>
                            <div>
                                <h4 style="margin: 0; color: #333; font-size: 18px;">Manajemen User</h4>
                                <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Kelola Daftar User</p>
                            </div>
                        </div>
                    </div>
                </a>

                <a href="admin_transactions.php" style="text-decoration: none;">
                    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: transform 0.2s; cursor: pointer;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <i class="fas fa-receipt" style="font-size: 40px; color: #43e97b;"></i>
                            <div>
                                <h4 style="margin: 0; color: #333; font-size: 18px;">Cek Transaksi</h4>
                                <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Mengelola Transaksi User</p>
                            </div>
                        </div>
                    </div>
                </a>

                <a href="admin_topup.php" style="text-decoration: none;">
                    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: transform 0.2s; cursor: pointer;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <i class="fas fa-wallet" style="font-size: 40px; color: #43e97b;"></i>
                            <div>
                                <h4 style="margin: 0; color: #333; font-size: 18px;">Manajemen Top Up</h4>
                                <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Mengelola Nominal Top up</p>
                            </div>
                        </div>
                    </div>
                </a>

                <a href="admin_vouchers.php" style="text-decoration: none;">
                    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: transform 0.2s; cursor: pointer;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <i class="fas fa-ticket" style="font-size: 40px; color: #43e97b;"></i>
                            <div>
                                <h4 style="margin: 0; color: #333; font-size: 18px;">Manajemen Voucher</h4>
                                <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Mengelola Voucher Diskon</p>
                            </div>
                        </div>
                    </div>
                </a>

            </div>
        </section>

    </main>

</body>
</html>
