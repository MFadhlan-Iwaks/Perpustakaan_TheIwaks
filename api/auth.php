<?php
session_start();
require_once '../config/koneksi.php';
require_once 'response.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($method == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if ($action == 'register') {
        if (!isset($data['nama_lengkap']) || !isset($data['username']) || !isset($data['password'])) {
            sendError("Data register tidak lengkap!");
        }

        $nama_lengkap = mysqli_real_escape_string($koneksi, $data['nama_lengkap']);
        $username = mysqli_real_escape_string($koneksi, $data['username']);
        $password = password_hash($data['password'], PASSWORD_BCRYPT);
        $role = 'user';

        $cek_user = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username'");
        if (mysqli_num_rows($cek_user) > 0) {
            sendError("Username sudah digunakan!");
        }

        $query_insert = "INSERT INTO users (username, password, nama_lengkap, role) 
                         VALUES ('$username', '$password', '$nama_lengkap', '$role')";

        if (mysqli_query($koneksi, $query_insert)) {
            sendSuccess("Akun berhasil dibuat!");
        } else {
            sendError("Terjadi kesalahan server.", 500);
        }

    } elseif ($action == 'login') {
        if (!isset($data['username']) || !isset($data['password'])) {
            sendError("Username dan password diperlukan!");
        }

        $username = mysqli_real_escape_string($koneksi, $data['username']);
        $password = $data['password'];

        $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username'");

        if (mysqli_num_rows($query) === 1) {
            $data_user = mysqli_fetch_assoc($query);

            if (password_verify($password, $data_user['password'])) {
                $_SESSION['id_user'] = $data_user['id_user'];
                $_SESSION['username'] = $data_user['username'];
                $_SESSION['role'] = $data_user['role'];

                $user_info = [
                    'id_user' => $data_user['id_user'],
                    'username' => $data_user['username'],
                    'role' => $data_user['role'],
                    'nama_lengkap' => $data_user['nama_lengkap']
                ];
                sendSuccess("Login berhasil", $user_info);
            } else {
                sendError("Password salah!");
            }
        } else {
            sendError("Username tidak ditemukan!");
        }
    } else {
        sendError("Aksi tidak valid!");
    }
} else {
    sendError("Metode tidak diizinkan!", 405);
}
?>