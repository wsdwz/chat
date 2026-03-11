<?php

require_once '../../core/data_handler.php';

$groupManager = new GroupManager();

// 获取请求数据
$data = json_decode(file_get_contents('php://input'), true);
$groupId = $data['group_id'] ?? null;
$allowSpeak = $data['allow_speak'] ?? null;

if (!$groupId || $allowSpeak === null) {
    echo json_encode(['success' => false, 'message' => '缺少必要参数']);
    exit;
}

// 切换群聊全体发言权限
$success = $groupManager->toggleGroupSpeak($groupId, $allowSpeak);
echo json_encode(['success' => $success]);

?>