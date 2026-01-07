<aside class="sidebar">
    <div class="brand">
        <i class="fas fa-shield-alt"></i> GameRent Admin
    </div>
    
    <nav>
        <?php $page = basename($_SERVER['PHP_SELF']); ?>

        <a href="admin_dashboard.php" class="menu-btn <?= ($page == 'admin_dashboard.php') ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        
        <a href="admin_games.php" class="menu-btn <?= ($page == 'admin_games.php') ? 'active' : '' ?>">
            <i class="fas fa-gamepad"></i> Manajemen Game
        </a>
        
        <a href="admin_users.php" class="menu-btn <?= ($page == 'admin_users.php') ? 'active' : '' ?>">
            <i class="fas fa-users"></i> Manajemen User
        </a>
        
        <a href="admin_transactions.php" class="menu-btn <?= ($page == 'admin_transactions.php') ? 'active' : '' ?>">
            <i class="fas fa-receipt"></i> Cek Transaksi
        </a>

        <a href="admin_topup.php" class="menu-btn <?= ($page == 'admin_topup.php') ? 'active' : '' ?>">
            <i class="fas fa-wallet"></i> Atur Nominal Top Up
        </a>

        <a href="admin_vouchers.php" class="menu-btn <?= ($page == 'admin_vouchers.php') ? 'active' : '' ?>">
            <i class="fas fa-ticket-alt"></i> Voucher Diskon
        </a>

        <a href="index.php?preview=user" class="menu-btn">
            <i class="fas fa-eye"></i> Preview Dashboard
        </a>
    </nav>
</aside>
