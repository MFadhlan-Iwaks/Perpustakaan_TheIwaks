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
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h3 class="header-title" style="margin-bottom: 0; border: none; padding: 0;">👥 Kelola Pengguna
            </h3>
            <a href="user_tambah.php" class="btn-primary">+ Tambah Pengguna</a>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Hak Akses</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="user-table-body">
                    <tr>
                        <td colspan="5" style="text-align:center;">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    async function loadUsers() {
        try {
            const response = await fetch('api/user.php');
            const result = await response.json();

            const tableBody = document.getElementById('user-table-body');
            tableBody.innerHTML = ''; 

            if (result.status === 'success') {
                result.data.forEach((user, index) => {
                    const row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td><strong>${user.nama_lengkap}</strong></td>
                            <td>${user.username}</td>
                            <td>
                                <span style="background: ${user.role === 'petugas' ? '#e0e7ff' : '#f1f5f9'}; 
                                             color: ${user.role === 'petugas' ? '#4338ca' : '#475569'}; 
                                             padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: bold;">
                                    ${user.role === 'petugas' ? '🛡️ Petugas' : '👤 User'}
                                </span>
                            </td>
                            <td>
                                ${user.id_user != <?= $_SESSION['id_user']; ?> ? `
                                    <div style="display: flex; gap: 5px;">
                                        <a href="user_edit.php?id=${user.id_user}" class="btn-primary" style="background: #0ea5e9; font-size: 12px; padding: 6px 12px; border-radius: 6px;">Edit</a>
                                        <a href="#" class="btn-danger" style="font-size: 12px; padding: 6px 12px; border-radius: 6px;"
                                           onclick="hapusUser(${user.id_user}, '${user.nama_lengkap}'); return false;">Hapus</a>
                                    </div>
                                ` : '<span style="font-size: 12px; color: #94a3b8; font-style: italic;">Sedang Login</span>'}
                            </td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            }
        } catch (error) {
            console.error('Gagal mengambil data:', error);
            document.getElementById('user-table-body').innerHTML = '<tr><td colspan="5" style="text-align:center; color:red;">Gagal memuat data dari API.</td></tr>';
        }
    }

    async function hapusUser(idUser, nama) {
        if (confirm(`Yakin ingin menghapus pengguna "${nama}"?`)) {
            try {
                const response = await fetch(`api/user.php?id=${idUser}`, {
                    method: 'DELETE'
                });
                const result = await response.json();
                alert(result.message);
                if (result.status === 'success') {
                    loadUsers(); 
                }
            } catch (error) {
                alert('Gagal menghapus pengguna.');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', loadUsers);
</script>

<?php include 'layouts/footer.php'; ?>