<?php
session_start();
require_once 'config/koneksi.php';
require_once 'models/User.php';
require_once 'models/Buku.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header("Location: index.php"); exit();
}

$id_peminjaman = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$database = new Database();
$db = $database->getConnection();
$semua_user = (new User($db))->getAll();
$semua_buku = (new Buku($db))->getAll();

include 'layouts/header.php';
?>

<div class="card-container" style="max-width: 700px; margin: 0 auto;">
    <h3 class="header-title">✏️ Edit Transaksi Peminjaman</h3>
    <form id="form-edit-pinjam">
        <div style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569;">Peminjam</label>
            <select name="id_user" id="id_user" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
                <?php foreach($semua_user as $u): if($u['role'] == 'user'): ?>
                    <option value="<?= $u['id_user']; ?>"><?= $u['nama_lengkap']; ?></option>
                <?php endif; endforeach; ?>
            </select>
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569;">Buku yang Dipinjam</label>
            <select name="id_buku" id="id_buku" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
                <?php foreach($semua_buku as $b): ?>
                    <option value="<?= $b['id_buku']; ?>"><?= $b['judul']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display: flex; gap: 15px; margin-bottom: 15px;">
            <div style="flex: 1;">
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569;">Tgl Pinjam</label>
                <input type="date" name="tanggal_pinjam" id="tanggal_pinjam" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
            </div>
            <div style="flex: 1;">
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569;">Tgl Tenggat</label>
                <input type="date" name="tanggal_tenggat" id="tanggal_tenggat" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
            </div>
        </div>

        <div style="display: flex; gap: 15px; margin-bottom: 25px;">
            <div style="flex: 1;">
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569;">Status</label>
                <select name="status" id="status" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
                    <option value="dipinjam">Dipinjam</option>
                    <option value="dikembalikan">Dikembalikan</option>
                </select>
            </div>
            <div style="flex: 1;">
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569;">Denda (Rp)</label>
                <input type="number" name="denda" id="denda" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
            </div>
        </div>

        <button type="submit" class="btn-primary" style="width: 100%;">Update Data Transaksi</button>
    </form>
</div>

<script>
    const idPinjam = <?= $id_peminjaman; ?>;
    async function loadDetail() {
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
    }
    document.getElementById('form-edit-pinjam').addEventListener('submit', async function(e) {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(this).entries());
        const response = await fetch(`api/peminjaman.php?id=${idPinjam}`, {
            method: 'PUT', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const res = await response.json();
        alert(res.message);
        if (res.status === 'success') window.location.href = 'peminjaman_data.php';
    });
    document.addEventListener('DOMContentLoaded', loadDetail);
</script>
<?php include 'layouts/footer.php'; ?>