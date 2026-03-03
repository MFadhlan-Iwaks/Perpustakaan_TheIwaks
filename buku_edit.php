<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: petugas_dashboard.php");
    exit();
}

$id_buku = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM buku WHERE id_buku = '$id_buku'");
$buku = mysqli_fetch_assoc($query);

if (!$buku) {
    header("Location: petugas_dashboard.php");
    exit();
}

include 'layouts/header.php';
?>

<div class="card-container" style="max-width: 700px; margin: 0 auto;">
    <h3 class="header-title">Edit Data Buku</h3>

    <form id="editBukuForm" enctype="multipart/form-data">
        <input type="hidden" name="id_buku" value="<?= $buku['id_buku']; ?>">

        <div style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569;">Judul Buku</label>
            <input type="text" name="judul" value="<?= $buku['judul']; ?>" required
                style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
        </div>

        <div style="margin-bottom: 15px; display: flex; gap: 15px;">
            <div style="flex: 1;">
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569;">ISBN</label>
                <input type="text" name="isbn" value="<?= $buku['isbn']; ?>"
                    style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
            </div>
            <div style="flex: 1;">
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569;">Kategori</label>
                <input type="text" name="kategori" value="<?= $buku['kategori']; ?>" required
                    style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
            </div>
            <div style="flex: 1;">
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569;">Lokasi Rak</label>
                <input type="text" name="lokasi_rak" value="<?= $buku['lokasi_rak']; ?>" required
                    style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
            </div>
        </div>

        <div style="margin-bottom: 15px; display: flex; gap: 15px;">
            <div style="flex: 2;">
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569;">Penulis</label>
                <input type="text" name="penulis" value="<?= $buku['penulis']; ?>" required
                    style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
            </div>
            <div style="flex: 2;">
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569;">Penerbit</label>
                <input type="text" name="penerbit" value="<?= $buku['penerbit']; ?>" required
                    style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
            </div>
        </div>

        <div style="margin-bottom: 15px; display: flex; gap: 15px;">
            <div style="flex: 1;">
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569;">Tahun Terbit</label>
                <input type="number" name="tahun_terbit" value="<?= $buku['tahun_terbit']; ?>" required
                    style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
            </div>
            <div style="flex: 1;">
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569;">Jumlah Stok</label>
                <input type="number" name="stok" value="<?= $buku['stok']; ?>" required
                    style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
            </div>
        </div>

        <div style="margin-bottom: 25px;">
            <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569;">Ganti Cover (Biarkan kosong
                jika tidak ingin ganti)</label>
            <input type="file" name="gambar" accept="image/jpeg, image/png, image/jpg"
                style="width: 100%; padding: 10px; border: 1px dashed #cbd5e1; border-radius: 8px; background: #f8fafc;">
            <?php if ($buku['gambar']): ?>
                <div style="margin-top: 10px; font-size: 13px; color: #64748b; display: flex; align-items: center;">
                    Cover saat ini: <img src="assets/images/buku/<?= $buku['gambar']; ?>"
                        style="height: 50px; border-radius: 4px; margin-left: 10px; border: 1px solid #ccc;">
                </div>
            <?php endif; ?>
        </div>

        <div id="api-message" style="margin-bottom: 15px; font-size: 14px; text-align: center;"></div>

        <button type="submit" class="btn-primary" style="width: 100%;">Update Data Buku</button>
    </form>
</div>

<script>
    document.getElementById('editBukuForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const messageDiv = document.getElementById('api-message');
        const submitBtn = this.querySelector('button[type="submit"]');
        
        messageDiv.textContent = 'Memperbarui...';
        messageDiv.style.color = '#475569';
        submitBtn.disabled = true;

        try {
            const response = await fetch('api/buku.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.status === 'success') {
                messageDiv.textContent = 'Buku berhasil diperbarui!';
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