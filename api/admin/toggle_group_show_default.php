<?php

require_once '../../core/data_handler.php';

$groupManager = new GroupManager();

// 获取请求数据
$data = json_decode(file_get_contents('php://input'), true);
$groupId = $data['group_id'] ?? null;
$showInDefault = isset($data['show_in_default']) ? (int)$data['show_in_default'] : 0;

if (!$groupId) {
    echo json_encode(['success' => false, 'message' => '缺少必要参数']);
    exit;
}

// 更新群聊的 show_in_default 字段
$success = $groupManager->updateGroup($groupId, ['show_in_default' => $showInDefault]);
echo json_encode(['success' => $success]);

?>