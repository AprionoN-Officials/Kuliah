<aside class="sidebar">
    <div class="brand">
        <i class="fas fa-shield-alt"></i> GameRent Admin
    </div>
    
    <nav>
        <?php $page = basename($_SERVER['PHP_SELF']); ?>

        <a href="../admin/dashboard.php" class="menu-btn <?= ($page == 'dashboard.php') ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        
        <a href="../admin/games.php" class="menu-btn <?= ($page == 'games.php') ? 'active' : '' ?>">
            <i class="fas fa-gamepad"></i> Manajemen Game
        </a>
        
        <a href="../admin/users.php" class="menu-btn <?= ($page == 'users.php') ? 'active' : '' ?>">
            <i class="fas fa-users"></i> Manajemen User
        </a>
        
        <a href="../admin/transactions.php" class="menu-btn <?= ($page == 'transactions.php') ? 'active' : '' ?>">
            <i class="fas fa-receipt"></i> Cek Transaksi
        </a>

        <a href="../admin/topup.php" class="menu-btn <?= ($page == 'topup.php') ? 'active' : '' ?>">
            <i class="fas fa-wallet"></i> Atur Nominal Top Up
        </a>

        <a href="../admin/vouchers.php" class="menu-btn <?= ($page == 'vouchers.php') ? 'active' : '' ?>">
            <i class="fas fa-ticket-alt"></i> Voucher Diskon
        </a>

        <a href="../user/dashboard.php?preview=user" class="menu-btn">
            <i class="fas fa-eye"></i> Preview Dashboard
        </a>
    </nav>
</aside>
