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
        <h3 class="header-title">➕ Tambah Buku Baru (via API POST)</h3>
        <form id="form-tambah-buku" enctype="multipart/form-data">
            <div style="margin-bottom: 15px;">
                <label>Judul Buku</label>
                <input type="text" name="judul" class="form-control" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div><label>ISBN</label><input type="text" name="isbn" class="form-control" required style="width: 100%; padding: 8px; margin-top: 5px;"></div>
                <div><label>Kategori</label><input type="text" name="kategori" class="form-control" required style="width: 100%; padding: 8px; margin-top: 5px;"></div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div><label>Penulis</label><input type="text" name="penulis" class="form-control" required style="width: 100%; padding: 8px; margin-top: 5px;"></div>
                <div><label>Penerbit</label><input type="text" name="penerbit" class="form-control" required style="width: 100%; padding: 8px; margin-top: 5px;"></div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                <div><label>Tahun Terbit</label><input type="number" name="tahun_terbit" class="form-control" required style="width: 100%; padding: 8px; margin-top: 5px;"></div>
                <div><label>Stok</label><input type="number" name="stok" class="form-control" required style="width: 100%; padding: 8px; margin-top: 5px;"></div>
            </div>
            <div style="margin-bottom: 15px;">
                <label>Lokasi Rak</label>
                <input type="text" name="lokasi_rak" class="form-control" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label>Gambar Sampul</label>
                <input type="file" name="gambar" class="form-control" accept="image/*" style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>
            <button type="submit" class="btn-primary" style="width: 100%;">Simpan via API POST</button>
            <a href="petugas_dashboard.php" style="display: block; text-align: center; margin-top: 15px; color: #64748b;">Kembali</a>
        </form>
    </div>
</div>

<script>
    document.getElementById('form-tambah-buku').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        try {
            const response = await fetch('api/buku.php', {
                method: 'POST',
                body: formData // Menggunakan FormData untuk dukungan upload file
            });
            const result = await response.json();
            alert(result.message);
            if (result.status === 'success') window.location.href = 'petugas_dashboard.php';
        } catch (error) { alert('Gagal menambah buku.'); }
    });
</script>

<?php include 'layouts/footer.php'; ?>