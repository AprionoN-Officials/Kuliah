<aside class="sidebar">
    <div class="brand">
        <i class="fas fa-gamepad"></i> GameRent
    </div>
    
    <nav>
        <?php 
            $page = basename($_SERVER['PHP_SELF']);
            $hide_user_links = (isset($_GET['preview']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
        ?>

        <a href="index.php<?= isset($_GET['preview']) ? '?preview=' . htmlspecialchars($_GET['preview']) : '' ?>" class="menu-btn <?= ($page == 'index.php') ? 'active' : '' ?>">
            <i class="fas fa-home"></i> Dashboard
        </a>

        <a href="daftargame.php<?= isset($_GET['preview']) ? '?preview=' . htmlspecialchars($_GET['preview']) : '' ?>" class="menu-btn <?= ($page == 'daftargame.php') ? 'active' : '' ?>">
            <i class="fas fa-list"></i> Daftar Game
        </a>

        <?php if (!$hide_user_links): ?>
        <a href="akun.php" class="menu-btn <?= ($page == 'akun.php') ? 'active' : '' ?>">
            <i class="fas fa-user"></i> Akun Saya
        </a>
        <a href="library.php" class="menu-btn <?= ($page == 'library.php') ? 'active' : '' ?>">
            <i class="fas fa-book-open"></i> Library
        </a>
        <a href="topup.php" class="menu-btn <?= ($page == 'topup.php') ? 'active' : '' ?>">
            <i class="fas fa-wallet"></i> Isi Saldo
        </a>
        <?php endif; ?>
    </nav>
</aside>