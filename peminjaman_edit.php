<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header("Location: index.php");
    exit();
}

$id_peminjaman = isset($_GET['id']) ? (int)$_GET['id'] : 0;
include 'layouts/header.php';

$users = mysqli_query($koneksi, "SELECT id_user, nama_lengkap FROM users WHERE role = 'user'");
$books = mysqli_query($koneksi, "SELECT id_buku, judul FROM buku");
?>

<div class="main-content">
    <div class="card-container">
        <h3 class="header-title">✏️ Edit Transaksi Peminjaman</h3>
        <form id="form-edit-pinjam">
            <div style="margin-bottom: 15px;">
                <label>Peminjam</label>
                <select name="id_user" id="id_user" class="form-control" required style="width:100%; padding:8px; margin-top:5px;">
                    <?php while($u = mysqli_fetch_assoc($users)): ?>
                        <option value="<?= $u['id_user']; ?>"><?= $u['nama_lengkap']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div style="margin-bottom: 15px;">
                <label>Buku</label>
                <select name="id_buku" id="id_buku" class="form-control" required style="width:100%; padding:8px; margin-top:5px;">
                    <?php while($b = mysqli_fetch_assoc($books)): ?>
                        <option value="<?= $b['id_buku']; ?>"><?= $b['judul']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div><label>Tgl Pinjam</label><input type="date" name="tanggal_pinjam" id="tanggal_pinjam" class="form-control" required style="width:100%; padding:8px; margin-top:5px;"></div>
                <div><label>Tgl Tenggat</label><input type="date" name="tanggal_tenggat" id="tanggal_tenggat" class="form-control" required style="width:100%; padding:8px; margin-top:5px;"></div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                <div><label>Status</label>
                    <select name="status" id="status" class="form-control" required style="width:100%; padding:8px; margin-top:5px;">
                        <option value="dipinjam">Dipinjam</option>
                        <option value="dikembalikan">Dikembalikan</option>
                    </select>
                </div>
                <div><label>Denda (Rp)</label><input type="number" name="denda" id="denda" class="form-control" value="0" style="width:100%; padding:8px; margin-top:5px;"></div>
            </div>
            <button type="submit" class="btn-primary" style="width: 100%;">Update Transaksi</button>
            <a href="peminjaman_data.php" style="display: block; text-align: center; margin-top: 15px; color: #64748b;">Kembali</a>
        </form>
    </div>
</div>

<script>
    const idPinjam = <?= $id_peminjaman; ?>;

    async function loadDetail() {
        try {
            const res = await fetch(`api/peminjaman.php?id=${idPinjam}`);
            const result = await res.json();
            if (result.status === 'success') {
                const p = result.data;
                document.getElementById('id_user').value = p.id_user;
                document.getElementById('id_buku').value = p.id_buku;
                document.getElementById('tanggal_pinjam').value = p.tanggal_pinjam;
                document.getElementById('tanggal_tenggat').value = p.tanggal_tenggat;
                document.getElementById('status').value = p.status;
                document.getElementById('denda').value = p.denda;
            }
        } catch (e) { alert('Gagal memuat data'); }
    }

    document.getElementById('form-edit-pinjam').addEventListener('submit', async function(e) {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(this).entries());
        try {
            const response = await fetch(`api/peminjaman.php?id=${idPinjam}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            alert(result.message);
            if (result.status === 'success') window.location.href = 'peminjaman_data.php';
        } catch (e) { alert('Gagal update'); }
    });

    document.addEventListener('DOMContentLoaded', loadDetail);
</script>

<?php include 'layouts/footer.php'; ?>