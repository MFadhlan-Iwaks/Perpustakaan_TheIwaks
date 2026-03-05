<?php
session_start();
require_once 'config/koneksi.php';
require_once 'models/Peminjaman.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: index.php");
    exit();
}

$id_user = $_SESSION['id_user'];

// Gunakan Model OOP
$database = new Database();
$db = $database->getConnection();
$pinjamModel = new Peminjaman($db);
$data_pinjam = $pinjamModel->getByUserId($id_user);

include 'layouts/header.php';
?>

<div class="card-container">
    <h3 class="header-title">Riwayat Pinjaman Saya</h3>

    <div style="overflow-x: auto;">
        <table style="width: 100%; min-width: 800px;">
            <thead>
                <tr>
                    <th style="width: 12%; text-align: center;">Cover</th>
                    <th style="width: 28%;">Judul Buku</th>
                    <th style="width: 15%;">Tanggal Pinjam</th>
                    <th style="width: 15%;">Tenggat Waktu</th>
                    <th style="width: 30%;">Status & Informasi Denda</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($data_pinjam) > 0): ?>
                    <?php foreach ($data_pinjam as $pinjam): ?>
                        <tr>
                            <td style="text-align: center; vertical-align: middle;">
                                <?php if ($pinjam['gambar']): ?>
                                    <img src="assets/images/buku/<?= $pinjam['gambar']; ?>" alt="Cover"
                                        style="height: 80px; width: 60px; object-fit: cover; border-radius: 6px; margin: 0 auto; display: block; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <?php else: ?>
                                    <span style="font-size: 12px; color: #94a3b8; font-style: italic;">No Image</span>
                                <?php endif; ?>
                            </td>

                            <td style="vertical-align: middle;"><strong><?= $pinjam['judul']; ?></strong></td>
                            <td style="vertical-align: middle;"><?= date('d M Y', strtotime($pinjam['tanggal_pinjam'])); ?></td>
                            <td style="vertical-align: middle; font-weight: 500; color: #0f172a;">
                                <?= date('d M Y', strtotime($pinjam['tanggal_tenggat'])); ?>
                            </td>
                            <td style="vertical-align: middle;">

                                <?php if ($pinjam['status'] == 'dipinjam'): ?>
                                    <span
                                        style="display: inline-block; padding: 4px 10px; background: #fef2f2; color: #dc3545; border-radius: 20px; font-size: 12px; font-weight: bold; margin-bottom: 5px;">Sedang
                                        Dipinjam</span><br>

                                    <?php
                                    $tanggal_sekarang = date('Y-m-d');
                                    if (strtotime($tanggal_sekarang) > strtotime($pinjam['tanggal_tenggat'])) {
                                        $selisih = strtotime($tanggal_sekarang) - strtotime($pinjam['tanggal_tenggat']);
                                        $hari_telat = floor($selisih / (60 * 60 * 24));
                                        $denda_berjalan = $hari_telat * 1000;
                                        ?>
                                        <span style="color: #b91c1c; font-size: 12px; font-weight: 600;">
                                            ⚠️ Terlambat <?= $hari_telat; ?> Hari (Denda: Rp
                                            <?= number_format($denda_berjalan, 0, ',', '.'); ?>)
                                        </span>
                                    <?php } else { ?>
                                        <span style="color: #059669; font-size: 12px; font-weight: 500;">Sisa waktu aman.</span>
                                    <?php } ?>

                                <?php else: ?>
                                    <span
                                        style="display: inline-block; padding: 4px 10px; background: #f0fdf4; color: #10b981; border-radius: 20px; font-size: 12px; font-weight: bold; margin-bottom: 5px;">Dikembalikan</span><br>

                                    <?php if ($pinjam['denda'] > 0): ?>
                                        <span style="color: #b91c1c; font-size: 12px; font-weight: 600;">Telah membayar denda: Rp
                                            <?= number_format($pinjam['denda'], 0, ',', '.'); ?></span>
                                    <?php else: ?>
                                        <span style="color: #10b981; font-size: 12px; font-weight: 600;">Dikembalikan Tepat Waktu
                                            👍</span>
                                    <?php endif; ?>

                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding: 40px; color: #64748b;">Kamu belum meminjam buku
                            apapun.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'layouts/footer.php'; ?>