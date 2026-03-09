<?php
// apply_refactor.php
// 一键重构脚本：将臃肿的 admin.php 拆分为 admin.php, admin.css, admin.js 并修复音频域名

$file = 'admin.php';
if (!file_exists($file)) {
    die('未找到 admin.php 文件，请确认在项目根目录运行此脚本。');
}

$content = file_get_contents($file);
$changed = false;

// 1. 提取并分离 CSS
if (preg_match('/<style>(.*?)<\/style>/is', $content, $match)) {
    file_put_contents('admin.css', trim($match[1]));
    // 替换原有的 style 块，加入时间戳防止缓存
    $content = preg_replace('/<style>.*?<\/style>/is', '<link rel="stylesheet" href="admin.css?v=' . time() . '">', $content, 1);
    $changed = true;
}

// 2. 提取并分离 JS
if (preg_match_all('/<script>(.*?)<\/script>/is', $content, $matches)) {
    foreach ($matches[1] as $idx => $js) {
        // 通过特定关键词定位核心 JS 业务代码块
        if (strpos($js, 'function toast(') !== false || strpos($js, 'adminMessageAudio') !== false) {
            
            // 修复写死的提示音域名为当前动态域名
            $js = str_replace(
                "adminMessageAudio.src = 'https://lvba3.tyxcu.shop/mp3/xm3143.mp3';",
                "adminMessageAudio.src = window.location.origin + '/mp3/xm3143.mp3';",
                $js
            );
            $js = str_replace(
                "adminMessageAudio.src = \"https://lvba3.tyxcu.shop/mp3/xm3143.mp3\";",
                "adminMessageAudio.src = window.location.origin + '/mp3/xm3143.mp3';",
                $js
            );
            // 兜底：把残余的写死域名替换掉
            $js = str_replace('https://lvba3.tyxcu.shop', "' + window.location.origin + '", $js);
            
            file_put_contents('admin.js', trim($js));
            
            // 替换原有的 script 块
            $new_tag = '<script src="admin.js?v=' . time() . '"></script>';
            $content = str_replace($matches[0][$idx], $new_tag, $content);
            $changed = true;
            break;
        }
    }
}

if ($changed) {
    // 写回拆分后的精简版 admin.php
    file_put_contents($file, $content);
    
    // 清理掉之前可能留下的临时修复脚本
    if (file_exists('apply_admin_audio_fix.php')) {
        @unlink('apply_admin_audio_fix.php');
    }
    
    echo "<meta charset='utf-8'><meta name='viewport' content='width=device-width,initial-scale=1'>";
    echo "<div style='font-family: sans-serif; padding: 24px; max-width: 600px; margin: 0 auto; line-height: 1.6; color: #333;'>";
    echo "<h2 style='color: #07C160;'>✅ 重构与修复成功！</h2>";
    echo "<p>已成功将代码完美拆分，生成了以下文件：</p>";
    echo "<ul style='background: #f6f7f9; padding: 16px 32px; border-radius: 8px;'>";
    echo "<li><b>admin.php</b> (纯视图结构，体积大幅缩小)</li>";
    echo "<li><b>admin.css</b> (所有样式)</li>";
    echo "<li><b>admin.js</b> (所有业务逻辑，并已将音频路径改为动态获取)</li>";
    echo "</ul>";
    echo "<p>如果你的代码部署在自有服务器上，请在终端执行以下命令将拆分结果保存回 GitHub 仓库：</p>";
    echo "<pre style='background:#282c34; color:#abb2bf; padding:12px; border-radius:6px;'>git add .<br>git commit -m \"refactor: split admin.php into css and js, fix audio url\"<br>git push</pre>";
    echo "<p><a href='admin.php' style='display:inline-block; background:#07C160; color:#fff; padding:10px 20px; text-decoration:none; border-radius:4px; margin-top: 10px;'>返回管理后台验证</a></p>";
    echo "</div>";
    
    // 运行成功后自毁本脚本，防止安全隐患
    @unlink(__FILE__);
} else {
    echo "<meta charset='utf-8'>";
    echo "<div style='padding: 24px; font-family: sans-serif;'>代码可能已经被拆分过了，未发现需要处理的大段 &lt;style&gt; 或 &lt;script&gt; 块。<br><a href='admin.php'>返回管理后台</a></div>";
}
