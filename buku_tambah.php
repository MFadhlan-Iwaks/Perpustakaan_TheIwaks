<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header("Location: index.php");
    exit();
}

include 'layouts/header.php';
?>

<div class="card-container" style="max-width: 700px; margin: 0 auto;">
    <h3 class="header-title">Tambah Buku Baru</h3>

    <form action="proses/buku_proses.php" method="POST" enctype="multipart/form-data">
        <div style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569;">Judul Buku</label>
            <input type="text" name="judul" required
                style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
        </div>

        <div style="margin-bottom: 15px; display: flex; gap: 15px;">
            <div style="flex: 1;">
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569;">ISBN</label>
                <input type="text" name="isbn" placeholder="Contoh: 978-623-..."
                    style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
            </div>
            <div style="flex: 1;">
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569;">Kategori</label>
                <input type="text" name="kategori" placeholder="Contoh: Fiksi, Sains" required
                    style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
            </div>
            <div style="flex: 1;">
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569;">Lokasi Rak</label>
                <input type="text" name="lokasi_rak" placeholder="Contoh: Rak A1" required
                    style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
            </div>
        </div>

        <div style="margin-bottom: 15px; display: flex; gap: 15px;">
            <div style="flex: 2;">
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569;">Penulis</label>
                <input type="text" name="penulis" required
                    style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
            </div>
            <div style="flex: 2;">
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569;">Penerbit</label>
                <input type="text" name="penerbit" required
                    style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
            </div>
        </div>

        <div style="margin-bottom: 15px; display: flex; gap: 15px;">
            <div style="flex: 1;">
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569;">Tahun Terbit</label>
                <input type="number" name="tahun_terbit" required
                    style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
            </div>
            <div style="flex: 1;">
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569;">Jumlah Stok</label>
                <input type="number" name="stok" required
                    style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
            </div>
        </div>

        <div style="margin-bottom: 25px;">
            <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569;">Cover Buku
                (Opsional)</label>
            <input type="file" name="gambar" accept="image/jpeg, image/png, image/jpg"
                style="width: 100%; padding: 10px; border: 1px dashed #cbd5e1; border-radius: 8px; background: #f8fafc;">
        </div>

        <button type="submit" name="tambah_buku" class="btn-primary" style="width: 100%;">Simpan Buku ke
            Database</button>
    </form>
</div>

<?php include 'layouts/footer.php'; ?>