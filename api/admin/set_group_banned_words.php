<?php
// 设置CORS头
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// 加载核心文件
require_once __DIR__ . '/../../core/data_handler.php';

// 获取参数
$groupId = $_GET['group_id'] ?? '';
$bannedWords = json_decode(file_get_contents('php://input'), true)['banned_words'] ?? [];

if (empty($groupId)) {
    echo json_encode([
        'success' => false,
        'message' => '群聊ID不能为空'
    ]);
    exit;
}

// 初始化GroupManager
$groupManager = new GroupManager();

// 更新群聊违禁词
$result = $groupManager->updateGroup($groupId, ['banned_words' => $bannedWords]);

echo json_encode([
    'success' => $result,
    'message' => $result ? '违禁词保存成功' : '违禁词保存失败'
]);
?>