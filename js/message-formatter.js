/**
 * 消息格式化工具
 * 用于将不同类型的消息格式化为友好的显示文本
 */

/**
 * 格式化最新消息的显示
 * @param {Object} message - 消息对象
 * @returns {String} - 格式化后的消息文本
 */
function formatLatestMessage(message) {
    if (!message) return '暂无消息';
    
    // 检查是否是聊天记录类型
    if (message.type === 'history') {
        try {
            const historyData = JSON.parse(message.content);
            return '[聊天记录] ' + (historyData.title || '群聊的聊天记录');
        } catch (e) {
            console.error('解析聊天记录失败:', e);
            return '[聊天记录]';
        }
    }
    
    // 处理其他类型
    switch (message.type) {
        case 'text':
            // 文本消息：显示内容，但限制长度
            return message.content ? 
                (message.content.length > 50 ? message.content.substring(0, 50) + '...' : message.content) : 
                '文本消息';
        case 'image':
            return '[图片]';
        case 'video':
            return '[视频]';
        case 'voice':
            return '[语音]';
        case 'file':
            return '[文件]';
        case 'card':
            return '[分享卡片]';
        default:
            // 未知类型，尝试显示内容
            if (message.content) {
                try {
                    // 尝试判断是否为JSON
                    const parsed = JSON.parse(message.content);
                    return '[特殊消息]';
                } catch (e) {
                    // 不是JSON，直接返回内容
                    return message.content.length > 50 ? 
                        message.content.substring(0, 50) + '...' : 
                        message.content;
                }
            }
            return '未知消息类型';
    }
}

/**
 * 格式化消息内容用于显示在聊天列表中
 * @param {Object} message - 消息对象
 * @returns {String} - 格式化后的HTML内容
 */
function formatMessagePreview(message) {
    if (!message) return '<span style="color: #999;">暂无消息</span>';
    
    const formattedText = formatLatestMessage(message);
    
    // 根据消息类型添加不同的图标
    let icon = '';
    switch (message.type) {
        case 'history':
            icon = '📋';
            break;
        case 'image':
            icon = '🖼️';
            break;
        case 'video':
            icon = '🎬';
            break;
        case 'voice':
            icon = '🎤';
            break;
        case 'file':
            icon = '📎';
            break;
        case 'card':
            icon = '🎴';
            break;
        default:
            icon = '';
    }
    
    return `<span style="color: #999;">${icon} ${formattedText}</span>`;
}

// 导出函数（如果使用模块化）
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        formatLatestMessage,
        formatMessagePreview
    };
}