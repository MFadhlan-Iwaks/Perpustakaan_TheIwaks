<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header("Location: ../index.php");
    exit();
}

if (isset($_POST['tambah_buku'])) {
    $judul        = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $isbn         = mysqli_real_escape_string($koneksi, $_POST['isbn']);
    $kategori     = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $lokasi_rak   = mysqli_real_escape_string($koneksi, $_POST['lokasi_rak']);
    $penulis      = mysqli_real_escape_string($koneksi, $_POST['penulis']);
    $penerbit     = mysqli_real_escape_string($koneksi, $_POST['penerbit']);
    $tahun_terbit = $_POST['tahun_terbit'];
    $stok         = $_POST['stok'];
    
    $nama_file_gambar = NULL; 
    
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $file_tmp = $_FILES['gambar']['tmp_name'];
        $nama_asli = $_FILES['gambar']['name'];
        $ekstensi = strtolower(pathinfo($nama_asli, PATHINFO_EXTENSION));
        
        $nama_file_gambar = uniqid() . '.' . $ekstensi;
        $direktori_tujuan = '../assets/images/buku/' . $nama_file_gambar;
        
        move_uploaded_file($file_tmp, $direktori_tujuan);
    }

    $query = "INSERT INTO buku (isbn, judul, kategori, penulis, penerbit, tahun_terbit, stok, lokasi_rak, gambar) 
              VALUES ('$isbn', '$judul', '$kategori', '$penulis', '$penerbit', '$tahun_terbit', '$stok', '$lokasi_rak', '$nama_file_gambar')";
              
    mysqli_query($koneksi, $query);
    header("Location: ../petugas_dashboard.php");
    exit();
}

if (isset($_GET['hapus'])) {
    $id_buku = $_GET['hapus'];
    
    $cek_gambar = mysqli_query($koneksi, "SELECT gambar FROM buku WHERE id_buku = '$id_buku'");
    if (mysqli_num_rows($cek_gambar) > 0) {
        $data_gambar = mysqli_fetch_assoc($cek_gambar);
        if ($data_gambar['gambar'] != NULL) {
            $path_gambar = '../assets/images/buku/' . $data_gambar['gambar'];
            if (file_exists($path_gambar)) {
                unlink($path_gambar); 
            }
        }
    }

    mysqli_query($koneksi, "DELETE FROM buku WHERE id_buku = '$id_buku'");
    
    header("Location: ../petugas_dashboard.php");
    exit();
}

if (isset($_POST['edit_buku'])) {
    $id_buku      = $_POST['id_buku'];
    $judul        = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $isbn         = mysqli_real_escape_string($koneksi, $_POST['isbn']);
    $kategori     = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $lokasi_rak   = mysqli_real_escape_string($koneksi, $_POST['lokasi_rak']);
    $penulis      = mysqli_real_escape_string($koneksi, $_POST['penulis']);
    $penerbit     = mysqli_real_escape_string($koneksi, $_POST['penerbit']);
    $tahun_terbit = $_POST['tahun_terbit'];
    $stok         = $_POST['stok'];
    $gambar_lama  = $_POST['gambar_lama'];
    
    $nama_file_gambar = $gambar_lama; 

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $file_tmp = $_FILES['gambar']['tmp_name'];
        $nama_asli = $_FILES['gambar']['name'];
        $ekstensi = strtolower(pathinfo($nama_asli, PATHINFO_EXTENSION));
        
        $nama_file_gambar = uniqid() . '.' . $ekstensi;
        $direktori_tujuan = '../assets/images/buku/' . $nama_file_gambar;
        
        if(move_uploaded_file($file_tmp, $direktori_tujuan)) {
            if ($gambar_lama != NULL && file_exists('../assets/images/buku/' . $gambar_lama)) {
                unlink('../assets/images/buku/' . $gambar_lama);
            }
        }
    }

    $query = "UPDATE buku SET 
                isbn = '$isbn', 
                judul = '$judul', 
                kategori = '$kategori', 
                penulis = '$penulis', 
                penerbit = '$penerbit', 
                tahun_terbit = '$tahun_terbit', 
                stok = '$stok', 
                lokasi_rak = '$lokasi_rak', 
                gambar = '$nama_file_gambar' 
              WHERE id_buku = '$id_buku'";
              
    mysqli_query($koneksi, $query);
    header("Location: ../petugas_dashboard.php");
    exit();
}
?>