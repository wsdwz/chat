<?php
header('Content-Type: application/json');

require_once 'core/data_handler.php';

$groupManager = new GroupManager();

// 获取所有群聊
$groups = $groupManager->getAllGroups();

// 输出结果
echo json_encode([
    'groups' => $groups,
    'count' => count($groups)
], JSON_UNESCAPED_UNICODE);
?>