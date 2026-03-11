<?php

// 设置CORS头，允许所有来源
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// 处理OPTIONS请求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// 使用绝对路径包含文件
require_once __DIR__ . '/core/data_handler.php';

// 检查请求方法
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => '请求方法错误，只支持POST请求']);
    exit;
}

// 获取请求数据
$input = [];
// 尝试从JSON格式获取
$jsonInput = json_decode(file_get_contents('php://input'), true);
if ($jsonInput) {
    $input = $jsonInput;
} else {
    // 尝试从表单格式获取
    $input = $_POST;
}

// 验证请求参数
if (!isset($input['group_id']) || !isset($input['user_id'])) {
    echo json_encode(['success' => false, 'message' => '缺少必要的请求参数']);
    exit;
}

$groupId = $input['group_id'];
$userId = $input['user_id'];

// 初始化管理器
$groupManager = new GroupManager();
$userManager = new UserManager();

// 验证群聊是否存在
$group = $groupManager->getGroup($groupId);
if (!$group) {
    echo json_encode(['success' => false, 'message' => '群聊不存在，请检查群聊口令是否正确']);
    exit;
}

// 使用原始ID进行后续操作，确保一致性
$originalGroupId = $group['id'];

// 验证用户是否存在
$user = $userManager->getUser($userId);
if (!$user) {
    // 用户不存在，自动创建用户
    $nickname = '用户_' . substr($userId, -6);
    $userData = [
        'id' => $userId,
        'nickname' => $nickname,
        'avatar' => 'https://picsum.photos/id/1005/60/60',
        'allow_speak' => true,
        'joined_groups' => [],
        'created_at' => date('Y-m-d H:i:s')
    ];
    $userManager->saveUser($userId, $userData);
    $user = $userData;
}

// 检查用户是否已经在该群聊中
if (isset($group['members']) && in_array($userId, $group['members'])) {
    echo json_encode(['success' => false, 'message' => '您已经在该群聊中了']);
    exit;
}

// 检查群人数限制
$memberLimit = $group['member_limit'] ?? 0;
$currentMemberCount = isset($group['members']) ? count($group['members']) : 0;
if ($memberLimit > 0 && $currentMemberCount >= $memberLimit) {
    echo json_encode(['success' => false, 'message' => '群聊人数已达上限，无法加入']);
    exit;
}

// 确保群聊成员列表存在
if (!isset($group['members'])) {
    $group['members'] = [];
}

// 确保成员加入时间映射存在
if (!isset($group['member_joined_times'])) {
    $group['member_joined_times'] = [];
}

// 将用户添加到群聊的成员列表中
$updatedMembers = $group['members'];
$updatedMembers[] = $userId;

// 记录用户加入时间
$updatedMemberJoinedTimes = $group['member_joined_times'];
$updatedMemberJoinedTimes[$userId] = date('Y-m-d H:i:s');

// 更新群聊信息
$groupManager->updateGroup($originalGroupId, [
    'members' => $updatedMembers,
    'member_joined_times' => $updatedMemberJoinedTimes
]);

// 更新用户的joined_groups数组
$user = $userManager->getUser($userId); // 重新获取用户信息，确保数据最新
$joinedGroups = $user['joined_groups'] ?? [];

// 确保joined_groups是数组
if (!is_array($joinedGroups)) {
    $joinedGroups = [];
}

// 确保群聊ID在用户的群聊列表中
if (!in_array($originalGroupId, $joinedGroups)) {
    $joinedGroups[] = $originalGroupId;
}

// 直接构建完整的用户数据并保存
$userData = [
    'id' => $userId,
    'nickname' => $user['nickname'] ?? '用户_' . substr($userId, -6),
    'avatar' => $user['avatar'] ?? 'https://picsum.photos/id/1005/60/60',
    'allow_speak' => $user['allow_speak'] ?? true,
    'joined_groups' => $joinedGroups,
    'created_at' => $user['created_at'] ?? date('Y-m-d H:i:s')
];

// 保存用户
$userManager->saveUser($userId, $userData);

// 重新获取用户信息，验证更新是否成功
$updatedUser = $userManager->getUser($userId);
$updatedJoinedGroups = $updatedUser['joined_groups'] ?? [];

// 确保群聊ID在用户的群聊列表中
if (!in_array($originalGroupId, $updatedJoinedGroups)) {
    // 如果不在，再次尝试保存
    $userData['joined_groups'] = $joinedGroups;
    $userManager->saveUser($userId, $userData);
}

// 确定显示的群聊ID（优先使用自定义ID）
$displayGroupId = !empty($group['custom_group_id']) ? $group['custom_group_id'] : $originalGroupId;

// 返回成功响应
echo json_encode([
    'success' => true,
    'message' => '加入群聊成功',
    'data' => [
        'group_id' => $displayGroupId,
        'original_group_id' => $originalGroupId,
        'custom_group_id' => $group['custom_group_id'] ?? null,
        'group_name' => $group['name'],
        'user_id' => $userId
    ]
]);

?>