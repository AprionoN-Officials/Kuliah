document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const username = document.getElementById('loginUser').value;
    const password = document.getElementById('loginPass').value;
    const btnSubmit = document.querySelector('.btn-primary');

    // Ubah tombol jadi loading
    btnSubmit.innerText = "Mengecek...";
    btnSubmit.disabled = true;

    try {
        const response = await fetch('http://localhost:3000/api/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password })
        });

        const data = await response.json();

        if (data.success) {
            alert("✅ " + data.message);
            // Di aplikasi nyata, di sini kita akan redirect ke Dashboard
            // window.location.href = 'dashboard.html';
        } else {
            alert("❌ Gagal: " + data.message);
        }

    } catch (error) {
        console.error(error);
        alert('Terjadi kesalahan koneksi.');
    } finally {
        btnSubmit.innerText = "Masuk (Login)";
        btnSubmit.disabled = false;
    }
});