<?php
// TOOL untuk generate hash password
// Akses: http://localhost/game-admin/hash_password.php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    $password = $_POST['password'];
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $result = [
        'password' => $password,
        'hash' => $hash,
        'verify' => password_verify($password, $hash)
    ];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Hash Generator</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 600px;
            width: 100%;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        input[type="password"], input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        input[type="password"]:focus, input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
        }
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        button:hover {
            transform: translateY(-2px);
        }
        .result-box {
            background: #f5f5f5;
            border-left: 4px solid #43e97b;
            padding: 20px;
            border-radius: 5px;
            margin-top: 30px;
        }
        .result-box h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 16px;
        }
        .result-item {
            margin-bottom: 15px;
        }
        .result-label {
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .result-value {
            background: white;
            padding: 12px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            word-break: break-all;
            border: 1px solid #e0e0e0;
            color: #333;
        }
        .copy-btn {
            width: 100%;
            padding: 8px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            margin-top: 8px;
        }
        .copy-btn:hover {
            background: #5568d3;
        }
        .verify-box {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 12px;
            border-radius: 4px;
            color: #155724;
            margin-top: 15px;
            font-size: 14px;
        }
        .info-box {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            padding: 15px;
            border-radius: 5px;
            color: #004085;
            margin-bottom: 20px;
            font-size: 13px;
            line-height: 1.6;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>🔐 Password Hash Generator</h1>
    <p class="subtitle">Generate bcrypt hash untuk password Anda</p>
    
    <div class="info-box">
        <strong>Cara Penggunaan:</strong><br>
        1. Masukkan password yang ingin di-hash<br>
        2. Klik tombol "Generate Hash"<br>
        3. Copy hash hasil ke database Anda
    </div>

    <form method="POST">
        <div class="form-group">
            <label for="password">Masukkan Password</label>
            <input type="password" id="password" name="password" placeholder="Contoh: admin" required>
        </div>
        
        <button type="submit">Generate Hash</button>
    </form>

    <?php if (isset($result)): ?>
    <div class="result-box">
        <h3>✅ Hasil Hash Password</h3>
        
        <div class="result-item">
            <div class="result-label">Password (Plain Text)</div>
            <div class="result-value"><?= htmlspecialchars($result['password']) ?></div>
            <button class="copy-btn" onclick="copyToClipboard(this)">Copy Password</button>
        </div>

        <div class="result-item">
            <div class="result-label">Hash Bcrypt (untuk database)</div>
            <div class="result-value" id="hash-value"><?= htmlspecialchars($result['hash']) ?></div>
            <button class="copy-btn" onclick="copyToClipboard('hash-value')">Copy Hash</button>
        </div>

        <div class="verify-box">
            ✓ Verifikasi Hash: <strong><?= $result['verify'] ? 'VALID' : 'INVALID' ?></strong>
        </div>

        <div class="result-item" style="margin-top: 20px;">
            <div class="result-label">SQL Query untuk Update Database</div>
            <div class="result-value" id="sql-query">UPDATE users SET password = '<?= $result['hash'] ?>' WHERE username = 'admin';</div>
            <button class="copy-btn" onclick="copyToClipboard('sql-query')">Copy SQL Query</button>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function copyToClipboard(elementId) {
    let text;
    if (typeof elementId === 'string') {
        text = document.getElementById(elementId).innerText;
    } else {
        text = elementId.previousElementSibling.innerText;
    }
    
    navigator.clipboard.writeText(text).then(() => {
        const btn = event.target;
        const originalText = btn.innerText;
        btn.innerText = '✓ Copied!';
        setTimeout(() => {
            btn.innerText = originalText;
        }, 2000);
    });
}
</script>

</body>
</html>
