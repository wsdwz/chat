<?php
// api/admin/send_card.php
session_start();
if (empty($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => '未授权']);
    exit;
}

require_once '../../core/data_handler.php';
$messageManager = new MessageManager();

$data = json_decode(file_get_contents('php://input'), true);
$groupId = $data['group_id'] ?? '';
$title = $data['title'] ?? '通知卡片';
$desc = $data['desc'] ?? '';
$url = $data['url'] ?? '';
$thumb = $data['thumb'] ?? '';

if (!$groupId) {
    echo json_encode(['success' => false, 'message' => '缺少群组ID']);
    exit;
}

$cardContent = json_encode([
    'title' => $title,
    'desc' => $desc,
    'url' => $url,
    'thumb' => $thumb,
    'source' => '系统推送'
], JSON_UNESCAPED_UNICODE);

$messageData = [
    'group_id' => $groupId,
    'user_id' => 'admin',
    'user_nickname' => '系统管理员',
    'user_avatar' => '',
    'type' => 'card',
    'content' => $cardContent,
    'is_admin' => true
];

$newMessage = $messageManager->sendMessage($messageData);
echo json_encode(['success' => true, 'message' => '卡片发送成功']);
