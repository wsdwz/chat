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

$messageManager = new MessageManager();

// 获取请求体中的数据
$requestBody = file_get_contents('php://input');
$data = json_decode($requestBody, true);

$groupId = $data['group_id'] ?? null;

if (!$groupId) {
    echo json_encode(['success' => false, 'message' => '缺少群聊ID']);
    exit;
}

// 撤回指定群聊的所有消息
$success = $messageManager->withdrawAllMessages($groupId);

if ($success) {
    echo json_encode(['success' => true, 'message' => '已成功撤回全部消息']);
} else {
    echo json_encode(['success' => false, 'message' => '撤回失败，可能没有可撤回的消息']);
}

?>