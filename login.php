<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>现代聊天室</title>
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
        
        .app-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: var(--card-background);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* 顶部导航栏 */
        .top-nav {
            background-color: var(--card-background);
            padding: 16px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .top-nav h1 {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-color);
        }
        
        .nav-actions {
            display: flex;
            gap: 16px;
        }
        
        .nav-btn {
            background: none;
            border: none;
            font-size: 20px;
            color: var(--primary-color);
            cursor: pointer;
        }
        
        /* 搜索栏 */
        .search-bar {
            padding: 12px 16px;
            background-color: var(--background-color);
        }
        
        .search-input {
            width: 100%;
            padding: 10px 16px;
            border: none;
            border-radius: var(--radius-full);
            background-color: var(--card-background);
            font-size: 14px;
            box-shadow: var(--shadow-sm);
        }
        
        .search-input::placeholder {
            color: var(--text-secondary);
        }
        
        /* 消息列表 */
        .messages-list {
            flex: 1;
            overflow-y: auto;
            padding-bottom: 80px;
        }
        
        .message-item {
            display: flex;
            align-items: center;
            padding: 16px;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        
        .message-item:hover {
            background-color: rgba(0, 122, 255, 0.05);
        }
        
        .message-item.active {
            background-color: rgba(0, 122, 255, 0.1);
        }
        
        .avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 12px;
            flex-shrink: 0;
        }
        
        .message-content {
            flex: 1;
            min-width: 0;
        }
        
        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4px;
        }
        
        .message-sender {
            font-weight: 600;
            font-size: 16px;
            color: var(--text-color);
        }
        
        .message-time {
            font-size: 12px;
            color: var(--text-secondary);
        }
        
        .message-preview {
            font-size: 14px;
            color: var(--text-secondary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .unread-badge {
            background-color: var(--danger-color);
            color: white;
            font-size: 12px;
            padding: 2px 8px;
            border-radius: 12px;
            margin-left: 8px;
        }
        
        /* 底部导航栏 */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            max-width: 600px;
            width: 100%;
            background-color: var(--card-background);
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: space-around;
            padding: 8px 0;
            z-index: 100;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .nav-tab {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 8px 16px;
            text-decoration: none;
            color: var(--text-secondary);
            transition: all 0.2s ease;
        }
        
        .nav-tab.active {
            color: var(--primary-color);
        }
        
        .nav-tab-icon {
            font-size: 24px;
            margin-bottom: 4px;
        }
        
        .nav-tab-label {
            font-size: 12px;
            font-weight: 500;
        }
        
        /* 浮动按钮 */
        .floating-btn {
            position: fixed;
            bottom: 80px;
            right: 20px;
            width: 56px;
            height: 56px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 122, 255, 0.4);
            transition: all 0.3s ease;
            z-index: 99;
        }
        
        .floating-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 16px rgba(0, 122, 255, 0.5);
        }
        
        /* 模态框 */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 200;
            animation: fadeIn 0.2s ease;
        }
        
        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background-color: var(--card-background);
            border-radius: var(--radius-md);
            padding: 24px;
            width: 90%;
            max-width: 400px;
            animation: slideUp 0.3s ease;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .modal-title {
            font-size: 18px;
            font-weight: 600;
        }
        
        .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--text-secondary);
        }
        
        /* 表单样式 */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-color);
        }
        
        .form-input {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            font-size: 14px;
            transition: border-color 0.2s ease;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        /* 按钮样式 */
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: var(--radius-sm);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #0056b3;
        }
        
        .btn-secondary {
            background-color: var(--border-color);
            color: var(--text-color);
        }
        
        .btn-secondary:hover {
            background-color: #d1d1d6;
        }
        
        /* 动画效果 */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        /* 滚动条样式 */
        .messages-list::-webkit-scrollbar {
            width: 6px;
        }
        
        .messages-list::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .messages-list::-webkit-scrollbar-thumb {
            background-color: var(--border-color);
            border-radius: 3px;
        }
        
        .messages-list::-webkit-scrollbar-thumb:hover {
            background-color: #c7c7cc;
        }
        
        /* 空状态 */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
            text-align: center;
            color: var(--text-secondary);
        }
        
        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 16px;
            opacity: 0.5;
        }
        
        .empty-state-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-color);
        }
        
        .empty-state-desc {
            font-size: 14px;
            line-height: 1.5;
        }
        
        /* 用户信息卡片 */
        .user-info-card {
            background-color: var(--card-background);
            padding: 16px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .user-details {
            flex: 1;
        }
        
        .user-nickname {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        
        .user-id {
            font-size: 14px;
            color: var(--text-secondary);
        }
        
        /* 加载动画 */
        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: var(--text-secondary);
        }
        
        .loading::after {
            content: '';
            width: 20px;
            height: 20px;
            border: 2px solid var(--border-color);
            border-top: 2px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- 顶部导航栏 -->
        <div class="top-nav">
            <h1>消息</h1>
            <div class="nav-actions">
                <button class="nav-btn" onclick="openMoreOptions()">⋯</button>
            </div>
        </div>
        
        <!-- 用户信息 -->
        <div id="userInfo" style="display: none;"></div>
        
        <!-- 消息列表 -->
        <div class="messages-list" id="messagesList">
            <div class="loading">加载中...</div>
        </div>
        
        <!-- 浮动按钮 -->
        <button class="floating-btn" onclick="openJoinGroupModal()">+</button>
        
        <!-- 底部导航栏 -->
        <nav class="bottom-nav">
            <a href="#" class="nav-tab">
                <div class="nav-tab-icon">🏠</div>
                <div class="nav-tab-label">首页</div>
            </a>
            <a href="#" class="nav-tab active">
                <div class="nav-tab-icon">💬</div>
                <div class="nav-tab-label">消息</div>
            </a>
            <a href="#" class="nav-tab">
                <div class="nav-tab-icon">📚</div>
                <div class="nav-tab-label">知识库</div>
            </a>
            <a href="#" class="nav-tab" onclick="openUserModal()">
                <div class="nav-tab-icon">👤</div>
                <div class="nav-tab-label">我的</div>
            </a>
        </nav>
    </div>
    
    <!-- 加入群聊模态框 -->
    <div id="joinGroupModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">加入群聊</h2>
                <button class="close-btn" onclick="closeJoinGroupModal()">&times;</button>
            </div>
            <div class="form-group">
                <label class="form-label" for="groupCode">群聊口令</label>
                <input type="text" class="form-input" id="groupCode" placeholder="请输入群聊ID" required>
            </div>
            <div style="display: flex; gap: 12px;">
                <button type="button" class="btn btn-secondary" onclick="closeJoinGroupModal()" style="flex: 1;">取消</button>
                <button type="button" class="btn btn-primary" onclick="joinGroupByCode()" style="flex: 1;">加入</button>
            </div>
        </div>
    </div>
    
    <!-- 用户设置模态框 -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">用户设置</h2>
                <button class="close-btn" onclick="closeUserModal()">&times;</button>
            </div>
            <form id="userForm">
                <input type="hidden" id="userId" name="user_id">
                <div class="form-group">
                    <label class="form-label" for="userNickname">昵称</label>
                    <input type="text" class="form-input" id="userNickname" name="userNickname" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="userAvatar">头像</label>
                    <input type="file" class="form-input" id="userAvatar" name="userAvatar" accept="image/*">
                </div>
                <div style="display: flex; gap: 12px;">
                    <button type="button" class="btn btn-secondary" onclick="closeUserModal()" style="flex: 1;">取消</button>
                    <button type="submit" class="btn btn-primary" style="flex: 1;">保存</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // 随机生成昵称
        function generateRandomNickname() {
            const prefixes = ['快乐', '阳光', '微笑', '开心', '活力', '可爱', '聪明', '勇敢', '善良', '温柔', '帅气', '美丽', '机智', '幽默', '活泼', '文静', '热情', '冷静', '大方', '优雅'];
            const suffixes = ['小猫', '小狗', '小兔', '小熊', '小鸟', '小鱼', '小鹿', '小象', '小猴子', '小狐狸', '小羊', '小狼', '小虎', '小狮子', '小熊猫', '小松鼠', '小浣熊', '小刺猬', '小企鹅', '小海豚'];
            const adjectives = ['超级', '无敌', '可爱', '聪明', '勇敢', '善良', '温柔', '帅气', '美丽', '机智'];
            // 使用时间戳和随机数确保唯一性
            const timestamp = Date.now().toString().slice(-4);
            const randomNum = Math.floor(Math.random() * 1000);
            const prefix = prefixes[Math.floor(Math.random() * prefixes.length)];
            const adjective = adjectives[Math.floor(Math.random() * adjectives.length)];
            const suffix = suffixes[Math.floor(Math.random() * suffixes.length)];
            return `${prefix}${adjective}${suffix}${timestamp}${randomNum}`;
        }

        // 存储每个群聊的最后消息时间戳和未读消息数
        let lastMessageTimestamps = {};
        let unreadMessageCounts = {};
        let messagesCheckInterval = null;
        
        // 生成匿名昵称
        function generateAnonymousNickname(userId) {
            // 从用户ID中提取数字部分
            const numericPart = userId.replace(/[^0-9]/g, '');
            // 使用用户ID的最后6个字符
            const randomChars = numericPart.substr(-6);
            return '匿名' + randomChars;
        }

        // 初始化
        function init() {
            // 启动新消息检查定时器
            startMessagesCheck();
            
            // 获取URL参数中的user_id
            const urlParams = new URLSearchParams(window.location.search);
            const urlUserId = urlParams.get('user_id');
            
            // 从服务器获取用户信息
            fetchUserFromServer(urlUserId).then(userData => {
                if (userData) {
                    // 显示用户信息
                    showUserInfo(userData);
                    
                    // 加载群聊列表
                    loadGroups();
                }
            }).catch(error => {
                console.error('初始化失败:', error);
            });
        }
        
        // 从服务器获取用户信息
        function fetchUserFromServer(urlUserId) {
            return new Promise((resolve, reject) => {
                let userData = null;
                
                // 确定用户ID
                let targetUserId = urlUserId;
                if (!targetUserId) {
                    const user = localStorage.getItem('user');
                    if (user) {
                        const tempUser = JSON.parse(user);
                        if (tempUser.id) {
                            targetUserId = tempUser.id;
                        }
                    }
                }
                
                // 无论是否有URL参数，都从服务器获取用户信息
                if (targetUserId) {
                    // 从服务器获取用户信息
                    fetch(`api/chat/get_user_groups.php?user_id=${targetUserId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // 用户存在于服务器，使用服务器上的信息
                                userData = {
                                    id: data.data.user_id,
                                    nickname: data.data.nickname,
                                    avatar: `https://picsum.photos/id/${Math.floor(Math.random() * 1000)}/60/60`,
                                    allow_speak: true,
                                    joined_groups: data.data.joined_groups.map(group => group.original_id),
                                    created_at: new Date().toISOString()
                                };
                                
                                // 保存到localStorage
                                localStorage.setItem('user', JSON.stringify(userData));
                                
                                resolve(userData);
                            } else {
                                // 用户不存在于服务器，创建新用户
                                const nickname = generateAnonymousNickname(targetUserId);
                                userData = {
                                    id: targetUserId,
                                    nickname: nickname,
                                    avatar: `https://picsum.photos/id/${Math.floor(Math.random() * 1000)}/60/60`,
                                    allow_speak: true,
                                    joined_groups: [],
                                    created_at: new Date().toISOString()
                                };
                                
                                // 保存到localStorage
                                localStorage.setItem('user', JSON.stringify(userData));
                                
                                // 自动保存用户到服务器
                                saveUserToServer(userData);
                                
                                resolve(userData);
                            }
                        })
                        .catch(error => {
                            console.error('从服务器获取用户信息失败:', error);
                            
                            // 发生错误，使用本地数据或创建新用户
                            let user = localStorage.getItem('user');
                            if (user) {
                                userData = JSON.parse(user);
                                // 验证用户ID是否有效
                                if (!userData.id || userData.id === 'undefined' || userData.id === null) {
                                    // 用户ID无效，清除本地数据并重新生成
                                    userData = null;
                                    localStorage.removeItem('user');
                                } else {
                                    // 更新昵称为匿名格式
                                    userData.nickname = generateAnonymousNickname(userData.id);
                                    // 保存到localStorage
                                    localStorage.setItem('user', JSON.stringify(userData));
                                }
                            }
                            
                            // 如果没有有效的用户信息，自动生成并注册
                            if (!userData) {
                                // 生成唯一的用户ID
                                const newUserId = targetUserId || 'user_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                                // 生成匿名昵称
                                const nickname = generateAnonymousNickname(newUserId);
                                // 创建用户对象
                                userData = {
                                    id: newUserId,
                                    nickname: nickname,
                                    avatar: `https://picsum.photos/id/${Math.floor(Math.random() * 1000)}/60/60`,
                                    allow_speak: true,
                                    joined_groups: [],
                                    created_at: new Date().toISOString()
                                };
                                
                                // 保存到localStorage
                                localStorage.setItem('user', JSON.stringify(userData));
                                
                                // 自动保存用户到服务器
                                saveUserToServer(userData);
                            }
                            
                            resolve(userData);
                        });
                } else {
                    // 如果没有用户ID，生成新用户
                    // 生成唯一的用户ID
                    const newUserId = 'user_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                    // 生成匿名昵称
                    const nickname = generateAnonymousNickname(newUserId);
                    // 创建用户对象
                    userData = {
                        id: newUserId,
                        nickname: nickname,
                        avatar: `https://picsum.photos/id/${Math.floor(Math.random() * 1000)}/60/60`,
                        allow_speak: true,
                        joined_groups: [],
                        created_at: new Date().toISOString()
                    };
                    
                    // 保存到localStorage
                    localStorage.setItem('user', JSON.stringify(userData));
                    
                    // 自动保存用户到服务器
                    saveUserToServer(userData);
                    
                    resolve(userData);
                }
            });
        }
        
        // 将用户信息保存到服务器
        function saveUserToServer(userData) {
            return new Promise((resolve, reject) => {
                fetch('api/chat/save_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(userData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('用户注册成功:', data);
                        resolve(data);
                    } else {
                        console.error('用户注册失败:', data.message);
                        resolve(data); // 即使失败也继续，因为可能是其他原因
                    }
                })
                .catch(error => {
                    console.error('保存用户到服务器失败:', error);
                    reject(error);
                });
            });
        }
        
        // 显示用户信息
        function showUserInfo(userData) {
            const userInfoDiv = document.getElementById('userInfo');
            userInfoDiv.innerHTML = `
                <div class="user-info-card">
                    <img src="${userData.avatar || 'https://picsum.photos/id/1005/60/60'}" alt="用户头像" class="user-avatar">
                    <div class="user-details">
                        <div class="user-nickname">${userData.nickname}</div>
                        <div class="user-id">ID: ${userData.id}</div>
                    </div>
                </div>
            `;
            userInfoDiv.style.display = 'block';
        }
        
        // 加载群聊列表
        function loadGroups() {
            const messagesList = document.getElementById('messagesList');
            messagesList.innerHTML = '<div class="loading">加载中...</div>';
            
            // 获取用户信息
            const user = localStorage.getItem('user');
            if (!user) {
                messagesList.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-state-icon">💬</div>
                        <h3 class="empty-state-title">暂无群聊</h3>
                        <p class="empty-state-desc">请点击右下角的"+"按钮，使用群聊口令加入群聊</p>
                    </div>
                `;
                return;
            }
            
            const userData = JSON.parse(user);
            
            // 从服务器获取用户的群聊列表
            fetch(`api/chat/get_user_groups.php?user_id=${userData.id}&t=${Date.now()}`) // 添加时间戳防止缓存
                .then(response => response.json())
                .then(data => {
                    console.log('获取用户群聊列表:', data);
                    if (data.success) {
                        // 获取所有群聊信息用于显示
                        fetch(`api/admin/groups.php?t=${Date.now()}`) // 添加时间戳防止缓存
                            .then(response => response.json())
                            .then(allGroups => {
                                console.log('获取所有群聊:', allGroups);
                                if (!Array.isArray(allGroups)) {
                                    allGroups = [];
                                }
                                
                                // 构建群聊ID索引
                                const groupsIndex = {};
                                allGroups.forEach(group => {
                                    groupsIndex[group.id] = group;
                                });
                                
                                // 过滤出用户已加入的群聊，并获取详细信息
                                const userGroups = data.data.joined_groups.map(groupInfo => {
                                    return groupsIndex[groupInfo.original_id] || null;
                                }).filter(Boolean);
                                
                                console.log('用户群聊列表:', userGroups);
                                renderGroupList(userGroups);
                            })
                            .catch(error => {
                                console.error('加载群聊详情失败:', error);
                                messagesList.innerHTML = `
                                    <div class="empty-state">
                                        <div class="empty-state-icon">❌</div>
                                        <h3 class="empty-state-title">加载失败</h3>
                                        <p class="empty-state-desc">请检查网络连接或刷新页面重试</p>
                                    </div>
                                `;
                            });
                    } else {
                        console.log('获取用户群聊列表失败:', data.message);
                        messagesList.innerHTML = `
                            <div class="empty-state">
                                <div class="empty-state-icon">💬</div>
                                <h3 class="empty-state-title">暂无群聊</h3>
                                <p class="empty-state-desc">请点击右下角的"+"按钮，使用群聊口令加入群聊</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('加载群聊失败:', error);
                    messagesList.innerHTML = `
                        <div class="empty-state">
                            <div class="empty-state-icon">❌</div>
                            <h3 class="empty-state-title">加载失败</h3>
                            <p class="empty-state-desc">请检查网络连接或刷新页面重试</p>
                        </div>
                    `;
                });
        }
        
        // 渲染群聊列表
        function renderGroupList(groups) {
            const messagesList = document.getElementById('messagesList');
            
            if (groups.length === 0) {
                messagesList.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-state-icon">💬</div>
                        <h3 class="empty-state-title">暂无群聊</h3>
                        <p class="empty-state-desc">请联系管理员创建群聊</p>
                    </div>
                `;
                return;
            }
            
            messagesList.innerHTML = groups.map(group => {
                const unreadCount = unreadMessageCounts[group.id] || 0;
                return `
                <div class="message-item" onclick="joinGroup('${group.id}')">
                    <img src="${group.avatar || 'https://picsum.photos/id/1/50/50'}" alt="${group.name}" class="avatar">
                    <div class="message-content">
                        <div class="message-header">
                            <div style="display: flex; align-items: center;">
                                <span class="message-sender">${group.name}</span>
                                ${unreadCount > 0 ? `
                                <span style="margin-left: 8px; background-color: var(--danger-color); color: white; border-radius: 10px; padding: 2px 6px; font-size: 12px; min-width: 20px; text-align: center;">
                                    ${unreadCount}
                                </span>
                                ` : ''}
                            </div>
                            <span class="message-time">${new Date(group.created_at).toLocaleDateString('zh-CN', { month: '2-digit', day: '2-digit' })} ${new Date(group.created_at).toLocaleTimeString('zh-CN', { hour: '2-digit', minute: '2-digit' })}</span>
                        </div>
                        <div class="message-preview">
                            ${group.desc || '无介绍'}
                            <span style="margin-left: 8px; font-size: 12px; color: var(--text-secondary);">
                                ${group.members?.length || 0}人
                            </span>
                        </div>
                    </div>
                </div>
            `;
            }).join('');
        }
        
        // 加入群聊
        function joinGroup(groupId) {
            let user = localStorage.getItem('user');
            let userData = null;
            
            // 清除该群聊的未读消息计数
            if (unreadMessageCounts[groupId]) {
                delete unreadMessageCounts[groupId];
            }
            
            if (user) {
                userData = JSON.parse(user);
                // 验证用户ID是否有效
                if (!userData.id || userData.id === 'undefined' || userData.id === null) {
                    // 用户ID无效，清除本地数据并重新生成
                    userData = null;
                    localStorage.removeItem('user');
                } else {
                    // 更新昵称为匿名格式
                    userData.nickname = generateAnonymousNickname(userData.id);
                }
            }
            
            // 如果没有有效的用户信息，自动生成并注册
            if (!userData) {
                // 生成唯一的用户ID
                const newUserId = 'user_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                // 生成匿名昵称
                const nickname = generateAnonymousNickname(newUserId);
                // 创建用户对象
                userData = {
                    id: newUserId,
                    nickname: nickname,
                    avatar: `https://picsum.photos/id/${Math.floor(Math.random() * 1000)}/60/60`,
                    allow_speak: true,
                    joined_groups: [groupId],
                    created_at: new Date().toISOString()
                };
                
                // 保存到localStorage
                localStorage.setItem('user', JSON.stringify(userData));
                
                // 自动保存用户到服务器
                saveUserToServer(userData).then(() => {
                    // 直接进入聊天室
                    window.location.href = `index.php?group_id=${groupId}&user_id=${userData.id}`;
                });
            } else {
                // 将群聊添加到用户的joined_groups数组中
                if (!userData.joined_groups) {
                    userData.joined_groups = [];
                }
                if (!userData.joined_groups.includes(groupId)) {
                    userData.joined_groups.push(groupId);
                    // 保存到localStorage
                    localStorage.setItem('user', JSON.stringify(userData));
                    // 自动保存用户到服务器
                    saveUserToServer(userData).then(() => {
                        // 直接进入聊天室
                        window.location.href = `index.php?group_id=${groupId}&user_id=${userData.id}`;
                    });
                } else {
                    // 直接进入聊天室
                    window.location.href = `index.php?group_id=${groupId}&user_id=${userData.id}`;
                }
            }
        }
        

        
        // 打开更多选项
        function openMoreOptions() {
            alert('更多选项功能开发中');
        }
        
        // 打开用户模态框
        function openUserModal() {
            const user = localStorage.getItem('user');
            if (user) {
                const userData = JSON.parse(user);
                document.getElementById('userNickname').value = userData.nickname;
                document.getElementById('userId').value = userData.id;
            } else {
                // 首次创建用户时，生成一个唯一的ID
                const newUserId = 'user_' + Date.now() + Math.floor(Math.random() * 1000);
                document.getElementById('userId').value = newUserId;
            }
            document.getElementById('userModal').classList.add('active');
        }
        
        // 关闭用户模态框
        function closeUserModal() {
            document.getElementById('userModal').classList.remove('active');
        }
        

        
        // 点击模态框外部关闭
        window.onclick = function(event) {
            const modal = document.getElementById('userModal');
            if (event.target == modal) {
                modal.classList.remove('active');
            }
        }
        
        // 启动新消息检查定时器
        function startMessagesCheck() {
            if (!messagesCheckInterval) {
                // 每3秒检查一次新消息
                messagesCheckInterval = setInterval(checkMessages, 3000);
                // 立即执行一次检查
                checkMessages();
            }
        }
        
        // 停止新消息检查定时器
        function stopMessagesCheck() {
            if (messagesCheckInterval) {
                clearInterval(messagesCheckInterval);
                messagesCheckInterval = null;
            }
        }
        
        // 检查每个群聊的新消息
        function checkMessages() {
            // 获取所有群聊列表
            fetch('api/admin/groups.php')
                .then(response => response.json())
                .then(groups => {
                    if (!Array.isArray(groups)) {
                        groups = [];
                    }
                    
                    // 检查每个群聊的新消息
                    groups.forEach(group => {
                        const groupId = group.id;
                        // 构建请求URL，添加最后消息时间戳参数（如果有）
                        let url = `api/chat/get_messages.php?group_id=${groupId}`;
                        if (lastMessageTimestamps[groupId]) {
                            url += `&last_timestamp=${lastMessageTimestamps[groupId]}`;
                        }
                        
                        fetch(url)
                            .then(res => res.json())
                            .then(messages => {
                                if (messages.length > 0) {
                                    // 检查是否有新消息
                                    const newMessages = messages.filter(msg => msg.timestamp > (lastMessageTimestamps[groupId] || 0));
                                    
                                    if (newMessages.length > 0) {
                                        // 更新未读消息计数
                                        if (!unreadMessageCounts[groupId]) {
                                            unreadMessageCounts[groupId] = 0;
                                        }
                                        unreadMessageCounts[groupId] += newMessages.length;
                                        
                                        // 更新最后消息时间戳
                                        lastMessageTimestamps[groupId] = newMessages[newMessages.length - 1].timestamp;
                                        
                                        // 更新群聊列表显示
                                        loadGroups();
                                    }
                                }
                            })
                            .catch(error => {
                                console.error(`检查群聊 ${groupId} 新消息失败:`, error);
                            });
                    });
                })
                .catch(error => {
                    console.error('获取群聊列表失败:', error);
                });
        }
        
        // 打开加入群聊模态框
        function openJoinGroupModal() {
            document.getElementById('joinGroupModal').classList.add('active');
        }
        
        // 关闭加入群聊模态框
        function closeJoinGroupModal() {
            document.getElementById('joinGroupModal').classList.remove('active');
        }
        
        // 通过群聊口令加入群聊
        function joinGroupByCode() {
            const groupCode = document.getElementById('groupCode').value.trim();
            
            if (!groupCode) {
                alert('请输入群聊口令');
                return;
            }
            
            // 验证群聊是否存在
            fetch(`api/admin/groups.php?group_id=${groupCode}`)
                .then(response => response.json())
                .then(groupData => {
                    // 检查返回的数据格式
                    let group = null;
                    if (groupData && typeof groupData === 'object' && !Array.isArray(groupData) && groupData.id) {
                        group = groupData;
                    } else if (groupData && Array.isArray(groupData) && groupData.length > 0) {
                        // 如果返回的是数组，检查是否有匹配的群聊（同时检查id和custom_group_id）
                        group = groupData.find(g => g.id === groupCode || g.custom_group_id === groupCode);
                    }
                    
                    if (group) {
                        // 群聊存在，显示确认弹窗
                        if (confirm(`确定要加入群聊「${group.name}」吗？`)) {
                            // 用户确认加入，继续加入流程
                            let user = localStorage.getItem('user');
                            let userData = null;
                            
                            // 清除该群聊的未读消息计数
                            if (unreadMessageCounts[group.id]) {
                                delete unreadMessageCounts[group.id];
                            }
                            
                            if (user) {
                                userData = JSON.parse(user);
                                // 验证用户ID是否有效
                                if (!userData.id || userData.id === 'undefined' || userData.id === null) {
                                    // 用户ID无效，清除本地数据并重新生成
                                    userData = null;
                                    localStorage.removeItem('user');
                                } else {
                                    // 检查用户是否已经加入过该群聊
                                    if (userData.joined_groups && userData.joined_groups.includes(group.id)) {
                                        // 用户已经在该群
                                        closeJoinGroupModal();
                                        alert('您已经在该群聊中了！');
                                        return;
                                    }
                                }
                            }
                            
                            // 如果没有有效的用户信息，自动生成并注册
                            if (!userData) {
                                // 生成唯一的用户ID
                                const newUserId = 'user_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                                // 生成匿名昵称
                                const nickname = generateAnonymousNickname(newUserId);
                                // 创建用户对象
                                userData = {
                                    id: newUserId,
                                    nickname: nickname,
                                    avatar: `https://picsum.photos/id/${Math.floor(Math.random() * 1000)}/60/60`,
                                    allow_speak: true,
                                    joined_groups: [group.id],
                                    created_at: new Date().toISOString()
                                };
                                
                                // 保存到localStorage
                                localStorage.setItem('user', JSON.stringify(userData));
                                
                                // 自动保存用户到服务器
                                saveUserToServer(userData).then(() => {
                                    // 关闭模态框
                                    closeJoinGroupModal();
                                    // 刷新群聊列表
                                    loadGroups();
                                    // 显示加入成功提示
                                    alert('加入群聊成功！');
                                });
                            } else {
                                // 更新昵称为匿名格式
                                userData.nickname = generateAnonymousNickname(userData.id);
                                // 将群聊添加到用户的joined_groups数组中
                                if (!userData.joined_groups) {
                                    userData.joined_groups = [];
                                }
                                userData.joined_groups.push(group.id);
                                // 保存到localStorage
                                localStorage.setItem('user', JSON.stringify(userData));
                                // 自动保存用户到服务器
                                saveUserToServer(userData).then(() => {
                                    // 关闭模态框
                                    closeJoinGroupModal();
                                    // 刷新群聊列表
                                    loadGroups();
                                    // 显示加入成功提示
                                    alert('加入群聊成功！');
                                });
                            }
                        }
                    } else {
                        // 群聊不存在
                        alert('群聊不存在，请检查群聊口令是否正确');
                    }
                })
                .catch(error => {
                    console.error('验证群聊失败:', error);
                    alert('验证群聊失败，请检查网络连接');
                });
        }
        
        // 点击模态框外部关闭
        window.onclick = function(event) {
            const userModal = document.getElementById('userModal');
            const joinGroupModal = document.getElementById('joinGroupModal');
            
            if (event.target == userModal) {
                userModal.classList.remove('active');
            }
            
            if (event.target == joinGroupModal) {
                joinGroupModal.classList.remove('active');
            }
        }
        
        // 页面加载完成后初始化
        window.addEventListener('DOMContentLoaded', init);
    </script>
</body>
</html>