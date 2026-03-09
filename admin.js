// Toast 提示函数
function toast(message, type = 'info') {
    const toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        console.error('toastContainer not found');
        return;
    }
    
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    let icon = '';
    switch (type) {
        case 'success': icon = '✅'; break;
        case 'error': icon = '❌'; break;
        case 'warning': icon = '⚠️'; break;
        case 'info':
        default: icon = 'ℹ️'; break;
    }
    
    toast.innerHTML = `${icon} ${message}`;
    toastContainer.appendChild(toast);
    
    setTimeout(() => toast.remove(), 3000);
}

const apiCache = {
    data: {},
    timestamp: {},
    maxAge: 30000,
    
    get(key) {
        const now = Date.now();
        if (this.data[key] && (now - this.timestamp[key] < this.maxAge)) {
            return this.data[key];
        }
        return null;
    },
    
    set(key, value) {
        this.data[key] = value;
        this.timestamp[key] = Date.now();
    },
    
    clear(key) {
        if (key) {
            delete this.data[key];
            delete this.timestamp[key];
        } else {
            this.data = {};
            this.timestamp = {};
        }
    }
};

let groupDetailRefreshInterval = null;

// ========== 群链接功能（新增）==========

// 构造群链接
function buildGroupLink(groupId) {
    const { protocol, host } = window.location;
    return `${protocol}//${host}/index.php?group_id=${encodeURIComponent(groupId)}`;
}

// 复制群链接
function copyGroupLink(groupId) {
    const link = buildGroupLink(groupId);
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(link)
            .then(() => toast('群链接已复制', 'success'))
            .catch(() => fallbackCopy(link));
    } else {
        fallbackCopy(link);
    }

    function fallbackCopy(text) {
        const input = document.createElement('input');
        input.value = text;
        document.body.appendChild(input);
        input.select();
        try {
            document.execCommand('copy');
            toast('群链接已复制', 'success');
        } catch (e) {
            console.error(e);
            toast('复制失败，请手动复制', 'error');
        }
        document.body.removeChild(input);
    }
}

// 新窗口打开群链接
function openGroupLink(groupId) {
    const link = buildGroupLink(groupId);
    window.open(link, '_blank');
}

// ========== 页面导航函数 ==========

function setActiveMenu(key) {
    document.querySelectorAll('.menu-item').forEach(item => item.classList.remove('active'));
    const menuItems = document.querySelectorAll('.menu-item');
    if (key === 'groups' && menuItems[0]) menuItems[0].classList.add('active');
    if (key === 'chat' && menuItems[1]) menuItems[1].classList.add('active');
    if (key === 'stats' && menuItems[2]) menuItems[2].classList.add('active');
    if (key === 'config' && menuItems[3]) menuItems[3].classList.add('active');
}

function loadGroups() {
    setActiveMenu('groups');
    const mainContent = document.getElementById('mainContent');
    mainContent.innerHTML = `
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">群管理</h2>
                <button class="btn btn-primary btn-sm" onclick="openCreateGroupModal()">创建群聊</button>
            </div>
            <div id="groupsContainer" class="loading">加载中...</div>
        </div>
    `;
    fetchGroups();
}

function loadChatMessages() {
    setActiveMenu('chat');
    toast('此功能正在开发中', 'info');
}

function loadGroupInfo() {
    setActiveMenu('stats');
    toast('此功能正在开发中', 'info');
}

function loadAdminConfig() {
    setActiveMenu('config');
    toast('此功能正在开发中', 'info');
}

// ========== 群列表相关 ==========

function fetchGroups() {
    const container = document.getElementById('groupsContainer');
    
    container.innerHTML = '<div class="loading">加载中...</div>';
    
    fetch('api/admin/groups.php')
        .then(res => {
            if (!res.ok) throw new Error('网络响应失败');
            return res.json();
        })
        .then(data => {
            console.log('群列表数据:', data);
            
            if (!Array.isArray(data) || data.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-state-icon">📭</div>
                        <h3 class="empty-state-title">还没有群聊</h3>
                        <p class="empty-state-desc">点击右上角"创建群聊"按钮，新建一个群聊。</p>
                    </div>
                `;
                return;
            }

            const cards = data.map(group => {
                const groupName = group.name || '未命名群';
                const groupDesc = group.desc || group.description || '暂无群介绍';
                const groupAvatar = group.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(groupName)}&background=07C160&color=fff&size=80`;
                const groupId = group.id || group.group_id;
                
                return `
                    <div class="group-info-card" onclick="openGroupDetail('${groupId}')">
                        <div class="group-info-header">
                            <img src="${groupAvatar}" class="group-info-avatar" alt="${groupName}" onerror="this.src='https://ui-avatars.com/api/?name=G&background=EBEBEB&color=999&size=80'">
                            <div style="flex:1;min-width:0;">
                                <div class="group-info-name">${groupName}</div>
                                <div class="group-info-desc">${groupDesc}</div>
                            </div>
                        </div>
                        <div class="group-info-stats">
                            <div class="stat-item">
                                <div class="stat-value">${group.member_count || 0}</div>
                                <div class="stat-label">群成员</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">${group.today_active_users || 0}</div>
                                <div class="stat-label">今日活跃</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">${group.total_active_users || 0}</div>
                                <div class="stat-label">总活跃</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">${group.today_msg_count || 0}</div>
                                <div class="stat-label">今日消息</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">${group.total_msg_count || 0}</div>
                                <div class="stat-label">总消息</div>
                            </div>
                        </div>
                        <div class="group-info-footer">
                            <span class="tag ${group.allow_speak !== false ? 'tag-success' : 'tag-danger'}">
                                ${group.allow_speak !== false ? '允许发言' : '全员禁言'}
                            </span>
                            <button class="btn btn-secondary btn-xs" onclick="event.stopPropagation(); editGroup('${groupId}')">编辑</button>
                            <button class="btn btn-danger btn-xs" onclick="event.stopPropagation(); deleteGroup('${groupId}')">解散</button>
                        </div>
                    </div>
                `;
            }).join('');

            container.innerHTML = `<div class="group-info-grid">${cards}</div>`;
        })
        .catch(err => {
            console.error('加载群列表失败:', err);
            container.innerHTML = `
                <div class="empty-state">
                    <div class="empty-state-icon">😢</div>
                    <h3 class="empty-state-title">加载失败</h3>
                    <p class="empty-state-desc">请刷新页面重试。错误：${err.message}</p>
                </div>
            `;
        });
}

// ========== 群详情（含群链接）==========

function openGroupDetail(groupId) {
    const modal = document.getElementById('groupDetailModal');
    const modalGroupName = document.getElementById('modalGroupName');
    const groupDetailContent = document.getElementById('groupDetailContent');

    modal.dataset.groupId = groupId;

    if (groupDetailRefreshInterval) {
        clearInterval(groupDetailRefreshInterval);
        groupDetailRefreshInterval = null;
    }

    modalGroupName.textContent = '加载中...';
    modal.classList.add('active');
    groupDetailContent.innerHTML = '<div class="loading">加载中...</div>';

    function renderGroupDetail(group) {
        modalGroupName.textContent = group.name || '群详情';
        const createdDate = new Date(group.created_at);
        const now = new Date();
        const daysSinceCreation = Math.floor((now - createdDate) / (1000 * 60 * 60 * 24));

        const groupLink = buildGroupLink(group.id);

        groupDetailContent.innerHTML = `
            <div style="text-align: center; padding-bottom: 24px; border-bottom: 1px solid #F0F0F0; margin-bottom: 24px;">
                <div style="display: inline-block; position: relative; margin-bottom: 16px;">
                    <img src="${group.avatar || 'https://picsum.photos/id/18/80/80'}" alt="${group.name}" style="width: 80px; height: 80px; border-radius: 12px; object-fit: cover; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                    ${group.online_count > 0 ? `<div style="position: absolute; bottom: -4px; right: -4px; width: 20px; height: 20px; background: #07C160; border-radius: 50%; border: 3px solid #FFF;"></div>` : ''}
                </div>
                <h3 style="font-size: 20px; font-weight: 600; color: #1A1A1A; margin: 0 0 8px 0">${group.name}</h3>
                <p style="font-size: 13px; color: #8C8C8C; margin: 0;">
                    ${group.members?.length || 0} 位群成员 · ${group.today_active_users || 0} 今日活跃
                </p>
            </div>

            <div style="margin-bottom: 24px;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                    <div style="width: 3px; height: 14px; background: #07C160; border-radius: 2px;"></div>
                    <div style="font-size: 14px; font-weight: 600; color: #1A1A1A;">数据统计</div>
                </div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;">
                    <div style="background: #F6F7F9; padding: 16px; border-radius: 8px; text-align: center;">
                        <div style="font-size: 24px; font-weight: 600; color: #07C160; margin-bottom: 4px;">${group.today_active_users || 0}</div>
                        <div style="font-size: 12px; color: #8C8C8C;">今日活跃</div>
                    </div>
                    <div style="background: #F6F7F9; padding: 16px; border-radius: 8px; text-align: center;">
                        <div style="font-size: 24px; font-weight: 600; color: #1677FF; margin-bottom: 4px;">${group.total_active_users || 0}</div>
                        <div style="font-size: 12px; color: #8C8C8C;">总活跃</div>
                    </div>
                    <div style="background: #F6F7F9; padding: 16px; border-radius: 8px; text-align: center;">
                        <div style="font-size: 24px; font-weight: 600; color: #FAAD14; margin-bottom: 4px;">${daysSinceCreation}</div>
                        <div style="font-size: 12px; color: #8C8C8C;">建群天数</div>
                    </div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                <div>
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                        <div style="width: 3px; height: 14px; background: #07C160; border-radius: 2px;"></div>
                        <div style="font-size: 14px; font-weight: 600; color: #1A1A1A;">基础信息</div>
                    </div>
                    <div style="background: #FFF; border: 1px solid #EBEBEB; border-radius: 8px; padding: 16px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 12px; border-bottom: 1px solid #F5F5F5; margin-bottom: 12px;">
                            <div style="color: #8C8C8C; font-size: 13px;">群 ID</div>
                            <div style="color: #1A1A1A; font-size: 13px; font-family: monospace; background: #F6F7F9; padding: 4px 8px; border-radius: 4px;">${group.id}</div>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 12px; border-bottom: 1px solid #F5F5F5; margin-bottom: 12px;">
                            <div style="color: #8C8C8C; font-size: 13px;">人数上限</div>
                            <div style="color: #1A1A1A; font-size: 13px;">${group.member_limit ? group.member_limit + '人' : '无限制'}</div>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 12px; border-bottom: 1px solid #F5F5F5; margin-bottom: 12px;">
                            <div style="color: #8C8C8C; font-size: 13px;">群标签</div>
                            <div style="color: #1A1A1A; font-size: 13px;">${group.tag || '暂无'}</div>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="color: #8C8C8C; font-size: 13px;">创建时间</div>
                            <div style="color: #1A1A1A; font-size: 13px;">${new Date(group.created_at).toLocaleString()}</div>
                        </div>
                    </div>
                </div>

                <div>
                    <div style="margin-bottom: 16px;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                            <div style="width: 3px; height: 14px; background: #07C160; border-radius: 2px;"></div>
                            <div style="font-size: 14px; font-weight: 600; color: #1A1A1A;">群管理</div>
                        </div>
                        <div style="background: #FFF; border: 1px solid #EBEBEB; border-radius: 8px; padding: 16px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 12px; border-bottom: 1px solid #F5F5F5; margin-bottom: 12px;">
                                <div style="font-size: 13px; color: #1A1A1A;">全员禁言</div>
                                <label class="toggle-switch">
                                    <input type="checkbox" ${!group.allow_speak ? 'checked' : ''} onchange="toggleGroupSpeak('${group.id}', !this.checked)">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div style="font-size: 13px; color: #1A1A1A;">允许发图片</div>
                                <label class="toggle-switch">
                                    <input type="checkbox" ${group.allow_image_upload !== false ? 'checked' : ''} onchange="toggleGroupImageUpload('${group.id}', this.checked)">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                            <div style="width: 3px; height: 14px; background: #07C160; border-radius: 2px;"></div>
                            <div style="font-size: 14px; font-weight: 600; color: #1A1A1A;">群链接</div>
                        </div>
                        <div style="background: #FFF; border: 1px solid #EBEBEB; border-radius: 8px; padding: 12px 12px 10px;">
                            <div style="font-size: 12px; color: #8C8C8C; margin-bottom: 6px;">
                                这个链接用于邀请用户直接进入本群。
                            </div>
                            <div style="display:flex; align-items:center; gap:8px;">
                                <div style="flex:1; font-size: 12px; color:#595959; background:#F6F7F9; border-radius:4px; padding:6px 8px; word-break:break-all;">
                                    ${groupLink}
                                </div>
                            </div>
                            <div style="display:flex; gap:8px; margin-top:10px; justify-content:flex-end;">
                                <button class="btn btn-secondary btn-sm" onclick="copyGroupLink('${group.id}')">复制链接</button>
                                <button class="btn btn-primary btn-sm" onclick="openGroupLink('${group.id}')">打开链接</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 24px;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                    <div style="width: 3px; height: 14px; background: #07C160; border-radius: 2px;"></div>
                    <div style="font-size: 14px; font-weight: 600; color: #1A1A1A;">群公告与介绍</div>
                </div>
                <div style="background: #FFF; border: 1px solid #EBEBEB; border-radius: 8px; padding: 16px;">
                    <div style="font-size: 13px; color: #595959; line-height: 1.6;">
                        <strong style="color:#1A1A1A;">公告：</strong>${group.announcement || '暂无公告'}
                    </div>
                    <div style="font-size: 13px; color: #595959; line-height: 1.6; margin-top:5px;">
                        <strong style="color:#1A1A1A;">介绍：</strong>${group.desc || '暂无介绍'}
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; padding-top: 16px; border-top: 1px solid #F0F0F0; margin-top: 24px;">
                <button class="btn btn-primary" onclick="editGroup('${group.id}')">编辑信息</button>
                <button class="btn btn-danger" onclick="deleteGroup('${group.id}')">解散群聊</button>
            </div>
        `;
    }

    function fetchGroupDetail() {
        fetch(`api/admin/groups.php?group_id=${groupId}`)
            .then(res => res.json())
            .then(group => renderGroupDetail(group))
            .catch(err => {
                console.error('加载群详情失败:', err);
                groupDetailContent.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-state-icon">😢</div>
                        <h3 class="empty-state-title">加载失败</h3>
                        <p class="empty-state-desc">请检查网络连接或稍后重试。</p>
                    </div>
                `;
            });
    }

    fetchGroupDetail();
    groupDetailRefreshInterval = setInterval(fetchGroupDetail, 10000);
}

function closeGroupDetailModal() {
    const modal = document.getElementById('groupDetailModal');
    modal.classList.remove('active');
    if (groupDetailRefreshInterval) {
        clearInterval(groupDetailRefreshInterval);
        groupDetailRefreshInterval = null;
    }
}

// ========== 创建/编辑/删除群聊 ==========

function openCreateGroupModal() {
    document.getElementById('createGroupModal').classList.add('active');
}

function closeCreateGroupModal() {
    document.getElementById('createGroupModal').classList.remove('active');
    document.getElementById('createGroupForm').reset();
}

function editGroup(groupId) {
    fetch(`api/admin/groups.php?group_id=${groupId}`)
        .then(res => res.json())
        .then(group => {
            document.getElementById('editGroupId').value = group.id;
            document.getElementById('editGroupName').value = group.name;
            document.getElementById('editGroupDesc').value = group.desc || '';
            document.getElementById('editGroupAnnouncement').value = group.announcement || '';
            document.getElementById('editGroupMemberLimit').value = group.member_limit || '0';
            document.getElementById('editGroupTag').value = group.tag || '';
            document.getElementById('editGroupModal').classList.add('active');
        })
        .catch(err => {
            console.error(err);
            toast('加载群信息失败', 'error');
        });
}

function closeEditGroupModal() {
    document.getElementById('editGroupModal').classList.remove('active');
}

function deleteGroup(groupId) {
    if (!confirm('确定要解散这个群聊吗？此操作不可恢复！')) return;
    
    fetch(`api/admin/groups.php?group_id=${groupId}`, { method: 'DELETE' })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                toast('群聊已解散', 'success');
                fetchGroups();
                closeGroupDetailModal();
            } else {
                toast(data.message || '删除失败', 'error');
            }
        })
        .catch(err => toast('网络错误', 'error'));
}

function toggleGroupSpeak(groupId, allowSpeak) {
    fetch('api/admin/groups.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ group_id: groupId, allow_speak: allowSpeak })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) toast(allowSpeak ? '已允许发言' : '已全员禁言', 'success');
        else toast(data.message || '操作失败', 'error');
    });
}

function toggleGroupImageUpload(groupId, allowImage) {
    fetch('api/admin/groups.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ group_id: groupId, allow_image_upload: allowImage })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) toast(allowImage ? '已允许发图片' : '已禁止发图片', 'success');
        else toast(data.message || '操作失败', 'error');
    });
}

// 点击遮罩层关闭模态框
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) {
            this.closest('.modal').classList.remove('active');
        }
    });
});

// 页面初始化
document.addEventListener('DOMContentLoaded', function() {
    loadGroups();
});