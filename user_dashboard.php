<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: index.php");
    exit();
}

include 'layouts/header.php';
?>

<div class="main-content">
    <div style="margin-bottom: 25px;">
        <h2 style="font-size: 24px; color: #1e293b; margin: 0;">📚 Katalog Buku Perpustakaan</h2>
        <p style="color: #64748b; margin-top: 5px;">Silahkan pilih buku yang ingin Anda pinjam (Maks 3 buku).</p>
    </div>

    <div id="book-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
        <!-- Data buku akan diisi via API -->
        <p style="text-align: center; grid-column: 1/-1;">Memuat katalog...</p>
    </div>
</div>

<script>
    async function loadBooks() {
        try {
            const response = await fetch('api/buku.php');
            const result = await response.json();
            const grid = document.getElementById('book-grid');
            grid.innerHTML = '';

            if (result.status === 'success') {
                result.data.forEach(b => {
                    const img = b.gambar ? `assets/images/buku/${b.gambar}` : 'https://via.placeholder.com/150x200?text=No+Cover';
                    const card = `
                        <div class="card-container" style="display: flex; gap: 15px; align-items: flex-start; transition: transform 0.2s;">
                            <img src="${img}" style="width: 100px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            <div style="flex: 1;">
                                <h4 style="margin: 0 0 5px 0; font-size: 16px; color: #1e293b;">${b.judul}</h4>
                                <p style="font-size: 12px; color: #64748b; margin: 0 0 10px 0;">Oleh: ${b.penulis}</p>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                    <span style="font-size: 11px; background: #f1f5f9; padding: 4px 8px; border-radius: 4px;">${b.kategori}</span>
                                    <span style="font-size: 12px; font-weight: bold; color: ${b.stok > 0 ? '#10b981' : '#ef4444'}">Stok: ${b.stok}</span>
                                </div>
                                ${b.stok > 0 
                                    ? `<button onclick="pinjamBuku(${b.id_buku})" class="btn-primary" style="width: 100%; font-size: 12px; padding: 8px;">Pinjam Buku</button>`
                                    : `<button disabled class="btn-primary" style="width: 100%; font-size: 12px; padding: 8px; background: #cbd5e1; cursor: not-allowed;">Stok Habis</button>`
                                }
                            </div>
                        </div>
                    `;
                    grid.innerHTML += card;
                });
            }
        } catch (error) { grid.innerHTML = 'Gagal memuat katalog.'; }
    }

    async function pinjamBuku(idBuku) {
        if (confirm('Konfirmasi peminjaman buku ini?')) {
            try {
                const response = await fetch('api/peminjaman.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id_user: <?= $_SESSION['id_user']; ?>,
                        id_buku: idBuku
                    })
                });
                const result = await response.json();
                alert(result.message);
                if (result.status === 'success') loadBooks();
            } catch (error) { alert('Gagal memproses peminjaman.'); }
        }
    }

    document.addEventListener('DOMContentLoaded', loadBooks);
</script>

<?php include 'layouts/footer.php'; ?>