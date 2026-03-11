<?php
header('Content-Type: application/json');

require_once 'core/data_handler.php';

$messageManager = new MessageManager();

// 获取群聊1的消息
$group1Messages = $messageManager->getGroupMessages('1');
// 获取群聊2的消息
$group2Messages = $messageManager->getGroupMessages('2');

// 输出结果
echo json_encode([
    'group1_messages' => $group1Messages,
    'group2_messages' => $group2Messages
], JSON_UNESCAPED_UNICODE);
?>