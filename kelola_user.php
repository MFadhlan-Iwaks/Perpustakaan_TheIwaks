<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header("Location: index.php");
    exit();
}

$query_users = mysqli_query($koneksi, "SELECT * FROM users ORDER BY role ASC, nama_lengkap ASC");

include 'layouts/header.php';
?>

<div class="main-content">
    <div class="card-container">

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h3 class="header-title" style="margin-bottom: 0; border: none; padding: 0;">👥 Kelola Pengguna</h3>
            <a href="user_tambah.php" class="btn-primary">+ Tambah Pengguna</a>
        </div>

        <?php if (isset($_SESSION['pesan'])): ?>
            <div
                style="background: #dcfce7; color: #166534; padding: 12px 20px; border-radius: 8px; margin-bottom: 25px; border-left: 5px solid #22c55e;">
                ✅ <?= $_SESSION['pesan'];
                unset($_SESSION['pesan']); ?>
            </div>
        <?php endif; ?>

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
                <tbody>
                    <?php $no = 1;
                    while ($u = mysqli_fetch_assoc($query_users)): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><strong><?= $u['nama_lengkap']; ?></strong></td>
                            <td><?= $u['username']; ?></td>
                            <td>
                                    <?php if ($u['role'] == 'petugas'): ?>
                                    <span
                                        style="background: #e0e7ff; color: #4338ca; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: bold;">🛡️
                                        Petugas</span>
                                    <?php else: ?>
                                    <span
                                        style="background: #f1f5f9; color: #475569; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: bold;">👤
                                        User</span>
                                    <?php endif; ?>
                            </td>
                            <td>
                                    <?php if ($u['id_user'] != $_SESSION['id_user']): ?>
                                    <div style="display: flex; gap: 5px;">
                                        <a href="user_edit.php?id=<?= $u['id_user']; ?>" class="btn-primary" style="background: #0ea5e9; font-size: 12px; padding: 6px 12px; border-radius: 6px;">Edit</a>
                                        <a href="#" class="btn-danger" style="font-size: 12px; padding: 6px 12px; border-radius: 6px;"
                                            onclick="hapusUser(<?= $u['id_user']; ?>, '<?= addslashes($u['nama_lengkap']); ?>'); return false;">Hapus</a>
                                    </div>
                                    <?php else: ?>
                                    <span style="font-size: 12px; color: #94a3b8; font-style: italic;">Sedang Login</span>
                                    <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
    async function hapusUser(idUser, nama) {
        showModal(`Yakin ingin menghapus pengguna "${nama}" dari database?`, async () => {
            try {
                const response = await fetch(`api/user.php?id=${idUser}`, {
                    method: 'DELETE'
                });
                const result = await response.json();
                alert(result.message);
                if (result.status === 'success') {
                    location.reload();
                }
            } catch (error) {
                alert('Gagal menghapus pengguna.');
            }
        });
    }
</script>

<?php include 'layouts/footer.php'; ?>