<?php

class UserController {
    private $userModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->userModel = new User($db);
    }

    private function sendJson($status, $message, $data = null, $code = 200) {
        http_response_code($code);
        header("Content-Type: application/json; charset=UTF-8");
        $response = ['status' => $status, 'message' => $message];
        if ($data !== null) $response['data'] = $data;
        echo json_encode($response);
        exit();
    }

    public function getUser($id = null) {
        if ($id) {
            $user = $this->userModel->getById($id);
            if ($user) $this->sendJson('success', 'User ditemukan', $user);
            else $this->sendJson('error', 'User tidak ditemukan', null, 404);
        } else {
            $users = $this->userModel->getAll();
            $this->sendJson('success', 'List user diambil', $users);
        }
    }

    public function createUser($data) {
        if (empty($data['username']) || empty($data['nama_lengkap']) || empty($data['role']) || empty($data['password'])) {
            $this->sendJson('error', 'Data tidak lengkap', null, 400);
        }

        if ($this->userModel->getByUsername($data['username'])) {
            $this->sendJson('error', 'Username sudah terdaftar.', null, 400);
        }

        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        
        $insertId = $this->userModel->create($data);
        if ($insertId) $this->sendJson('success', 'User ditambahkan', ['id' => $insertId], 201);
        else $this->sendJson('error', 'Gagal menambahkan user', null, 500);
    }

    public function updateUser($id, $data) {
        if (!$id) $this->sendJson('error', 'ID diperlukan', null, 400);
        
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        if ($this->userModel->update($id, $data)) {
            $this->sendJson('success', 'User diperbarui');
        } else {
            $this->sendJson('error', 'Gagal memperbarui user', null, 500);
        }
    }

    public function deleteUser($id) {
        if (!$id) $this->sendJson('error', 'ID diperlukan', null, 400);
        
        if ($this->userModel->delete($id)) {
            $this->sendJson('success', 'User dihapus');
        } else {
            $this->sendJson('error', 'Gagal menghapus', null, 500);
        }
    }
}
?>