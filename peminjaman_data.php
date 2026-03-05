<?php
session_start();
require_once 'config/koneksi.php';
require_once 'models/Peminjaman.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header("Location: index.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$pinjamModel = new Peminjaman($db);
$data_peminjaman = $pinjamModel->getAll();

include 'layouts/header.php';
?>

<div class="card-container">
    <h3 class="header-title">Data Peminjaman Buku</h3>

    <div style="overflow-x: auto;">
        <table style="width: 100%; min-width: 900px;">
            <thead>
                <tr>
                    <th style="width: 18%;">Nama Peminjam</th>
                    <th style="width: 25%;">Judul Buku</th>
                    <th style="width: 13%;">Tgl Pinjam</th>
                    <th style="width: 13%;">Tenggat</th>
                    <th style="width: 15%;">Status & Denda</th>
                    <th style="width: 16%; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($data_peminjaman) > 0): ?>
                    <?php foreach ($data_peminjaman as $pinjam): ?>
                        <tr>
                            <td style="vertical-align: middle;"><strong><?= $pinjam['nama_lengkap']; ?></strong></td>
                            <td style="vertical-align: middle;"><?= $pinjam['judul']; ?></td>
                            <td style="vertical-align: middle;"><?= date('d M Y', strtotime($pinjam['tanggal_pinjam'])); ?></td>
                            <td style="vertical-align: middle; color: #ef4444; font-weight: 500;">
                                <?= date('d M Y', strtotime($pinjam['tanggal_tenggat'])); ?>
                            </td>
                            <td style="vertical-align: middle;">
                                <?php if ($pinjam['status'] == 'dipinjam'): ?>
                                    <span style="display: inline-block; padding: 4px 10px; background: #fef2f2; color: #dc3545; border-radius: 20px; font-size: 12px; font-weight: bold;">Sedang Dipinjam</span>
                                <?php else: ?>
                                    <span style="display: inline-block; padding: 4px 10px; background: #f0fdf4; color: #10b981; border-radius: 20px; font-size: 12px; font-weight: bold;">Dikembalikan</span><br>
                                    <small style="color: #64748b; display: inline-block; margin-top: 4px;">(<?= date('d M Y', strtotime($pinjam['tanggal_dikembalikan'])); ?>)</small>
                                <?php endif; ?>

                                <?php if ($pinjam['status'] == 'dikembalikan'): ?>
                                    <br>
                                    <?php if ($pinjam['denda'] > 0): ?>
                                        <span style="display: inline-block; margin-top: 5px; background: #fee2e2; color: #b91c1c; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">
                                            Denda: Rp <?= number_format($pinjam['denda'], 0, ',', '.'); ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="display: inline-block; margin-top: 5px; color: #10b981; font-size: 12px; font-weight: bold;">Tepat Waktu</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td style="vertical-align: middle;">
                                <div style="display: flex; gap: 6px; justify-content: center; flex-wrap: wrap;">
                                    <?php if ($pinjam['status'] == 'dipinjam'): ?>
                                        <a href="#"
                                            onclick="kembalikanBuku(<?= $pinjam['id_peminjaman']; ?>, '<?= addslashes($pinjam['judul']); ?>', '<?= addslashes($pinjam['nama_lengkap']); ?>'); return false;"
                                            class="btn-primary"
                                            style="background: linear-gradient(135deg, #f59e0b, #d97706); font-size: 12px; padding: 6px 10px; border-radius: 6px; box-shadow: none;">Selesaikan</a>
                                    <?php endif; ?>
                                    
                                    <a href="peminjaman_edit.php?id=<?= $pinjam['id_peminjaman']; ?>" class="btn-primary" 
                                        style="background: #0ea5e9; font-size: 12px; padding: 6px 10px; border-radius: 6px; box-shadow: none;">Edit</a>
                                    
                                    <a href="#" onclick="hapusPeminjaman(<?= $pinjam['id_peminjaman']; ?>); return false;" class="btn-danger" 
                                        style="font-size: 12px; padding: 6px 10px; border-radius: 6px;">Hapus</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding: 40px; color: #64748b;">Belum ada data peminjaman saat ini.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Fungsi Mengembalikan Buku (Auto hitung denda & tambah stok)
function kembalikanBuku(id, judul, nama) {
    showModal(`Konfirmasi pengembalian buku ${judul} oleh ${nama}?`, async function() {
        try {
            const response = await fetch(`api/peminjaman.php?id=${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(null) 
            });
            const result = await response.json();
            alert(result.message);
            if(result.status === 'success') location.reload();
        } catch (e) {
            alert('Gagal memproses pengembalian buku');
        }
    });
}

// Fungsi Hapus Peminjaman
function hapusPeminjaman(id) {
    showModal(`Yakin ingin menghapus riwayat peminjaman ini secara permanen dari database?`, async function() {
        try {
            const response = await fetch(`api/peminjaman.php?id=${id}`, {
                method: 'DELETE'
            });
            const result = await response.json();
            alert(result.message);
            if(result.status === 'success') location.reload();
        } catch (e) {
            alert('Gagal menghapus data');
        }
    });
}
</script>

<?php include 'layouts/footer.php'; ?>