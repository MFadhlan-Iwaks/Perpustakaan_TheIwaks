<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['id'])) {
    $id_buku = $_GET['id'];
    $id_user = $_SESSION['id_user'];

    $query_cek_limit = mysqli_query($koneksi, "SELECT COUNT(*) as total_pinjam FROM peminjaman WHERE id_user = '$id_user' AND status = 'dipinjam'");
    $data_limit = mysqli_fetch_assoc($query_cek_limit);

    if ($data_limit['total_pinjam'] >= 3) {
        $_SESSION['pesan'] = "Batas Pinjam Tercapai! Kamu masih memiliki 3 buku yang belum dikembalikan.";
        $_SESSION['tipe_pesan'] = "error"; // Penanda untuk warna merah di notifikasi
        header("Location: ../user_dashboard.php");
        exit();
    }

    $cek_stok = mysqli_query($koneksi, "SELECT stok, judul FROM buku WHERE id_buku = '$id_buku'");
    $data_buku = mysqli_fetch_assoc($cek_stok);

    if ($data_buku['stok'] > 0) {
        $tanggal_pinjam = date('Y-m-d');
        $tanggal_tenggat = date('Y-m-d', strtotime('+7 days'));

        $query_pinjam = "INSERT INTO peminjaman (id_user, id_buku, tanggal_pinjam, tanggal_tenggat, status) 
                         VALUES ('$id_user', '$id_buku', '$tanggal_pinjam', '$tanggal_tenggat', 'dipinjam')";

        if (mysqli_query($koneksi, $query_pinjam)) {
            mysqli_query($koneksi, "UPDATE buku SET stok = stok - 1 WHERE id_buku = '$id_buku'");

            $_SESSION['pesan'] = "Berhasil meminjam buku: " . $data_buku['judul'] . ". Harap kembalikan sebelum tenggat waktu!";
            $_SESSION['tipe_pesan'] = "success";
        } else {
            $_SESSION['pesan'] = "Gagal memproses peminjaman.";
            $_SESSION['tipe_pesan'] = "error";
        }
    } else {
        $_SESSION['pesan'] = "Maaf, stok buku sedang kosong.";
        $_SESSION['tipe_pesan'] = "error";
    }

    header("Location: ../user_dashboard.php");
    exit();
} else {
    header("Location: ../user_dashboard.php");
    exit();
}
?>