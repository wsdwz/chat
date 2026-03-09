<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>群聊设置</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --primary-color: #007AFF;
            --secondary-color: #5AC8FA;
            --text-color: #000000;
            --text-secondary: #8E8E93;
            --background-color: #F2F2F7;
            --card-background: #FFFFFF;
            --border-color: #E5E5EA;
            --success-color: #34C759;
            --danger-color: #FF3B30;
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 8px rgba(0, 0, 0, 0.1);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-full: 20px;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.5;
            overflow-x: hidden;
        }
        
        .settings-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: var(--card-background);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* 顶部导航栏 */
        .settings-header {
            background-color: var(--card-background);
            padding: 16px;
            border-bottom: 1px solid var(--border-color);
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
            color: var(--primary-color);
            padding: 0;
        }
        
        .header-title {
            flex: 1;
            font-size: 18px;
            font-weight: 600;
            text-align: center;
        }
        
        /* 主内容区域 */
        .settings-content {
            flex: 1;
            overflow-y: auto;
        }
        
        /* 群聊信息卡片 */
        .group-info-card {
            background-color: var(--card-background);
            padding: 16px;
            border-bottom: 10px solid var(--background-color);
        }
        
        .group-info-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
        }
        
        .group-avatar {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            object-fit: cover;
        }
        
        .group-details {
            flex: 1;
        }
        
        .group-name {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        
        .group-id {
            font-size: 14px;
            color: var(--text-secondary);
        }
        
        .edit-btn {
            background: none;
            border: none;
            font-size: 20px;
            color: var(--primary-color);
            cursor: pointer;
        }
        
        /* 成员列表 */
        .members-section {
            background-color: var(--card-background);
            padding: 16px;
            border-bottom: 10px solid var(--background-color);
        }
        
        .section-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 12px;
        }
        
        .members-list {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
        }
        
        .member-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            width: 70px;
        }
        
        .member-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .member-name {
            font-size: 12px;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }
        
        .invite-btn {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background-color: #F0F0F0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: var(--primary-color);
            border: none;
            cursor: pointer;
        }
        
        /* 设置选项 */
        .settings-section {
            background-color: var(--card-background);
            padding: 16px;
            margin-bottom: 10px;
        }
        
        .setting-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .setting-item:last-child {
            border-bottom: none;
        }
        
        .setting-label {
            font-size: 16px;
        }
        
        .setting-value {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: var(--text-secondary);
        }
        
        .setting-arrow {
            font-size: 18px;
            color: var(--text-secondary);
        }
        
        /* 底部导航栏 */
        .bottom-nav {
            background-color: var(--card-background);
            border-top: 1px solid var(--border-color);
            padding: 8px 0;
            display: flex;
            justify-content: space-around;
            position: sticky;
            bottom: 0;
            z-index: 100;
        }
        
        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            padding: 8px 16px;
            text-decoration: none;
            color: var(--text-secondary);
            transition: all 0.2s ease;
        }
        
        .nav-item.active {
            color: var(--primary-color);
        }
        
        .nav-icon {
            font-size: 24px;
        }
        
        .nav-label {
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="settings-container">
        <!-- 顶部导航栏 -->
        <div class="settings-header">
            <button class="back-btn" onclick="goBack()">&larr;</button>
            <div class="header-title">群聊设置</div>
            <div style="width: 24px;"></div> <!-- 占位符，保持标题居中 -->
        </div>
        
        <!-- 主内容区域 -->
        <div class="settings-content">
            <!-- 群聊信息卡片 -->
            <div class="group-info-card">
                <div class="group-info-header">
                    <img id="groupAvatar" src="https://picsum.photos/id/1/80/80" alt="群聊头像" class="group-avatar">
                    <div class="group-details">
                        <div id="groupName" class="group-name">群聊名称</div>
                        <div id="groupId" class="group-id">群聊ID: 123456789</div>
                    </div>
                    <button class="edit-btn">›</button>
                </div>
            </div>
            
            <!-- 成员列表 -->
            <div class="members-section">
                <div class="section-title">
                    <span>群聊成员</span>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 14px; color: var(--text-secondary);">查看105名群成员</span>
                        <span style="font-size: 18px; color: var(--text-secondary);">›</span>
                    </div>
                </div>
                
                <div class="members-list">
                    <!-- 成员列表将通过JavaScript动态生成 -->
                    <div class="member-item">
                        <img src="https://picsum.photos/id/1005/70/70" alt="成员头像" class="member-avatar">
                        <div class="member-name">成员1</div>
                    </div>
                    <div class="member-item">
                        <img src="https://picsum.photos/id/1006/70/70" alt="成员头像" class="member-avatar">
                        <div class="member-name">成员2</div>
                    </div>
                    <div class="member-item">
                        <img src="https://picsum.photos/id/1007/70/70" alt="成员头像" class="member-avatar">
                        <div class="member-name">成员3</div>
                    </div>
                    <div class="member-item">
                        <img src="https://picsum.photos/id/1008/70/70" alt="成员头像" class="member-avatar">
                        <div class="member-name">成员4</div>
                    </div>
                    <div class="member-item">
                        <button class="invite-btn">+</button>
                        <div class="member-name">邀请</div>
                    </div>
                </div>
            </div>
            
            <!-- 群聊信息设置 -->
            <div class="settings-section">
                <div class="section-title">群聊信息</div>
                
                <div class="setting-item">
                    <div class="setting-label">群聊名称</div>
                    <div class="setting-value">
                        <span>群聊名称</span>
                        <span class="setting-arrow">›</span>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">群号和二维码</div>
                    <div class="setting-value">
                        <span>@12345678</span>
                        <span class="setting-arrow">›</span>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">群公告</div>
                    <div class="setting-value">
                        <span>未设置</span>
                        <span class="setting-arrow">›</span>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">我的本群昵称</div>
                    <div class="setting-value">
                        <span>未设置</span>
                        <span class="setting-arrow">›</span>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">群聊备注</div>
                    <div class="setting-value">
                        <span>未设置</span>
                        <span class="setting-arrow">›</span>
                    </div>
                </div>
            </div>
            
            <!-- 群内功能 -->
            <div class="settings-section">
                <div class="section-title">群内功能</div>
                
                <div class="setting-item">
                    <div class="setting-label">群应用中心</div>
                    <div class="setting-value">
                        <span class="setting-arrow">›</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // 获取URL参数
        const urlParams = new URLSearchParams(window.location.search);
        const groupId = urlParams.get('group_id');
        const userId = urlParams.get('user_id');
        
        let groupData = null;
        
        // 初始化
        function init() {
            if (!groupId || !userId) {
                alert('参数错误，无法查看群聊设置');
                goBack();
                return;
            }
            
            // 加载群聊信息
            loadGroupInfo();
        }
        
        // 返回上一页
        function goBack() {
            window.history.back();
        }
        
        // 加载群聊信息
        function loadGroupInfo() {
            fetch(`api/admin/groups.php?group_id=${groupId}`)
                .then(response => response.json())
                .then(group => {
                    if (group) {
                        groupData = group;
                        updateGroupInfo(group);
                    }
                })
                .catch(error => {
                    console.error('加载群聊信息失败:', error);
                });
        }
        
        // 更新群聊信息
        function updateGroupInfo(group) {
            document.getElementById('groupName').textContent = group.name;
            document.getElementById('groupId').textContent = `群聊ID: ${group.id}`;
            document.getElementById('groupAvatar').src = group.avatar || 'https://picsum.photos/id/1/80/80';
        }
        
        // 页面加载完成后初始化
        window.addEventListener('DOMContentLoaded', init);
    </script>
</body>
</html>