<?php
require_once 'middleware.php';
require_once '../config/koneksi.php';
require_once '../models/User.php';
require_once '../controllers/UserController.php';

cekAksesAPI('petugas');

$method = $_SERVER['REQUEST_METHOD'];
$id_user = isset($_GET['id']) ? (int) $_GET['id'] : null;

$controller = new UserController();

switch ($method) {
    case 'GET':
        $controller->getUser($id_user);
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $controller->createUser($data);
        break;
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $controller->updateUser($id_user, $data);
        break;
    case 'DELETE':
        $controller->deleteUser($id_user);
        break;
    default:
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Metode tidak diizinkan']);
        break;
}
?>