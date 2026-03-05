<?php

class PeminjamanController {
    private $db;
    private $pinjamModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->pinjamModel = new Peminjaman($this->db);
    }

    private function sendJson($status, $message, $data = null, $code = 200) {
        http_response_code($code);
        header("Content-Type: application/json; charset=UTF-8");
        $response = ['status' => $status, 'message' => $message];
        if ($data !== null) { $response['data'] = $data; }
        echo json_encode($response);
        exit();
    }

    public function getPeminjaman($id = null, $id_user = null) {
        if ($id) {
            $data = $this->pinjamModel->getById($id);
            if ($data) $this->sendJson('success', 'Data ditemukan', $data);
            else $this->sendJson('error', 'Data tidak ditemukan', null, 404);
        } else if ($id_user) {
            $list = $this->pinjamModel->getByUserId($id_user);
            $this->sendJson('success', 'List peminjaman user diambil', $list);
        } else {
            $list = $this->pinjamModel->getAll();
            $this->sendJson('success', 'List peminjaman diambil', $list);
        }
    }

    public function createPeminjaman($postData) {
        $data = [
            'id_user' => (int) $postData['id_user'],
            'id_buku' => (int) $postData['id_buku'],
            'tgl_pinjam' => date('Y-m-d'),
            'tgl_tenggat' => date('Y-m-d', strtotime('+7 days'))
        ];

        $insertId = $this->pinjamModel->create($data);
        if ($insertId) {
            $this->sendJson('success', 'Peminjaman berhasil', ['id' => $insertId]);
        } else {
            $this->sendJson('error', 'Gagal memproses pinjam', null, 500);
        }
    }

    public function updatePeminjaman($id, $putData) {
        if (!$id) $this->sendJson('error', 'ID diperlukan', null, 400);

        if ($putData) {
            cekAksesAPI('petugas'); 
            if ($this->pinjamModel->updateManual($id, $putData['status'], (int)$putData['denda'])) {
                $this->sendJson('success', 'Data diperbarui');
            } else {
                $this->sendJson('error', 'Gagal update manual', null, 500);
            }
        } 
        else {
            $proses = $this->pinjamModel->prosesKembali($id);
            if ($proses['status']) {
                $this->sendJson('success', 'Buku dikembalikan', ['denda' => $proses['denda']]);
            } else {
                $this->sendJson('error', $proses['message'], null, 500);
            }
        }
    }

    public function deletePeminjaman($id) {
        cekAksesAPI('petugas');
        if ($this->pinjamModel->delete($id)) {
            $this->sendJson('success', 'Dihapus');
        } else {
            $this->sendJson('error', 'Gagal hapus', null, 500);
        }
    }
}
?>