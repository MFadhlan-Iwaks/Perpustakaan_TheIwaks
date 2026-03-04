<?php
header("Content-Type: application/json; charset=UTF-8");

function sendResponse($status, $message, $data = null)
{
    $response = [
        'status' => $status,
        'message' => $message,
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    echo json_encode($response);
    exit();
}

function sendError($message, $code = 400)
{
    http_response_code($code);
    sendResponse('error', $message);
}

function sendSuccess($message, $data = null, $code = 200)
{
    http_response_code($code);
    sendResponse('success', $message, $data);
}
?>