<?php

require_once __DIR__ . '/../../core/data_handler.php';

// 设置CORS头
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}

$groupManager = new GroupManager();

// 获取请求数据
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $groupId = $data['group_id'] ?? null;
    $actionId = $data['action_id'] ?? null;
    
    // 验证必填字段
    if (!$groupId || !$actionId) {
        echo json_encode(['success' => false, 'message' => '缺少必填字段']);
        exit();
    }
    
    // 获取群聊信息
    $group = $groupManager->getGroup($groupId);
    if (!$group) {
        echo json_encode(['success' => false, 'message' => '群聊不存在']);
        exit();
    }
    
    $quickActions = $group['quick_actions'] ?? [];
    $index = -1;
    
    // 找到要更新的标签
    foreach ($quickActions as $i => $action) {
        if ($action['id'] === $actionId) {
            $index = $i;
            break;
        }
    }
    
    if ($index === -1) {
        echo json_encode(['success' => false, 'message' => '标签不存在']);
        exit();
    }
    
    // 增加点击次数
    $quickActions[$index]['click_count'] = ($quickActions[$index]['click_count'] ?? 0) + 1;
    
    // 更新群聊
    if ($groupManager->updateGroup($groupId, ['quick_actions' => $quickActions])) {
        echo json_encode(['success' => true, 'message' => '统计成功', 'data' => $quickActions[$index]]);
    } else {
        echo json_encode(['success' => false, 'message' => '统计失败']);
    }
} else {
    echo json_encode(['success' => false, 'message' => '不支持的请求方法']);
}

?>
