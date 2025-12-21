document.getElementById('registerForm').addEventListener('submit', async function(e) {
    e.preventDefault(); // Mencegah halaman reload otomatis

    // 1. Ambil data dari input
    const usernameInput = document.getElementById('username').value;
    const passwordInput = document.getElementById('password').value;
    
    // Siapkan tombol agar terlihat sedang proses
    const btnSubmit = document.querySelector('.btn-primary');
    const originalText = btnSubmit.innerText;
    btnSubmit.innerText = "Sedang Memproses...";
    btnSubmit.disabled = true;

    try {
        // 2. KIRIM DATA KE SERVER (Real Connection)
        const response = await fetch('http://localhost:3000/api/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                username: usernameInput,
                password: passwordInput
            })
        });

        const data = await response.json();

        // 3. CEK HASIL DARI SERVER
        if (data.success) {
            // BERHASIL:
            // Sembunyikan Form
            document.getElementById('register-section').style.display = 'none';
            // Tampilkan Bagian Kode
            document.getElementById('recovery-section').style.display = 'block';
            
            // TAMPILKAN KODE ASLI DARI SERVER
            // (Sesuai dokumen: Kode ini dikirim dari authController.js)
            document.getElementById('recoveryCodeDisplay').innerText = data.recoveryCode;
        } else {
            // GAGAL (Misal: Username sudah dipakai):
            alert('Gagal Mendaftar: ' + data.message);
        }

    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan koneksi ke server.');
    } finally {
        // Kembalikan tombol seperti semula
        btnSubmit.innerText = originalText;
        btnSubmit.disabled = false;
    }
});