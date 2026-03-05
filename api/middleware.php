<?php
session_start();

function cekAksesAPI($role_diperlukan = null)
{
    if (!isset($_SESSION['id_user'])) {
        http_response_code(401);
        echo json_encode([
            'status' => 'error',
            'message' => 'Akses ditolak! Silakan login terlebih dahulu.'
        ]);
        exit();
    }

    if ($role_diperlukan && $_SESSION['role'] !== $role_diperlukan) {
        http_response_code(403);
        echo json_encode([
            'status' => 'error',
            'message' => 'Akses ditolak! Anda tidak memiliki izin untuk aksi ini.'
        ]);
        exit();
    }
}
?>