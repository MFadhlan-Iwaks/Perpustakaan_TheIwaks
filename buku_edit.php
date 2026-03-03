<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header("Location: index.php");
    exit();
}

$id_buku = isset($_GET['id']) ? (int)$_GET['id'] : 0;
include 'layouts/header.php';
?>

<div class="main-content">
    <div class="card-container">
        <h3 class="header-title">✏️ Edit Buku (via API PUT)</h3>
        <form id="form-edit-buku">
            <input type="hidden" name="id_buku" value="<?= $id_buku; ?>">
            <div style="margin-bottom: 15px;">
                <label>Judul Buku</label>
                <input type="text" name="judul" id="judul" class="form-control" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div><label>ISBN</label><input type="text" name="isbn" id="isbn" class="form-control" required style="width: 100%; padding: 8px; margin-top: 5px;"></div>
                <div><label>Kategori</label><input type="text" name="kategori" id="kategori" class="form-control" required style="width: 100%; padding: 8px; margin-top: 5px;"></div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div><label>Penulis</label><input type="text" name="penulis" id="penulis" class="form-control" required style="width: 100%; padding: 8px; margin-top: 5px;"></div>
                <div><label>Penerbit</label><input type="text" name="penerbit" id="penerbit" class="form-control" required style="width: 100%; padding: 8px; margin-top: 5px;"></div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                <div><label>Tahun Terbit</label><input type="number" name="tahun_terbit" id="tahun_terbit" class="form-control" required style="width: 100%; padding: 8px; margin-top: 5px;"></div>
                <div><label>Stok</label><input type="number" name="stok" id="stok" class="form-control" required style="width: 100%; padding: 8px; margin-top: 5px;"></div>
            </div>
            <div style="margin-bottom: 15px;">
                <label>Lokasi Rak</label>
                <input type="text" name="lokasi_rak" id="lokasi_rak" class="form-control" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>
            <button type="submit" class="btn-primary" style="width: 100%;">Update Data via API PUT</button>
            <a href="petugas_dashboard.php" style="display: block; text-align: center; margin-top: 15px; color: #64748b;">Kembali</a>
        </form>
    </div>
</div>

<script>
    const idBuku = <?= $id_buku; ?>;

    async function loadBukuDetail() {
        try {
            const response = await fetch(`api/buku.php?id=${idBuku}`);
            const result = await response.json();
            if (result.status === 'success') {
                const b = result.data;
                document.getElementById('judul').value = b.judul;
                document.getElementById('isbn').value = b.isbn;
                document.getElementById('kategori').value = b.kategori;
                document.getElementById('penulis').value = b.penulis;
                document.getElementById('penerbit').value = b.penerbit;
                document.getElementById('tahun_terbit').value = b.tahun_terbit;
                document.getElementById('stok').value = b.stok;
                document.getElementById('lokasi_rak').value = b.lokasi_rak;
            } else { alert('Buku tidak ditemukan'); window.location.href='petugas_dashboard.php'; }
        } catch (error) { alert('Gagal memuat detail buku.'); }
    }

    document.getElementById('form-edit-buku').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch(`api/buku.php?id=${idBuku}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            alert(result.message);
            if (result.status === 'success') window.location.href = 'petugas_dashboard.php';
        } catch (error) { alert('Gagal mengupdate buku.'); }
    });

    document.addEventListener('DOMContentLoaded', loadBukuDetail);
</script>

<?php include 'layouts/footer.php'; ?>