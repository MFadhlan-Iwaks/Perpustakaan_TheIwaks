<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/koneksi.php';
require_once 'response.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($method !== 'POST') {
    sendError("Metode tidak diizinkan!", 405);
}

$data = json_decode(file_get_contents("php://input"), true);

if ($action === 'register') {
    if (empty($data['nama_lengkap']) || empty($data['username']) || empty($data['password'])) {
        sendError("Data registrasi tidak lengkap!");
    }

    $nama_lengkap = $data['nama_lengkap'];
    $username = $data['username'];
    $password = password_hash($data['password'], PASSWORD_BCRYPT);
    $role = 'user';

    $stmt_cek = mysqli_prepare($koneksi, "SELECT id_user FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt_cek, "s", $username);
    mysqli_stmt_execute($stmt_cek);
    mysqli_stmt_store_result($stmt_cek);

    if (mysqli_stmt_num_rows($stmt_cek) > 0) {
        sendError("Username sudah digunakan!");
    }
    mysqli_stmt_close($stmt_cek);

    $stmt_insert = mysqli_prepare($koneksi, "INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt_insert, "ssss", $username, $password, $nama_lengkap, $role);

    if (mysqli_stmt_execute($stmt_insert)) {
        sendSuccess("Akun berhasil dibuat!");
    } else {
        sendError("Terjadi kesalahan server saat menyimpan data.", 500);
    }
    mysqli_stmt_close($stmt_insert);

} elseif ($action === 'login') {
    if (empty($data['username']) || empty($data['password'])) {
        sendError("Username dan password diperlukan!");
    }

    $username = $data['username'];
    $password = $data['password'];

    $stmt = mysqli_prepare($koneksi, "SELECT * FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($data_user = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $data_user['password'])) {
            $_SESSION['id_user'] = $data_user['id_user'];
            $_SESSION['username'] = $data_user['username'];
            $_SESSION['role'] = $data_user['role'];
            $_SESSION['nama_lengkap'] = $data_user['nama_lengkap'];

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
    mysqli_stmt_close($stmt);

} else {
    sendError("Aksi tidak valid!");
}
?>