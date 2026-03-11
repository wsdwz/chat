<?php
/**
 * 管理后台新版自动更新脚本
 * 使用方法：在浏览器访问这个文件，即可从 GitHub 仓库拉取最新的 admin.html、admin.css、admin.js
 */

// 要更新的文件列表
$files = [
    'admin.html' => 'https://raw.githubusercontent.com/wsdwz/chat/main/admin.html',
    'admin.css' => 'https://raw.githubusercontent.com/wsdwz/chat/main/admin.css',
    'admin.js' => 'https://raw.githubusercontent.com/wsdwz/chat/main/admin.js'
];

// 国内加速代理前缀（多个备用）
$proxyPrefixes = [
    "https://ghproxy.net/",
    "https://mirror.ghproxy.com/",
    "https://ghp.ci/",
    ""  // 直连
];

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>管理后台更新工具</title>";
echo "<style>
body{font-family:Arial,sans-serif;max-width:900px;margin:50px auto;padding:20px;background:#f5f5f5;}
.box{background:white;padding:20px;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);margin-bottom:20px;}
.success{color:#28a745;font-weight:bold;}
.error{color:#dc3545;font-weight:bold;}
.info{color:#007bff;}
.warning{color:#ff9500;}
table{width:100%;border-collapse:collapse;margin-top:10px;}
th,td{padding:10px;text-align:left;border-bottom:1px solid #dee2e6;}
th{background:#f8f9fa;font-weight:600;}
.status{display:inline-block;padding:4px 12px;border-radius:12px;font-size:12px;}
.status-success{background:#d4edda;color:#155724;}
.status-error{background:#f8d7da;color:#721c24;}
.status-pending{background:#fff3cd;color:#856404;}
code{background:#f8f9fa;padding:2px 6px;border-radius:3px;font-size:13px;}
.btn{display:inline-block;padding:10px 20px;background:#007bff;color:white;text-decoration:none;border-radius:4px;margin-top:10px;}
.btn:hover{background:#0056b3;}
</style></head><body>";

echo "<div class='box'>";
echo "<h1>🔄 管理后台自动更新工具 v2.0</h1>";
echo "<p class='info'>从 GitHub 仓库拉取最新的 admin.html、admin.css、admin.js</p>";
echo "</div>";

// 开始更新
echo "<div class='box'>";
echo "<h2>📦 文件更新进度</h2>";
echo "<table>";
echo "<tr><th>文件名</th><th>状态</th><th>大小</th><th>详情</th></tr>";

$results = [];
$successCount = 0;
$totalFiles = count($files);

foreach ($files as $filename => $rawUrl) {
    $result = [
        'filename' => $filename,
        'success' => false,
        'size' => 0,
        'message' => '',
        'backup' => false
    ];
    
    // 尝试不同代理
    $content = false;
    $usedProxy = '';
    
    foreach ($proxyPrefixes as $prefix) {
        $url = $prefix . $rawUrl;
        
        $context = stream_context_create([
            'http' => [
                'timeout' => 30,
                'header' => "User-Agent: Mozilla/5.0\\r\\n"
            ]
        ]);
        
        $content = @file_get_contents($url, false, $context);
        
        if ($content !== false && strlen($content) > 100) {
            $usedProxy = $prefix ? $prefix : '直连';
            break;
        }
    }
    
    if ($content === false || strlen($content) < 100) {
        $result['message'] = '拉取失败';
        echo "<tr>";
        echo "<td><code>{$filename}</code></td>";
        echo "<td><span class='status status-error'>❌ 失败</span></td>";
        echo "<td>-</td>";
        echo "<td class='error'>所有代理均无法拉取</td>";
        echo "</tr>";
        $results[] = $result;
        continue;
    }
    
    // 备份现有文件
    $targetPath = __DIR__ . '/' . $filename;
    if (file_exists($targetPath)) {
        $backupPath = __DIR__ . '/' . $filename . '.backup.' . date('YmdHis');
        if (copy($targetPath, $backupPath)) {
            $result['backup'] = true;
        }
    }
    
    // 写入新文件
    if (file_put_contents($targetPath, $content) !== false) {
        $result['success'] = true;
        $result['size'] = strlen($content);
        $result['message'] = $usedProxy;
        $successCount++;
        
        echo "<tr>";
        echo "<td><code>{$filename}</code></td>";
        echo "<td><span class='status status-success'>✅ 成功</span></td>";
        echo "<td>" . number_format($result['size']) . " 字节</td>";
        echo "<td class='success'>来源: {$usedProxy}" . ($result['backup'] ? ' | 已备份' : '') . "</td>";
        echo "</tr>";
    } else {
        $result['message'] = '写入失败（权限不足）';
        echo "<tr>";
        echo "<td><code>{$filename}</code></td>";
        echo "<td><span class='status status-error'>❌ 失败</span></td>";
        echo "<td>" . number_format(strlen($content)) . " 字节</td>";
        echo "<td class='error'>拉取成功但写入失败，请检查文件夹权限</td>";
        echo "</tr>";
    }
    
    $results[] = $result;
}

echo "</table>";
echo "</div>";

// 显示总结
echo "<div class='box'>";
echo "<h2>📊 更新总结</h2>";

if ($successCount === $totalFiles) {
    echo "<p class='success'>✨ 所有文件更新成功！({$successCount}/{$totalFiles})</p>";
    echo "<p>更新时间: <strong>" . date('Y-m-d H:i:s') . "</strong></p>";
    echo "<hr>";
    echo "<p class='info'><strong>下一步：</strong></p>";
    echo "<ul>";
    echo "<li>访问新版管理后台：<a href='admin.html' class='btn'>打开 admin.html</a></li>";
    echo "<li>旧版后台仍可使用：<a href='admin.php'>admin.php</a></li>";
    echo "</ul>";
} elseif ($successCount > 0) {
    echo "<p class='warning'>⚠️ 部分文件更新成功 ({$successCount}/{$totalFiles})</p>";
    echo "<p>请检查失败的文件并重试</p>";
} else {
    echo "<p class='error'>❌ 所有文件更新失败</p>";
    echo "<p>可能原因：</p>";
    echo "<ul>";
    echo "<li>网络连接问题（无法访问 GitHub）</li>";
    echo "<li>文件夹权限不足（无法写入文件）</li>";
    echo "<li>防火墙拦截</li>";
    echo "</ul>";
    echo "<p>建议：尝试手动从 <a href='https://github.com/wsdwz/chat' target='_blank'>GitHub 仓库</a> 下载文件并上传</p>";
}

echo "</div>";

// 清理旧备份
echo "<div class='box'>";
echo "<h2>🗑️ 备份文件管理</h2>";
$allBackups = array_merge(
    glob(__DIR__ . '/admin.html.backup.*'),
    glob(__DIR__ . '/admin.css.backup.*'),
    glob(__DIR__ . '/admin.js.backup.*')
);

if (count($allBackups) > 0) {
    echo "<p class='info'>当前备份文件数量: " . count($allBackups) . "</p>";
    
    // 保留最近 3 个备份
    if (count($allBackups) > 9) {
        rsort($allBackups);
        $toDelete = array_slice($allBackups, 9);
        $deleted = 0;
        foreach ($toDelete as $file) {
            if (unlink($file)) {
                $deleted++;
            }
        }
        if ($deleted > 0) {
            echo "<p class='success'>已清理 {$deleted} 个旧备份文件</p>";
        }
    }
} else {
    echo "<p class='info'>暂无备份文件</p>";
}
echo "</div>";

// 使用说明
echo "<div class='box' style='background:#f8f9fa;'>";
echo "<h2>📖 使用说明</h2>";
echo "<ul>";
echo "<li><strong>新版后台</strong>（推荐）：访问 <code>admin.html</code>，界面更现代，功能更完善</li>";
echo "<li><strong>旧版后台</strong>：访问 <code>admin.php</code>，所有功能集成在一个文件</li>";
echo "<li><strong>自动备份</strong>：更新前会自动备份现有文件，保留最近 3 次备份</li>";
echo "<li><strong>恢复方法</strong>：如需恢复，将 <code>.backup.*</code> 文件改回原名即可</li>";
echo "</ul>";
echo "<hr>";
echo "<p style='text-align:center;color:#666;'>仓库地址：<a href='https://github.com/wsdwz/chat' target='_blank'>github.com/wsdwz/chat</a></p>";
echo "</div>";

echo "</body></html>";
?>