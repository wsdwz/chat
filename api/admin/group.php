<?php
// 临时开启错误日志，写入到文件
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/delete_error.log');
error_reporting(E_ALL);
ini_set('display_errors', 0);

// 清空输出缓冲区
if (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: application/json; charset=utf-8');

// 加载核心类
require_once __DIR__ . '/../../core/sqlite_manager.php';

// 只处理 DELETE 请求
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    echo json_encode(['success' => false, 'message' => '不支持的请求方法']);
    exit;
}

$groupId = $_GET['group_id'] ?? '';

if (empty($groupId)) {
    echo json_encode(['success' => false, 'message' => '缺少群聊ID']);
    exit;
}

try {
    $db = new SQLiteManager();
    
    // 1. 先查询该群的所有消息，获取图片/视频路径
    $messages = $db->load('chat_messages', ['group_id' => $groupId]);
    
    // 2. 删除服务器上的图片/视频文件
    $deletedFiles = 0;
    foreach ($messages as $msg) {
        if (in_array($msg['type'], ['image', 'video'])) {
            $filePath = $msg['content'];
            
            // 如果是相对路径（/uploads/xxx），转换为绝对路径
            if (strpos($filePath, '/uploads/') === 0) {
                $filePath = __DIR__ . '/../../' . ltrim($filePath, '/');
            }
            // 如果是 Base64 或外链，跳过
            elseif (strpos($filePath, 'http') === 0 || strpos($filePath, 'data:') === 0) {
                continue;
            }
            
            // 删除文件
            if (file_exists($filePath)) {
                unlink($filePath);
                $deletedFiles++;
            }
        }
    }
    
    // 3. 删除该群的所有聊天记录
    // 使用自定义SQL删除消息
    $db->execute("DELETE FROM chat_messages WHERE group_id = ?", [$groupId]);
    $deletedMessages = count($messages);
    
    // 4. 删除群聊本身
    $groupDeleted = $db->delete('chat_groups', $groupId);
    
    if ($groupDeleted) {
        echo json_encode([
            'success' => true, 
            'message' => "群聊已删除，同时清理了 {$deletedMessages} 条消息和 {$deletedFiles} 个媒体文件"
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => '群聊不存在或已被删除']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => '删除失败: ' . $e->getMessage()]);
}
?>
