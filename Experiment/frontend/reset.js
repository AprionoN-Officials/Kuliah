document.getElementById('resetForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const username = document.getElementById('resetUser').value;
    const recoveryCode = document.getElementById('oldCode').value;
    const newPassword = document.getElementById('newPass').value;
    const btnSubmit = document.querySelector('.btn-primary');

    btnSubmit.innerText = "Memproses...";
    btnSubmit.disabled = true;

    try {
        // Kirim data ke endpoint Reset
        const response = await fetch('http://localhost:3000/api/reset-password', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, recoveryCode, newPassword })
        });

        const data = await response.json();

        if (data.success) {
            // SUKSES: Tampilkan Kode Baru
            document.getElementById('reset-form-section').style.display = 'none';
            document.getElementById('new-code-section').style.display = 'block';
            
            // Masukkan kode baru dari server ke layar
            document.getElementById('newCodeDisplay').innerText = data.newRecoveryCode;
        } else {
            // GAGAL (Kode salah / User tidak ada)
            alert("❌ Gagal: " + data.message);
        }

    } catch (error) {
        console.error(error);
        alert('Terjadi kesalahan koneksi.');
    } finally {
        btnSubmit.innerText = "Reset Password";
        btnSubmit.disabled = false;
    }
});