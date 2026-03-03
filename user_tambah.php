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

        <form id="tambahUserForm">

            <div style="margin-bottom: 15px;">
                <label
                    style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; color: #475569;">Nama
                    Lengkap</label>
                <input type="text" name="nama_lengkap" id="nama_lengkap" required placeholder="Masukkan nama lengkap"
                    style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
            </div>

            <div style="margin-bottom: 15px;">
                <label
                    style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; color: #475569;">Username</label>
                <input type="text" name="username" id="username" required placeholder="Buat username untuk login"
                    style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
            </div>

            <div style="margin-bottom: 15px;">
                <label
                    style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; color: #475569;">Password</label>
                <input type="password" name="password" id="password" required placeholder="Buat password minimal 6 karakter"
                    style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
            </div>

            <div style="margin-bottom: 25px;">
                <label
                    style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; color: #475569;">Hak
                    Akses (Role)</label>
                <select name="role" id="role" required
                    style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; background: white;">
                    <option value="" disabled selected>-- Pilih Hak Akses --</option>
                    <option value="user">User (Anggota Perpustakaan)</option>
                    <option value="petugas">Petugas (Admin)</option>
                </select>
            </div>

            <div id="api-message" style="margin-bottom: 15px; font-size: 14px; text-align: center;"></div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <a href="kelola_user.php"
                    style="padding: 10px 20px; background: #f1f5f9; color: #475569; text-decoration: none; border-radius: 8px; font-weight: 600;">Batal</a>
                <button type="submit" class="btn-primary"
                    style="padding: 10px 20px; border: none; border-radius: 8px;">Simpan Pengguna</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('tambahUserForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const nama_lengkap = document.getElementById('nama_lengkap').value;
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        const role = document.getElementById('role').value;
        const messageDiv = document.getElementById('api-message');
        
        messageDiv.textContent = 'Menyimpan...';
        messageDiv.style.color = '#475569';

        try {
            const response = await fetch('api/user.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ nama_lengkap, username, password, role })
            });

            const result = await response.json();

            if (result.status === 'success') {
                messageDiv.textContent = 'Pengguna berhasil ditambahkan!';
                messageDiv.style.color = '#166534';
                
                setTimeout(() => {
                    window.location.href = 'kelola_user.php';
                }, 1500);
            } else {
                messageDiv.textContent = result.message;
                messageDiv.style.color = '#991b1b';
            }
        } catch (error) {
            messageDiv.textContent = 'Terjadi kesalahan pada server.';
            messageDiv.style.color = '#991b1b';
            console.error('Error:', error);
        }
    });
</script>
    </div>
</div>

<?php include 'layouts/footer.php'; ?>