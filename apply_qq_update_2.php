<?php
$file = 'index.php';
if (!file_exists($file)) {
    die("index.php 文件不存在。");
}
$content = file_get_contents($file);

if (strpos($content, 'qq-group-selector-modal') !== false) {
    die("群聊选择转发弹窗已经注入过啦！<br><a href='index.php'>点击此处返回聊天室</a>");
}

$custom_css = <<< 'EOT'
/* --- QQ Forward Group Selector --- */
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
/* --- End QQ Forward Group Selector --- */
EOT;

$content = str_replace('/* --- End QQ Style --- */', $custom_css . "\n/* --- End QQ Style --- */", $content);

$custom_js = <<< 'EOT'
        // --- 注入的 群聊转发选择逻辑 ---
        
        // 缓存要转发的 payload 数据
        window.pendingForwardPayload = null;

        // 覆盖原先直接发送的 forwardSelectedAsCard 方法
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
            
            window.pendingForwardPayload = {
                title: "群聊的聊天记录",
                items: items
            };
            
            openGroupSelectorModal();
        };

        window.openGroupSelectorModal = function() {
            if(!document.getElementById('qqGroupSelectorModal')) {
                const modalHtml = `
                    <div class="qq-group-selector-modal" id="qqGroupSelectorModal">
                        <div class="qq-header">
                            <button class="qq-close" onclick="closeGroupSelectorModal()">取消</button>
                            <div class="qq-title">选择发送的群聊</div>
                            <div style="width: 32px;"></div>
                        </div>
                        <div class="qq-group-list" id="qqGroupListContainer">
                            <div style="text-align:center; padding: 20px; color:#999;">加载中...</div>
                        </div>
                    </div>
                `;
                document.body.insertAdjacentHTML('beforeend', modalHtml);
            }
            
            const modal = document.getElementById('qqGroupSelectorModal');
            modal.classList.add('active');
            
            // 获取用户加入的群聊列表
            fetch('api/admin/groups.php')
                .then(res => res.json())
                .then(groups => {
                    const listContainer = document.getElementById('qqGroupListContainer');
                    if(!groups || groups.length === 0) {
                        listContainer.innerHTML = '<div style="text-align:center; padding: 20px; color:#999;">暂无可转发的群聊</div>';
                        return;
                    }
                    
                    let html = '';
                    groups.forEach(g => {
                        const isCurrent = (g.id === groupId) ? 'is-current' : '';
                        // 用户如果加入了该群聊（或者自己就是群成员），才能转发
                        // 如果后端接口返回所有群聊，前端也可直接全列出来，点击时会发给对应 group_id
                        html += `
                            <div class="qq-group-item ${isCurrent}" onclick="executeForwardToGroup('${g.id}')">
                                <div class="qq-group-info">${g.name || '群聊'}</div>
                            </div>
                        `;
                    });
                    listContainer.innerHTML = html;
                })
                .catch(err => {
                    document.getElementById('qqGroupListContainer').innerHTML = '<div style="text-align:center; padding: 20px; color:red;">加载群聊失败</div>';
                });
        };

        window.closeGroupSelectorModal = function() {
            const modal = document.getElementById('qqGroupSelectorModal');
            if(modal) modal.classList.remove('active');
        };

        window.executeForwardToGroup = function(targetGroupId) {
            if(!window.pendingForwardPayload) return;
            
            const payload = window.pendingForwardPayload;
            
            fetch('api/chat/send_message.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    group_id: targetGroupId,
                    user_id: userId,
                    user_nickname: userData.nickname,
                    user_avatar: userData.avatar,
                    type: 'history',
                    content: JSON.stringify(payload)
                })
            }).then(r => r.json()).then(res => {
                if(res.success) {
                    alert('转发成功！');
                    closeGroupSelectorModal();
                    
                    // 退出多选模式
                    if(typeof document.body.classList !== 'undefined') {
                        document.body.classList.remove('selection-mode');
                        const checkboxes = document.querySelectorAll('.msg-checkbox');
                        checkboxes.forEach(cb => cb.parentElement.classList.remove('selected'));
                    }
                    
                    // 如果转发的是本群，刷新消息列表
                    if(targetGroupId === groupId) {
                        loadMessages();
                        setTimeout(() => scrollToBottom(false, true), 100);
                    }
                } else {
                    alert('转发失败: ' + res.message);
                }
            }).catch(err => alert("网络错误，发送失败"));
        };
        // --- End 转发群聊选择逻辑 ---
EOT;

$content = str_replace('// --- End 注入逻辑 ---', $custom_js . "\n// --- End 注入逻辑 ---", $content);

file_put_contents($file, $content);
echo "<html><head><meta charset='utf-8'><title>转发群聊选择更新成功</title><meta name='viewport' content='width=device-width,initial-scale=1'></head><body style='font-family:sans-serif; text-align:center; padding-top: 50px;'><h2>转发群聊选择面板 注入成功！🎉</h2><p style='color: #666;'>点击合并转发后会拉起 QQ 风格的群聊选择界面，支持转发到其它群或当前群。</p><br><br><button style='background:#0089FF; color:#fff; border:none; padding:12px 24px; border-radius:8px; font-size:16px; cursor:pointer;' onclick='location.href=\"index.php\"'>返回聊天室体验</button></body></html>";
?>