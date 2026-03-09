<?php

// 设置CORS头，允许所有来源
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// 处理OPTIONS请求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../../core/data_handler.php';

$userManager = new UserManager();

// 获取请求数据（支持JSON和表单数据）
$data = null;
if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
    // 从JSON获取数据
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);
} else {
    // 从表单获取数据
    $data = $_POST;
}

// 获取用户ID和昵称
$userId = $data['id'] ?? $data['user_id'] ?? 'user_' . date('YmdHis') . rand(1000, 9999);
$userNickname = $data['nickname'] ?? $data['userNickname'] ?? null;

// 获取现有用户信息
$existingUser = $userManager->getUser($userId);

// 如果没有提供昵称参数，但用户已存在，则使用现有昵称
if (!$userNickname) {
    if ($existingUser) {
        $userNickname = $existingUser['nickname'];
    } else {
        echo json_encode(['success' => false, 'message' => '缺少昵称参数']);
        exit;
    }
}

// 处理头像上传
$avatar = null;
if (isset($_FILES['userAvatar']) && $_FILES['userAvatar']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/../../uploads/avatars/';
    // 确保上传目录存在
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $filename = uniqid() . '_' . basename($_FILES['userAvatar']['name']);
    $targetPath = $uploadDir . $filename;
    $tmpPath = $_FILES['userAvatar']['tmp_name'];
    
    // 检查上传文件的大小和类型
    $fileSize = $_FILES['userAvatar']['size'];
    $fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
    
    // 允许的文件类型
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    
    if (!in_array($fileType, $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => '只允许上传JPG、JPEG、PNG、GIF类型的文件']);
        exit;
    }
    
    // 限制文件大小为5MB
    if ($fileSize > 5 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => '文件大小不能超过5MB']);
        exit;
    }
    
    // 压缩上传的头像图片
    if (compressImage($tmpPath, $targetPath)) {
        $avatar = 'uploads/avatars/' . $filename;
    } else {
        echo json_encode(['success' => false, 'message' => '文件上传失败，请检查服务器权限']);
        exit;
    }
}

// 检查昵称是否重复
$groupManager = new GroupManager();
$joinedGroups = $data['joined_groups'] ?? $existingUser['joined_groups'] ?? [];
$isDuplicate = false;
$duplicateGroup = '';

foreach ($joinedGroups as $groupId) {
    if ($userManager->isNicknameDuplicate($groupId, $userNickname, $userId)) {
        $isDuplicate = true;
        $group = $groupManager->getGroup($groupId);
        $duplicateGroup = $group['name'];
        break;
    }
}

if ($isDuplicate) {
    echo json_encode(['success' => false, 'message' => '该昵称在群聊 "' . $duplicateGroup . '" 中已存在']);
    exit;
}

// 创建或更新用户
$userData = [
    'id' => $userId,
    'nickname' => $userNickname,
    'allow_speak' => true,
    'joined_groups' => $joinedGroups,
    'created_at' => $existingUser['created_at'] ?? date('Y-m-d H:i:s')
];

// 如果有新头像，则更新头像
if ($avatar) {
    $userData['avatar'] = $avatar;
} elseif ($existingUser && $existingUser['avatar']) {
    // 如果没有新头像，但用户已有头像，则使用现有头像
    $userData['avatar'] = $existingUser['avatar'];
} elseif (isset($data['avatar'])) {
    // 如果JSON数据中包含头像，则使用它
    $userData['avatar'] = $data['avatar'];
}

// 保存用户信息
if ($existingUser) {
    // 更新现有用户
    $userManager->updateUser($userId, $userData);
} else {
    // 创建新用户
    $userManager->saveUser($userId, $userData);
}

// 清除该用户的缓存，确保下次请求能获取到最新数据
$cacheDir = __DIR__ . '/../../core/cache';
$cacheKey = md5('get_user_groups_' . $userId);
$cacheFile = $cacheDir . '/' . $cacheKey . '.json';
@unlink($cacheFile);

echo json_encode(['success' => true, 'user' => $userData]);

// 图片压缩函数
function compressImage($source, $destination, $quality = 80, $maxWidth = 200) {
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

?>