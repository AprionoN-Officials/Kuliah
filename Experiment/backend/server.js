const express = require('express');
const cors = require('cors');
const bodyParser = require('body-parser');
const authController = require('./authController');

const app = express();
const PORT = 3000;

// Middleware (Agar bisa baca JSON dan diakses dari Frontend)
app.use(cors());
app.use(bodyParser.json());

// ROUTE: Pendaftaran
// Ketika ada yang kirim data ke alamat '/api/register', jalankan fungsi di authController
app.post('/api/register', authController.register);

app.post('/api/login', authController.login); 

app.post('/api/reset-password', authController.resetPassword);

// Jalankan Server
app.listen(PORT, () => {
    console.log(`🚀 Server berjalan di http://localhost:${PORT}`);

    
});