<?php
session_start();
require_once 'db_connect.php';
require_once 'process.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: myblog.php');
    exit;
}

$data = [
    'name'      => $_POST['name'] ?? '',
    'email'     => $_POST['email'] ?? '',
    'phone'     => $_POST['phone'] ?? '',
    'comment'   => $_POST['comment'] ?? '',
    'agreement' => isset($_POST['agreement']) ? true : false,
];

$isLoggedIn = isset($_SESSION['user_id']);
$userId = $isLoggedIn ? $_SESSION['user_id'] : null;

$result = processCooperation($data, $isLoggedIn, $userId, $db);

if ($result['success']) {
    $params = ['success' => 1];
    if (isset($result['login'])) {
        $params['login'] = $result['login'];
        $params['password'] = $result['password'];
        $params['profile_url'] = $result['profile_url'];
    }
    header('Location: myblog.php?' . http_build_query($params));
} else {
    $_SESSION['cooperation_errors'] = $result['errors'] ?? [];
    $_SESSION['cooperation_data'] = $data;
    header('Location: myblog.php?error=1');
}
exit;
