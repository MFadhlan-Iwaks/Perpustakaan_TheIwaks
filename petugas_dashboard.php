<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header("Location: index.php");
    exit();
}

include 'layouts/header.php';
?>

<div class="main-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="font-size: 24px; color: #1e293b; margin: 0;">👋 Halo, <?= $_SESSION['username']; ?>!</h2>
        <a href="buku_tambah.php" class="btn-primary" style="padding: 10px 20px;">+ Tambah Buku Baru</a>
    </div>

    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="card-container" style="text-align: center; border-left: 5px solid #3b82f6;">
            <p style="color: #64748b; font-size: 14px;">Total Buku</p>
            <h3 id="stat-buku" style="font-size: 28px; margin: 5px 0;">...</h3>
        </div>
        <div class="card-container" style="text-align: center; border-left: 5px solid #10b981;">
            <p style="color: #64748b; font-size: 14px;">Peminjaman Aktif</p>
            <h3 id="stat-pinjam" style="font-size: 28px; margin: 5px 0;">...</h3>
        </div>
    </div>

    <div class="card-container">
        <h3 class="header-title" style="margin-bottom: 20px;">📚 Kelola Katalog Buku </h3>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Sampul</th>
                        <th>Info Buku</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Lokasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="buku-table-body">
                    <tr><td colspan="7" style="text-align:center;">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="custom-modal" class="modal-overlay">
    <div class="modal-box">
        <h3 class="modal-title" id="modal-title">Konfirmasi</h3>
        <p class="modal-text" id="modal-message"></p>
        <div class="modal-actions">
            <button onclick="closeModal()" class="btn-modal-cancel">Batal</button>
            <button id="modal-confirm-btn" class="btn-modal-confirm">Konfirmasi</button>
        </div>
    </div>
</div>

<script>
    function showModal(message, callback) {
        const modal = document.getElementById('custom-modal');
        const messageElement = document.getElementById('modal-message');
        const confirmBtn = document.getElementById('modal-confirm-btn');

        messageElement.innerText = message;
        modal.classList.add('active');

        confirmBtn.onclick = () => {
            callback();
            closeModal();
        };
    }

    function closeModal() {
        document.getElementById('custom-modal').classList.remove('active');
    }

    async function loadDashboardData() {
        try {
            const resBuku = await fetch('api/buku.php');
            const dataBuku = await resBuku.json();
        
            const resPinjam = await fetch('api/peminjaman.php');
            const dataPinjam = await resPinjam.json();

            if (dataBuku.status === 'success') document.getElementById('stat-buku').innerText = dataBuku.data.length;
            if (dataPinjam.status === 'success') {
                const aktif = dataPinjam.data.filter(p => p.status === 'dipinjam').length;
                document.getElementById('stat-pinjam').innerText = aktif;
            }

            const tableBody = document.getElementById('buku-table-body');
            tableBody.innerHTML = '';
            
            if (dataBuku.status === 'success') {
                dataBuku.data.forEach((b, index) => {
                    const img = b.gambar ? `assets/images/buku/${b.gambar}` : 'https://via.placeholder.com/50x70?text=No+Cover';
                    const row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td><img src="${img}" style="width: 50px; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></td>
                            <td>
                                <strong>${b.judul}</strong><br>
                                <span style="font-size: 11px; color: #64748b;">${b.penulis} | ISBN: ${b.isbn}</span>
                            </td>
                            <td><span style="background: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-size: 11px;">${b.kategori}</span></td>
                            <td><b style="color: ${b.stok > 0 ? '#10b981' : '#ef4444'}">${b.stok}</b></td>
                            <td>${b.lokasi_rak}</td>
                            <td>
                                <div style="display: flex; gap: 5px;">
                                    <a href="buku_edit.php?id=${b.id_buku}" class="btn-primary" style="background: #0ea5e9; font-size: 11px; padding: 5px 10px;">Edit</a>
                                    <button onclick="hapusBuku(${b.id_buku}, '${b.judul.replace(/'/g, "\\'")}')" class="btn-danger" style="font-size: 11px; padding: 5px 10px;">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            }
        } catch (error) { console.error('Error load dashboard:', error); }
    }

    async function hapusBuku(id, judul) {
        showModal(`Yakin ingin menghapus buku "${judul}"?`, async () => {
            try {
                const res = await fetch(`api/buku.php?id=${id}`, { method: 'DELETE' });
                const result = await res.json();
                alert(result.message);
                loadDashboardData();
            } catch (error) { alert('Gagal menghapus.'); }
        });
    }

    document.addEventListener('DOMContentLoaded', loadDashboardData);
</script>

<?php include 'layouts/footer.php'; ?>