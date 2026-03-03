<?php
session_start();
session_destroy();

// Jika diakses via API (ada parameter JSON atau dari Thunder Client)
if (isset($_GET['api']) || $_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET') {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'message' => 'Logout berhasil. Session telah dihapus.'
    ]);
} else {
    // Jika diakses via browser biasa
    header("Location: ../index.php");
}
exit();
?>