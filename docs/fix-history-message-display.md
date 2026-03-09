# 修复：群聊列表中聊天记录显示为JSON的问题

## 问题描述

在 `admin.php` 的群聊列表中，当最新消息为“聊天记录”类型时，直接显示了JSON字符串，导致页面不美观且难以阅读。

### 示例
```json
// 问题显示：
{"type":"history","title":"群聊的聊天记录","messages":[...]}

// 期望显示：
[聊天记录] 群聊的聊天记录
```

## 解决方案

### 1. 使用消息格式化工具

我们创建了 `js/message-formatter.js` 文件，包含两个格式化函数：

#### `formatLatestMessage(message)`
将消息对象格式化为纯文本。

```javascript
// 示例使用
const message = {
    type: 'history',
    content: '{"title":"群聊的聊天记录","messages":[...]}'
};

const text = formatLatestMessage(message);
// 返回："[聊天记录] 群聊的聊天记录"
```

#### `formatMessagePreview(message)`
将消息对象格式化为HTML，带图标。

```javascript
// 示例使用
const html = formatMessagePreview(message);
// 返回："<span style=\"color: #999;\">📋 [聊天记录] 群聊的聊天记录</span>"
```

### 2. 在 admin.php 中引入

在 `admin.php` 的 `<head>` 标签中添加：

```html
<!-- 在 </head> 之前添加 -->
<script src="js/message-formatter.js"></script>
```

### 3. 修改群聊列表渲染代码

找到 `admin.php` 中渲染群聊信息的代码，通常在 `loadGroupInfo()` 函数中。

#### 修改前：
```javascript
<div class="group-info-desc">${group.latest_message?.content || '暂无消息'}</div>
```

#### 修改后：
```javascript
<div class="group-info-desc">${formatLatestMessage(group.latest_message)}</div>
```

或者使用带图标的版本：
```javascript
<div class="group-info-desc">${formatMessagePreview(group.latest_message)}</div>
```

## 支持的消息类型

| 消息类型 | 显示效果 |
|----------|----------|
| `text` | 文本内容（超长截断） |
| `history` | `[聊天记录] 标题` |
| `image` | `[图片]` |
| `video` | `[视频]` |
| `voice` | `[语音]` |
| `file` | `[文件]` |
| `card` | `[分享卡片]` |

## 完整修改示例

### 步骤1：在 admin.php 中引入脚本

```html
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <!-- ... 其他 head 内容 ... -->
    
    <!-- 消息格式化工具 -->
    <script src="js/message-formatter.js"></script>
</head>
```

### 步骤2：修改渲染函数

找到 `loadGroupInfo()` 或类似的函数，修改如下：

```javascript
function loadGroupInfo() {
    // ... 其他代码 ...
    
    fetch('api/admin/groups.php')
        .then(response => response.json())
        .then(groups => {
            const html = groups.map(group => `
                <div class="group-info-card" onclick="openGroupDetail('${group.id}')">
                    <div class="group-info-header">
                        <img src="${group.avatar}" class="group-info-avatar">
                        <div style="flex: 1; min-width: 0;">
                            <div class="group-info-name">${group.name}</div>
                            <!-- 使用 formatLatestMessage 函数 -->
                            <div class="group-info-desc">${formatLatestMessage(group.latest_message)}</div>
                        </div>
                    </div>
                    <!-- ... 其他内容 ... -->
                </div>
            `).join('');
            
            document.getElementById('groupInfoList').innerHTML = html;
        });
}
```

## 测试

1. 刷新 `admin.php` 页面
2. 查看群聊列表，确认聊天记录消息显示为 `[聊天记录] 标题` 而不是JSON
3. 测试其他消息类型（图片、视频等）是否正常显示

## 注意事项

1. **确保文件路径正确**：`js/message-formatter.js` 必须可访问
2. **浏览器缓存**：修改后可能需要强制刷新（Ctrl+F5）
3. **全局应用**：建议在所有显示最新消息的地方都使用此函数

## 相关文件

- `js/message-formatter.js` - 消息格式化工具
- `admin.php` - 管理后台页面
- `api/admin/groups.php` - 群聊数据API

## 更新历史

- **2026-03-09**: 初始版本，修复聊天记录显示JSON问题