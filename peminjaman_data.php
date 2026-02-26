<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header("Location: index.php");
    exit();
}

$query_peminjaman = mysqli_query($koneksi, "
    SELECT p.*, b.judul, u.nama_lengkap 
    FROM peminjaman p 
    JOIN buku b ON p.id_buku = b.id_buku 
    JOIN users u ON p.id_user = u.id_user 
    ORDER BY p.status ASC, p.tanggal_pinjam DESC
");

include 'layouts/header.php';
?>

<div class="card-container">
    <h3 class="header-title">Data Peminjaman Buku</h3>

    <div style="overflow-x: auto;">
        <table style="width: 100%; min-width: 800px;">
            <thead>
                <tr>
                    <th style="width: 20%;">Nama Peminjam</th>
                    <th style="width: 25%;">Judul Buku</th>
                    <th style="width: 15%;">Tgl Pinjam</th>
                    <th style="width: 15%;">Tenggat</th>
                    <th style="width: 15%;">Status & Denda</th>
                    <th style="width: 10%; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($query_peminjaman) > 0): ?>
                    <?php while ($pinjam = mysqli_fetch_assoc($query_peminjaman)): ?>
                        <tr>
                            <td style="vertical-align: middle;"><strong><?= $pinjam['nama_lengkap']; ?></strong></td>
                            <td style="vertical-align: middle;"><?= $pinjam['judul']; ?></td>
                            <td style="vertical-align: middle;"><?= date('d M Y', strtotime($pinjam['tanggal_pinjam'])); ?></td>
                            <td style="vertical-align: middle; color: #ef4444; font-weight: 500;">
                                <?= date('d M Y', strtotime($pinjam['tanggal_tenggat'])); ?>
                            </td>
                            <td style="vertical-align: middle;">
                                <?php if ($pinjam['status'] == 'dipinjam'): ?>
                                    <span
                                        style="display: inline-block; padding: 4px 10px; background: #fef2f2; color: #dc3545; border-radius: 20px; font-size: 12px; font-weight: bold;">Sedang
                                        Dipinjam</span>
                                <?php else: ?>
                                    <span
                                        style="display: inline-block; padding: 4px 10px; background: #f0fdf4; color: #10b981; border-radius: 20px; font-size: 12px; font-weight: bold;">Dikembalikan</span><br>
                                    <small
                                        style="color: #64748b; display: inline-block; margin-top: 4px;">(<?= date('d M Y', strtotime($pinjam['tanggal_dikembalikan'])); ?>)</small>
                                <?php endif; ?>

                                <?php if ($pinjam['status'] == 'dikembalikan'): ?>
                                    <br>
                                    <?php if ($pinjam['denda'] > 0): ?>
                                        <span
                                            style="display: inline-block; margin-top: 5px; background: #fee2e2; color: #b91c1c; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">
                                            Denda: Rp <?= number_format($pinjam['denda'], 0, ',', '.'); ?>
                                        </span>
                                    <?php else: ?>
                                        <span
                                            style="display: inline-block; margin-top: 5px; color: #10b981; font-size: 12px; font-weight: bold;">Tepat
                                            Waktu</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td style="vertical-align: middle;">
                                <div style="display: flex; justify-content: center;">
                                    <?php if ($pinjam['status'] == 'dipinjam'): ?>
                                        <a href="#"
                                            onclick="showModal('Konfirmasi pengembalian buku <?= addslashes($pinjam['judul']); ?> oleh <?= addslashes($pinjam['nama_lengkap']); ?>?', 'proses/kembali_proses.php?id_peminjaman=<?= $pinjam['id_peminjaman']; ?>&id_buku=<?= $pinjam['id_buku']; ?>'); return false;"
                                            class="btn-primary"
                                            style="background: linear-gradient(135deg, #f59e0b, #d97706); font-size: 13px; padding: 6px 12px; border-radius: 6px; box-shadow: none;">Selesaikan</a>
                                    <?php else: ?>
                                        <span
                                            style="background: #f1f5f9; color: #94a3b8; font-size: 12px; padding: 6px 12px; border-radius: 6px; font-weight: 600;">Selesai</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding: 40px; color: #64748b;">Belum ada data peminjaman
                            saat ini.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
include 'layouts/footer.php';
?>