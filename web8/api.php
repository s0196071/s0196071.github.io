<?php
session_start();
require_once 'db_connect.php';
require_once 'process.php';

header('Content-Type: application/json; charset=utf-8');

$input = file_get_contents('php://input');
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';

// Парсинг XML или JSON
if (stripos($contentType, 'xml') !== false) {
    libxml_use_internal_errors(true);
    $xml = simplexml_load_string($input);
    if ($xml === false) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid XML']);
        exit;
    }
    $data = json_decode(json_encode($xml), true);
} else {
    $data = json_decode($input, true);
    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
        exit;
    }
}

$isLoggedIn = isset($_SESSION['user_id']);
$userId = $isLoggedIn ? $_SESSION['user_id'] : null;

$result = processCooperation($data, $isLoggedIn, $userId, $db);

if (!$result['success'] && isset($result['errors'])) {
    http_response_code(422); // Unprocessable Entity
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
