<?php
require_once 'middleware.php';
require_once '../config/koneksi.php';
require_once '../models/Peminjaman.php';
require_once '../controllers/PeminjamanController.php';

$method = $_SERVER['REQUEST_METHOD'];
$id_peminjaman = isset($_GET['id']) ? (int) $_GET['id'] : null;
$id_user_filter = isset($_GET['id_user']) ? (int) $_GET['id_user'] : null;

cekAksesAPI();

$controller = new PeminjamanController();

switch ($method) {
    case 'GET':
        $controller->getPeminjaman($id_peminjaman, $id_user_filter);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $controller->createPeminjaman($data);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $controller->updatePeminjaman($id_peminjaman, $data);
        break;

    case 'DELETE':
        $controller->deletePeminjaman($id_peminjaman);
        break;

    default:
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Metode tidak diizinkan']);
        break;
}
?>