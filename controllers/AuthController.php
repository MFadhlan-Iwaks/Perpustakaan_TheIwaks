<?php

class AuthController {
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

    public function register($data) {
        if (empty($data['nama_lengkap']) || empty($data['username']) || empty($data['password'])) {
            $this->sendJson('error', 'Data registrasi tidak lengkap!', null, 400);
        }

        $cek = $this->userModel->getByUsername($data['username']);
        if ($cek) {
            $this->sendJson('error', 'Username sudah digunakan!', null, 400);
        }

        $userData = [
            'username' => $data['username'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'nama_lengkap' => $data['nama_lengkap'],
            'role' => 'user' // Default role
        ];

        if ($this->userModel->create($userData)) {
            $this->sendJson('success', 'Akun berhasil dibuat!', null, 201);
        } else {
            $this->sendJson('error', 'Terjadi kesalahan server saat menyimpan data.', null, 500);
        }
    }

    public function login($data) {
        if (empty($data['username']) || empty($data['password'])) {
            $this->sendJson('error', 'Username dan password diperlukan!', null, 400);
        }

        $user = $this->userModel->getByUsername($data['username']);

        if ($user && password_verify($data['password'], $user['password'])) {
            if (session_status() === PHP_SESSION_NONE) { session_start(); }
            
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];

            $userInfo = [
                'id_user' => $user['id_user'],
                'username' => $user['username'],
                'role' => $user['role'],
                'nama_lengkap' => $user['nama_lengkap']
            ];

            $this->sendJson('success', 'Login berhasil', $userInfo);
        } else {
            $this->sendJson('error', 'Username atau Password salah!', null, 401);
        }
    }
}
?>