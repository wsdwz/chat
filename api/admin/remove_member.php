<?php

require_once '../../core/data_handler.php';

$groupManager = new GroupManager();

// 获取请求数据
$data = json_decode(file_get_contents('php://input'), true);
$groupId = $data['group_id'] ?? null;
$userId = $data['user_id'] ?? null;

if (!$groupId || !$userId) {
    echo json_encode(['success' => false, 'message' => '缺少群聊ID或用户ID']);
    exit;
}

// 移除成员
$success = $groupManager->removeMember($groupId, $userId);
echo json_encode(['success' => $success]);

?>