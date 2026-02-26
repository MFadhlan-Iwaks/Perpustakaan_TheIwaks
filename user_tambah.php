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
    <div class="card-container" style="max-width: 600px; margin: 0 auto;">
        <h3 class="header-title">➕ Tambah Pengguna Baru</h3>

        <form action="proses/user_proses.php" method="POST">

            <div style="margin-bottom: 15px;">
                <label
                    style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; color: #475569;">Nama
                    Lengkap</label>
                <input type="text" name="nama_lengkap" required placeholder="Masukkan nama lengkap"
                    style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
            </div>

            <div style="margin-bottom: 15px;">
                <label
                    style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; color: #475569;">Username</label>
                <input type="text" name="username" required placeholder="Buat username untuk login"
                    style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
            </div>

            <div style="margin-bottom: 15px;">
                <label
                    style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; color: #475569;">Password</label>
                <input type="password" name="password" required placeholder="Buat password minimal 6 karakter"
                    style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
            </div>

            <div style="margin-bottom: 25px;">
                <label
                    style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; color: #475569;">Hak
                    Akses (Role)</label>
                <select name="role" required
                    style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; background: white;">
                    <option value="" disabled selected>-- Pilih Hak Akses --</option>
                    <option value="user">User (Anggota Perpustakaan)</option>
                    <option value="petugas">Petugas (Admin)</option>
                </select>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <a href="kelola_user.php"
                    style="padding: 10px 20px; background: #f1f5f9; color: #475569; text-decoration: none; border-radius: 8px; font-weight: 600;">Batal</a>
                <button type="submit" name="tambah_user" class="btn-primary"
                    style="padding: 10px 20px; border: none; border-radius: 8px;">Simpan Pengguna</button>
            </div>
        </form>
    </div>
</div>

<?php include 'layouts/footer.php'; ?>