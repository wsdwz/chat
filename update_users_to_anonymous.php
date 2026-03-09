<?php
// 读取用户数据
$usersFile = 'core/data/users.json';
$users = json_decode(file_get_contents($usersFile), true);

// 遍历所有用户并更新昵称
foreach ($users as &$user) {
    // 跳过管理员账号
    if ($user['id'] === 'admin') {
        continue;
    }
    
    // 从用户ID中提取数字部分
    $userId = $user['id'];
    $numericPart = preg_replace('/[^0-9]/', '', $userId);
    
    // 如果没有数字部分，使用随机数字
    if (empty($numericPart)) {
        $randomChars = substr(md5($userId), 0, 6);
    } else {
        // 使用用户ID的最后6个字符
        $randomChars = substr($numericPart, -6);
    }
    
    // 更新昵称为匿名格式
    $user['nickname'] = '匿名' . $randomChars;
}

// 保存更新后的用户数据
file_put_contents($usersFile, json_encode($users, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

echo "所有用户已成功更新为匿名格式！";
?>