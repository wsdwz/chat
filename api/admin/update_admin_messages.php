<?php

// 设置CORS头，允许所有来源
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// 处理OPTIONS请求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../../core/data_handler.php';

$messageManager = new MessageManager();

// 处理更新管理员消息请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $nickname = $data['nickname'] ?? '';
    $avatar = $data['avatar'] ?? '';
    
    if (!$nickname) {
        echo json_encode(['success' => false, 'message' => '缺少昵称参数']);
        exit();
    }
    
    $result = $messageManager->updateAdminMessages($nickname, $avatar);
    if ($result) {
        echo json_encode(['success' => true, 'message' => '管理员消息更新成功']);
    } else {
        echo json_encode(['success' => false, 'message' => '没有需要更新的管理员消息']);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => '不支持的请求方法']);
?>