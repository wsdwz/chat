<?php
// apply_qq_update.php
// 此脚本用于给 index.php 注入 QQ 风格 UI，以及卡片、合并转发、视频预览和 @全体成员 支持。
$file = 'index.php';
if (!file_exists($file)) {
    die("index.php 文件不存在，请确保此文件在项目根目录。");
}
$content = file_get_contents($file);

if (strpos($content, 'QQ Style Overrides') !== false) {
    die("QQ风格和各项新功能已经注入过啦！<br><a href='index.php'>点击此处返回聊天室体验</a>");
}

$custom_css = <<< 'EOT'
/* --- QQ Style Overrides --- */
:root {
    --primary-color: #0089FF; 
    --primary-light: rgba(0, 137, 255, 0.1);
    --primary-dark: #0077E6;
    --own-message-bg: #0089FF;
}
.message-avatar {
    border-radius: 50% !important;
}
.message-sender {
    color: #878B99 !important;
}
.message.own .message-content {
    background: var(--own-message-bg) !important;
    border-radius: 16px 16px 4px 16px !important;
    color: #FFFFFF !important;
}
.message.other .message-content {
    background: #FFFFFF !important;
    border-radius: 16px 16px 16px 4px !important;
    color: #000000 !important;
    box-shadow: 0 1px 4px rgba(0,0,0,0.05);
}
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
.message-card {
    background: #FFFFFF !important;
    border-radius: 16px !important;
    width: 240px !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04) !important;
    padding: 0 !important;
    display: flex; flex-direction: column;
}
.message-card-body {
    padding: 12px 16px;
    display: flex; gap: 10px; text-align: left;
}
.message-card-title {
    font-size: 15px; font-weight: bold; color: #000;
}
.message-card-desc {
    font-size: 13px; color: #878B99; margin-top: 4px;
}
.message-card-thumb {
    width: 60px; height: 60px; border-radius: 8px; object-fit: cover;
}
.message-card-footer {
    padding: 6px 16px; font-size: 11px; color: #878B99;
    border-top: 0.5px solid #F0F0F0; text-align: left;
}
.message-history-card {
    background: #FFFFFF !important;
    border-radius: 16px !important;
    width: 250px !important;
    padding: 16px !important;
    text-align: left;
}
.message-history-title {
    font-size: 15px; font-weight: 500; color: #000000; margin-bottom: 10px;
}
.message-history-item {
    font-size: 13px; color: #878B99; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.message-history-footer {
    font-size: 12px; color: #878B99; margin-top: 10px; padding-top: 10px; border-top: 0.5px solid #F0F0F0;
}
.qq-history-modal {
    display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
    background: #F5F6FA; z-index: 2000; flex-direction: column;
    transform: translateX(100%); transition: transform 0.3s ease;
}
.qq-history-modal.active {
    display: flex; transform: translateX(0);
}
.qq-header {
    height: 44px; min-height: 44px; background: #FFFFFF; display: flex; align-items: center; justify-content: space-between;
    padding: env(safe-area-inset-top) 16px 0; border-bottom: 0.5px solid #EBEBEB;
}
.qq-close {
    font-size: 15px; color: #0089FF; background: transparent; border: none; cursor: pointer;
}
.qq-title {
    font-size: 17px; font-weight: 500;
}
.qq-body {
    flex: 1; overflow-y: auto; -webkit-overflow-scrolling: touch; padding-bottom: 30px;
}
/* --- End QQ Style --- */
EOT;

$content = str_replace('</style>', $custom_css . "\n</style>", $content);

$custom_js = <<< 'EOT'
        // --- 注入的 QQ卡片、合并转发、@全体成员、视频预览逻辑 ---
        
        if(typeof window.originalAddMessageToDOM === 'undefined'){
            window.originalAddMessageToDOM = addMessageToDOM;
        }

        // 覆盖格式化逻辑
        addMessageToDOM = function(message) {
            const isOwn = message.user_id === userId;
            
            // @全体成员高亮处理
            if (message.type === 'text' && message.content && message.content.includes('@全体成员')) {
                message.content = message.content.replace(/@全体成员/g, '<span class="mention-all-tag">@全体成员</span>');
            }
            
            // 解析卡片和合并转发历史
            if (message.type === 'card' || message.type === 'history') {
                try {
                    const payload = typeof message.content === 'string' ? JSON.parse(message.content) : message.content;
                    if (message.type === 'card') {
                        message.content = `
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
                        message.content = `
                            <div class="message-history-card" onclick="openQQHistoryModal('${encodedPayload}')">
                                <div class="message-history-title">${payload.title || '群聊的聊天记录'}</div>
                                <div class="message-history-list">${itemsHtml}</div>
                                <div class="message-history-footer">查看${payload.items ? payload.items.length : 0}条转发消息</div>
                            </div>
                        `;
                    }
                } catch(e) {
                    console.error("解析卡片失败", e);
                }
            }
            
            // 视频全屏处理 (点击视频原生全屏播放)
            if (message.type === 'video') {
                message.content = `<video src="${message.content}" class="message-video" style="max-width: 100%; border-radius: 12px; margin: 4px 0; object-fit: cover;" onclick="enterVideoFullscreen(this)" preload="metadata"></video>`;
            }
            
            window.originalAddMessageToDOM(message);
        };
        
        window.enterVideoFullscreen = function(videoEl) {
            if (videoEl.requestFullscreen) {
                videoEl.requestFullscreen();
            } else if (videoEl.webkitRequestFullscreen) {
                videoEl.webkitRequestFullscreen();
            } else if (videoEl.webkitEnterFullscreen) {
                videoEl.webkitEnterFullscreen();
            }
            videoEl.play();
        };

        window.openQQHistoryModal = function(payloadStr) {
            try {
                const payload = JSON.parse(decodeURIComponent(payloadStr));
                if(!document.getElementById('qqHistoryModalContainer')) {
                    const modalHtml = `
                        <div class="qq-history-modal" id="qqHistoryModalContainer">
                            <div class="qq-header">
                                <button class="qq-close" onclick="document.getElementById('qqHistoryModalContainer').classList.remove('active')">关闭</button>
                                <div class="qq-title">${payload.title || '聊天记录'}</div>
                                <div style="width: 32px;"></div>
                            </div>
                            <div class="qq-body wx-body" id="qqHistoryModalBody"></div>
                        </div>
                    `;
                    document.body.insertAdjacentHTML('beforeend', modalHtml);
                }
                const modal = document.getElementById('qqHistoryModalContainer');
                const body = document.getElementById('qqHistoryModalBody');
                
                let itemsHtml = (payload.items || []).map(it => `
                    <div class="wx-item">
                        <img src="${it.avatar || 'https://picsum.photos/id/1005/36/36'}" class="wx-avatar">
                        <div class="wx-content">
                            <div class="wx-name">${it.from}</div>
                            <div class="wx-text">${it.text}</div>
                        </div>
                    </div>
                `).join('');
                
                body.innerHTML = itemsHtml;
                setTimeout(() => modal.classList.add('active'), 10);
            } catch(e) {
                alert("记录解析失败");
            }
        };

        window.forwardSelectedAsCard = function() {
            const selectedEls = document.querySelectorAll('.message.selected');
            if (selectedEls.length === 0) {
                alert('请先选择要转发的消息');
                return;
            }
            
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
                if(contentEl && contentEl.querySelector('.voice-message')) text = '[语音]';
                
                items.push({ from, avatar, text });
            });
            
            const payload = {
                title: "群聊的聊天记录",
                items: items
            };
            
            fetch('api/chat/send_message.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    group_id: groupId,
                    user_id: userId,
                    user_nickname: userData.nickname,
                    user_avatar: userData.avatar,
                    type: 'history',
                    content: JSON.stringify(payload)
                })
            }).then(r => r.json()).then(res => {
                if(res.success) {
                    alert('已合并转发成功！');
                    if(typeof document.body.classList !== 'undefined') {
                        document.body.classList.remove('selection-mode');
                        const checkboxes = document.querySelectorAll('.msg-checkbox');
                        checkboxes.forEach(cb => cb.parentElement.classList.remove('selected'));
                    }
                    loadMessages();
                } else {
                    alert('转发失败: ' + res.message);
                }
            }).catch(err => alert("网络错误，发送失败"));
        };
        // --- End 注入逻辑 ---
EOT;

$content = str_replace('</script>', "\n" . $custom_js . "\n</script>", $content);

file_put_contents($file, $content);
echo "<html><head><meta charset='utf-8'><title>更新成功</title><meta name='viewport' content='width=device-width,initial-scale=1'></head><body style='font-family:sans-serif; text-align:center; padding-top: 50px;'><h2>QQ UI & 新功能 更新成功！🎉</h2><p style='color: #666;'>卡片渲染、合并转发、视频全屏、@全体成员 等功能已成功植入 index.php，主色调已切为QQ蓝。</p><br><br><button style='background:#0089FF; color:#fff; border:none; padding:12px 24px; border-radius:8px; font-size:16px; cursor:pointer;' onclick='location.href=\"index.php\"'>返回聊天室体验</button></body></html>";
?>