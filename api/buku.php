<?php
require_once 'middleware.php';
require_once '../config/koneksi.php';
require_once '../models/Buku.php';
require_once '../controllers/BukuController.php';

$method = $_SERVER['REQUEST_METHOD'];
$id_buku = isset($_GET['id']) ? (int) $_GET['id'] : null;

cekAksesAPI();
if (in_array($method, ['POST', 'PUT', 'DELETE'])) {
    cekAksesAPI('petugas');
}

$controller = new BukuController();

switch ($method) {
    case 'GET':
        $controller->getBuku($id_buku);
        break;
        
    case 'POST':
        $controller->createBuku($_POST, $_FILES);
        break;
        
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $controller->updateBuku($id_buku, $data);
        break;
        
    case 'DELETE':
        $controller->deleteBuku($id_buku);
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Metode tidak diizinkan']);
        break;
}
?>