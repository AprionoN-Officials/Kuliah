<?php 
include "../config/database.php";
include "../config/getdata.php";
session_start();

function resolveGameImage($game) {
    $base_dir = __DIR__ . '/../aset/images/';
    $base_url = '../aset/images/';
    $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

    $db_name = isset($game['gambar']) ? basename($game['gambar']) : '';
    if ($db_name && file_exists($base_dir . $db_name)) {
        return $base_url . $db_name;
    }

    $slug = strtolower(str_replace(' ', '_', $game['judul'] ?? ''));
    if ($slug) {
        foreach ($allowed_ext as $ext) {
            $candidate = $slug . '.' . $ext;
            if (file_exists($base_dir . $candidate)) {
                return $base_url . $candidate;
            }
        }
    }

    if (file_exists($base_dir . 'default.jpg')) {
        return $base_url . 'default.jpg';
    }

    return $base_url . 'tes.png';
}

// Mode preview untuk admin: ?preview=user
$preview_param = (($_GET['preview'] ?? null) === 'user') ? 'user' : null;
$is_admin = isset($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'admin';
$is_preview = $is_admin && $preview_param !== null;

// Proteksi: admin normal dialihkan, kecuali sedang preview
if ($is_admin && !$is_preview) {
    header("Location: ../admin/dashboard.php");
    exit;
}

// Cek login (opsional, tapi biasanya admin/user harus login)
$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Game - GameRent</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../aset/style.css">
    <style>
        .search-trigger {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border: 1px solid #dcdcdc;
            border-radius: 8px;
            background: #fff;
            cursor: pointer;
            transition: box-shadow .2s ease;
        }
        .search-trigger:hover { box-shadow: 0 4px 10px rgba(0,0,0,0.08); }
        .search-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1500;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .search-modal .modal-box {
            background: #fff;
            width: 100%;
            max-width: 640px;
            max-height: 90vh;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.15);
            position: relative;
            overflow-y: auto;
        }
        .search-modal .close-btn {
            position: absolute;
            right: 14px;
            top: 10px;
            font-size: 22px;
            cursor: pointer;
            color: #888;
        }
        .search-input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #dcdcdc;
            border-radius: 8px;
            margin-bottom: 12px;
        }
        .search-result-item {
            display: flex;
            gap: 12px;
            padding: 10px;
            border: 1px solid #f0f0f0;
            border-radius: 10px;
            margin-bottom: 10px;
            align-items: center;
        }
        .search-result-item img {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: 8px;
        }
        .search-result-item .title { font-weight: 700; }
        .search-empty { color: #888; text-align: center; padding: 12px; }
    </style>
</head>
<body>
    <?php include "../aset/sidebar.php"; ?>

    <main class="main-content">
        <?php if ($is_preview): ?>
        <div class="alert" style="background:#e8f4ff;border:1px solid #b6d7ff;color:#0a3d62;margin-bottom:15px;">
            Mode Preview Admin — <a href="../admin/dashboard.php" style="color:#0a3d62;text-decoration:underline;">kembali ke dashboard admin</a>
        </div>
        <?php endif; ?>
        <header class="top-bar">
            <div class="welcome-text">
                <h2>Daftar <b>Game</b></h2>
            </div>
            <div class="user-action">
                <button class="search-trigger" onclick="openSearchModal()">
                    <i class="fas fa-search"></i>
                    <span>Cari Game</span>
                </button>
            </div>
        </header>

        <section>
            <div class="section-title">
                Semua Koleksi Game
            </div>

            <div class="game-grid">
                <?php
                // Menggunakan tabel 'games' sesuai dengan index.php
                $query = "SELECT * FROM games ORDER BY id DESC";
                $result = mysqli_query($conn, $query);
                
                if ($result && mysqli_num_rows($result) > 0) {
                    while($game = mysqli_fetch_assoc($result)):
                        $path_gambar = resolveGameImage($game);
                ?>

                <div class="game-card">
                    <img src="<?= $path_gambar; ?>" class="game-img" alt="<?= htmlspecialchars($game['judul']); ?>">
                    
                    <div class="card-body">
                        <div class="game-title"><?= htmlspecialchars($game['judul']); ?></div>
                        <span class="game-genre"><?= htmlspecialchars($game['genre']); ?></span>
                        
                        <div class="price-tag">
                            Sewa: Rp <?= number_format($game['harga_sewa']); ?> / hari<br>
                            Beli: Rp <?= number_format($game['harga_beli']); ?>
                        </div>

                        <?php $detail_link = 'detail.php?id=' . $game['id'] . ($is_preview ? '&preview=user' : ''); ?>
                        <a href="<?= $detail_link; ?>" class="btn btn-primary btn-block">
                            Lihat Detail
                        </a>
                    </div>
                </div>

                <?php 
                    endwhile; 
                } else {
                    echo "<p style='grid-column: 1/-1; text-align: center; padding: 20px; color: var(--text-grey);'>Belum ada data game atau tabel 'games' tidak ditemukan.</p>";
                }
                ?>
            </div>
        </section>
    </main>

    <div id="searchModal" class="search-modal" onclick="backdropClose(event)">
        <div class="modal-box">
            <span class="close-btn" onclick="closeSearchModal()">&times;</span>
            <h3 style="margin-top:0; margin-bottom:10px;">Cari Game</h3>
            <input id="searchInput" type="text" class="search-input" placeholder="Ketik judul atau genre" oninput="renderSearchResults()" autofocus>
            <div id="searchResults"></div>
        </div>
    </div>

    <script>
        function openSearchModal() {
            const modal = document.getElementById('searchModal');
            modal.style.display = 'flex';
            const input = document.getElementById('searchInput');
            input.value = '';
            renderSearchResults();
            setTimeout(() => input.focus(), 50);
        }

        function closeSearchModal() {
            document.getElementById('searchModal').style.display = 'none';
        }

        function backdropClose(event) {
            if (event.target.id === 'searchModal') {
                closeSearchModal();
            }
        }

        function renderSearchResults() {
            const q = (document.getElementById('searchInput').value || '').toLowerCase();
            const cards = Array.from(document.querySelectorAll('.game-card'));
            const container = document.getElementById('searchResults');
            container.innerHTML = '';

            if (!q) {
                const info = document.createElement('div');
                info.className = 'search-empty';
                info.textContent = 'Ketik nama atau genre game untuk mencari.';
                container.appendChild(info);
                return;
            }

            const results = cards.map(card => {
                const title = (card.querySelector('.game-title')?.textContent || '').trim();
                const genre = (card.querySelector('.game-genre')?.textContent || '').trim();
                const link = card.querySelector('a.btn-primary')?.getAttribute('href') || '#';
                const img = card.querySelector('img')?.getAttribute('src') || '';
                return { title, genre, link, img };
            }).filter(item => {
                return item.title.toLowerCase().includes(q) || item.genre.toLowerCase().includes(q);
            }).sort((a, b) => a.title.localeCompare(b.title, 'id', { sensitivity: 'base' }));

            if (results.length === 0) {
                const empty = document.createElement('div');
                empty.className = 'search-empty';
                empty.textContent = 'Tidak ada game yang cocok.';
                container.appendChild(empty);
                return;
            }

            results.forEach(item => {
                const div = document.createElement('div');
                div.className = 'search-result-item';
                div.innerHTML = `
                    <img src="${item.img}" alt="${item.title}">
                    <div style=\"flex:1;\">
                        <div class=\"title\">${item.title}</div>
                        <div style=\"color:#666; font-size:13px;\">${item.genre}</div>
                    </div>
                    <a href="${item.link}" class="btn btn-primary" style="padding:8px 12px;">Lihat</a>
                `;
                container.appendChild(div);
            });
        }
    </script>
</body>
</html>
