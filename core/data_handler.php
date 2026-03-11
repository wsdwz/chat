<?php

require_once __DIR__ . '/data_manager.php';
require_once __DIR__ . '/sqlite_manager.php';

/**
 * 群聊数据处理类
 */
class GroupManager {
    private $dataManager;
    
    public function __construct() {
        // 使用SQLiteManager替代DataManager
        $this->dataManager = new SQLiteManager();
        // 迁移JSON数据到SQLite
        $this->dataManager->migrateFromJson();
    }
    
    /**
     * 获取所有群聊
     * @return array 群聊列表
     */
    public function getAllGroups() {
        return $this->dataManager->load('chat_groups');
    }
    
    /**
     * 获取单个群聊
     * @param string $groupId 群聊ID
     * @return array|null 群聊信息
     */
    public function getGroup($groupId) {
        $groups = $this->getAllGroups();
        foreach ($groups as $group) {
            if ($group['id'] === $groupId || (!empty($group['custom_group_id']) && $group['custom_group_id'] === $groupId)) {
                return $group;
            }
        }
        return null;
    }
    
    /**
     * 创建群聊
     * @param array $groupData 群聊数据
     * @return array 创建的群聊
     */
    public function createGroup($groupData) {
        $newGroup = [
            'id' => $this->dataManager->generateId(),
            'custom_group_id' => null,
            'name' => $groupData['name'],
            'desc' => $groupData['desc'] ?? '',
            'avatar' => $groupData['avatar'] ?? null,
            'banned_words' => $groupData['banned_words'] ?? [],
            'announcement' => $groupData['announcement'] ?? '',
            'allow_speak' => true,
            'allow_image_upload' => true,
            'quick_actions' => [], // 底部标签字段
            'members' => [],
            'member_titles' => [], // 成员头衔映射
            'member_joined_times' => [], // 成员加入时间映射
            'online_users' => [], // 添加在线用户字段
            'member_limit' => $groupData['member_limit'] ?? 0, // 群人数限制，0表示无限制
            'tag' => $groupData['tag'] ?? '', // 群聊专属标签
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $this->dataManager->save('chat_groups', $newGroup);
        
        return $newGroup;
    }
    
    /**
     * 更新用户在线状态
     * @param string $groupId 群聊ID
     * @param string $userId 用户ID
     * @return bool 是否成功
     */
    public function updateUserOnlineStatus($groupId, $userId) {
        $group = $this->getGroup($groupId);
        if (!$group) {
            return false;
        }
        
        // 初始化online_users字段（如果不存在）
        if (!isset($group['online_users'])) {
            $group['online_users'] = [];
        }
        
        // 更新或添加用户在线状态
        $group['online_users'][$userId] = [
            'last_active' => time()
        ];
        
        // 清理超过5分钟未活跃的用户
        $now = time();
        foreach ($group['online_users'] as $uid => $status) {
            if ($now - $status['last_active'] > 300) {
                unset($group['online_users'][$uid]);
            }
        }
        
        // 更新群聊
        return $this->updateGroup($groupId, ['online_users' => $group['online_users']]);
    }
    
    /**
     * 获取群聊在线人数
     * @param string $groupId 群聊ID
     * @return int 在线人数
     */
    public function getOnlineUserCount($groupId) {
        $group = $this->getGroup($groupId);
        if (!$group || !isset($group['online_users'])) {
            return 0;
        }
        
        // 清理超过5分钟未活跃的用户
        $now = time();
        $onlineCount = 0;
        foreach ($group['online_users'] as $status) {
            if ($now - $status['last_active'] <= 300) {
                $onlineCount++;
            }
        }
        
        return $onlineCount;
    }
    
    /**
     * 获取群聊在线用户列表
     * @param string $groupId 群聊ID
     * @return array 在线用户列表
     */
    public function getOnlineUsers($groupId) {
        $group = $this->getGroup($groupId);
        if (!$group || !isset($group['online_users'])) {
            return [];
        }
        
        // 清理超过5分钟未活跃的用户
        $now = time();
        $onlineUsers = [];
        foreach ($group['online_users'] as $uid => $status) {
            if ($now - $status['last_active'] <= 300) {
                $onlineUsers[] = $uid;
            }
        }
        
        return $onlineUsers;
    }
    
    /**
     * 一次性计算所有群聊的活跃用户统计信息
     * @return array 包含每个群聊的今日活跃人数和总活跃人数的数组
     */
    public function calculateAllGroupsActiveUsers() {
        // 使用正确的表名
        $messages = $this->dataManager->load('chat_messages');
        
        // 计算今日的起始时间戳（凌晨0点）
        $todayStart = strtotime(date('Y-m-d 00:00:00'));
        
        // 初始化统计数组
        $todayActiveUsers = [];
        $totalActiveUsers = [];
        
        // 遍历所有消息，一次性计算所有群聊的活跃用户
        foreach ($messages as $message) {
            $groupId = $message['group_id'];
            $userId = $message['user_id'];
            $messageTime = strtotime($message['timestamp']);
            
            // 初始化群聊统计（如果不存在）
            if (!isset($todayActiveUsers[$groupId])) {
                $todayActiveUsers[$groupId] = [];
            }
            if (!isset($totalActiveUsers[$groupId])) {
                $totalActiveUsers[$groupId] = [];
            }
            
            // 更新总活跃用户
            $totalActiveUsers[$groupId][$userId] = true;
            
            // 更新今日活跃用户
            if ($messageTime >= $todayStart) {
                $todayActiveUsers[$groupId][$userId] = true;
            }
        }
        
        // 转换为计数
        $result = [];
        foreach (array_unique(array_merge(array_keys($todayActiveUsers), array_keys($totalActiveUsers))) as $groupId) {
            $result[$groupId] = [
                'today_active' => count($todayActiveUsers[$groupId] ?? []),
                'total_active' => count($totalActiveUsers[$groupId] ?? [])
            ];
        }
        
        return $result;
    }
    
    /**
     * 计算今日活跃人数
     * @param string $groupId 群聊ID
     * @return int 今日活跃人数
     */
    public function calculateTodayActiveUsers($groupId) {
        // 使用正确的表名
        $allMessages = $this->dataManager->load('chat_messages');
        $messages = [];
        foreach ($allMessages as $message) {
            if ($message['group_id'] === $groupId) {
                $messages[] = $message;
            }
        }
        
        // 计算今日的起始时间戳（凌晨0点）
        $todayStart = strtotime(date('Y-m-d 00:00:00'));
        $activeUsers = [];
        
        foreach ($messages as $message) {
            $messageTime = strtotime($message['timestamp']);
            if ($messageTime >= $todayStart) {
                $activeUsers[$message['user_id']] = true;
            }
        }
        
        return count($activeUsers);
    }
    
    /**
     * 计算总活跃人数
     * @param string $groupId 群聊ID
     * @return int 总活跃人数
     */
    public function calculateTotalActiveUsers($groupId) {
        // 使用正确的表名
        $allMessages = $this->dataManager->load('chat_messages');
        $messages = [];
        foreach ($allMessages as $message) {
            if ($message['group_id'] === $groupId) {
                $messages[] = $message;
            }
        }
        
        $activeUsers = [];
        
        foreach ($messages as $message) {
            $activeUsers[$message['user_id']] = true;
        }
        
        return count($activeUsers);
    }
    
    /**
     * 更新群聊
     * @param string $groupId 群聊ID
     * @param array $updateData 更新数据
     * @return bool 是否成功
     */
    public function updateGroup($groupId, $updateData) {
        // 查找目标群聊
        $group = $this->getGroup($groupId);
        if (!$group) {
            return false;
        }
        
        // 检查自定义ID是否唯一
        if (isset($updateData['custom_group_id'])) {
            $customId = $updateData['custom_group_id'];
            if (!empty($customId)) {
                $allGroups = $this->getAllGroups();
                foreach ($allGroups as $g) {
                    if ($g['id'] !== $group['id'] && (!empty($g['custom_group_id']) && $g['custom_group_id'] === $customId)) {
                        return false; // 自定义ID已存在
                    }
                }
            }
        }
        
        // 合并更新数据
        $updatedGroup = array_merge($group, $updateData);
        
        // 确保tag字段被正确处理
        if (!isset($updatedGroup['tag'])) {
            $updatedGroup['tag'] = '';
        }
        
        // 使用原始的id字段作为主键，而不是传入的groupId参数
        $this->dataManager->save('chat_groups', $updatedGroup, $group['id']);
        return true;
    }
    
    /**
     * 删除群聊
     * @param string $groupId 群聊ID
     * @return bool 是否成功
     */
  /**
 * 删除群聊
 * @param string $groupId 群聊ID
 * @return bool 是否成功
 */
public function deleteGroup($groupId) {
    // 查找目标群聊
    $group = $this->getGroup($groupId);
    if (!$group) {
        return false;
    }
    
    // 1. 删除该群聊的所有消息和关联文件
    $messageManager = new MessageManager();
    
    // 获取该群的所有消息
    $allMessages = $this->dataManager->load('chat_messages');
    $groupMessages = [];
    foreach ($allMessages as $msg) {
        if ($msg['group_id'] === $groupId) {
            $groupMessages[] = $msg;
        }
    }
    
    // 删除消息关联的图片/视频文件
    foreach ($groupMessages as $msg) {
        if (in_array($msg['type'], ['image', 'video'])) {
            $filePath = $msg['content'];
            
            // 跳过 Base64 和外链
            if (strpos($filePath, 'data:') === 0 || 
                strpos($filePath, 'http://') === 0 || 
                strpos($filePath, 'https://') === 0) {
                continue;
            }
            
            // 构建文件绝对路径
            if ($filePath[0] === '/') {
                $fullPath = $_SERVER['DOCUMENT_ROOT'] . $filePath;
            } else {
                $fullPath = __DIR__ . '/../' . ltrim($filePath, '/');
            }
            
            // 安全删除文件
            if (file_exists($fullPath) && is_file($fullPath)) {
                @unlink($fullPath);
            }
        }
    }
    
    // 2. 删除所有消息记录
    $messageManager->withdrawAllMessages($groupId);
    
    // 3. 删除群聊本身
    $result = $this->dataManager->delete('chat_groups', $groupId);
    return $result;
}

    
    /**
     * 切换群聊全体发言权限
     * @param string $groupId 群聊ID
     * @param bool $allowSpeak 是否允许发言
     * @return bool 是否成功
     */
    public function toggleGroupSpeak($groupId, $allowSpeak) {
        return $this->updateGroup($groupId, ['allow_speak' => $allowSpeak ? 1 : 0]);
    }
    
    /**
     * 切换群聊图片上传权限
     * @param string $groupId 群聊ID
     * @param bool $allowImageUpload 是否允许图片上传
     * @return bool 是否成功
     */
    public function toggleGroupImageUpload($groupId, $allowImageUpload) {
        return $this->updateGroup($groupId, ['allow_image_upload' => $allowImageUpload]);
    }
    
    /**
     * 获取群聊成员
     * @param string $groupId 群聊ID
     * @return array 成员列表
     */
    public function getGroupMembers($groupId) {
        $group = $this->getGroup($groupId);
        if (!$group) {
            return [];
        }
        
        $users = $this->dataManager->load('chat_users');
        $members = [];
        
        // 获取成员头衔映射
        $memberTitles = $group['member_titles'] ?? [];
        // 获取成员加入时间映射
        $memberJoinedTimes = $group['member_joined_times'] ?? [];
        
        foreach ($users as $user) {
            if (in_array($user['id'], $group['members'])) {
                // 添加头衔信息
                $user['title'] = $memberTitles[$user['id']] ?? null;
                // 添加加入时间信息
                $user['joined_time'] = $memberJoinedTimes[$user['id']] ?? null;
                $members[] = $user;
            }
        }
        
        return $members;
    }
    
    /**
     * 计算群聊今日新加入成员数量
     * @param string $groupId 群聊ID
     * @return int 今日新加入成员数量
     */
    public function calculateTodayNewMembers($groupId) {
        $group = $this->getGroup($groupId);
        if (!$group) {
            return 0;
        }
        
        $memberJoinedTimes = $group['member_joined_times'] ?? [];
        $todayStart = strtotime(date('Y-m-d 00:00:00'));
        $count = 0;
        
        foreach ($memberJoinedTimes as $joinedTime) {
            if (strtotime($joinedTime) >= $todayStart) {
                $count++;
            }
        }
        
        return $count;
    }
    
    /**
     * 计算群聊昨日新加入成员数量
     * @param string $groupId 群聊ID
     * @return int 昨日新加入成员数量
     */
    public function calculateYesterdayNewMembers($groupId) {
        $group = $this->getGroup($groupId);
        if (!$group) {
            return 0;
        }
        
        $memberJoinedTimes = $group['member_joined_times'] ?? [];
        $yesterdayStart = strtotime('-1 day', strtotime(date('Y-m-d 00:00:00')));
        $yesterdayEnd = strtotime(date('Y-m-d 00:00:00'));
        $count = 0;
        
        foreach ($memberJoinedTimes as $joinedTime) {
            $joinedTimestamp = strtotime($joinedTime);
            if ($joinedTimestamp >= $yesterdayStart && $joinedTimestamp < $yesterdayEnd) {
                $count++;
            }
        }
        
        return $count;
    }
    
    /**
     * 从群聊中移除成员
     * @param string $groupId 群聊ID
     * @param string $userId 用户ID
     * @return bool 是否成功
     */
    public function removeMember($groupId, $userId) {
        $group = $this->getGroup($groupId);
        if (!$group) {
            return false;
        }
        
        // 从群聊成员列表中移除用户ID
        $memberIndex = array_search($userId, $group['members']);
        if ($memberIndex !== false) {
            unset($group['members'][$memberIndex]);
            // 重置数组索引
            $group['members'] = array_values($group['members']);
            // 如果设置了头衔，也移除
            if (isset($group['member_titles']) && isset($group['member_titles'][$userId])) {
                unset($group['member_titles'][$userId]);
            }
            // 更新群聊
            return $this->updateGroup($groupId, ['members' => $group['members'], 'member_titles' => $group['member_titles']]);
        }
        
        return false;
    }
    
    /**
     * 设置成员头衔
     * @param string $groupId 群聊ID
     * @param string $userId 用户ID
     * @param string $title 头衔
     * @return bool 是否成功
     */
    public function setMemberTitle($groupId, $userId, $title) {
        $group = $this->getGroup($groupId);
        if (!$group) {
            return false;
        }
        
        // 初始化member_titles字段（如果不存在）
        if (!isset($group['member_titles'])) {
            $group['member_titles'] = [];
        }
        
        // 设置头衔
        $group['member_titles'][$userId] = $title;
        
        // 更新群聊
        return $this->updateGroup($groupId, ['member_titles' => $group['member_titles']]);
    }
}

/**
 * 用户数据处理类
 */
class UserManager {
    private $dataManager;
    
    public function __construct() {
        // 使用SQLiteManager
        $this->dataManager = new SQLiteManager();
    }
    
    /**
     * 获取所有用户
     * @return array 用户列表
     */
    public function getAllUsers() {
        return $this->dataManager->load('chat_users');
    }
    
    /**
     * 获取单个用户
     * @param string $userId 用户ID
     * @return array|null 用户信息
     */
    public function getUser($userId) {
        $users = $this->getAllUsers();
        foreach ($users as $user) {
            if ($user['id'] === $userId) {
                return $user;
            }
        }
        return null;
    }
    
    /**
     * 创建用户
     * @param array $userData 用户数据
     * @return array 创建的用户
     */
    public function createUser($userData) {
        $newUser = [
            'id' => $this->dataManager->generateId(),
            'nickname' => $userData['nickname'],
            'avatar' => $userData['avatar'] ?? 'https://picsum.photos/id/1005/60/60',
            'allow_speak' => true,
            'joined_groups' => [],
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $this->dataManager->save('chat_users', $newUser);
        
        return $newUser;
    }
    
    /**
     * 更新用户
     * @param string $userId 用户ID
     * @param array $updateData 更新数据
     * @return bool 是否成功
     */
    public function updateUser($userId, $updateData) {
        // 查找用户
        $user = $this->getUser($userId);
        if (!$user) {
            return false;
        }
        
        // 合并更新数据
        $updatedUser = array_merge($user, $updateData);
        $this->dataManager->save('chat_users', $updatedUser, $userId);
        return true;
    }
    
    /**
     * 切换用户发言权限
     * @param string $userId 用户ID
     * @param bool $allowSpeak 是否允许发言
     * @return bool 是否成功
     */
    public function toggleUserSpeak($userId, $allowSpeak) {
        return $this->updateUser($userId, ['allow_speak' => $allowSpeak ? 1 : 0]);
    }
    
    /**
     * 检查群聊中是否存在重复昵称
     * @param string $groupId 群聊ID
     * @param string $nickname 昵称
     * @param string|null $excludeUserId 排除的用户ID（用于更新昵称时）
     * @return bool 是否存在重复昵称
     */
    public function isNicknameDuplicate($groupId, $nickname, $excludeUserId = null) {
        $groupManager = new GroupManager();
        $group = $groupManager->getGroup($groupId);
        if (!$group) {
            return false;
        }
        
        $users = $this->getAllUsers();
        foreach ($users as $user) {
            // 检查用户是否在群聊中
            if (in_array($user['id'], $group['members'])) {
                // 排除当前用户（用于更新昵称时）
                if ($excludeUserId && $user['id'] === $excludeUserId) {
                    continue;
                }
                // 检查昵称是否重复
                if ($user['nickname'] === $nickname) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * 保存用户数据（使用指定的用户ID）
     * @param string $userId 用户ID
     * @param array $userData 用户数据
     * @return bool 是否成功
     */
    public function saveUser($userId, $userData) {
        // 直接保存用户数据，使用指定的用户ID
        $this->dataManager->save('chat_users', $userData, $userId);
        return true;
    }
}

/**
 * 消息数据处理类
 */
class MessageManager {
    private $dataManager;
    
    public function __construct() {
        // 使用SQLiteManager
        $this->dataManager = new SQLiteManager();
    }
    
    /**
     * 获取群聊消息
     * @param string $groupId 群聊ID
     * @param string|null $lastTimestamp 最后一条消息的时间戳，只返回此时间戳之后的消息
     * @return array 消息列表
     */
    public function getGroupMessages($groupId, $lastTimestamp = null) {
        // 计算3天前的时间戳
        $threeDaysAgo = date('Y-m-d H:i:s', strtotime('-3 days'));
        
        if ($lastTimestamp) {
            // 使用自定义查询获取指定时间戳之后的消息，且在3天内
            $sql = "SELECT * FROM chat_messages WHERE group_id = ? AND timestamp > ? AND timestamp >= ? ORDER BY timestamp ASC";
            $messages = $this->dataManager->query($sql, [$groupId, $lastTimestamp, $threeDaysAgo]);
        } else {
            // 使用自定义查询直接获取指定群聊的消息，且在3天内
            $sql = "SELECT * FROM chat_messages WHERE group_id = ? AND timestamp >= ? ORDER BY timestamp ASC";
            $messages = $this->dataManager->query($sql, [$groupId, $threeDaysAgo]);
        }
        
        // 获取群聊信息，用于获取成员头衔
        $groupManager = new GroupManager();
        $group = $groupManager->getGroup($groupId);
        
        // 添加用户头衔信息
        $groupMessages = [];
        foreach ($messages as $message) {
            // 添加用户头衔信息
            $messageWithTitle = $message;
            
            // 从群成员信息中获取用户头衔
            if ($group && isset($group['member_titles']) && isset($group['member_titles'][$message['user_id']])) {
                $messageWithTitle['user_title'] = $group['member_titles'][$message['user_id']];
            } else {
                $messageWithTitle['user_title'] = '';
            }
            
            $groupMessages[] = $messageWithTitle;
        }
        
        return $groupMessages;
    }
    
    /**
     * 获取所有消息
     * @return array 所有消息列表
     */
    public function getAllMessages() {
        return $this->dataManager->load('chat_messages');
    }
    
    /**
     * 发送消息
     * @param array $messageData 消息数据
     * @return array 发送的消息
     */
    public function sendMessage($messageData) {
        // 过滤违禁词
        $content = $messageData['content'];
        $group_id = $messageData['group_id'];
        
        // 获取群聊信息
        $groupManager = new GroupManager();
        $group = $groupManager->getGroup($group_id);
        
        // 如果群聊存在且有违禁词列表，进行违禁词过滤
        if ($group && isset($group['banned_words']) && !empty($group['banned_words'])) {
            foreach ($group['banned_words'] as $word) {
                if (!empty($word)) {
                    $content = str_ireplace($word, '**', $content);
                }
            }
        }
        
        $newMessage = [
            'id' => $this->dataManager->generateId(),
            'group_id' => $messageData['group_id'],
            'user_id' => $messageData['user_id'],
            'user_nickname' => $messageData['user_nickname'],
            'user_avatar' => $messageData['user_avatar'] ?? null,
            'type' => $messageData['type'],
            'content' => $content,
            'timestamp' => date('Y-m-d H:i:s'),
            'is_admin' => $messageData['is_admin'] ?? false
        ];
        
        // 如果是语音消息且包含时长信息，添加时长字段
        if ($messageData['type'] === 'voice' && isset($messageData['duration'])) {
            $newMessage['duration'] = $messageData['duration'];
        }
        
        $this->dataManager->save('chat_messages', $newMessage);
        
        return $newMessage;
    }
    
    /**
     * 撤回指定群聊的所有消息
     * @param string $groupId 群聊ID
     * @return bool 是否成功
     */
    public function withdrawAllMessages($groupId) {
        // 使用自定义SQL删除指定群聊的所有消息
        $sql = "DELETE FROM chat_messages WHERE group_id = ?";
        $result = $this->dataManager->execute($sql, [$groupId]);
        return $result;
    }
    
    /**
     * 撤回单条消息
     * @param string $messageId 消息ID
     * @return bool 是否成功
     */
    public function withdrawMessage($messageId) {
        // 删除指定ID的消息
        $result = $this->dataManager->delete('chat_messages', $messageId);
        return $result;
    }
    
    /**
     * 更新所有管理员消息的昵称和头像
     * @param string $nickname 新的管理员昵称
     * @param string $avatar 新的管理员头像URL
     * @return bool 是否成功
     */
    public function updateAdminMessages($nickname, $avatar) {
        $messages = $this->dataManager->load('chat_messages');
        $updated = false;
        
        foreach ($messages as &$message) {
            // 检查是否是管理员消息：
            // 1. 有is_admin字段且为true
            // 2. 或者user_id为'admin'（历史消息可能没有is_admin字段）
            $isAdminMessage = (isset($message['is_admin']) && $message['is_admin']) || $message['user_id'] === 'admin';
            
            if ($isAdminMessage) {
                // 无论昵称是否相同，都更新昵称、头像和is_admin标记
                $oldNickname = $message['user_nickname'] ?? '';
                $oldAvatar = $message['user_avatar'] ?? '';
                
                // 更新字段
                $message['user_nickname'] = $nickname;
                $message['user_avatar'] = $avatar;
                $message['is_admin'] = true; // 确保所有管理员消息都有is_admin标记
                
                // 检查是否有变化
                if ($oldNickname !== $nickname || $oldAvatar !== $avatar) {
                    $updated = true;
                }
            }
        }
        
        // 如果有消息被更新，保存
        if ($updated) {
            foreach ($messages as $message) {
                $this->dataManager->save('chat_messages', $message, $message['id']);
            }
            return true;
        }
        return false;
    }
}

?>