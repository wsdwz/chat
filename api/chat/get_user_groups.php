<?php
// 设置CORS头
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// 启用输出缓冲，提高响应速度
ob_start();

// 动态获取基础域名和路径
$protocol = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$scriptPath = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';

// 构建基础URL
$baseUrl = $protocol . '://' . $host;

// 计算基础路径（移除api/chat/get_user_groups.php部分）
$pathParts = explode('/', $scriptPath);
if (count($pathParts) >= 4) {
    // 保留到test目录
    $basePath = implode('/', array_slice($pathParts, 0, -3));
    if (!empty($basePath)) {
        $baseUrl .= $basePath;
    }
}

// 确保URL以/结尾
if (substr($baseUrl, -1) !== '/') {
    $baseUrl .= '/';
}

// 导入数据处理类
// 使用绝对路径包含文件
require_once __DIR__ . '/../../core/data_handler.php';

// 初始化数据管理器
$groupManager = new GroupManager();
$userManager = new UserManager();
$messageManager = new MessageManager();

// 获取请求参数
$userId = null;

// 尝试从GET参数获取
if (isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];
}

// 尝试从POST参数获取
if (!$userId && isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];
}

// 尝试从JSON请求体获取
if (!$userId && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $jsonInput = file_get_contents('php://input');
    if ($jsonInput) {
        $jsonData = json_decode($jsonInput, true);
        if (isset($jsonData['user_id'])) {
            $userId = $jsonData['user_id'];
        }
    }
}

if (!$userId) {
    echo json_encode([
        'success' => false,
        'message' => '缺少user_id参数',
        'debug' => [
            'get_params' => $_GET,
            'post_params' => $_POST,
            'request_method' => $_SERVER['REQUEST_METHOD']
        ]
    ]);
    ob_end_flush();
    exit;
}

// 查找用户
$user = $userManager->getUser($userId);

// 如果用户不存在，自动创建用户记录
    if (!$user) {
        // 生成匿名昵称
        $numericPart = preg_replace('/[^0-9]/', '', $userId);
        $randomChars = substr($numericPart, -6);
        $nickname = '匿名' . $randomChars;
        
        // 创建新用户对象
        $userData = [
            'id' => $userId,
            'nickname' => $nickname,
            'avatar' => 'https://picsum.photos/id/1005/60/60',
            'allow_speak' => true,
            'joined_groups' => [],
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // 保存用户
        $userManager->saveUser($userId, $userData);
        // 重新从数据库获取用户信息
        $user = $userManager->getUser($userId);
        if (!$user) {
            $user = $userData;
        }
    }

// 获取用户加入的群聊ID
$joinedGroupIds = $user['joined_groups'] ?? [];

// 确保joinedGroups是数组
if (!is_array($joinedGroupIds)) {
    $joinedGroupIds = [];
}

// 获取所有群聊，用于快速查找和恢复用户群聊列表
$allGroups = $groupManager->getAllGroups();
// 构建群聊ID索引
$groupIndex = [];
// 从群聊的members字段中恢复用户的群聊列表
$recoveredGroupIds = [];
foreach ($allGroups as $group) {
    $groupIndex[$group['id']] = $group;
    if (!empty($group['custom_group_id'])) {
        $groupIndex[$group['custom_group_id']] = $group;
    }
    // 检查用户是否在群聊的members字段中
    if (isset($group['members']) && is_array($group['members']) && in_array($userId, $group['members'])) {
        $recoveredGroupIds[] = $group['id'];
    }
}

// 如果用户的joined_groups字段为空，使用从群聊的members字段中恢复的群聊列表
if (empty($joinedGroupIds) && !empty($recoveredGroupIds)) {
    $joinedGroupIds = $recoveredGroupIds;
    // 更新用户的joined_groups字段
    $userData = [
        'id' => $userId,
        'nickname' => $user['nickname'] ?? '用户_' . substr($userId, -6),
        'avatar' => $user['avatar'] ?? 'https://picsum.photos/id/' . rand(1, 1000) . '/60/60',
        'allow_speak' => $user['allow_speak'] ?? true,
        'joined_groups' => $joinedGroupIds,
        'created_at' => $user['created_at'] ?? date('Y-m-d H:i:s')
    ];
    $userManager->saveUser($userId, $userData);
}

foreach ($joinedGroupIds as $groupId) {
    // 查找群聊
    $group = null;
    // 首先在索引中查找
    if (isset($groupIndex[$groupId])) {
        $group = $groupIndex[$groupId];
    } else {
        // 如果索引中没有，使用getGroup方法查找
        $group = $groupManager->getGroup($groupId);
    }
    
    if ($group) {
        // 获取群聊消息
        $messages = $messageManager->getGroupMessages($groupId);
        
        // 快速获取最新消息
        $latestMessage = null;
        if (!empty($messages)) {
            // 按时间戳排序获取最新消息
            usort($messages, function($a, $b) {
                $timestampA = isset($a['timestamp']) ? strtotime($a['timestamp']) : 0;
                $timestampB = isset($b['timestamp']) ? strtotime($b['timestamp']) : 0;
                return $timestampB - $timestampA; // 降序排序
            });
            
            $latestMessage = $messages[0]; // 第一个就是最新的
            // 处理最新消息中的用户头像
            if (isset($latestMessage['user_avatar']) && $latestMessage['user_avatar'] && strpos($latestMessage['user_avatar'], 'http') !== 0) {
                $latestMessage['user_avatar'] = $baseUrl . $latestMessage['user_avatar'];
            }
        }
        
        // 构建群聊信息，只包含需要的字段
        $displayId = !empty($group['custom_group_id']) ? $group['custom_group_id'] : $group['id'];
        // 为avatar为null的群聊设置默认随机图片
        $avatar = $group['avatar'] ? ($group['avatar'] && strpos($group['avatar'], 'http') === 0 ? $group['avatar'] : $baseUrl . $group['avatar']) : 'https://picsum.photos/id/' . rand(1, 1000) . '/100/100';
        // 计算群聊成员数量
        $memberCount = isset($group['members']) && is_array($group['members']) ? count($group['members']) : 0;
        // 获取群人数上限，0表示无限制，转换为999999
        $memberLimit = $group['member_limit'] ?? 0;
        if ($memberLimit === 0) {
            $memberLimit = 999999;
        }
        // 获取群聊标签
        $tag = $group['tag'] ?? '';
        // 构建聊天室URL，同时支持自定义ID和旧ID
        $chatUrl = $baseUrl . 'index.php?group_id=' . $displayId . '&user_id=' . $userId;
        $groupInfo = [
            'id' => $displayId,
            'original_id' => $group['id'],
            'custom_group_id' => $group['custom_group_id'] ?? null,
            'name' => $group['name'] ?? '未命名群聊',
            'avatar' => $avatar,
            'domain' => $baseUrl,
            'chat_url' => $chatUrl,
            'old_chat_url' => $baseUrl . 'index.php?group_id=' . $group['id'] . '&user_id=' . $userId,
            'member_count' => $memberCount,
            'member_limit' => $memberLimit,
            'tag' => $tag,
            'latest_message' => $latestMessage ? [
                'content' => $latestMessage['content'] ?? '',
                'timestamp' => $latestMessage['timestamp'] ?? '',
                'user_nickname' => $latestMessage['user_nickname'] ?? '',
                'type' => $latestMessage['type'] ?? 'text'
            ] : null
        ];
        
        $userGroups[] = $groupInfo;
    }
}

// 构建响应数据
$response = [
    'success' => true,
    'domain' => $baseUrl,
    'data' => [
        'user_id' => $userId,
        'nickname' => $user['nickname'] ?? '未知',
        'joined_groups' => $userGroups
    ]
];

// 生成JSON响应
$jsonResponse = json_encode($response);

// 返回结果
echo $jsonResponse;
ob_end_flush();
?>