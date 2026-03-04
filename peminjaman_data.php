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
    <div class="card-container">
        <h3 class="header-title">📋 Data Peminjaman Buku</h3>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Peminjam</th>
                        <th>Judul Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Tenggat</th>
                        <th>Status</th>
                        <th>Denda</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="pinjam-table-body">
                    <tr><td colspan="8" style="text-align:center;">Memuat data...</td></tr>
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

    async function loadPeminjaman() {
        try {
            const response = await fetch('api/peminjaman.php');
            const result = await response.json();
            const tableBody = document.getElementById('pinjam-table-body');
            tableBody.innerHTML = '';

            if (result.status === 'success') {
                result.data.forEach((p, index) => {
                    const statusBadge = p.status === 'dipinjam' 
                        ? `<span style="background: #fef9c3; color: #854d0e; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold;">Dipinjam</span>`
                        : `<span style="background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold;">Dikembalikan</span>`;

                    const actionBtn = p.status === 'dipinjam'
                        ? `<div style="display:flex; gap:5px;">
                            <button onclick="kembalikanBuku(${p.id_peminjaman})" class="btn-primary" style="background: #22c55e; font-size: 11px; padding: 5px 10px;">Kembalikan</button>
                            <a href="peminjaman_edit.php?id=${p.id_peminjaman}" class="btn-primary" style="background: #0ea5e9; font-size: 11px; padding: 5px 10px;">Edit</a>
                           </div>`
                        : `<div style="display:flex; gap:5px;">
                            <a href="peminjaman_edit.php?id=${p.id_peminjaman}" class="btn-primary" style="background: #0ea5e9; font-size: 11px; padding: 5px 10px;">Edit</a>
                            <button onclick="hapusPinjaman(${p.id_peminjaman})" class="btn-danger" style="font-size: 11px; padding: 5px 10px;">Hapus</button>
                           </div>`;

                    const row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${p.nama_lengkap}</td>
                            <td><strong>${p.judul}</strong></td>
                            <td>${p.tanggal_pinjam}</td>
                            <td>${p.tanggal_tenggat}</td>
                            <td>${statusBadge}</td>
                            <td style="color: ${p.denda > 0 ? 'red' : 'green'}; font-weight: bold;">Rp ${new Intl.NumberFormat('id-ID').format(p.denda)}</td>
                            <td>${actionBtn}</td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            }
        } catch (error) {
            console.error('Gagal memuat data:', error);
        }
    }

    async function kembalikanBuku(id) {
        showModal('Proses pengembalian buku?', async () => {
            try {
                const response = await fetch(`api/peminjaman.php?id=${id}`, { method: 'PUT' });
                const result = await response.json();
                alert(result.message + (result.data ? `\nDenda: Rp ${result.data.denda}` : ''));
                loadPeminjaman();
            } catch (error) { alert('Gagal memproses.'); }
        });
    }

    async function hapusPinjaman(id) {
        showModal('Hapus histori peminjaman ini?', async () => {
            try {
                const response = await fetch(`api/peminjaman.php?id=${id}`, { method: 'DELETE' });
                const result = await response.json();
                alert(result.message);
                loadPeminjaman();
            } catch (error) { alert('Gagal menghapus.'); }
        });
    }

    document.addEventListener('DOMContentLoaded', loadPeminjaman);
</script>

<?php include 'layouts/footer.php'; ?>