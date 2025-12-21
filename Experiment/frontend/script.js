document.getElementById('registerForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Mencegah halaman reload

    // 1. Ambil input (Nanti ini dikirim ke server)
    const username = document.getElementById('username').value;
    
    // 2. SIMULASI: Anggap server sudah membalas sukses & mengirim kode
    // Nanti bagian ini kita ganti dengan koneksi ke Backend sungguhan
    const simulasiKodeServer = "ABC-" + Math.floor(100 + Math.random() * 900) + "-XYZ";

    // 3. Ubah Tampilan: Sembunyikan Form -> Tampilkan Kode [cite: 81]
    document.getElementById('register-section').style.display = 'none';
    document.getElementById('recovery-section').style.display = 'block';
    
    // 4. Masukkan kode ke dalam kotak
    document.getElementById('recoveryCodeDisplay').innerText = simulasiKodeServer;
});