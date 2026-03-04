<?php
session_start();
session_destroy();

if (isset($_GET['api']) || $_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET') {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'message' => 'Logout berhasil. Session telah dihapus.'
    ]);
} else {
    header("Location: ../index.php");
}
exit();
?>