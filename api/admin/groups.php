<?php

// 禁止输出PHP错误信息
ini_set('display_errors', 0);
error_reporting(0);

// 设置CORS头，允许所有来源
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// 处理OPTIONS请求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    require_once '../../core/data_handler.php';

// 初始化GroupManager
$groupManager = new GroupManager();

// 获取单个群聊
if (isset($_GET['group_id'])) {
    $groupId = $_GET['group_id'];
    $group = $groupManager->getGroup($groupId);
    if ($group) {
        // 添加在线人数信息
        $group['online_count'] = $groupManager->getOnlineUserCount($groupId);
        // 计算今日和昨日新加入成员数量
        $group['today_new_members'] = $groupManager->calculateTodayNewMembers($groupId);
        $group['yesterday_new_members'] = $groupManager->calculateYesterdayNewMembers($groupId);
        // 直接使用现有方法计算单群聊的活跃用户
        $group['today_active_users'] = $groupManager->calculateTodayActiveUsers($groupId);
        $group['total_active_users'] = $groupManager->calculateTotalActiveUsers($groupId);
        echo json_encode($group);
    } else {
        echo json_encode(null);
    }
    exit;
}

// 获取所有群聊或统计数据
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // 检查是否请求统计数据
    if (isset($_GET['statistics'])) {
        // 获取统计数据
        $groups = $groupManager->getAllGroups();
        $totalGroups = count($groups);
        
        // 获取所有用户
        $usersManager = new UserManager();
        $allUsers = $usersManager->getAllUsers();
        $totalUsers = count($allUsers);
        
        // 获取所有消息
        $messageManager = new MessageManager();
        $allMessages = $messageManager->getAllMessages();
        $totalMessages = count($allMessages);
        
        // 计算今日活跃用户
        $todayStart = strtotime(date('Y-m-d 00:00:00'));
        $todayActiveUsers = [];
        foreach ($allMessages as $message) {
            $messageTime = strtotime($message['timestamp']);
            if ($messageTime >= $todayStart) {
                $todayActiveUsers[$message['user_id']] = true;
            }
        }
        $todayActiveUsersCount = count($todayActiveUsers);
        
        // 返回统计数据
        echo json_encode([
            'totalGroups' => $totalGroups,
            'totalUsers' => $totalUsers,
            'totalMessages' => $totalMessages,
            'todayActiveUsers' => $todayActiveUsersCount
        ]);
        exit;
    }
    
    // 常规群聊列表请求
    $groups = $groupManager->getAllGroups();
    
    // 一次性计算所有群聊的活跃用户统计信息
    $activeUsersStats = $groupManager->calculateAllGroupsActiveUsers();
    
    // 为每个群聊添加在线人数信息和新加入成员数量
    foreach ($groups as &$group) {
        $groupId = $group['id'];
        
        // 计算在线人数
        $onlineCount = 0;
        if (isset($group['online_users'])) {
            $now = time();
            foreach ($group['online_users'] as $status) {
                if ($now - $status['last_active'] <= 300) {
                    $onlineCount++;
                }
            }
        }
        $group['online_count'] = $onlineCount;
        
        // 计算今日和昨日新加入成员数量
        $todayNewMembers = 0;
        $yesterdayNewMembers = 0;
        if (isset($group['member_joined_times'])) {
            $todayStart = strtotime(date('Y-m-d 00:00:00'));
            $yesterdayStart = strtotime('-1 day', $todayStart);
            $yesterdayEnd = $todayStart;
            
            foreach ($group['member_joined_times'] as $joinedTime) {
                $joinedTimestamp = strtotime($joinedTime);
                if ($joinedTimestamp >= $todayStart) {
                    $todayNewMembers++;
                } elseif ($joinedTimestamp >= $yesterdayStart && $joinedTimestamp < $yesterdayEnd) {
                    $yesterdayNewMembers++;
                }
            }
        }
        $group['today_new_members'] = $todayNewMembers;
        $group['yesterday_new_members'] = $yesterdayNewMembers;
        
        // 设置活跃用户统计信息
        $group['today_active_users'] = $activeUsersStats[$groupId]['today_active'] ?? 0;
        $group['total_active_users'] = $activeUsersStats[$groupId]['total_active'] ?? 0;
    }
    echo json_encode($groups);
    exit;
}

// 创建群聊
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 处理文件上传
    $avatar = null;
    if (isset($_FILES['groupAvatar']) && $_FILES['groupAvatar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../uploads/images/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $filename = uniqid() . '_' . basename($_FILES['groupAvatar']['name']);
        $targetPath = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['groupAvatar']['tmp_name'], $targetPath)) {
            $avatar = 'uploads/images/' . $filename;
        }
    }
    
    // 获取表单数据
    $groupName = $_POST['groupName'];
    $groupDesc = $_POST['groupDesc'];
    $groupAnnouncement = $_POST['groupAnnouncement'];
    $bannedWords = $_POST['groupBannedWords'];
    $memberLimit = $_POST['groupMemberLimit'] ?? 0;
    $groupTag = $_POST['groupTag'] ?? '';
    
    // 处理违禁词
    $bannedWordsArray = [];
    if (!empty($bannedWords)) {
        $bannedWordsArray = array_map('trim', explode(',', $bannedWords));
    }
    
    // 创建群聊
    $groupData = [
        'name' => $groupName,
        'desc' => $groupDesc,
        'announcement' => $groupAnnouncement,
        'avatar' => $avatar,
        'banned_words' => $bannedWordsArray,
        'member_limit' => $memberLimit,
        'tag' => $groupTag
    ];
    
    $newGroup = $groupManager->createGroup($groupData);
    echo json_encode(['success' => true, 'group' => $newGroup]);
    exit;
}

} catch (Exception $e) {
    // 返回错误信息
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

?>