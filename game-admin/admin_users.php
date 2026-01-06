<?php
session_start();
include 'config/database.php';

// Proteksi: Hanya admin yang boleh akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$message = "";
$message_type = "";

// HAPUS USER
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Jangan izinkan hapus admin
    $check_query = "SELECT role FROM users WHERE id = $id";
    $check_result = mysqli_query($conn, $check_query);
    $user_data = mysqli_fetch_assoc($check_result);
    
    if ($user_data['role'] === 'admin') {
        $message = "Tidak dapat menghapus akun admin!";
        $message_type = "error";
    } else {
        $delete_query = "DELETE FROM users WHERE id = $id";
        if (mysqli_query($conn, $delete_query)) {
            $message = "User berhasil dihapus!";
            $message_type = "success";
        } else {
            $message = "Gagal menghapus user!";
            $message_type = "error";
        }
    }
}

// EDIT USER (UPDATE SALDO atau ROLE)
if (isset($_POST['edit'])) {
    $id = intval($_POST['id']);
    $saldo = floatval($_POST['saldo']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    
    $query = "UPDATE users SET saldo=$saldo, role='$role' WHERE id=$id";
    
    if (mysqli_query($conn, $query)) {
        $message = "User berhasil diupdate!";
        $message_type = "success";
    } else {
        $message = "Gagal mengupdate user!";
        $message_type = "error";
    }
}

// RESET PASSWORD
if (isset($_POST['reset_password'])) {
    $id = intval($_POST['user_id']);
    $new_password = password_hash('12345678', PASSWORD_DEFAULT); // Default password
    
    $query = "UPDATE users SET password='$new_password' WHERE id=$id";
    
    if (mysqli_query($conn, $query)) {
        $message = "Password berhasil direset menjadi '12345678'";
        $message_type = "success";
    } else {
        $message = "Gagal mereset password!";
        $message_type = "error";
    }
}

// Ambil semua data users
$users_query = "SELECT * FROM users ORDER BY id DESC";
$users_result = mysqli_query($conn, $users_query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="aset/style.css">
    <style>
        .table-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f5576c;
            color: white;
            font-weight: 600;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .btn-action {
            padding: 6px 12px;
            margin: 0 3px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 13px;
            display: inline-block;
            border: none;
            cursor: pointer;
        }
        .btn-edit {
            background: #4facfe;
            color: white;
        }
        .btn-delete {
            background: #f5576c;
            color: white;
        }
        .btn-reset {
            background: #ffa726;
            color: white;
        }
        .badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-admin {
            background: #667eea;
            color: white;
        }
        .badge-user {
            background: #43e97b;
            color: white;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }
        .modal-content {
            background: white;
            margin: 50px auto;
            padding: 30px;
            width: 90%;
            max-width: 500px;
            border-radius: 10px;
            position: relative;
        }
        .close {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 30px;
            cursor: pointer;
            color: #999;
        }
    </style>
</head>
<body>

    <?php include 'aset/admin_sidebar.php'; ?>

    <main class="main-content">
        <header class="top-bar">
            <div class="welcome-text">
                <h2>Manajemen User</h2>
            </div>
        </header>

        <section>
            <?php if($message): ?>
                <div class="alert alert-<?= $message_type ?>">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Saldo</th>
                            <th>Tanggal Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($user = mysqli_fetch_assoc($users_result)): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td>
                                <span class="badge badge-<?= $user['role'] ?>">
                                    <?= strtoupper($user['role']) ?>
                                </span>
                            </td>
                            <td>Rp <?= number_format($user['saldo']) ?></td>
                            <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                            <td>
                                <a href="javascript:void(0)" onclick='editUser(<?= json_encode($user) ?>)' class="btn-action btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                
                                <button onclick='resetPassword(<?= $user['id'] ?>)' class="btn-action btn-reset">
                                    <i class="fas fa-key"></i> Reset PW
                                </button>
                                
                                <?php if($user['role'] !== 'admin'): ?>
                                <a href="?delete=<?= $user['id'] ?>" onclick="return confirm('Yakin hapus user ini?')" class="btn-action btn-delete">
                                    <i class="fas fa-trash"></i> Hapus
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- Modal Edit User -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editModal')">&times;</span>
            <h2 style="margin-top: 0;">Edit User</h2>
            <form method="POST">
                <input type="hidden" name="id" id="edit_id">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" id="edit_username" class="form-control" disabled>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" id="edit_role" class="form-control">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Saldo (Rp)</label>
                    <input type="number" name="saldo" id="edit_saldo" class="form-control" required>
                </div>
                <button type="submit" name="edit" class="btn btn-primary">Update User</button>
            </form>
        </div>
    </div>

    <!-- Modal Reset Password -->
    <div id="resetModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('resetModal')">&times;</span>
            <h2 style="margin-top: 0;">Reset Password</h2>
            <p>Password user akan direset menjadi: <strong>12345678</strong></p>
            <p>User dapat login dengan password default ini dan mengubahnya nanti.</p>
            <form method="POST">
                <input type="hidden" name="user_id" id="reset_user_id">
                <button type="submit" name="reset_password" class="btn btn-primary">Ya, Reset Password</button>
                <button type="button" onclick="closeModal('resetModal')" class="btn" style="background: #ccc;">Batal</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function editUser(user) {
            document.getElementById('edit_id').value = user.id;
            document.getElementById('edit_username').value = user.username;
            document.getElementById('edit_role').value = user.role;
            document.getElementById('edit_saldo').value = user.saldo;
            openModal('editModal');
        }

        function resetPassword(userId) {
            document.getElementById('reset_user_id').value = userId;
            openModal('resetModal');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>

</body>
</html>
