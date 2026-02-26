<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: index.php");
    exit();
}

$keyword = "";
if (isset($_GET['search'])) {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['search']);
    $query_katalog = mysqli_query($koneksi, "SELECT * FROM buku WHERE stok > 0 AND (judul LIKE '%$keyword%' OR penulis LIKE '%$keyword%' OR kategori LIKE '%$keyword%') ORDER BY id_buku DESC");
} else {
    $query_katalog = mysqli_query($koneksi, "SELECT * FROM buku WHERE stok > 0 ORDER BY id_buku DESC");
}

include 'layouts/header.php';
?>

<div class="card-container">
    <h3 class="header-title">Katalog Buku Tersedia</h3>

    <form action="user_dashboard.php" method="GET"
        style="margin-bottom: 30px; display: flex; gap: 10px; max-width: 600px;">
        <input type="text" name="search" value="<?= htmlspecialchars($keyword); ?>"
            placeholder="Cari judul buku, penulis, atau kategori..."
            style="flex: 1; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 15px; outline: none; transition: 0.3s;"
            onfocus="this.style.borderColor='#4f46e5'" onblur="this.style.borderColor='#cbd5e1'">

        <button type="submit" class="btn-primary"
            style="padding: 12px 24px; border-radius: 8px; font-size: 15px; cursor: pointer; border: none;">Cari</button>

        <?php if ($keyword != ""): ?>
            <a href="user_dashboard.php"
                style="padding: 12px 20px; border-radius: 8px; font-size: 15px; text-decoration: none; display: flex; align-items: center; justify-content: center; background: #f1f5f9; color: #475569; font-weight: 600; border: 1px solid #e2e8f0;">Reset</a>
        <?php endif; ?>
    </form>

    <?php if (isset($_SESSION['pesan'])): ?>
        <?php
        $tipe = isset($_SESSION['tipe_pesan']) ? $_SESSION['tipe_pesan'] : 'success';
        $bg = ($tipe == 'error') ? '#fef2f2' : '#dcfce7';
        $color = ($tipe == 'error') ? '#991b1b' : '#166534';
        $border = ($tipe == 'error') ? '#ef4444' : '#22c55e';
        $icon = ($tipe == 'error') ? '❌' : '✅';
        ?>
        <div
            style="background: <?= $bg; ?>; color: <?= $color; ?>; padding: 12px 20px; border-radius: 8px; margin-bottom: 25px; border-left: 5px solid <?= $border; ?>; font-weight: 500;">
            <?= $icon; ?>     <?= $_SESSION['pesan'];
                   unset($_SESSION['pesan']);
                   unset($_SESSION['tipe_pesan']); ?>
        </div>
    <?php endif; ?>

    <?php if (mysqli_num_rows($query_katalog) == 0 && $keyword != ""): ?>
        <div
            style="text-align: center; padding: 50px 20px; color: #64748b; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1;">
            <div style="font-size: 40px; margin-bottom: 10px;">📚❓</div>
            <h4 style="color: #334155; margin-bottom: 5px;">Buku tidak ditemukan</h4>
            <p>Tidak ada buku yang cocok dengan kata kunci "<strong><?= htmlspecialchars($keyword); ?></strong>".</p>
        </div>
    <?php endif; ?>

    <div class="grid-buku">
        <?php while ($buku = mysqli_fetch_assoc($query_katalog)): ?>
            <div class="card-buku">
                <?php if ($buku['gambar']): ?>
                    <img src="assets/images/buku/<?= $buku['gambar']; ?>" alt="Cover <?= $buku['judul']; ?>">
                <?php else: ?>
                    <div
                        style="height: 200px; background: #f1f5f9; display: flex; flex-direction: column; align-items: center; justify-content: center; margin-bottom: 20px; border-radius: 12px; color: #94a3b8;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                        <span style="margin-top: 10px; font-weight: 500; font-size: 14px;">No Cover</span>
                    </div>
                <?php endif; ?>

                <h4><?= $buku['judul']; ?></h4>
                <p class="penulis"><?= $buku['penulis']; ?></p>
                <p
                    style="font-size: 12px; color: #64748b; margin-bottom: 15px; margin-top: -10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                    <?= $buku['kategori']; ?></p>

                <div class="card-footer-info">
                    <span class="stok-badge">Stok: <?= $buku['stok']; ?></span>

                    <a href="#"
                        onclick="showModal('Pinjam buku <?= addslashes($buku['judul']); ?> ini selama 7 hari?', 'proses/pinjam_proses.php?id=<?= $buku['id_buku']; ?>'); return false;"
                        class="btn-pinjam">Pinjam Buku</a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include 'layouts/footer.php'; ?>