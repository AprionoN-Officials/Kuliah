-- Update password admin dengan hash yang benar
-- Password: admin123
UPDATE users SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE username = 'admin';

-- Atau jika ingin membuat akun admin baru dengan password yang sudah di-hash:
-- INSERT INTO users (username, password, role, saldo) VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 0);

-- Password yang di-atas adalah hash dari 'password'
-- Untuk password 'admin123' gunakan query di bawah ini:
-- UPDATE users SET password = '$2y$10$4BwYhKg8YBwP0Z7MRqXvH.YPmLNhK.DGq/FQHl0M9E8JlvnGPEcyC' WHERE username = 'admin';
