<?php

// 禁止输出PHP错误信息
ini_set('display_errors', 0);
error_reporting(0);

// 设置CORS头，允许所有来源
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// 处理OPTIONS请求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    require_once '../../core/data_handler.php';

$messageManager = new MessageManager();
$groupId = $_GET['group_id'] ?? null;
$groupIds = $_GET['group_ids'] ?? null;
$lastTimestamp = $_GET['last_timestamp'] ?? null;
$lastTimestamps = $_GET['last_timestamps'] ?? null;

if ($groupIds) {
    // 处理批量获取多个群的消息
    $groupIdsArray = explode(',', $groupIds);
    $lastTimestampsArray = $lastTimestamps ? explode(',', $lastTimestamps) : [];
    
    $result = [];
    foreach ($groupIdsArray as $index => $gid) {
        $lt = $lastTimestampsArray[$index] ?? null;
        $messages = $messageManager->getGroupMessages($gid, $lt);
        $result[$gid] = $messages;
    }
    
    echo json_encode($result);
} elseif ($groupId) {
    // 获取单个群聊消息
    $messages = $messageManager->getGroupMessages($groupId, $lastTimestamp);
    echo json_encode($messages);
} else {
    echo json_encode(['success' => false, 'message' => '缺少群聊ID']);
    exit;
}

} catch (Exception $e) {
    // 返回错误信息
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

?>