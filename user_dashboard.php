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
    <div style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 20px;">
        <div>
            <h2 style="font-size: 32px; color: #1e293b; margin: 0; font-weight: 800; letter-spacing: -0.5px;">📚 Katalog Perpustakaan</h2>
            <p style="color: #64748b; margin-top: 5px; font-size: 16px;">Temukan inspirasi dan ilmu pengetahuan baru hari ini.</p>
        </div>
        <div style="position: relative; width: 100%; max-width: 450px;">
            <input type="text" id="search-book" placeholder="Cari berdasarkan judul, penulis, atau kategori..." 
                style="width: 100%; padding: 16px 15px 16px 50px; border-radius: 16px; border: 1px solid #e2e8f0; font-size: 15px; outline: none; transition: all 0.3s; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); background: white;">
            <span style="position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 20px;">🔍</span>
        </div>
    </div>

    <div class="card-container" style="background-color: #f1f5f9; padding: 40px; border: none; box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); min-height: 400px;">
        <div id="book-grid" class="grid-buku">
            <div style="grid-column: 1/-1; text-align: center; padding: 50px;">
                <p style="color: #64748b;">Sedang memuat katalog buku...</p>
            </div>
        </div>
    </div>
</div>

<div id="custom-modal" class="modal-overlay">
    <div class="modal-box">
        <h3 class="modal-title" id="modal-title">Konfirmasi Pinjam</h3>
        <p class="modal-text" id="modal-message"></p>
        <div class="modal-actions">
            <button onclick="closeModal()" class="btn-modal-cancel">Batal</button>
            <button id="modal-confirm-btn" class="btn-modal-confirm">Konfirmasi</button>
        </div>
    </div>
</div>

<script>
    let allBooks = [];
    let modalConfirmCallback = null;

    function showModal(message, callback) {
        document.getElementById('modal-message').innerText = message;
        document.getElementById('custom-modal').classList.add('active');
        modalConfirmCallback = callback;
    }

    function closeModal() {
        document.getElementById('custom-modal').classList.remove('active');
        modalConfirmCallback = null;
    }

    document.getElementById('modal-confirm-btn').addEventListener('click', () => {
        if (modalConfirmCallback) modalConfirmCallback();
        closeModal();
    });

    async function loadBooks() {
        try {
            const response = await fetch('api/buku.php');
            const result = await response.json();
            
            if (result.status === 'success') {
                allBooks = result.data;
                renderBooks(allBooks);
            }
        } catch (error) { 
            document.getElementById('book-grid').innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 50px;"><p style="color: #ef4444;">Gagal memuat katalog buku. Pastikan koneksi server tersedia.</p></div>'; 
        }
    }

    function renderBooks(books) {
        const grid = document.getElementById('book-grid');
        grid.innerHTML = '';

        if (books.length === 0) {
            grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 80px;"><p style="color: #64748b; font-size: 18px; font-weight: 500;">Oops! Buku yang Anda cari tidak ditemukan.</p></div>';
            return;
        }

        books.forEach((b) => {
            const img = b.gambar ? `assets/images/buku/${b.gambar}` : 'https://via.placeholder.com/200x300?text=No+Cover';
            const card = `
                <div class="card-buku">
                    <div style="position: relative; margin-bottom: 15px;">
                        <img src="${img}" alt="${b.judul}">
                        <span style="position: absolute; top: 12px; right: 12px; font-size: 11px; background: rgba(255,255,255,0.95); padding: 5px 12px; border-radius: 20px; color: #1e293b; font-weight: 700; backdrop-filter: blur(4px); box-shadow: 0 2px 4px rgba(0,0,0,0.1); border: 1px solid rgba(0,0,0,0.05);">${b.kategori}</span>
                    </div>
                    <div style="flex: 1; display: flex; flex-direction: column; padding: 0 5px;">
                        <h4 style="margin: 0 0 10px 0; font-size: 17px; color: #1e293b; font-weight: 700; height: 48px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-align: center;">${b.judul}</h4>
                        <p class="penulis" style="margin-bottom: 20px; text-align: center;">${b.penulis}</p>
                        
                        <div class="card-footer-info" style="margin-top: auto;">
                            <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 15px;">
                                <span class="stok-badge" style="background: ${b.stok > 0 ? '#dcfce7' : '#fee2e2'}; color: ${b.stok > 0 ? '#166534' : '#991b1b'};">
                                    ${b.stok > 0 ? `Stok: ${b.stok}` : 'Stok Habis'}
                                </span>
                            </div>
                            
                            ${b.stok > 0 
                                ? `<button onclick="pinjamBuku(${b.id_buku}, '${b.judul.replace(/'/g, "\\'")}')" class="btn-pinjam">Pinjam Buku</button>`
                                : `<button disabled class="btn-pinjam" style="background: #cbd5e1; cursor: not-allowed; transform: none; box-shadow: none;">Stok Habis</button>`
                            }
                        </div>
                    </div>
                </div>
            `;
            grid.innerHTML += card;
        });
    }

    document.getElementById('search-book').addEventListener('input', (e) => {
        const keyword = e.target.value.toLowerCase();
        const filtered = allBooks.filter(b => 
            b.judul.toLowerCase().includes(keyword) || 
            b.penulis.toLowerCase().includes(keyword) ||
            b.kategori.toLowerCase().includes(keyword)
        );
        renderBooks(filtered);
    });

    async function pinjamBuku(idBuku, judul) {
        showModal(`Konfirmasi: Apakah Anda yakin ingin meminjam buku "${judul}"?`, async () => {
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
            } catch (error) { alert('Gagal memproses peminjaman buku.'); }
        });
    }

    document.addEventListener('DOMContentLoaded', loadBooks);
</script>

<?php include 'layouts/footer.php'; ?>