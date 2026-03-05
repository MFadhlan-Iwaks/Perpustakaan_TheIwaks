<?php

class BukuController {
    private $db;
    private $bukuModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        
        $this->bukuModel = new Buku($this->db);
    }

    private function sendJson($status, $message, $data = null, $code = 200) {
        http_response_code($code);
        header("Content-Type: application/json; charset=UTF-8");
        $response = ['status' => $status, 'message' => $message];
        if ($data !== null) { 
            $response['data'] = $data; 
        }
        echo json_encode($response);
        exit();
    }

    public function getBuku($id = null) {
        if ($id) {
            $buku = $this->bukuModel->getById($id);
            if ($buku) {
                $this->sendJson('success', 'Data ditemukan', $buku);
            } else {
                $this->sendJson('error', 'Buku tidak ditemukan', null, 404);
            }
        } else {
            $buku_list = $this->bukuModel->getAll();
            $this->sendJson('success', 'List buku berhasil diambil', $buku_list);
        }
    }

    public function createBuku($postData, $filesData) {
        $nama_file_gambar = NULL;
        
        if (isset($filesData['gambar']) && $filesData['gambar']['error'] === 0) {
            $nama_file_gambar = uniqid() . '.' . strtolower(pathinfo($filesData['gambar']['name'], PATHINFO_EXTENSION));
            move_uploaded_file($filesData['gambar']['tmp_name'], '../assets/images/buku/' . $nama_file_gambar);
        }
        
        $data = [
            'isbn' => $postData['isbn'] ?? '',
            'judul' => $postData['judul'] ?? '',
            'kategori' => $postData['kategori'] ?? '',
            'penulis' => $postData['penulis'] ?? '',
            'penerbit' => $postData['penerbit'] ?? '',
            'tahun_terbit' => $postData['tahun_terbit'] ?? '',
            'stok' => (int)($postData['stok'] ?? 0),
            'lokasi_rak' => $postData['lokasi_rak'] ?? '',
            'gambar' => $nama_file_gambar
        ];

        $insertId = $this->bukuModel->create($data);
        if ($insertId) {
            $this->sendJson('success', 'Buku ditambahkan', ['id' => $insertId], 201);
        } else {
            $this->sendJson('error', 'Gagal menambahkan buku', null, 500);
        }
    }

    public function updateBuku($id, $putData) {
        if (!$id) {
            $this->sendJson('error', 'ID diperlukan', null, 400);
        }
        
        if ($this->bukuModel->update($id, $putData)) {
            $this->sendJson('success', 'Data buku diperbarui');
        } else {
            $this->sendJson('error', 'Gagal memperbarui buku', null, 500);
        }
    }

    public function deleteBuku($id) {
        if (!$id) {
            $this->sendJson('error', 'ID diperlukan', null, 400);
        }
        
        if ($this->bukuModel->delete($id)) {
            $this->sendJson('success', 'Buku dihapus');
        } else {
            $this->sendJson('error', 'Gagal menghapus', null, 500);
        }
    }
}
?>