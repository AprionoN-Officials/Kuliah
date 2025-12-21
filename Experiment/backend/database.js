const mysql = require('mysql2');

// Konfigurasi koneksi (Standar XAMPP)
const db = mysql.createConnection({
    host: 'localhost',
    user: 'root',      // User default XAMPP
    password: '',      // Password default XAMPP (kosong)
    database: 'chat_auth_db'
});

// Cek koneksi saat file ini dijalankan
db.connect((err) => {
    if (err) {
        console.error('❌ Gagal koneksi database:', err);
    } else {
        console.log('✅ Berhasil terhubung ke Database MySQL');
    }
});

module.exports = db;