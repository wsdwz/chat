<?php
session_start();
$config = require_once 'core/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username === $config['admin']['username'] && $password === $config['admin']['password']) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $error = "用户名或密码错误";
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台管理登录</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #F6F7F9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: #fff; padding: 40px 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); width: 100%; max-width: 360px; border: 1px solid #EBEBEB; }
        .login-box h2 { text-align: center; margin-top: 0; margin-bottom: 24px; color: #1A1A1A; font-size: 20px; font-weight: 600; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: #1A1A1A; }
        .form-group input { width: 100%; padding: 10px 12px; border: 1px solid #EBEBEB; border-radius: 4px; outline: none; font-size: 14px; transition: border-color 0.2s; }
        .form-group input:focus { border-color: #07C160; }
        .btn { width: 100%; padding: 12px; background: #07C160; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 15px; font-weight: 500; margin-top: 8px; transition: background 0.2s; }
        .btn:hover { background: #06AD56; }
        .error { color: #FF4D4F; margin-bottom: 16px; text-align: center; font-size: 13px; background: #FFF2F0; padding: 8px; border-radius: 4px; border: 1px solid #FFCCC7; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>系统管理登录</h2>
        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
        <form method="POST">
            <div class="form-group">
                <label>用户名</label>
                <input type="text" name="username" required placeholder="请输入管理员用户名">
            </div>
            <div class="form-group">
                <label>密码</label>
                <input type="password" name="password" required placeholder="请输入管理员密码">
            </div>
            <button type="submit" class="btn">登录</button>
        </form>
    </div>
</body>
</html>