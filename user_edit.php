<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header("Location: index.php");
    exit();
}

$id_user = isset($_GET['id']) ? (int)$_GET['id'] : 0;
include 'layouts/header.php';
?>

<div class="main-content">
    <div class="card-container">
        <h3 class="header-title">✏️ Edit Pengguna</h3>

        <form id="form-edit-user">
            <input type="hidden" name="id_user" value="<?= $id_user; ?>">
            <div style="margin-bottom: 15px;">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control" required style="width: 100%; padding: 10px; margin-top: 5px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label>Username</label>
                <input type="text" name="username" id="username" class="form-control" required style="width: 100%; padding: 10px; margin-top: 5px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label>Password (Kosongkan jika tidak diganti)</label>
                <input type="password" name="password" class="form-control" style="width: 100%; padding: 10px; margin-top: 5px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label>Hak Akses</label>
                <select name="role" id="role" class="form-control" required style="width: 100%; padding: 10px; margin-top: 5px;">
                    <option value="user">User</option>
                    <option value="petugas">Petugas</option>
                </select>
            </div>
            <button type="submit" class="btn-primary" style="width: 100%;">Update</button>
            <a href="kelola_user.php" style="display: block; text-align: center; margin-top: 15px; color: #64748b;">Kembali</a>
        </form>
    </div>
</div>

<script>
    const idUser = <?= $id_user; ?>;

    async function loadUserDetail() {
        try {
            const response = await fetch(`api/user.php?id=${idUser}`);
            const result = await response.json();
            
            if (result.status === 'success') {
                const user = result.data;
                document.getElementById('nama_lengkap').value = user.nama_lengkap;
                document.getElementById('username').value = user.username;
                document.getElementById('role').value = user.role;
            } else {
                alert('User tidak ditemukan');
                window.location.href = 'kelola_user.php';
            }
        } catch (error) {
            alert('Gagal mengambil detail user.');
        }
    }

    document.getElementById('form-edit-user').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch(`api/user.php?id=${idUser}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            
            alert(result.message);
            if (result.status === 'success') {
                window.location.href = 'kelola_user.php';
            }
        } catch (error) {
            alert('Gagal mengupdate user.');
        }
    });

    document.addEventListener('DOMContentLoaded', loadUserDetail);
</script>

<?php include 'layouts/footer.php'; ?>