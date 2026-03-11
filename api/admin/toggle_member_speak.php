<?php

require_once '../../core/data_handler.php';

$userManager = new UserManager();

// 获取请求数据
$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['user_id'] ?? null;
$allowSpeak = $data['allow_speak'] ?? null;

if (!$userId || $allowSpeak === null) {
    echo json_encode(['success' => false, 'message' => '缺少必要参数']);
    exit;
}

// 切换成员发言权限
$success = $userManager->toggleUserSpeak($userId, $allowSpeak);
echo json_encode(['success' => $success]);

?>