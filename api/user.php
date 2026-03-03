<?php
require_once '../config/koneksi.php';
require_once 'response.php';

$method = $_SERVER['REQUEST_METHOD'];
$id_user = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($method) {
    case 'GET':
        if ($id_user) {
            $query = mysqli_query($koneksi, "SELECT id_user, username, nama_lengkap, role FROM users WHERE id_user = $id_user");
            $user = mysqli_fetch_assoc($query);
            if ($user) {
                sendSuccess("User ditemukan", $user);
            } else {
                sendError("User tidak ditemukan", 404);
            }
        } else {
            $query = mysqli_query($koneksi, "SELECT id_user, username, nama_lengkap, role FROM users ORDER BY id_user DESC");
            $users = [];
            while ($row = mysqli_fetch_assoc($query)) {
                $users[] = $row;
            }
            sendSuccess("List user berhasil diambil", $users);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($data['id_user']) ? (int)$data['id_user'] : null);

        if (!isset($data['username']) || !isset($data['nama_lengkap']) || !isset($data['role'])) {
            sendError("Data user tidak lengkap");
        }

        $username = mysqli_real_escape_string($koneksi, $data['username']);
        $nama_lengkap = mysqli_real_escape_string($koneksi, $data['nama_lengkap']);
        $role = mysqli_real_escape_string($koneksi, $data['role']);

        if ($id) {
            // Update Logic
            $cek_username = mysqli_query($koneksi, "SELECT id_user FROM users WHERE username = '$username' AND id_user != $id");
            if (mysqli_num_rows($cek_username) > 0) {
                sendError("Username '$username' sudah digunakan oleh user lain.");
            }

            $query = "UPDATE users SET username = '$username', nama_lengkap = '$nama_lengkap', role = '$role'";
            
            // Update password only if provided
            if (!empty($data['password'])) {
                $password = password_hash($data['password'], PASSWORD_BCRYPT);
                $query .= ", password = '$password'";
            }
            
            $query .= " WHERE id_user = $id";

            if (mysqli_query($koneksi, $query)) {
                sendSuccess("User berhasil diperbarui");
            } else {
                sendError("Gagal memperbarui user", 500);
            }
        } else {
            // Insert Logic
            if (empty($data['password'])) sendError("Password diperlukan untuk user baru");
            $password = password_hash($data['password'], PASSWORD_BCRYPT);

            $cek_username = mysqli_query($koneksi, "SELECT username FROM users WHERE username = '$username'");
            if (mysqli_num_rows($cek_username) > 0) {
                sendError("Username '$username' sudah terdaftar.");
            }

            $query = "INSERT INTO users (username, password, nama_lengkap, role) VALUES ('$username', '$password', '$nama_lengkap', '$role')";
            if (mysqli_query($koneksi, $query)) {
                sendSuccess("User berhasil ditambahkan", ['id_user' => mysqli_insert_id($koneksi)], 201);
            } else {
                sendError("Gagal menambahkan user", 500);
            }
        }
        break;

    case 'DELETE':
        if (!$id_user) sendError("ID user diperlukan");
        
        if (mysqli_query($koneksi, "DELETE FROM users WHERE id_user = $id_user")) {
            sendSuccess("User berhasil dihapus");
        } else {
            sendError("Gagal menghapus user", 500);
        }
        break;

    default:
        sendError("Metode tidak diizinkan", 405);
        break;
}
?>