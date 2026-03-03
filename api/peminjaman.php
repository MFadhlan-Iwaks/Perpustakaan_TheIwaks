<?php
require_once '../config/koneksi.php';
require_once 'response.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($method) {
    case 'GET':
        $query_str = "SELECT p.*, b.judul, u.nama_lengkap FROM peminjaman p 
                      JOIN buku b ON p.id_buku = b.id_buku 
                      JOIN users u ON p.id_user = u.id_user";
        
        if (isset($_GET['id_user'])) {
            $id_user = (int)$_GET['id_user'];
            $query_str .= " WHERE p.id_user = $id_user";
        }
        
        $query = mysqli_query($koneksi, $query_str);
        $peminjaman_list = [];
        while ($row = mysqli_fetch_assoc($query)) {
            $peminjaman_list[] = $row;
        }
        sendSuccess("Data peminjaman berhasil diambil", $peminjaman_list);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        
        if ($action == 'pinjam') {
            if (!isset($data['id_user']) || !isset($data['id_buku'])) {
                sendError("ID user dan ID buku diperlukan");
            }
            $id_user = (int)$data['id_user'];
            $id_buku = (int)$data['id_buku'];

            $query_cek_limit = mysqli_query($koneksi, "SELECT COUNT(*) as total_pinjam FROM peminjaman WHERE id_user = '$id_user' AND status = 'dipinjam'");
            $data_limit = mysqli_fetch_assoc($query_cek_limit);

            if ($data_limit['total_pinjam'] >= 3) {
                sendError("Batas Pinjam Tercapai! Kamu masih memiliki 3 buku yang belum dikembalikan.");
            }

            $cek_stok = mysqli_query($koneksi, "SELECT stok, judul FROM buku WHERE id_buku = '$id_buku'");
            $data_buku = mysqli_fetch_assoc($cek_stok);

            if ($data_buku && $data_buku['stok'] > 0) {
                $tanggal_pinjam = date('Y-m-d');
                $tanggal_tenggat = date('Y-m-d', strtotime('+7 days'));

                $query_pinjam = "INSERT INTO peminjaman (id_user, id_buku, tanggal_pinjam, tanggal_tenggat, status) 
                                 VALUES ('$id_user', '$id_buku', '$tanggal_pinjam', '$tanggal_tenggat', 'dipinjam')";

                if (mysqli_query($koneksi, $query_pinjam)) {
                    mysqli_query($koneksi, "UPDATE buku SET stok = stok - 1 WHERE id_buku = '$id_buku'");
                    sendSuccess("Berhasil meminjam buku: " . $data_buku['judul'], ['id_peminjaman' => mysqli_insert_id($koneksi)]);
                } else {
                    sendError("Gagal memproses peminjaman.");
                }
            } else {
                sendError("Maaf, stok buku sedang kosong atau buku tidak ditemukan.");
            }

        } elseif ($action == 'kembali') {
            if (!isset($data['id_peminjaman'])) {
                sendError("ID peminjaman diperlukan");
            }
            $id_peminjaman = (int)$data['id_peminjaman'];

            $query_cek = mysqli_query($koneksi, "SELECT * FROM peminjaman WHERE id_peminjaman = '$id_peminjaman'");
            $data_pinjam = mysqli_fetch_assoc($query_cek);
            
            if (!$data_pinjam || $data_pinjam['status'] == 'dikembalikan') {
                sendError("Data peminjaman tidak ditemukan atau sudah dikembalikan.");
            }

            $id_buku = $data_pinjam['id_buku'];
            $tanggal_tenggat = $data_pinjam['tanggal_tenggat'];
            $tanggal_sekarang = date('Y-m-d');

            $denda = 0;
            $tarif_denda_per_hari = 20000;

            if (strtotime($tanggal_sekarang) > strtotime($tanggal_tenggat)) {
                $selisih_waktu = strtotime($tanggal_sekarang) - strtotime($tanggal_tenggat);
                $selisih_hari = floor($selisih_waktu / (60 * 60 * 24));
                $denda = $selisih_hari * $tarif_denda_per_hari;
            }

            $query_update_pinjam = "UPDATE peminjaman 
                                    SET status = 'dikembalikan', 
                                        tanggal_dikembalikan = '$tanggal_sekarang',
                                        denda = '$denda' 
                                    WHERE id_peminjaman = '$id_peminjaman'";

            if (mysqli_query($koneksi, $query_update_pinjam)) {
                mysqli_query($koneksi, "UPDATE buku SET stok = stok + 1 WHERE id_buku = '$id_buku'");
                sendSuccess("Buku berhasil dikembalikan", ['denda' => $denda]);
            } else {
                sendError("Gagal memproses pengembalian.");
            }
        } else {
            sendError("Aksi tidak valid");
        }
        break;

    case 'DELETE':
        $id_peminjaman = isset($_GET['id']) ? (int)$_GET['id'] : null;
        if (!$id_peminjaman) sendError("ID peminjaman diperlukan");

        $cek_pinjam = mysqli_query($koneksi, "SELECT status, id_buku FROM peminjaman WHERE id_peminjaman = $id_peminjaman");
        $data_pinjam = mysqli_fetch_assoc($cek_pinjam);

        if (!$data_pinjam) sendError("Data peminjaman tidak ditemukan", 404);

        if ($data_pinjam['status'] == 'dipinjam') {
            $id_buku = $data_pinjam['id_buku'];
            mysqli_query($koneksi, "UPDATE buku SET stok = stok + 1 WHERE id_buku = $id_buku");
        }

        if (mysqli_query($koneksi, "DELETE FROM peminjaman WHERE id_peminjaman = $id_peminjaman")) {
            sendSuccess("Peminjaman berhasil dihapus dan stok diperbarui (jika perlu)");
        } else {
            sendError("Gagal menghapus peminjaman", 500);
        }
        break;

    default:
        sendError("Metode tidak diizinkan", 405);
        break;
}
?>