<?php
require_once 'middleware.php';
require_once '../config/koneksi.php';
require_once 'response.php';

$method = $_SERVER['REQUEST_METHOD'];
$id_buku = isset($_GET['id']) ? (int)$_GET['id'] : null;

// KEAMANAN: Semua aksi API Buku butuh login
cekAksesAPI(); 

// KEAMANAN TAMBAHAN: POST, PUT, DELETE hanya untuk Petugas
if (in_array($method, ['POST', 'PUT', 'DELETE'])) {
    cekAksesAPI('petugas');
}

switch ($method) {
    case 'GET':
        if ($id_buku) {
            $query = mysqli_query($koneksi, "SELECT * FROM buku WHERE id_buku = $id_buku");
            $buku = mysqli_fetch_assoc($query);
            if ($buku) sendSuccess("Data ditemukan", $buku);
            else sendError("Buku tidak ditemukan", 404);
        } else {
            $query = mysqli_query($koneksi, "SELECT * FROM buku ORDER BY id_buku DESC");
            $buku_list = [];
            while ($row = mysqli_fetch_assoc($query)) { $buku_list[] = $row; }
            sendSuccess("List buku berhasil diambil", $buku_list);
        }
        break;

    case 'POST': // CREATE
        $judul        = isset($_POST['judul']) ? mysqli_real_escape_string($koneksi, $_POST['judul']) : '';
        $isbn         = isset($_POST['isbn']) ? mysqli_real_escape_string($koneksi, $_POST['isbn']) : '';
        $kategori     = isset($_POST['kategori']) ? mysqli_real_escape_string($koneksi, $_POST['kategori']) : '';
        $lokasi_rak   = isset($_POST['lokasi_rak']) ? mysqli_real_escape_string($koneksi, $_POST['lokasi_rak']) : '';
        $penulis      = isset($_POST['penulis']) ? mysqli_real_escape_string($koneksi, $_POST['penulis']) : '';
        $penerbit     = isset($_POST['penerbit']) ? mysqli_real_escape_string($koneksi, $_POST['penerbit']) : '';
        $tahun_terbit = isset($_POST['tahun_terbit']) ? $_POST['tahun_terbit'] : '';
        $stok         = isset($_POST['stok']) ? (int)$_POST['stok'] : 0;
        
        $nama_file_gambar = NULL; 
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
            $nama_file_gambar = uniqid() . '.' . strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            move_uploaded_file($_FILES['gambar']['tmp_name'], '../assets/images/buku/' . $nama_file_gambar);
        }

        $query = "INSERT INTO buku (isbn, judul, kategori, penulis, penerbit, tahun_terbit, stok, lokasi_rak, gambar) 
                  VALUES ('$isbn', '$judul', '$kategori', '$penulis', '$penerbit', '$tahun_terbit', '$stok', '$lokasi_rak', '$nama_file_gambar')";
        if (mysqli_query($koneksi, $query)) sendSuccess("Buku ditambahkan", ['id' => mysqli_insert_id($koneksi)], 201);
        else sendError("Gagal menambahkan buku", 500);
        break;

    case 'PUT': // UPDATE (Data Teks)
        if (!$id_buku) sendError("ID diperlukan");
        $data = json_decode(file_get_contents("php://input"), true);
        $judul = mysqli_real_escape_string($koneksi, $data['judul']);
        $isbn = mysqli_real_escape_string($koneksi, $data['isbn']);
        $kategori = mysqli_real_escape_string($koneksi, $data['kategori']);
        $lokasi_rak = mysqli_real_escape_string($koneksi, $data['lokasi_rak']);
        $stok = (int)$data['stok'];

        $query = "UPDATE buku SET isbn='$isbn', judul='$judul', kategori='$kategori', stok='$stok', lokasi_rak='$lokasi_rak' WHERE id_buku=$id_buku";
        if (mysqli_query($koneksi, $query)) sendSuccess("Data buku diperbarui");
        else sendError("Gagal memperbarui buku", 500);
        break;

    case 'DELETE':
        if (!$id_buku) sendError("ID diperlukan");
        if (mysqli_query($koneksi, "DELETE FROM buku WHERE id_buku=$id_buku")) sendSuccess("Buku dihapus");
        else sendError("Gagal menghapus", 500);
        break;

    default:
        sendError("Metode tidak diizinkan", 405);
        break;
}
?>