<?php
header('Content-Type: application/json');

// 测试转发消息到群聊2
$payload = [
    'group_id' => '2',
    'user_id' => 'test_user',
    'user_nickname' => '测试用户',
    'user_avatar' => 'https://picsum.photos/id/1005/60/60',
    'type' => 'history',
    'content' => json_encode([
        'title' => '测试转发',
        'items' => [
            ['from' => '测试用户', 'text' => '测试消息1'],
            ['from' => '测试用户', 'text' => '测试消息2']
        ]
    ])
];

// 发送请求
$ch = curl_init('http://localhost/html/api/chat/send_message.php');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo json_encode(['error' => $error]);
} else {
    echo $response;
}
?>