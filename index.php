<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>聊天室</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            /* 替换为 QQ 经典蓝 */
            --primary-color: #0089FF; 
            --primary-light: rgba(0, 137, 255, 0.1);
            --primary-dark: #0077E6;
            --secondary-color: #5AC8FA;
            --text-color: #2C3E50;
            --text-secondary: #64748B;
            --text-tertiary: #94A3B8;
            --background-color: #F5F6FA; /* QQ聊天背景色 */
            --card-background: #FFFFFF;
            --border-color: #E2E8F0;
            --border-light: #F1F5F9;
            --success-color: #07C160;
            --danger-color: #FF5252;
            --warning-color: #FFB020;
            
            /* QQ气泡背景 */
            --own-message-bg: #0089FF;
            --other-message-bg: #FFFFFF;
            
            --transition-fast: 0.15s ease;
            --transition-normal: 0.3s ease;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.5;
            overflow: hidden;
        }
        
        .chat-container {
            max-width: 600px;
            margin: 0 auto;
            height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: #F5F6FA;
        }
        
        /* 顶部导航栏 */
        .chat-header {
            background-color: #FFFFFF;
            padding: 12px 10px;
            display: flex;
            align-items: center;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .back-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--text-color);
            padding: 4px;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .header-info {
            flex: 1;
            min-width: 0;
            text-align: center;
        }
        
        .chat-title {
            font-size: 17px;
            font-weight: 500;
            color: var(--text-color);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* 消息区域 */
        .messages-area {
            flex: 1;
            padding: 10px;
            padding-top: 20px;
            padding-bottom: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 16px;
            overflow-x: hidden;
            background-color: #F5F6FA;
            -webkit-overflow-scrolling: touch;
        }
        
        /* 消息气泡 */
        .message {
            display: flex;
            position: relative;
            animation: messageSlideIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        @keyframes messageSlideIn {
            from { opacity: 0; transform: translateY(10px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        
        .message.own { justify-content: flex-end; }
        .message.other { justify-content: flex-start; }
        
        /* QQ头像全是正圆 */
        .message-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50% !important;
            object-fit: cover;
            flex-shrink: 0;
            margin: 0 10px 0 0;
        }
        .message.own .message-avatar {
            order: 2;
            margin: 0 0 0 10px;
        }
        
        .message-content-wrapper {
            max-width: 80%;
            display: flex;
            flex-direction: column;
        }
        .message.own .message-content-wrapper { align-items: flex-end; }
        .message.other .message-content-wrapper { align-items: flex-start; }
        
        /* QQ昵称颜色偏灰 */
        .message-sender {
            font-size: 12px;
            color: #878B99 !important;
            margin-bottom: 4px;
        }
        
        /* QQ 气泡圆角 */
        .message-content {
            max-width: 100%;
            padding: 10px 16px;
            word-wrap: break-word;
            line-height: 1.5;
            font-size: 15px;
        }
        
        /* 别人发的消息：白底、右上右下左下大圆角、左上小圆角 */
        .message.other .message-content {
            background: #FFFFFF !important;
            color: #000000 !important;
            border-radius: 16px 16px 16px 4px !important;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        }
        
        /* 自己发的消息：蓝底、左上左下右上大圆角、右下小圆角 */
        .message.own .message-content {
            background: var(--own-message-bg) !important;
            color: #FFFFFF !important;
            border-radius: 16px 16px 4px 16px !important;
        }
        
        /* @全体成员 高亮 */
        .mention-all-tag {
            display: inline-block;
            background: rgba(255, 140, 0, 0.1);
            color: #FF8C00;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: bold;
            margin-right: 4px;
        }

        /* ------------------ QQ风格卡片 (结构化消息) ------------------ */
        .message-card {
            background: #FFFFFF !important;
            border-radius: 16px !important;
            width: 240px !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04) !important;
            padding: 0 !important;
            display: flex; flex-direction: column;
            overflow: hidden;
            text-decoration: none;
        }
        .message.own .message-content:has(.message-card), 
        .message.other .message-content:has(.message-card) { 
            background: transparent !important; padding: 0 !important; box-shadow: none !important; border-radius: 0 !important;
        }
        .message-card-body {
            padding: 12px 16px; display: flex; gap: 10px; text-align: left; align-items: flex-start;
        }
        .message-card-info {
            flex: 1; display: flex; flex-direction: column; min-width: 0;
        }
        .message-card-title {
            font-size: 15px; font-weight: bold; color: #000; line-height: 1.4;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }
        .message-card-desc {
            font-size: 13px; color: #878B99; margin-top: 4px;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }
        .message-card-thumb {
            width: 52px; height: 52px; border-radius: 8px; object-fit: cover; flex-shrink: 0; background-color: #F3F4F6;
        }
        .message-card-footer {
            padding: 8px 16px; font-size: 11px; color: #878B99;
            border-top: 0.5px solid #F0F0F0; text-align: left; display: flex; align-items: center;
        }

        /* ------------------ QQ风格 合并转发卡片 ------------------ */
        .message-history-card {
            background: #FFFFFF !important;
            border-radius: 16px !important;
            width: 250px !important;
            padding: 16px !important;
            text-align: left;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04) !important;
            cursor: pointer;
        }
        .message.own .message-content:has(.message-history-card), 
        .message.other .message-content:has(.message-history-card) { 
            background: transparent !important; padding: 0 !important; box-shadow: none !important; border-radius: 0 !important;
        }
        .message-history-title {
            font-size: 15px; font-weight: 500; color: #000000; margin-bottom: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .message-history-list {
            display: flex; flex-direction: column; gap: 4px; margin-bottom: 10px;
        }
        .message-history-item {
            font-size: 13px; color: #878B99; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.4;
        }
        .message-history-footer {
            font-size: 12px; color: #878B99; padding-top: 10px; border-top: 0.5px solid #F0F0F0;
        }

        /* ------------------ QQ风格 嵌套预览模态框 ------------------ */
        .qq-history-modal {
            display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: #F5F6FA; z-index: 2000; flex-direction: column;
            transform: translateX(100%); transition: transform 0.3s cubic-bezier(0.25, 1, 0.5, 1);
        }
        .qq-history-modal.active {
            display: flex; transform: translateX(0);
        }
        .qq-header {
            height: 50px; min-height: 50px; background: #FFFFFF; display: flex; align-items: center; justify-content: space-between;
            padding: env(safe-area-inset-top) 16px 0; border-bottom: 0.5px solid #EBEBEB;
        }
        .qq-close {
            font-size: 16px; color: #0089FF; background: transparent; border: none; cursor: pointer; display:flex; align-items:center;
        }
        .qq-close::before {
            content: ''; width: 10px; height: 10px; border-left: 2px solid #0089FF; border-bottom: 2px solid #0089FF; transform: rotate(45deg); margin-right: 4px;
        }
        .qq-title {
            font-size: 17px; font-weight: 500; position: absolute; left: 50%; transform: translateX(-50%);
        }
        .qq-body {
            flex: 1; overflow-y: auto; -webkit-overflow-scrolling: touch; padding-bottom: 30px; background: #FFFFFF;
        }
        .wx-item {
            display: flex; padding: 16px 20px; border-bottom: 0.5px solid rgba(0,0,0,0.04);
        }
        .wx-avatar {
            width: 42px; height: 42px; border-radius: 50%; margin-right: 12px; object-fit: cover;
        }
        .wx-content {
            flex: 1; min-width: 0;
        }
        .wx-name {
            font-size: 14px; color: #878B99; margin-bottom: 4px;
        }
        .wx-text {
            font-size: 16px; color: #111111; line-height: 1.5; word-wrap: break-word;
        }

        /* ------------------ QQ风格 转发群聊选择器 ------------------ */
        .qq-group-selector-modal {
            display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: #F5F6FA; z-index: 2005; flex-direction: column;
            transform: translateY(100%); transition: transform 0.3s cubic-bezier(0.25, 1, 0.5, 1);
        }
        .qq-group-selector-modal.active {
            display: flex; transform: translateY(0);
        }
        .qq-group-list {
            flex: 1; overflow-y: auto; padding: 10px;
        }
        .qq-group-item {
            background: #FFFFFF; border-radius: 12px; padding: 16px; margin-bottom: 10px;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 1px 4px rgba(0,0,0,0.03); cursor: pointer;
        }
        .qq-group-item:active {
            background: #F0F0F0;
        }
        .qq-group-info {
            font-size: 16px; color: #000; font-weight: 500;
        }
        .qq-group-item.is-current::after {
            content: '当前群聊'; font-size: 12px; color: #0089FF; background: rgba(0,137,255,0.1);
            padding: 2px 8px; border-radius: 4px;
        }

        /* 图片与视频 */
        .message-image {
            max-width: 100%; max-height: 200px; border-radius: 8px; margin: 4px 0; cursor: pointer; object-fit: cover;
        }
        .message-video {
            max-width: 100%; max-height: 200px; border-radius: 12px; margin: 4px 0; cursor: pointer; object-fit: cover; background: #000;
        }
        .message-content:has(.message-image), .message-content:has(.message-video) {
            padding: 4px !important; background: transparent !important;
        }
        
        /* 快捷功能与输入区 */
        .input-area {
            background: white; position: sticky; bottom: 0; z-index: 100; display: flex; flex-direction: column; border-top: 1px solid #EBEBEB;
        }
        .quick-actions {
            display: flex; gap: 12px; padding: 10px; background-color: #F9FAFB; overflow-x: auto; scrollbar-width: none; align-items: center;
        }
        .quick-actions::-webkit-scrollbar { display: none; }
        .quick-action-item {
            display: flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 5px; background: #fff; cursor: pointer; font-size: 12px; white-space: nowrap;
        }
        .input-row {
            display: flex; align-items: center; padding: 8px 10px; gap: 10px; background: #FFFFFF; min-height: 50px;
        }
        #messageInput {
            flex: 1; padding: 8px 16px; border: none; border-radius: 18px; background-color: #F5F6FA; font-size: 14px; outline: none; resize: none; max-height: 80px; overflow-y: auto; line-height: 1.5;
        }
        
        /* 多选模式样式 */
        body.selection-mode .input-area { display: none !important; }
        body.selection-mode #selectionBottomBar { display: flex; }
        .message { transition: padding 0.25s ease; position: relative; }
        body.selection-mode .message { padding-left: 45px; }
        .msg-checkbox {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            width: 22px; height: 22px; border-radius: 50%; border: 1px solid #C9C9C9;
            background: #FFFFFF; display: none; align-items: center; justify-content: center; z-index: 10;
        }
        body.selection-mode .msg-checkbox { display: flex; }
        .message.selected .msg-checkbox { background: #0089FF; border-color: #0089FF; }
        .message.selected .msg-checkbox::after {
            content: ''; width: 5px; height: 10px; border: solid white; border-width: 0 2px 2px 0; transform: rotate(45deg); margin-bottom: 2px;
        }
        #selectionBottomBar {
            display: none; position: fixed; bottom: 0; left: 0; width: 100%; height: 60px; background: #FFFFFF; border-top: 0.5px solid #EBEBEB; z-index: 1000; justify-content: space-around; align-items: center; padding-bottom: env(safe-area-inset-bottom);
        }
        .sel-btn {
            display: flex; flex-direction: column; align-items: center; gap: 4px; background: none; border: none; color: #333; font-size: 11px; cursor: pointer;
        }
        .sel-icon { font-size: 20px; }

        /* 公告条 */
        .announcement-bar {
            background: #FFF3E0; color: #FF8C00; padding: 6px 14px; margin: 0 10px 10px; border-radius: 12px; font-size: 12px; display: flex; align-items: center; gap: 6px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="messages-area" id="messagesArea">
            <div style="text-align: center; color: #999; font-size: 12px; padding: 20px;">加载中...</div>
        </div>
        
        <div class="input-area">
            <div class="quick-actions" id="quickActionsContainer"></div>
            <div class="input-row" id="textInputRow">
                <button onclick="toggleSelectionMode()" style="background:none; border:none; font-size:20px; color:#878B99;">☰</button>
                <textarea id="messageInput" placeholder="发消息..."></textarea>
                <button onclick="sendMessage()" style="background:#0089FF; color:#fff; border:none; border-radius:18px; padding:6px 14px; font-size:14px; white-space:nowrap;">发送</button>
            </div>
        </div>
    </div>

    <!-- 底部多选操作栏 -->
    <div id="selectionBottomBar">
        <button class="sel-btn" onclick="forwardSelectedAsCard()">
            <div class="sel-icon">📑</div>
            <span>合并转发</span>
        </button>
        <button class="sel-btn" onclick="exitSelectionMode()">
            <div class="sel-icon">❌</div>
            <span>取消</span>
        </button>
    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        let groupId = urlParams.get('group_id') || '1';
        let userId = urlParams.get('user_id') || 'guest_' + Date.now();
        let userData = {
            id: userId,
            nickname: '用户_' + userId.slice(-4),
            avatar: 'https://picsum.photos/id/'+Math.floor(Math.random()*1000)+'/60/60'
        };

        function sendMessage() {
            const input = document.getElementById('messageInput');
            const content = input.value.trim();
            if (!content) return;
            
            fetch('api/chat/send_message.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    group_id: groupId, user_id: userId,
                    user_nickname: userData.nickname, user_avatar: userData.avatar,
                    type: 'text', content: content
                })
            }).then(r=>r.json()).then(data => {
                if(data.success) {
                    input.value = '';
                    loadMessages();
                    setTimeout(() => scrollToBottom(true), 100);
                } else {
                    alert(data.message);
                }
            });
        }

        function loadMessages() {
            fetch(`api/chat/get_messages.php?group_id=${groupId}`)
                .then(r => r.json())
                .then(messages => {
                    const area = document.getElementById('messagesArea');
                    area.innerHTML = '';
                    messages.forEach(msg => addMessageToDOM(msg, area));
                });
        }

        function addMessageToDOM(message, area) {
            const isOwn = message.user_id === userId;
            let contentHtml = message.content || '';

            // 解析 @全体成员
            if (message.type === 'text' && contentHtml.includes('@全体成员')) {
                contentHtml = contentHtml.replace(/@全体成员/g, '<span class="mention-all-tag">@全体成员</span>');
            }

            // 解析卡片与合并转发
            if (message.type === 'card' || message.type === 'history') {
                try {
                    const payload = typeof message.content === 'string' ? JSON.parse(message.content) : message.content;
                    if (message.type === 'card') {
                        contentHtml = `
                            <div class="message-card">
                                <div class="message-card-body">
                                    <div class="message-card-info">
                                        <div class="message-card-title">${payload.title || '应用推荐'}</div>
                                        <div class="message-card-desc">${payload.desc || ''}</div>
                                    </div>
                                    ${payload.thumbUrl ? `<img src="${payload.thumbUrl}" class="message-card-thumb">` : ''}
                                </div>
                                <div class="message-card-footer">${payload.appName || '来自外部应用'}</div>
                            </div>
                        `;
                    } else if (message.type === 'history') {
                        let itemsHtml = '';
                        if(payload.items && payload.items.length) {
                            itemsHtml = payload.items.slice(0,4).map(it => `<div class="message-history-item">${it.from}: ${it.text}</div>`).join('');
                        }
                        const encodedPayload = encodeURIComponent(JSON.stringify(payload));
                        contentHtml = `
                            <div class="message-history-card" onclick="openQQHistoryModal('${encodedPayload}')">
                                <div class="message-history-title">${payload.title || '群聊的聊天记录'}</div>
                                <div class="message-history-list">${itemsHtml}</div>
                                <div class="message-history-footer">查看${payload.items ? payload.items.length : 0}条转发消息</div>
                            </div>
                        `;
                    }
                } catch(e) { console.error("解析卡片失败", e); }
            }

            // 视频消息处理
            if (message.type === 'video') {
                contentHtml = `<video src="${message.content}" class="message-video" onclick="enterVideoFullscreen(this)" preload="metadata"></video>`;
            } else if (message.type === 'image') {
                contentHtml = `<img src="${message.content}" class="message-image">`;
            }

            const avatarUrl = message.user_avatar || `https://picsum.photos/id/10/36/36`;
            const senderName = message.user_nickname || '用户';

            const div = document.createElement('div');
            div.className = `message ${isOwn ? 'own' : 'other'}`;
            // 注入复选框用于多选合并转发
            const checkboxHtml = `<div class="msg-checkbox" onclick="toggleMsgSelect(this.parentElement)"></div>`;
            
            div.innerHTML = `
                ${checkboxHtml}
                ${!isOwn ? `<img src="${avatarUrl}" class="message-avatar">` : ''}
                <div class="message-content-wrapper">
                    ${!isOwn ? `<div class="message-sender">${senderName}</div>` : ''}
                    <div class="message-content">${contentHtml}</div>
                </div>
                ${isOwn ? `<img src="${avatarUrl}" class="message-avatar">` : ''}
            `;
            area.appendChild(div);
        }

        // 视频全屏API调用
        window.enterVideoFullscreen = function(videoEl) {
            if (videoEl.requestFullscreen) videoEl.requestFullscreen();
            else if (videoEl.webkitRequestFullscreen) videoEl.webkitRequestFullscreen();
            else if (videoEl.webkitEnterFullscreen) videoEl.webkitEnterFullscreen();
            videoEl.play();
        };

        function scrollToBottom(force=false) {
            const area = document.getElementById('messagesArea');
            if(area) area.scrollTop = area.scrollHeight;
        }

        setInterval(loadMessages, 3000);
        window.onload = loadMessages;

        // ---- 多选与转发逻辑 ----
        function toggleSelectionMode() {
            document.body.classList.toggle('selection-mode');
        }
        function exitSelectionMode() {
            document.body.classList.remove('selection-mode');
            document.querySelectorAll('.message.selected').forEach(el => el.classList.remove('selected'));
        }
        function toggleMsgSelect(el) {
            if (document.body.classList.contains('selection-mode')) {
                el.classList.toggle('selected');
            }
        }

        window.pendingForwardPayload = null;
        window.forwardSelectedAsCard = function() {
            const selectedEls = document.querySelectorAll('.message.selected');
            if (selectedEls.length === 0) return alert('请先选择消息');
            
            let items = [];
            selectedEls.forEach(el => {
                const senderEl = el.querySelector('.message-sender');
                const from = senderEl ? senderEl.textContent : '我';
                const avatarEl = el.querySelector('.message-avatar');
                const avatar = avatarEl ? avatarEl.src : '';
                const contentEl = el.querySelector('.message-content');
                let text = contentEl ? contentEl.innerText.substring(0, 50) : '[复杂消息]';
                if(contentEl && contentEl.querySelector('img.message-image')) text = '[图片]';
                if(contentEl && contentEl.querySelector('video.message-video')) text = '[视频]';
                items.push({ from, avatar, text });
            });
            
            window.pendingForwardPayload = { title: "群聊的聊天记录", items: items };
            openGroupSelectorModal();
        };

        window.openGroupSelectorModal = function() {
            if(!document.getElementById('qqGroupSelectorModal')) {
                const modalHtml = `
                    <div class="qq-group-selector-modal" id="qqGroupSelectorModal">
                        <div class="qq-header">
                            <button class="qq-close" onclick="closeGroupSelectorModal()">取消</button>
                            <div class="qq-title">发送给</div>
                            <div style="width: 32px;"></div>
                        </div>
                        <div class="qq-group-list" id="qqGroupListContainer">加载中...</div>
                    </div>
                `;
                document.body.insertAdjacentHTML('beforeend', modalHtml);
            }
            document.getElementById('qqGroupSelectorModal').classList.add('active');
            
            fetch('api/admin/groups.php').then(r=>r.json()).then(groups => {
                let html = '';
                groups.forEach(g => {
                    const isCur = (g.id == groupId) ? 'is-current' : '';
                    html += `<div class="qq-group-item ${isCur}" onclick="executeForwardToGroup('${g.id}')"><div class="qq-group-info">${g.name || '群聊'}</div></div>`;
                });
                document.getElementById('qqGroupListContainer').innerHTML = html;
            });
        };

        window.closeGroupSelectorModal = function() {
            const modal = document.getElementById('qqGroupSelectorModal');
            if(modal) modal.classList.remove('active');
        };

        window.executeForwardToGroup = function(targetGroupId) {
            if(!window.pendingForwardPayload) return;
            fetch('api/chat/send_message.php', {
                method: 'POST', headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    group_id: targetGroupId, user_id: userId,
                    user_nickname: userData.nickname, user_avatar: userData.avatar,
                    type: 'history', content: JSON.stringify(window.pendingForwardPayload)
                })
            }).then(r=>r.json()).then(res => {
                if(res.success) {
                    alert('转发成功！');
                    closeGroupSelectorModal();
                    exitSelectionMode();
                    if(targetGroupId == groupId) loadMessages();
                } else alert('失败: ' + res.message);
            });
        };

        window.openQQHistoryModal = function(payloadStr) {
            const payload = JSON.parse(decodeURIComponent(payloadStr));
            if(!document.getElementById('qqHistoryModalContainer')) {
                const modalHtml = `
                    <div class="qq-history-modal" id="qqHistoryModalContainer">
                        <div class="qq-header">
                            <button class="qq-close" onclick="document.getElementById('qqHistoryModalContainer').classList.remove('active')">返回</button>
                            <div class="qq-title">${payload.title || '聊天记录'}</div>
                            <div style="width: 32px;"></div>
                        </div>
                        <div class="qq-body wx-body" id="qqHistoryModalBody"></div>
                    </div>
                `;
                document.body.insertAdjacentHTML('beforeend', modalHtml);
            }
            
            let itemsHtml = (payload.items || []).map(it => `
                <div class="wx-item">
                    <img src="${it.avatar}" class="wx-avatar">
                    <div class="wx-content">
                        <div class="wx-name">${it.from}</div>
                        <div class="wx-text">${it.text}</div>
                    </div>
                </div>
            `).join('');
            
            document.getElementById('qqHistoryModalBody').innerHTML = itemsHtml;
            document.getElementById('qqHistoryModalContainer').classList.add('active');
        };
    </script>
</body>
</html>