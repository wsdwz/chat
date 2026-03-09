<?php

require_once '../../core/data_handler.php';

$groupManager = new GroupManager();

$groupId = $_GET['group_id'] ?? null;

if (!$groupId) {
    echo json_encode(['success' => false, 'message' => '缺少群聊ID']);
    exit;
}

// 获取群聊成员
$members = $groupManager->getGroupMembers($groupId);
echo json_encode(['success' => true, 'members' => $members]);

?>