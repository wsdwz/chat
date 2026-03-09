<?php
/**
 * 获取群违禁词
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// 加载核心文件
require_once __DIR__ . '/../../core/data_handler.php';

// 获取群ID
$group_id = $_GET['group_id'] ?? '';
if (empty($group_id)) {
    echo json_encode([
        'success' => false,
        'message' => '缺少群ID'
    ]);
    exit;
}

// 初始化GroupManager
$groupManager = new GroupManager();

// 获取群信息
$group = $groupManager->getGroup($group_id);

if (!$group) {
    echo json_encode([
        'success' => false,
        'message' => '群不存在'
    ]);
    exit;
}

// 返回违禁词列表
echo json_encode([
    'success' => true,
    'banned_words' => $group['banned_words'] ?? []
]);
?>