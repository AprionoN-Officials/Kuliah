<aside class="sidebar">
    <div class="brand">
        <i class="fas fa-gamepad"></i> GameRent
    </div>
    
    <nav>
        <?php $page = basename($_SERVER['PHP_SELF']); ?>

        <a href="index.php" class="menu-btn <?= ($page == 'index.php') ? 'active' : '' ?>">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="akun.php" class="menu-btn <?= ($page == 'akun.php') ? 'active' : '' ?>">
            <i class="fas fa-user"></i> Akun Saya
        </a>
        <a href="library.php" class="menu-btn <?= ($page == 'library.php') ? 'active' : '' ?>">
            <i class="fas fa-book-open"></i> Library
        </a>
        <a href="list_game.php" class="menu-btn <?= ($page == 'list_game.php') ? 'active' : '' ?>">
            <i class="fas fa-list"></i> Daftar Game
        </a>
        <a href="topup.php" class="menu-btn <?= ($page == 'topup.php') ? 'active' : '' ?>">
            <i class="fas fa-wallet"></i> Isi Saldo
        </a>
    </nav>
</aside>