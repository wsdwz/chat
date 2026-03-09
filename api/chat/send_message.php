<?php

// 设置CORS头，允许所有来源
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// 处理OPTIONS请求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../../core/data_handler.php';

$messageManager = new MessageManager();
$groupManager = new GroupManager();
$userManager = new UserManager();

// 获取请求数据
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';

// 检查是否是 multipart/form-data 请求
if (strpos($contentType, 'multipart/form-data') !== false) {
    // 处理文件上传
    $groupId = $_POST['group_id'] ?? null;
    $userId = $_POST['user_id'] ?? null;
    $userNickname = $_POST['user_nickname'] ?? null;
    $userAvatar = $_POST['user_avatar'] ?? null;
    $type = $_POST['type'] ?? 'text';
    $isAdmin = $_POST['is_admin'] ?? false;
    
    // 检查文件是否存在
    if (!isset($_FILES['file'])) {
        echo json_encode(['success' => false, 'message' => '缺少文件']);
        exit;
    }
    
    $file = $_FILES['file'];
    
    // 检查文件上传是否成功
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => '文件上传失败']);
        exit;
    }
    
    // 确保上传目录存在
    $uploadDir = '../../uploads/files/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // 生成唯一文件名
    $fileName = uniqid() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
    $filePath = $uploadDir . $fileName;
    
    // 移动文件到上传目录
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        echo json_encode(['success' => false, 'message' => '文件保存失败']);
        exit;
    }
    
    // 构建消息数据
    $messageData = [
        'group_id' => $groupId,
        'user_id' => $userId,
        'user_nickname' => $userNickname,
        'user_avatar' => $userAvatar,
        'type' => $type,
        'content' => '/uploads/files/' . $fileName,
        'is_admin' => $isAdmin
    ];
} else {
    // 处理 JSON 请求
    $messageData = json_decode(file_get_contents('php://input'), true);
    
    $groupId = $messageData['group_id'] ?? null;
    $userId = $messageData['user_id'] ?? null;
    $userNickname = $messageData['user_nickname'] ?? null;
    $userAvatar = $messageData['user_avatar'] ?? null;
}

// 检查必要参数
if (!$groupId || !$userNickname) {
    echo json_encode(['success' => false, 'message' => '缺少必要参数']);
    exit;
}

// 如果没有userId，生成一个临时游客ID
if (!$userId) {
    $userId = 'guest_' . uniqid();
}

// 确保用户在群聊成员列表中
$group = $groupManager->getGroup($groupId);
if (!$group) {
    echo json_encode(['success' => false, 'message' => '群聊不存在']);
    exit;
}

    // 检查用户是否已存在
    $user = $userManager->getUser($userId);
    
    // 如果是游客用户（guest_开头），直接允许发言，不检查昵称重复
    if (strpos($userId, 'guest_') === 0) {
        // 游客用户无需保存到数据库，直接发送消息
    } else {
        // 注册用户需要检查昵称重复
        // 检查群聊中是否存在重复昵称（排除当前用户自己）
        if ($userManager->isNicknameDuplicate($groupId, $userNickname, $userId)) {
            echo json_encode(['success' => false, 'message' => '该昵称在群聊中已存在']);
            exit;
        }
        
        if (!$user) {
            // 创建新用户
            $newUser = [
                'id' => $userId,
                'nickname' => $userNickname,
                'avatar' => $userAvatar,
                'allow_speak' => true,
                'joined_groups' => [$groupId],
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            // 使用UserManager的saveUser方法直接保存新用户
            $userManager->saveUser($userId, $newUser);
        } else {
            // 检查用户是否已加入该群聊，如果没有则添加
            if (!in_array($groupId, $user['joined_groups'])) {
                // 再次检查昵称是否重复（防止用户在加入前有其他用户使用了相同昵称）
                if ($userManager->isNicknameDuplicate($groupId, $userNickname, $userId)) {
                    echo json_encode(['success' => false, 'message' => '该昵称在群聊中已存在']);
                    exit;
                }
                
                $user['joined_groups'][] = $groupId;
                $userManager->updateUser($userId, $user);
            }
        }
    }

// 如果用户不在群成员列表中，添加他们
// 但游客用户（guest_开头）不需要添加到成员列表
if (strpos($userId, 'guest_') !== 0 && !in_array($userId, $group['members'])) {
    $group['members'][] = $userId;
    
    // 记录用户加入时间
    if (!isset($group['member_joined_times'])) {
        $group['member_joined_times'] = [];
    }
    $group['member_joined_times'][$userId] = date('Y-m-d H:i:s');
    
    // 保存更新后的群聊信息
    $groupManager->updateGroup($groupId, [
        'members' => $group['members'],
        'member_joined_times' => $group['member_joined_times']
    ]);
}

// 检查发言权限
$group = $groupManager->getGroup($groupId);
$user = $userManager->getUser($userId);

// 检查是否是管理员消息
$isAdminMessage = ($userId === 'admin') || ($messageData['is_admin'] ?? false);

// 只有非管理员才需要检查群聊全体禁言
if (!$isAdminMessage && $group['allow_speak'] != 1) {
    echo json_encode(['success' => false, 'message' => '群聊已关闭全体发言']);
    exit;
}

// 只有非管理员且已注册用户才需要检查个人发言权限
if (!$isAdminMessage && $user && $user['allow_speak'] != 1) {
    echo json_encode(['success' => false, 'message' => '您已被禁止发言']);
    exit;
}

// 发送消息
$newMessage = $messageManager->sendMessage($messageData);
echo json_encode(['success' => true, 'message' => $newMessage]);

?>