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

// 检查必要参数
$groupId = $_POST['group_id'] ?? null;
$userId = $_POST['user_id'] ?? null;
$userNickname = $_POST['user_nickname'] ?? null;
$type = $_POST['type'] ?? null;

if (!$groupId || !$userNickname || !$type || !isset($_FILES['file'])) {
    echo json_encode(['success' => false, 'message' => '缺少必要参数']);
    exit;
}

// 如果没有userId，生成一个临时游客ID
if (!$userId) {
    $userId = 'guest_' . uniqid();
}

// 检查发言权限
$group = $groupManager->getGroup($groupId);
$user = $userManager->getUser($userId);

// 检查是否是管理员消息
$isAdminMessage = ($userId === 'admin') || ($_POST['is_admin'] === 'true') || ($_POST['is_admin'] === true) || ($_POST['is_admin'] === 1);

// 只有非管理员才需要检查群聊全体禁言
if (!$isAdminMessage && !$group['allow_speak']) {
    echo json_encode(['success' => false, 'message' => '群聊已关闭全体发言']);
    exit;
}

// 游客用户和管理员默认允许发言，只有普通已注册用户才需要检查allow_speak
if (!$isAdminMessage && $user && !$user['allow_speak']) {
    echo json_encode(['success' => false, 'message' => '您已被禁止发言']);
    exit;
}

// 检查图片上传权限
if ($type === 'image' && (!$group['allow_image_upload'])) {
    echo json_encode(['success' => false, 'message' => '群聊已关闭图片上传']);
    exit;
}

// 图片压缩函数
function compressImage($source, $destination, $quality = 80, $maxWidth = 1200) {
    $info = getimagesize($source);
    $mime = $info['mime'];
    
    // 根据图片类型创建图片资源
    switch ($mime) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $image = imagecreatefrompng($source);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($source);
            break;
        default:
            return false;
    }
    
    // 获取原始尺寸
    $width = imagesx($image);
    $height = imagesy($image);
    
    // 计算缩放后的尺寸，保持比例
    if ($width > $maxWidth) {
        $ratio = $maxWidth / $width;
        $newWidth = $maxWidth;
        $newHeight = $height * $ratio;
    } else {
        $newWidth = $width;
        $newHeight = $height;
    }
    
    // 创建新的图片资源
    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    
    // 保持透明背景（针对PNG和GIF）
    if ($mime === 'image/png' || $mime === 'image/gif') {
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
        imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
    }
    
    // 缩放图片
    imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    
    // 保存压缩后的图片
    $result = false;
    switch ($mime) {
        case 'image/jpeg':
            $result = imagejpeg($newImage, $destination, $quality);
            break;
        case 'image/png':
            $result = imagepng($newImage, $destination, round($quality / 10));
            break;
        case 'image/gif':
            $result = imagegif($newImage, $destination);
            break;
    }
    
    // 释放资源
    imagedestroy($image);
    imagedestroy($newImage);
    
    return $result;
}

// 处理文件上传
$uploadDir = __DIR__ . '/../../uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// 根据文件类型创建子目录
$typeDir = strtolower($type) . 's/';
if (!file_exists($uploadDir . $typeDir)) {
    mkdir($uploadDir . $typeDir, 0777, true);
}

$filename = uniqid() . '_' . basename($_FILES['file']['name']);
$targetPath = $uploadDir . $typeDir . $filename;
$tmpPath = $_FILES['file']['tmp_name'];

$uploadSuccess = false;

// 如果是图片类型，进行压缩处理
if ($type === 'image') {
    $uploadSuccess = compressImage($tmpPath, $targetPath);
}
// 非图片类型直接保存
else {
    $uploadSuccess = move_uploaded_file($tmpPath, $targetPath);
}

if ($uploadSuccess) {
    // 构建文件URL
    $fileUrl = 'uploads/' . $typeDir . $filename;
    
    // 创建消息数据
    $messageData = [
        'group_id' => $groupId,
        'user_id' => $userId,
        'user_nickname' => $userNickname,
        'user_avatar' => $_POST['user_avatar'] ?? null,
        'type' => $type,
        'content' => $fileUrl,
        'is_admin' => $isAdminMessage ? 1 : 0 // 确保转换为整数
    ];
    
    // 如果是语音消息，添加时长信息
    if ($type === 'voice' && isset($_POST['duration'])) {
        $messageData['duration'] = (int)$_POST['duration'];
    }
    
    // 发送消息
    $newMessage = $messageManager->sendMessage($messageData);
    echo json_encode(['success' => true, 'message' => $newMessage]);
} else {
    echo json_encode(['success' => false, 'message' => '文件上传失败']);
}

?>