<?php
require_once 'middleware.php';
require_once '../config/koneksi.php';
require_once 'response.php';

$method = $_SERVER['REQUEST_METHOD'];
$id_peminjaman = isset($_GET['id']) ? (int)$_GET['id'] : null;

// KEAMANAN: Semua aksi API Peminjaman wajib Login
cekAksesAPI(); 

switch ($method) {
    case 'GET':
        if ($id_peminjaman) {
            $q = mysqli_query($koneksi, "SELECT * FROM peminjaman WHERE id_peminjaman = $id_peminjaman");
            $d = mysqli_fetch_assoc($q);
            if ($d) sendSuccess("Data ditemukan", $d);
            else sendError("Data tidak ditemukan", 404);
        } else {
            $query_str = "SELECT p.*, b.judul, u.nama_lengkap FROM peminjaman p JOIN buku b ON p.id_buku = b.id_buku JOIN users u ON p.id_user = u.id_user";
            if (isset($_GET['id_user'])) {
                $query_str .= " WHERE p.id_user = " . (int)$_GET['id_user'];
            }
            $query = mysqli_query($koneksi, $query_str);
            $list = [];
            while ($row = mysqli_fetch_assoc($query)) { $list[] = $row; }
            sendSuccess("List peminjaman diambil", $list);
        }
        break;

    case 'POST': // CREATE
        $data = json_decode(file_get_contents("php://input"), true);
        $id_user = (int)$data['id_user'];
        $id_buku = (int)$data['id_buku'];
        $tgl_pinjam = date('Y-m-d');
        $tgl_tenggat = date('Y-m-d', strtotime('+7 days'));

        $query = "INSERT INTO peminjaman (id_user, id_buku, tanggal_pinjam, tanggal_tenggat, status) VALUES ($id_user, $id_buku, '$tgl_pinjam', '$tgl_tenggat', 'dipinjam')";
        if (mysqli_query($koneksi, $query)) {
            mysqli_query($koneksi, "UPDATE buku SET stok = stok - 1 WHERE id_buku = $id_buku");
            sendSuccess("Peminjaman berhasil", ['id' => mysqli_insert_id($koneksi)]);
        } else sendError("Gagal memproses pinjam", 500);
        break;

    case 'PUT': // UPDATE / KEMBALI
        if (!$id_peminjaman) sendError("ID diperlukan");
        $data = json_decode(file_get_contents("php://input"), true);

        if ($data) {
            // Update manual (hanya petugas)
            cekAksesAPI('petugas');
            $status = mysqli_real_escape_string($koneksi, $data['status']);
            $query = "UPDATE peminjaman SET status='$status', denda=" . (int)$data['denda'] . " WHERE id_peminjaman=$id_peminjaman";
            if (mysqli_query($koneksi, $query)) sendSuccess("Data diperbarui");
            else sendError("Gagal update");
        } else {
            // Kembali otomatis
            $q = mysqli_query($koneksi, "SELECT * FROM peminjaman WHERE id_peminjaman=$id_peminjaman");
            $p = mysqli_fetch_assoc($q);
            if (!$p || $p['status'] == 'dikembalikan') sendError("Data tidak valid");

            $selisih = floor((time() - strtotime($p['tanggal_tenggat'])) / 86400);
            $denda = $selisih > 0 ? $selisih * 20000 : 0;

            if (mysqli_query($koneksi, "UPDATE peminjaman SET status='dikembalikan', tanggal_dikembalikan='".date('Y-m-d')."', denda=$denda WHERE id_peminjaman=$id_peminjaman")) {
                mysqli_query($koneksi, "UPDATE buku SET stok = stok + 1 WHERE id_buku = {$p['id_buku']}");
                sendSuccess("Buku dikembalikan", ['denda' => $denda]);
            } else sendError("Gagal proses kembali");
        }
        break;

    case 'DELETE':
        cekAksesAPI('petugas');
        if (mysqli_query($koneksi, "DELETE FROM peminjaman WHERE id_peminjaman=$id_peminjaman")) sendSuccess("Dihapus");
        else sendError("Gagal hapus");
        break;

    default:
        sendError("Metode tidak diizinkan", 405);
        break;
}
?>