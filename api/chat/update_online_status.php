<?php

// 设置CORS头，允许所有来源
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// 处理OPTIONS请求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../../core/data_handler.php';

// 获取请求数据
$groupId = $_POST['group_id'] ?? null;
$userId = $_POST['user_id'] ?? null;

if (!$groupId || !$userId) {
    echo json_encode(['success' => false, 'message' => '缺少必要参数']);
    exit;
}

$groupManager = new GroupManager();

// 更新用户在线状态
$success = $groupManager->updateUserOnlineStatus($groupId, $userId);

if ($success) {
    // 获取更新后的在线人数
    $onlineCount = $groupManager->getOnlineUserCount($groupId);
    echo json_encode([
        'success' => true,
        'online_count' => $onlineCount
    ]);
} else {
    echo json_encode(['success' => false, 'message' => '更新在线状态失败']);
}

?>