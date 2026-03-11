<?php

// 设置CORS头
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// 处理OPTIONS请求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../../core/data_handler.php';

$groupManager = new GroupManager();

// 获取请求数据
$input = json_decode(file_get_contents('php://input'), true);
$groupId = $input['group_id'] ?? null;
$userId = $input['user_id'] ?? null;
$title = $input['title'] ?? '';

if (!$groupId || !$userId) {
    echo json_encode(['success' => false, 'message' => '缺少必要参数']);
    exit;
}

// 设置成员头衔
$success = $groupManager->setMemberTitle($groupId, $userId, $title);
echo json_encode(['success' => $success, 'message' => $success ? '头衔设置成功' : '头衔设置失败']);

?>