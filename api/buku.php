<?php
require_once '../config/koneksi.php';
require_once 'response.php';

$method = $_SERVER['REQUEST_METHOD'];
$id_buku = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($method) {
    case 'GET':
        if ($id_buku) {
            $query = mysqli_query($koneksi, "SELECT * FROM buku WHERE id_buku = $id_buku");
            $buku = mysqli_fetch_assoc($query);
            if ($buku) {
                sendSuccess("Data buku ditemukan", $buku);
            } else {
                sendError("Buku tidak ditemukan", 404);
            }
        } else {
            $query = mysqli_query($koneksi, "SELECT * FROM buku ORDER BY id_buku DESC");
            $buku_list = [];
            while ($row = mysqli_fetch_assoc($query)) {
                $buku_list[] = $row;
            }
            sendSuccess("List buku berhasil diambil", $buku_list);
        }
        break;

    case 'POST':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id_buku']) ? (int)$_POST['id_buku'] : null);
        
        $judul        = isset($_POST['judul']) ? mysqli_real_escape_string($koneksi, $_POST['judul']) : '';
        $isbn         = isset($_POST['isbn']) ? mysqli_real_escape_string($koneksi, $_POST['isbn']) : '';
        $kategori     = isset($_POST['kategori']) ? mysqli_real_escape_string($koneksi, $_POST['kategori']) : '';
        $lokasi_rak   = isset($_POST['lokasi_rak']) ? mysqli_real_escape_string($koneksi, $_POST['lokasi_rak']) : '';
        $penulis      = isset($_POST['penulis']) ? mysqli_real_escape_string($koneksi, $_POST['penulis']) : '';
        $penerbit     = isset($_POST['penerbit']) ? mysqli_real_escape_string($koneksi, $_POST['penerbit']) : '';
        $tahun_terbit = isset($_POST['tahun_terbit']) ? $_POST['tahun_terbit'] : '';
        $stok         = isset($_POST['stok']) ? (int)$_POST['stok'] : 0;
        
        if ($id) {
            $cek_lama = mysqli_query($koneksi, "SELECT gambar FROM buku WHERE id_buku = $id");
            $data_lama = mysqli_fetch_assoc($cek_lama);
            if (!$data_lama) sendError("Buku tidak ditemukan", 404);
            
            $nama_file_gambar = $data_lama['gambar'];

            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
                if ($nama_file_gambar && file_exists('../assets/images/buku/' . $nama_file_gambar)) {
                    unlink('../assets/images/buku/' . $nama_file_gambar);
                }
                
                $file_tmp = $_FILES['gambar']['tmp_name'];
                $nama_asli = $_FILES['gambar']['name'];
                $ekstensi = strtolower(pathinfo($nama_asli, PATHINFO_EXTENSION));
                $nama_file_gambar = uniqid() . '.' . $ekstensi;
                $direktori_tujuan = '../assets/images/buku/' . $nama_file_gambar;
                move_uploaded_file($file_tmp, $direktori_tujuan);
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
                      WHERE id_buku = $id";
            
            if (mysqli_query($koneksi, $query)) {
                sendSuccess("Buku berhasil diperbarui");
            } else {
                sendError("Gagal memperbarui buku: " . mysqli_error($koneksi), 500);
            }
        } else {
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
                      
            if (mysqli_query($koneksi, $query)) {
                sendSuccess("Buku berhasil ditambahkan", ['id_buku' => mysqli_insert_id($koneksi)], 201);
            } else {
                sendError("Gagal menambahkan buku: " . mysqli_error($koneksi), 500);
            }
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!$id_buku) sendError("ID buku diperlukan untuk update");

        $judul        = mysqli_real_escape_string($koneksi, $data['judul']);
        $isbn         = mysqli_real_escape_string($koneksi, $data['isbn']);
        $kategori     = mysqli_real_escape_string($koneksi, $data['kategori']);
        $lokasi_rak   = mysqli_real_escape_string($koneksi, $data['lokasi_rak']);
        $penulis      = mysqli_real_escape_string($koneksi, $data['penulis']);
        $penerbit     = mysqli_real_escape_string($koneksi, $data['penerbit']);
        $tahun_terbit = $data['tahun_terbit'];
        $stok         = (int)$data['stok'];

        $query = "UPDATE buku SET 
                    isbn = '$isbn', 
                    judul = '$judul', 
                    kategori = '$kategori', 
                    penulis = '$penulis', 
                    penerbit = '$penerbit', 
                    tahun_terbit = '$tahun_terbit', 
                    stok = '$stok', 
                    lokasi_rak = '$lokasi_rak'
                  WHERE id_buku = $id_buku";
                  
        if (mysqli_query($koneksi, $query)) {
            sendSuccess("Buku berhasil diperbarui");
        } else {
            sendError("Gagal memperbarui buku: " . mysqli_error($koneksi), 500);
        }
        break;

    case 'DELETE':
        if (!$id_buku) sendError("ID buku diperlukan untuk hapus");
        
        $cek_gambar = mysqli_query($koneksi, "SELECT gambar FROM buku WHERE id_buku = $id_buku");
        if ($data_gambar = mysqli_fetch_assoc($cek_gambar)) {
            if ($data_gambar['gambar'] != NULL) {
                $path_gambar = '../assets/images/buku/' . $data_gambar['gambar'];
                if (file_exists($path_gambar)) unlink($path_gambar);
            }
        }

        if (mysqli_query($koneksi, "DELETE FROM buku WHERE id_buku = $id_buku")) {
            sendSuccess("Buku berhasil dihapus");
        } else {
            sendError("Gagal menghapus buku: " . mysqli_error($koneksi), 500);
        }
        break;

    default:
        sendError("Metode tidak diizinkan", 405);
        break;
}
?>