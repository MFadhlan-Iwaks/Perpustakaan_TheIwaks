<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header("Location: index.php");
    exit();
}

$query_buku = mysqli_query($koneksi, "SELECT * FROM buku ORDER BY id_buku DESC");

include 'layouts/header.php'; 
?>

<div class="card-container">
    <h3 class="header-title">Daftar Buku Perpustakaan</h3>
    
    <div style="overflow-x: auto;">
        <table style="width: 100%; min-width: 800px;">
            <thead>
                <tr>
                    <th style="width: 12%; text-align: center;">Cover</th>
                    <th style="width: 33%;">Judul & Info</th>
                    <th style="width: 20%;">Penulis</th>
                    <th style="width: 15%;">Stok & Rak</th>
                    <th style="width: 20%; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($query_buku)) : ?>
                <tr>
                    <td style="text-align: center; vertical-align: middle;">
                        <?php if ($row['gambar']) : ?>
                            <img src="assets/images/buku/<?= $row['gambar']; ?>" class="img-cover" alt="Cover" style="margin: 0 auto; display: block;">
                        <?php else : ?>
                            <span style="font-size: 12px; color: #94a3b8; font-style: italic;">No Image</span>
                        <?php endif; ?>
                    </td>
                    <td style="vertical-align: middle;">
                        <strong style="font-size: 15px; color: #0f172a;"><?= $row['judul']; ?></strong><br>
                        <small style="color: #64748b;"><?= $row['kategori']; ?> | ISBN: <?= $row['isbn'] ? $row['isbn'] : '-'; ?></small><br>
                        <small style="color: #64748b;"><?= $row['penerbit']; ?> (<?= $row['tahun_terbit']; ?>)</small>
                    </td>
                    <td style="color: #334155; font-weight: 500; vertical-align: middle;">
                        <?= $row['penulis']; ?>
                    </td>
                    <td style="vertical-align: middle;">
                        <span style="background: #e0f2fe; color: #0284c7; padding: 4px 10px; border-radius: 12px; font-weight: bold; font-size: 13px;">Stok: <?= $row['stok']; ?></span><br>
                        <small style="color: #059669; font-weight: 600; display: inline-block; margin-top: 6px;">📍 <?= $row['lokasi_rak']; ?></small>
                    </td>
                    <td style="vertical-align: middle;">
                        <div style="display: flex; gap: 8px; align-items: center; justify-content: center;">
                            <a href="buku_edit.php?id=<?= $row['id_buku']; ?>" class="btn-primary" style="background: #0ea5e9; font-size: 13px; padding: 0 16px; height: 34px; display: inline-flex; align-items: center; justify-content: center; box-shadow: none; border-radius: 6px;">Edit</a>
                            
                            <a href="#" onclick="showModal('Yakin ingin menghapus buku <?= addslashes($row['judul']); ?> dari database?', 'proses/buku_proses.php?hapus=<?= $row['id_buku']; ?>'); return false;" class="btn-danger" style="font-size: 13px; padding: 0 16px; height: 34px; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px;">Hapus</a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
include 'layouts/footer.php'; 
?>