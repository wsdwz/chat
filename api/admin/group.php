<?php

// 开启错误抑制，避免警告信息混入JSON响应
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json; charset=utf-8');
require_once '../../core/data_handler.php';

$groupManager = new GroupManager();

$groupId = $_GET['group_id'] ?? null;

if (!$groupId) {
    echo json_encode(['success' => false, 'message' => '缺少群聊ID']);
    exit;
}

// 获取群聊详情
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $group = $groupManager->getGroup($groupId);
    if ($group) {
        // 添加在线人数信息
        $group['online_count'] = $groupManager->getOnlineUserCount($groupId);
        // 计算活跃用户
        $group['today_active_users'] = $groupManager->calculateTodayActiveUsers($groupId);
        $group['total_active_users'] = $groupManager->calculateTotalActiveUsers($groupId);
        echo json_encode($group);
    } else {
        echo json_encode(null);
    }
    exit;
}

// 删除群聊
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $success = $groupManager->deleteGroup($groupId);
    echo json_encode(['success' => $success]);
    exit;
}

// 更新群聊
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 处理文件上传
    $avatar = null;
    $uploadError = null;
    if (isset($_FILES['groupAvatar']) && $_FILES['groupAvatar']['error'] === UPLOAD_ERR_OK) {
        // 确定上传目录路径
        $uploadDir = realpath(__DIR__ . '/../../') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
        // 确保上传目录存在
        if (!file_exists($uploadDir)) {
            $mkdirResult = mkdir($uploadDir, 0755, true);
            if (!$mkdirResult) {
                $uploadError = '无法创建上传目录: ' . $uploadDir;
            } else {
                // 尝试设置目录权限
                chmod($uploadDir, 0755);
            }
        } else {
            // 尝试设置目录权限
            @chmod($uploadDir, 0755);
        }
        
        // 检查目录是否存在
        if (!$uploadError && file_exists($uploadDir)) {
            // 检查目录权限
            $permissions = substr(decoct(fileperms($uploadDir)), -4);
            // 尝试直接创建一个临时文件来测试
            $testFile = $uploadDir . uniqid() . '.tmp';
            $canWrite = @fopen($testFile, 'w');
            if ($canWrite) {
                fclose($canWrite);
                unlink($testFile);
                
                // 清理文件名，移除空格和特殊字符
                $cleanFilename = preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES['groupAvatar']['name']));
                $filename = uniqid() . '_' . $cleanFilename;
                $targetPath = $uploadDir . $filename;
                
                // 尝试移动文件
                if (move_uploaded_file($_FILES['groupAvatar']['tmp_name'], $targetPath)) {
                    // 尝试设置文件权限
                    @chmod($targetPath, 0644);
                    $avatar = 'uploads/images/' . $filename;
                } else {
                    $uploadError = '无法移动上传的文件: 错误码 ' . $_FILES['groupAvatar']['error'] . '，目标路径: ' . $targetPath;
                }
            } else {
                $uploadError = '上传目录不可写: ' . $uploadDir . '，当前权限: ' . $permissions . '，PHP用户: ' . (get_current_user() ?: 'unknown');
            }
        } else {
            $uploadError = '上传目录不存在: ' . $uploadDir;
        }
    }
    
    // 如果有上传错误，返回错误信息
    if ($uploadError) {
        echo json_encode(['success' => false, 'message' => '头像上传失败: ' . $uploadError]);
        exit;
    }
    
    // 获取表单数据
    $groupName = $_POST['groupName'] ?? '';
    $groupDesc = $_POST['groupDesc'] ?? '';
    $groupAnnouncement = $_POST['groupAnnouncement'] ?? '';
    $bannedWords = $_POST['groupBannedWords'] ?? '';
    $customGroupId = $_POST['customGroupId'] ?? null;
    $memberLimit = $_POST['groupMemberLimit'] ?? 0;
    $groupTag = $_POST['groupTag'] ?? '';
    
    // 调试信息
    error_log('Group Tag: ' . $groupTag);
    error_log('POST Data: ' . print_r($_POST, true));
    
    // 处理违禁词
    $bannedWordsArray = [];
    if (!empty($bannedWords)) {
        $bannedWordsArray = array_map('trim', explode(',', $bannedWords));
    }
    
    // 构建更新数据
    $updateData = [
        'name' => $groupName,
        'desc' => $groupDesc,
        'announcement' => $groupAnnouncement,
        'banned_words' => $bannedWordsArray,
        'member_limit' => $memberLimit
    ];
    
    // 确保tag字段被正确处理，无论是否为空
    $updateData['tag'] = $groupTag;
    
    // 处理自定义群ID
    if (isset($_POST['customGroupId'])) {
        // 验证自定义ID格式
        if (!empty($customGroupId)) {
            if (!preg_match('/^\d{5,10}$/', $customGroupId)) {
                echo json_encode(['success' => false, 'message' => '自定义群ID必须是5-10位数字']);
                exit;
            }
        }
        $updateData['custom_group_id'] = $customGroupId;
    }
    
    // 如果有新的头像，添加到更新数据中
    if ($avatar) {
        $updateData['avatar'] = $avatar;
    }
    
    $success = $groupManager->updateGroup($groupId, $updateData);
    if ($success) {
        // 更新成功后，返回更新后的群聊信息
        $updatedGroup = $groupManager->getGroup($groupId);
        if ($updatedGroup) {
            // 添加在线人数信息
            $updatedGroup['online_count'] = $groupManager->getOnlineUserCount($groupId);
            // 计算活跃用户
            $updatedGroup['today_active_users'] = $groupManager->calculateTodayActiveUsers($groupId);
            $updatedGroup['total_active_users'] = $groupManager->calculateTotalActiveUsers($groupId);
            echo json_encode(['success' => true, 'group' => $updatedGroup]);
        } else {
            echo json_encode(['success' => false, 'message' => '更新成功，但无法获取更新后的群聊信息']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => '更新群聊失败，可能是自定义ID已存在']);
    }
    exit;
}

?>