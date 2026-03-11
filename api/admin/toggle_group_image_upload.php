<?php

require_once '../../core/data_handler.php';

$groupManager = new GroupManager();

// 获取请求数据
$data = json_decode(file_get_contents('php://input'), true);
$groupId = $data['group_id'] ?? null;
$allowImageUpload = $data['allow_image_upload'] ?? null;

if (!$groupId || $allowImageUpload === null) {
    echo json_encode(['success' => false, 'message' => '缺少必要参数']);
    exit;
}

// 切换群聊图片上传权限
$success = $groupManager->toggleGroupImageUpload($groupId, $allowImageUpload);
echo json_encode(['success' => $success]);

?>