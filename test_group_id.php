<?php

require_once 'core/data_handler.php';

// 测试新ID生成
$groupManager = new GroupManager();

echo "=== 测试1: 生成新的群聊ID ===\n";

// 创建一个测试群聊
$testGroupData = [
    'name' => '测试群聊',
    'desc' => '测试群聊描述',
    'avatar' => null,
    'banned_words' => [],
    'announcement' => '测试公告'
];

$newGroup = $groupManager->createGroup($testGroupData);
echo "新群聊ID: {$newGroup['id']}\n";
echo "ID长度: " . strlen($newGroup['id']) . " 位\n";
echo "是否为5-10位数字: " . (preg_match('/^\d{5,10}$/', $newGroup['id']) ? '是' : '否') . "\n";
echo "自定义ID初始值: " . ($newGroup['custom_group_id'] ?? '未设置') . "\n";

echo "\n=== 测试2: 设置自定义群ID ===\n";

// 测试设置自定义ID
$customId = '123456'; // 6位数字
$updateResult = $groupManager->updateGroup($newGroup['id'], ['custom_group_id' => $customId]);
echo "设置自定义ID '$customId': " . ($updateResult ? '成功' : '失败') . "\n";

// 获取更新后的群聊
$updatedGroup = $groupManager->getGroup($newGroup['id']);
echo "更新后群聊信息:\n";
echo "原始ID: {$updatedGroup['id']}\n";
echo "自定义ID: {$updatedGroup['custom_group_id']}\n";

echo "\n=== 测试3: 测试自定义ID唯一性 ===\n";

// 尝试为另一个群聊设置相同的自定义ID
$testGroupData2 = [
    'name' => '测试群聊2',
    'desc' => '测试群聊描述2',
    'avatar' => null,
    'banned_words' => [],
    'announcement' => '测试公告2'
];

$newGroup2 = $groupManager->createGroup($testGroupData2);
$duplicateResult = $groupManager->updateGroup($newGroup2['id'], ['custom_group_id' => $customId]);
echo "尝试为第二个群聊设置相同的自定义ID: " . ($duplicateResult ? '成功 (错误!)' : '失败 (正确 - 保持唯一性)') . "\n";

echo "\n=== 测试4: 测试通过自定义ID获取群聊 ===\n";

// 通过自定义ID获取群聊
$groupByCustomId = $groupManager->getGroup($customId);
echo "通过自定义ID '$customId' 获取群聊: " . ($groupByCustomId ? '成功' : '失败') . "\n";
if ($groupByCustomId) {
    echo "获取到的群聊原始ID: {$groupByCustomId['id']}\n";
    echo "获取到的群聊自定义ID: {$groupByCustomId['custom_group_id']}\n";
}

echo "\n=== 测试5: 清理测试数据 ===\n";

// 删除测试群聊
$deleteResult1 = $groupManager->deleteGroup($newGroup['id']);
$deleteResult2 = $groupManager->deleteGroup($newGroup2['id']);
echo "删除测试群聊1: " . ($deleteResult1 ? '成功' : '失败') . "\n";
echo "删除测试群聊2: " . ($deleteResult2 ? '成功' : '失败') . "\n";

echo "\n=== 测试完成 ===\n";
?>