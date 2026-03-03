<?php
session_start();
require_once 'response.php';

function cekAksesAPI($role_diperlukan = null) {

    if (!isset($_SESSION['id_user'])) {
        sendError("Akses ditolak! Silakan login terlebih dahulu.", 401);
        exit();
    }

    if ($role_diperlukan && $_SESSION['role'] !== $role_diperlukan) {
        sendError("Akses ditolak! Anda tidak memiliki izin untuk aksi ini.", 403);
        exit();
    }
}
?>