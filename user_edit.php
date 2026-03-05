<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header("Location: index.php"); exit();
}
$id_user = isset($_GET['id']) ? (int)$_GET['id'] : 0;
include 'layouts/header.php';
?>

<div class="card-container" style="max-width: 600px; margin: 0 auto;">
    <h3 class="header-title">✏️ Edit Pengguna</h3>
    <form id="form-edit-user">
        <div style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569;">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" id="nama_lengkap" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569;">Username</label>
            <input type="text" name="username" id="username" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569;">Password (Kosongkan jika tidak diganti)</label>
            <input type="password" name="password" placeholder="Masukkan password baru jika ingin diubah" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
        </div>

        <div style="margin-bottom: 25px;">
            <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569;">Hak Akses</label>
            <select name="role" id="role" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; background: white;">
                <option value="user">User (Anggota)</option>
                <option value="petugas">Petugas (Admin)</option>
            </select>
        </div>

        <button type="submit" class="btn-primary" style="width: 100%;">Update Pengguna</button>
    </form>
</div>

<script>
    const idUser = <?= $id_user; ?>;
    async function loadDetail() {
        const res = await fetch(`api/user.php?id=${idUser}`);
        const result = await res.json();
        if (result.status === 'success') {
            document.getElementById('nama_lengkap').value = result.data.nama_lengkap;
            document.getElementById('username').value = result.data.username;
            document.getElementById('role').value = result.data.role;
        }
    }
    document.getElementById('form-edit-user').addEventListener('submit', async function(e) {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(this).entries());
        const response = await fetch(`api/user.php?id=${idUser}`, {
            method: 'PUT', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const res = await response.json();
        alert(res.message);
        if (res.status === 'success') window.location.href = 'kelola_user.php';
    });
    document.addEventListener('DOMContentLoaded', loadDetail);
</script>
<?php include 'layouts/footer.php'; ?>