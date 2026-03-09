<?php
// apply_admin_audio_fix.php
// 一键修复 admin.php 中写死的提示音域名，将其改为当前访问域名。

$file = 'admin.php';
if (!file_exists($file)) {
    http_response_code(404);
    die('admin.php 不存在，请确认在项目根目录运行此脚本');
}

$content = file_get_contents($file);

$before1 = "adminMessageAudio.src = 'https://lvba3.tyxcu.shop/mp3/xm3143.mp3';";
$before2 = "adminMessageAudio.src = \"https://lvba3.tyxcu.shop/mp3/xm3143.mp3\";";
$after   = "adminMessageAudio.src = window.location.origin + '/mp3/xm3143.mp3';";

$changed = false;
if (strpos($content, $before1) !== false) {
    $content = str_replace($before1, $after, $content);
    $changed = true;
}
if (strpos($content, $before2) !== false) {
    $content = str_replace($before2, $after, $content);
    $changed = true;
}

// 兜底：把所有写死域名替换为当前域名（只替换 https://lvba3.tyxcu.shop 前缀）
if (strpos($content, 'https://lvba3.tyxcu.shop') !== false) {
    // JS 字符串中替换为 window.location.origin
    $content = str_replace('https://lvba3.tyxcu.shop', "' + window.location.origin + '", $content);
    $changed = true;
}

if (!$changed) {
    echo "<meta charset='utf-8'><meta name='viewport' content='width=device-width,initial-scale=1'>";
    echo "<div style='font-family: sans-serif; padding: 24px;'>";
    echo "<h3>未发现需要修复的写死域名</h3>";
    echo "<p>如果你已经修复过，或 admin.php 中使用了不同的域名字符串，此脚本不会做任何改动。</p>";
    echo "<p><a href='admin.php'>返回管理后台</a></p>";
    echo "</div>";
    exit;
}

file_put_contents($file, $content);

echo "<meta charset='utf-8'><meta name='viewport' content='width=device-width,initial-scale=1'>";
echo "<div style='font-family: sans-serif; padding: 24px;'>";
echo "<h3>admin.php 提示音域名修复成功</h3>";
echo "<p>已将提示音改为：<code>window.location.origin + /mp3/xm3143.mp3</code></p>";
echo "<p><a href='admin.php'>进入管理后台</a></p>";
echo "</div>";
