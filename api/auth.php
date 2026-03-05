<?php
require_once '../config/koneksi.php';
require_once '../models/User.php';
require_once '../controllers/AuthController.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Metode tidak diizinkan!']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$controller = new AuthController();

if ($action === 'register') {
    $controller->register($data);
} elseif ($action === 'login') {
    $controller->login($data);
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Aksi tidak valid!']);
}
?>