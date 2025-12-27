// 1. Variabel Penghitung (Ditaruh di luar agar nilainya tersimpan)
let failedAttempts = 0;
const MAX_ATTEMPTS = 3;

document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const username = document.getElementById('loginUser').value;
    const password = document.getElementById('loginPass').value;
    const btnSubmit = document.querySelector('.btn-primary');

    // Cek dulu: Apakah sudah terblokir? (Jaga-jaga jika user mengakali HTML)
    if (failedAttempts >= MAX_ATTEMPTS) {
        alert("Akses terkunci sementara karena terlalu banyak percobaan gagal.");
        return;
    }

    // Ubah tampilan tombol saat loading
    btnSubmit.innerText = "Mengecek...";
    btnSubmit.disabled = true;

    try {
        const response = await fetch('http://localhost:3000/api/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password })
        });

        const data = await response.json();

        // --- BAGIAN YANG ANDA MINTA DIUBAH ADA DI SINI ---
        if (data.success) {
            // Jika SUKSES: Reset penghitung ke 0
            failedAttempts = 0; 
            alert("✅ " + data.message);
            // window.location.href = 'dashboard.html'; (Nanti diaktifkan)
            
            // Kembalikan tombol (opsional, karena biasanya pindah halaman)
            btnSubmit.innerText = "Masuk (Login)";
            btnSubmit.disabled = false;

        } else {
            // Jika GAGAL: Tambah hitungan salah
            failedAttempts++;
            const sisaPercobaan = MAX_ATTEMPTS - failedAttempts;

            if (failedAttempts >= MAX_ATTEMPTS) {
                // Skenario: Sudah salah 3 kali
                alert("❌ Gagal: Password salah.\n🚫 Akun dikunci sementara. Silakan coba lagi nanti atau reset password.");
                
                // Matikan Tombol secara permanen di tampilan
                btnSubmit.innerText = "Terkunci (Maks 3x)";
                btnSubmit.disabled = true; 
                btnSubmit.style.backgroundColor = "#ccc"; // Ubah warna jadi abu-abu
            } else {
                // Skenario: Masih ada sisa kesempatan
                alert(`❌ Gagal: ${data.message}\n⚠️ Sisa percobaan: ${sisaPercobaan} kali lagi.`);
                
                // Nyalakan tombol lagi untuk mencoba ulang
                btnSubmit.innerText = "Masuk (Login)";
                btnSubmit.disabled = false;
            }
        }
        // ------------------------------------------------

    } catch (error) {
        console.error(error);
        alert('Terjadi kesalahan koneksi.');
        btnSubmit.innerText = "Masuk (Login)";
        btnSubmit.disabled = false;
    }
});