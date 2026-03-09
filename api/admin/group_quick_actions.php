<?php

require_once __DIR__ . '/../../core/data_handler.php';

// 设置CORS头
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}

$groupManager = new GroupManager();

// 获取请求方法和数据
$method = $_SERVER['REQUEST_METHOD'];
$groupId = $_GET['group_id'] ?? null;

// 验证groupId
if (!$groupId) {
    echo json_encode(['success' => false, 'message' => '缺少group_id参数']);
    exit();
}

switch ($method) {
    // 添加底部标签
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $type = $data['type'] ?? '';
        $title = $data['title'] ?? '';
        $url = $data['url'] ?? '';
        
        // 验证必填字段
        if (!$type || !$title || !$url) {
            echo json_encode(['success' => false, 'message' => '缺少必填字段']);
            exit();
        }
        
        // 验证标签类型
        $allowedTypes = ['welfare', 'red_packet', 'video', 'image', 'activity'];
        if (!in_array($type, $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => '无效的标签类型']);
            exit();
        }
        
        // 获取群聊信息
        $group = $groupManager->getGroup($groupId);
        if (!$group) {
            echo json_encode(['success' => false, 'message' => '群聊不存在']);
            exit();
        }
        
        // 初始化quick_actions字段
        if (!isset($group['quick_actions'])) {
            $group['quick_actions'] = [];
        }
        
        // 创建新标签
        $newAction = [
            'id' => uniqid() . rand(1000, 9999),
            'type' => $type,
            'title' => $title,
            'url' => $url,
            'icon' => getIconByType($type),
            'click_count' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // 添加到群聊
        $group['quick_actions'][] = $newAction;
        
        // 更新群聊
        if ($groupManager->updateGroup($groupId, ['quick_actions' => $group['quick_actions']])) {
            echo json_encode(['success' => true, 'message' => '添加成功', 'data' => $newAction]);
        } else {
            echo json_encode(['success' => false, 'message' => '添加失败']);
        }
        break;
    
    // 获取底部标签列表
    case 'GET':
        $group = $groupManager->getGroup($groupId);
        if (!$group) {
            echo json_encode(['success' => false, 'message' => '群聊不存在']);
            exit();
        }
        
        $quickActions = $group['quick_actions'] ?? [];
        echo json_encode(['success' => true, 'data' => $quickActions]);
        break;
    
    // 更新底部标签
    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $actionId = $data['id'] ?? '';
        $title = $data['title'] ?? '';
        $url = $data['url'] ?? '';
        
        if (!$actionId) {
            echo json_encode(['success' => false, 'message' => '缺少id参数']);
            exit();
        }
        
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
        
        // 更新标签
        if ($title) {
            $quickActions[$index]['title'] = $title;
        }
        if ($url) {
            $quickActions[$index]['url'] = $url;
        }
        
        if ($groupManager->updateGroup($groupId, ['quick_actions' => $quickActions])) {
            echo json_encode(['success' => true, 'message' => '更新成功', 'data' => $quickActions[$index]]);
        } else {
            echo json_encode(['success' => false, 'message' => '更新失败']);
        }
        break;
    
    // 删除底部标签
    case 'DELETE':
        $actionId = $_GET['action_id'] ?? '';
        
        if (!$actionId) {
            echo json_encode(['success' => false, 'message' => '缺少action_id参数']);
            exit();
        }
        
        $group = $groupManager->getGroup($groupId);
        if (!$group) {
            echo json_encode(['success' => false, 'message' => '群聊不存在']);
            exit();
        }
        
        $quickActions = $group['quick_actions'] ?? [];
        $newActions = [];
        
        // 过滤掉要删除的标签
        foreach ($quickActions as $action) {
            if ($action['id'] !== $actionId) {
                $newActions[] = $action;
            }
        }
        
        if ($groupManager->updateGroup($groupId, ['quick_actions' => $newActions])) {
            echo json_encode(['success' => true, 'message' => '删除成功']);
        } else {
            echo json_encode(['success' => false, 'message' => '删除失败']);
        }
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => '不支持的请求方法']);
        break;
}

/**
 * 根据类型获取图标
 */
function getIconByType($type) {
    $icons = [
        'welfare' => '🎁',
        'red_packet' => '🧧',
        'video' => '🎬',
        'image' => '🖼️',
        'activity' => '🎉'
    ];
    return $icons[$type] ?? '📌';
}

?>
