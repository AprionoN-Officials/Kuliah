<?php
function getUserSaldo($userId, $connection) {
    // Prepare the SQL statement to prevent SQL injection
    $query = "SELECT saldo FROM users WHERE id = ?";
    $stmt = mysqli_prepare($connection, $query);

    if ($stmt) {
        // Bind the user ID parameter
        mysqli_stmt_bind_param($stmt, "i", $userId);

        // Execute the statement
        mysqli_stmt_execute($stmt);

        // Get the result
        $result = mysqli_stmt_get_result($stmt);

        // Fetch the user's data
        $user = mysqli_fetch_assoc($result);

        // Close the statement
        mysqli_stmt_close($stmt);

        // Return the saldo if user is found, otherwise return 0
        return $user ? $user['saldo'] : 0;
    }

    // Return 0 if the query preparation fails
    return 0;
}

function resolveGameImage($game) {
    // Path directory fisik (untuk file_exists)
    // __DIR__ adalah F:\...\config
    $base_dir = __DIR__ . '/../aset/images/';
    
    // URL relatif untuk browser (asumsi dipanggil dari folder user/ atau admin/)
    $base_url = '../aset/images/';
    
    $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

    $db_name = isset($game['gambar']) ? basename($game['gambar']) : '';
    
    // 1. Cek file sesuai database
    if ($db_name && file_exists($base_dir . $db_name)) {
        return $base_url . $db_name;
    }

    // 2. Cek file berdasarkan Slug Judul (backup legacy)
    $slug = strtolower(str_replace(' ', '_', $game['judul'] ?? ''));
    if ($slug) {
        foreach ($allowed_ext as $ext) {
            $candidate = $slug . '.' . $ext;
            if (file_exists($base_dir . $candidate)) {
                return $base_url . $candidate;
            }
        }
    }

    // 3. Fallback Default
    if (file_exists($base_dir . 'default.jpg')) {
        return $base_url . 'default.jpg';
    }

    return $base_url . 'default.jpg';
}