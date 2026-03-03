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

    <form id="tambahBukuForm" enctype="multipart/form-data">
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

        <div id="api-message" style="margin-bottom: 15px; font-size: 14px; text-align: center;"></div>

        <button type="submit" class="btn-primary" style="width: 100%;">Simpan Buku ke
            Database</button>
    </form>
</div>

<script>
    document.getElementById('tambahBukuForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const messageDiv = document.getElementById('api-message');
        const submitBtn = this.querySelector('button[type="submit"]');
        
        messageDiv.textContent = 'Menyimpan...';
        messageDiv.style.color = '#475569';
        submitBtn.disabled = true;

        try {
            const response = await fetch('api/buku.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.status === 'success') {
                messageDiv.textContent = 'Buku berhasil ditambahkan!';
                messageDiv.style.color = '#166534';
                
                setTimeout(() => {
                    window.location.href = 'petugas_dashboard.php';
                }, 1500);
            } else {
                messageDiv.textContent = result.message;
                messageDiv.style.color = '#991b1b';
                submitBtn.disabled = false;
            }
        } catch (error) {
            messageDiv.textContent = 'Terjadi kesalahan pada server.';
            messageDiv.style.color = '#991b1b';
            submitBtn.disabled = false;
            console.error('Error:', error);
        }
    });
</script>
</div>

<?php include 'layouts/footer.php'; ?>