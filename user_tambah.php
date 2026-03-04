<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header("Location: index.php");
    exit();
}

include 'layouts/header.php';
?>

<div class="main-content">
    <div class="card-container">
        <h3 class="header-title">➕ Tambah Pengguna Baru</h3>

        <form id="form-tambah-user">
            <div style="margin-bottom: 15px;">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control" required style="width: 100%; padding: 10px; margin-top: 5px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required style="width: 100%; padding: 10px; margin-top: 5px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required style="width: 100%; padding: 10px; margin-top: 5px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label>Hak Akses</label>
                <select name="role" class="form-control" required style="width: 100%; padding: 10px; margin-top: 5px;">
                    <option value="user">User</option>
                    <option value="petugas">Petugas</option>
                </select>
            </div>
            <button type="submit" class="btn-primary" style="width: 100%;">Simpan</button>
            <a href="kelola_user.php" style="display: block; text-align: center; margin-top: 15px; color: #64748b;">Kembali</a>
        </form>
    </div>
</div>

<script>
    document.getElementById('form-tambah-user').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('api/user.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            
            alert(result.message);
            if (result.status === 'success') {
                window.location.href = 'kelola_user.php';
            }
        } catch (error) {
            alert('Gagal menambah user.');
        }
    });
</script>

<?php include 'layouts/footer.php'; ?>