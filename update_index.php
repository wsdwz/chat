<?php
/**
 * index.php 自动更新脚本
 * 使用方法：在浏览器访问这个文件，即可从 GitHub 仓库拉取最新的 index.php
 */

// 1. 使用国内加速代理拉取你仓库里完好的原版 index.php
// 多个代理地址，如果第一个失败会自动尝试下一个
$proxies = [
    "https://ghproxy.net/https://raw.githubusercontent.com/wsdwz/chat/main/index.php",
    "https://mirror.ghproxy.com/https://raw.githubusercontent.com/wsdwz/chat/main/index.php",
    "https://ghp.ci/https://raw.githubusercontent.com/wsdwz/chat/main/index.php",
    "https://raw.githubusercontent.com/wsdwz/chat/main/index.php"  // 备用：直接拉取
];

// 2. 目标文件路径
$targetFile = __DIR__ . '/index.php';

// 3. 备份文件路径
$backupFile = __DIR__ . '/index.php.backup.' . date('YmdHis');

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>index.php 更新工具</title>";
echo "<style>body{font-family:Arial,sans-serif;max-width:800px;margin:50px auto;padding:20px;background:#f5f5f5;}";
echo ".box{background:white;padding:20px;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);margin-bottom:20px;}";
echo ".success{color:#28a745;font-weight:bold;}.error{color:#dc3545;font-weight:bold;}.info{color:#007bff;}";
echo "pre{background:#f8f9fa;padding:10px;border-radius:4px;overflow-x:auto;}</style></head><body>";

echo "<div class='box'><h1>🔄 index.php 自动更新工具</h1>";
echo "<p class='info'>从 GitHub 仓库拉取最新的 index.php 文件</p></div>";

echo "<div class='box'>";

// 4. 尝试从多个代理拉取文件
echo "<h3>步骤 1：拉取最新文件</h3>";
$content = false;
$usedProxy = '';

foreach ($proxies as $url) {
    echo "<p>尝试从: <code>" . htmlspecialchars($url) . "</code></p>";
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 30,
            'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n"
        ]
    ]);
    
    $content = @file_get_contents($url, false, $context);
    
    if ($content !== false && strlen($content) > 1000) {
        echo "<p class='success'>✅ 拉取成功！文件大小: " . number_format(strlen($content)) . " 字节</p>";
        $usedProxy = $url;
        break;
    } else {
        echo "<p class='error'>❌ 拉取失败，尝试下一个...</p>";
    }
}

if ($content === false || strlen($content) < 1000) {
    echo "<p class='error'>❌ 错误：所有代理都无法拉取文件，请检查网络连接或稍后再试。</p>";
    echo "</div></body></html>";
    exit;
}

echo "</div>";

// 5. 备份现有文件
echo "<div class='box'><h3>步骤 2：备份现有文件</h3>";
if (file_exists($targetFile)) {
    if (copy($targetFile, $backupFile)) {
        echo "<p class='success'>✅ 备份成功: <code>" . basename($backupFile) . "</code></p>";
    } else {
        echo "<p class='error'>⚠️ 备份失败，但将继续更新</p>";
    }
} else {
    echo "<p class='info'>ℹ️ 目标文件不存在，将创建新文件</p>";
}
echo "</div>";

// 6. 写入新文件
echo "<div class='box'><h3>步骤 3：更新文件</h3>";
if (file_put_contents($targetFile, $content) !== false) {
    echo "<p class='success'>✅ 更新成功！</p>";
    echo "<p>新文件大小: <strong>" . number_format(strlen($content)) . "</strong> 字节</p>";
    echo "<p>更新时间: <strong>" . date('Y-m-d H:i:s') . "</strong></p>";
    
    // 显示文件信息
    $fileInfo = stat($targetFile);
    echo "<p>文件权限: <code>" . substr(sprintf('%o', $fileInfo['mode']), -4) . "</code></p>";
    
    echo "<hr>";
    echo "<p class='success'>✨ 操作完成！你现在可以刷新页面查看最新的 index.php</p>";
    echo "<p><a href='index.php' style='color:#007bff;text-decoration:none;'>➡️ 点击这里访问 index.php</a></p>";
    
} else {
    echo "<p class='error'>❌ 错误：无法写入文件！</p>";
    echo "<p>请检查文件夹权限，确保 PHP 有写入权限。</p>";
    
    // 如果写入失败，显示手动更新方法
    echo "<hr><h4>手动更新方法：</h4>";
    echo "<p>1. 复制下面的内容：</p>";
    echo "<p><button onclick=\"copyContent()\" style=\"padding:8px 16px;background:#007bff;color:white;border:none;border-radius:4px;cursor:pointer;\">复制文件内容</button></p>";
    echo "<textarea id='fileContent' style='width:100%;height:200px;font-family:monospace;font-size:12px;'>" . htmlspecialchars($content) . "</textarea>";
    echo "<script>function copyContent(){var t=document.getElementById('fileContent');t.select();document.execCommand('copy');alert('复制成功！');}</script>";
    echo "<p>2. 手动替换服务器上的 index.php 文件</p>";
}

echo "</div>";

// 7. 清理过期备份（保留最近 5 个）
echo "<div class='box'><h3>步骤 4：清理旧备份</h3>";
$backupFiles = glob(__DIR__ . '/index.php.backup.*');
if (count($backupFiles) > 5) {
    rsort($backupFiles);
    $filesToDelete = array_slice($backupFiles, 5);
    foreach ($filesToDelete as $file) {
        if (unlink($file)) {
            echo "<p>删除旧备份: " . basename($file) . "</p>";
        }
    }
    echo "<p class='info'>ℹ️ 保留最近 5 个备份文件</p>";
} else {
    echo "<p class='info'>ℹ️ 当前备份文件数量: " . count($backupFiles) . "</p>";
}
echo "</div>";

echo "<div class='box' style='background:#f8f9fa;'>";
echo "<h3>📝 使用说明</h3>";
echo "<ul>";
echo "<li>这个脚本会自动从 GitHub 仓库拉取最新的 index.php</li>";
echo "<li>更新前会自动备份现有文件</li>";
echo "<li>如果需要恢复，可以使用备份文件还原</li>";
echo "<li><strong>建议：</strong>更新后测试功能是否正常</li>";
echo "</ul>";
echo "<hr>";
echo "<p style='text-align:center;color:#666;font-size:14px;'>🛡️ 仓库地址: <a href='https://github.com/wsdwz/chat' target='_blank'>https://github.com/wsdwz/chat</a></p>";
echo "</div>";

echo "</body></html>";
?>