<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: index.php");
    exit();
}

include 'layouts/header.php';
?>

<div class="card-container">
    <h3 class="header-title">Katalog Buku Tersedia</h3>

    <div style="margin-bottom: 30px; display: flex; gap: 10px; max-width: 600px;">
        <input type="text" id="search-input" placeholder="Cari judul buku, penulis, atau kategori..."
            style="flex: 1; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 15px; outline: none;">
        <button onclick="searchBooks()" class="btn-primary" style="padding: 12px 24px; border-radius: 8px;">Cari</button>
    </div>

    <div class="grid-buku" id="book-grid">
        </div>
</div>

<script>
    async function loadBooks(keyword = '') {
        const grid = document.getElementById('book-grid');
        try {
            const response = await fetch('api/buku.php');
            const result = await response.json();
            
            if (result.status === 'success') {
                let books = result.data;
                
                // Filter client-side sederhana untuk pencarian
                if(keyword) {
                    books = books.filter(b => 
                        b.judul.toLowerCase().includes(keyword.toLowerCase()) || 
                        b.penulis.toLowerCase().includes(keyword.toLowerCase())
                    );
                }

                grid.innerHTML = books.map(buku => `
                    <div class="card-buku">
                        ${buku.gambar 
                            ? `<img src="assets/images/buku/${buku.gambar}" alt="Cover ${buku.judul}">`
                            : `<div style="height: 200px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; border-radius: 12px; color: #94a3b8;">No Cover</div>`
                        }
                        <h4>${buku.judul}</h4>
                        <p class="penulis">${buku.penulis}</p>
                        <p style="font-size: 12px; color: #64748b; margin-bottom: 15px; font-weight: 600; text-transform: uppercase;">${buku.kategori}</p>

                        <div class="card-footer-info">
                            <span class="stok-badge">Stok: ${buku.stok}</span>
                            <button onclick="pinjamBuku(${buku.id_buku}, '${buku.judul.replace(/'/g, "\\'")}')" class="btn-pinjam">Pinjam Buku</button>
                        </div>
                    </div>
                `).join('');
            }
        } catch (e) { grid.innerHTML = 'Gagal memuat buku.'; }
    }

    function searchBooks() {
        const val = document.getElementById('search-input').value;
        loadBooks(val);
    }

    function pinjamBuku(id, judul) {
        showModal(`Pinjam buku ${judul} ini selama 7 hari?`, async function() {
            try {
                const response = await fetch('api/peminjaman.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_user: <?= $_SESSION['id_user']; ?>, id_buku: id })
                });
                const res = await response.json();
                alert(res.message);
                if(res.status === 'success') loadBooks();
            } catch (e) { alert('Gagal memproses pinjaman'); }
        });
    }

    document.addEventListener('DOMContentLoaded', () => loadBooks());
</script>

<?php include 'layouts/footer.php'; ?>