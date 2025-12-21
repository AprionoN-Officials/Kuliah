const bcrypt = require('bcrypt');
const db = require('./database');

// Fungsi bantuan untuk membuat Kode Acak (Misal: ABC-123-XYZ)
function generateRecoveryCode() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let code = '';
    // Format: XXX-XXX-XXX (Total 9 karakter acak + 2 strip)
    for (let i = 0; i < 3; i++) code += chars.charAt(Math.floor(Math.random() * chars.length));
    code += '-';
    for (let i = 0; i < 3; i++) code += chars.charAt(Math.floor(Math.random() * chars.length));
    code += '-';
    for (let i = 0; i < 3; i++) code += chars.charAt(Math.floor(Math.random() * chars.length));
    return code;
}

exports.register = async (req, res) => {
    const { username, password } = req.body;

    try {
        // 1. Buat Kode Pemulihan Unik (Plain Text)
        const rawRecoveryCode = generateRecoveryCode();

        // 2. Hash Password & Hash Recovery Code (Bcrypt)
        // Kita gunakan salt rounds 10 (standar)
        const hashedPassword = await bcrypt.hash(password, 10);
        const hashedRecoveryCode = await bcrypt.hash(rawRecoveryCode, 10);

        // 3. Simpan ke Database (Username, Hash Pass, Hash Code)
        const sql = `INSERT INTO users (username, password, recovery_code) VALUES (?, ?, ?)`;
        
        db.query(sql, [username, hashedPassword, hashedRecoveryCode], (err, result) => {
            if (err) {
                // Jika error (misal username sudah ada)
                console.error(err);
                return res.status(500).json({ success: false, message: 'Gagal mendaftar atau Username sudah dipakai.' });
            }

            // 4. SUKSES: Kirim Kode Asli ke Frontend untuk ditampilkan ke user
            // Ingat: Kode asli TIDAK disimpan di DB, hanya dikirim sekali ini saja.
            res.json({
                success: true,
                message: 'Registrasi berhasil',
                recoveryCode: rawRecoveryCode 
            });
        });

    } catch (error) {
        console.error(error);
        res.status(500).send('Terjadi kesalahan server');
    }
};

exports.login = (req, res) => {
    const { username, password } = req.body;

    // 1. Cari Username di Database
    const sql = 'SELECT * FROM users WHERE username = ?';
    
    db.query(sql, [username], async (err, results) => {
        if (err) return res.status(500).json({ message: 'Server Error' });
        
        // Jika username tidak ditemukan
        if (results.length === 0) {
            return res.status(401).json({ success: false, message: 'Username tidak ditemukan' });
        }

        const user = results[0];

        // 2. Bandingkan Password Input vs Password Hash di Database
        // Kita gunakan bcrypt.compare()
        const isMatch = await bcrypt.compare(password, user.password);

        if (isMatch) {
            // LOGIN SUKSES
            res.json({ 
                success: true, 
                message: 'Login Berhasil! Selamat datang, ' + user.username 
            });
        } else {
            // PASSWORD SALAH
            res.status(401).json({ success: false, message: 'Password salah' });
        }
    });
};


exports.resetPassword = (req, res) => {
    const { username, recoveryCode, newPassword } = req.body;

    // 1. Cari User berdasarkan Username
    const sql = 'SELECT * FROM users WHERE username = ?';
    
    db.query(sql, [username], async (err, results) => {
        if (err || results.length === 0) {
            return res.status(404).json({ success: false, message: 'Username tidak ditemukan' });
        }

        const user = results[0];

        // 2. VERIFIKASI: Bandingkan Kode Input vs Kode Hash di Database
        // Sesuai Dokumen: "Apakah ABC-123... cocok dengan hash di DB?"
        const isCodeValid = await bcrypt.compare(recoveryCode, user.recovery_code);

        if (!isCodeValid) {
            // Gagal verifikasi
            return res.status(401).json({ success: false, message: 'Kode Pemulihan SALAH!' });
        }

        // 3. JIKA SUKSES: Lakukan Pembaruan Ganda (Password Baru + Kode Baru)
        try {
            // A. Generate Kode Baru (Rotasi Kode)
            const newRawRecoveryCode = generateRecoveryCode(); 

            // B. Hash Password Baru & Hash Kode Baru
            const newHashedPassword = await bcrypt.hash(newPassword, 10);
            const newHashedRecoveryCode = await bcrypt.hash(newRawRecoveryCode, 10);

            // C. Update Database (Timpa data lama)
            const updateSql = 'UPDATE users SET password = ?, recovery_code = ? WHERE id = ?';
            
            db.query(updateSql, [newHashedPassword, newHashedRecoveryCode, user.id], (err, result) => {
                if (err) return res.status(500).json({ message: 'Gagal update database' });

                // D. Output Final: Kirim Kode BARU ke User
                res.json({
                    success: true,
                    message: 'Password berhasil direset.',
                    newRecoveryCode: newRawRecoveryCode // Tampilkan ini ke user
                });
            });

        } catch (error) {
            return res.status(500).json({ message: 'Error enkripsi server' });
        }
    });
};