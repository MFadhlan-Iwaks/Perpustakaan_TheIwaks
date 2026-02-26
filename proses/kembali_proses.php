<?php
session_start();
require_once '../config/koneksi.php';

if ($_SESSION['role'] != 'petugas') {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['id_peminjaman']) && isset($_GET['id_buku'])) {
    $id_peminjaman = $_GET['id_peminjaman'];
    $id_buku = $_GET['id_buku'];

    $tanggal_sekarang = date('Y-m-d');

    $query_cek = mysqli_query($koneksi, "SELECT tanggal_tenggat FROM peminjaman WHERE id_peminjaman = '$id_peminjaman'");
    $data_pinjam = mysqli_fetch_assoc($query_cek);
    $tanggal_tenggat = $data_pinjam['tanggal_tenggat'];

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
    }
}

header("Location: ../peminjaman_data.php");
exit();
?>