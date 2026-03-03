<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Perpustakaan Modern</title>
    <link rel="stylesheet" href="assets/css/layout.css">
</head>

<body>
    <nav class="navbar-fixed">
        <a href="#" class="nav-brand" style="text-decoration: none;">
            <h2>📚 Perpus The Iwaks</h2>
        </a>

        <button class="hamburger" id="hamburger-btn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <div class="nav-links" id="nav-menu">
            <?php if ($_SESSION['role'] == 'petugas'): ?>
                <a href="petugas_dashboard.php">Daftar Buku</a>
                <a href="buku_tambah.php">Tambah Buku</a>
                <a href="peminjaman_data.php">Data Peminjaman</a>
                <a href="kelola_user.php">Kelola Pengguna</a>
                <a href="api/logout.php" class="btn-logout">Logout (<?= $_SESSION['username']; ?>)</a>
            <?php else: ?>
                <a href="user_dashboard.php">Katalog Buku</a>
                <a href="user_pinjaman.php">Pinjaman Saya</a>
                <a href="api/logout.php" class="btn-logout">Logout (<?= $_SESSION['username']; ?>)</a>
            <?php endif; ?>
        </div>
    </nav>

    <script>
        document.getElementById('hamburger-btn').addEventListener('click', function () {
            document.getElementById('nav-menu').classList.toggle('active');
        });
    </script>

    <main class="main-content">