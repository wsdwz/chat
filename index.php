
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
            --primary-color: #07C160;
            --primary-light: rgba(7, 193, 96, 0.1);
            --primary-dark: #05A857;
            --secondary-color: #5AC8FA;
            --text-color: #2C3E50;
            --text-secondary: #64748B;
            --text-tertiary: #94A3B8;
            --background-color: #F8FAFC;
            --card-background: #FFFFFF;
            --border-color: #E2E8F0;
            --border-light: #F1F5F9;
            --success-color: #07C160;
            --danger-color: #FF5252;
            --warning-color: #FFB020;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --radius-sm: 6px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
            --radius-full: 9999px;
            --own-message-bg: linear-gradient(135deg, #07C160 0%, #06B65A 100%);
            --other-message-bg: #FFFFFF;
            --announcement-bg: linear-gradient(135deg, #FFFBE6 0%, #FFF9C4 100%);
            --announcement-color: #92400E;
            --transition-fast: 0.15s ease;
            --transition-normal: 0.3s ease;
            --transition-slow: 0.5s ease;
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
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
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
            border-radius: 50%;
            transition: all 0.2s ease;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .back-btn:hover {
            background-color: var(--background-color);
        }
        
        .header-info {
            flex: 1;
            min-width: 0;
        }
        
        .chat-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .chat-status {
            font-size: 10px;
            color: var(--text-secondary);
            font-weight: 400;
        }
        

        
        /* 消息区域 */
        .messages-area {
            flex: 1;
            padding: 10px;
            padding-top: 40px;
            padding-bottom: 10px;
            overflow-y: auto;
            background-color: #F9FAFB;
            display: flex;
            flex-direction: column;
            gap: 16px;
            overflow-x: hidden;
        }
        
        /* 消息气泡 */
        .message {
            display: flex;
            position: relative;
            animation: messageSlideIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        @keyframes messageSlideIn {
            from {
                opacity: 0;
                transform: translateY(10px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .message.own {
            justify-content: flex-end;
        }
        
        .message.other {
            justify-content: flex-start;
        }
        
        .message-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            margin: 0 10px 0 0;
            border: 1px solid #E5E7EB;
            transition: all 0.2s ease;
        }
        
        .message-avatar:hover {
            transform: scale(1.05);
            border-color: #D1D5DB;
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
        
        .message.own .message-content-wrapper {
            align-items: flex-end;
        }
        
        .message.other .message-content-wrapper {
            align-items: flex-start;
        }
        
        .message-sender {
            font-size: 11px;
            color: var(--text-secondary);
            margin-bottom: 6px;
            letter-spacing: 0.1px;
            position: relative;
        }
        
        .message-content {
            max-width: 100%;
            padding: 12px 16px;
            border-radius: 5px;
            position: relative;
            word-wrap: break-word;
            line-height: 1.5;
            font-size: 12px;
            transition: all 0.2s ease;
        }
        
        .message.other .message-content {
            background: #FFFFFF;
            color: #111827;
        }
        
        .message.own .message-content {
            background: #3D7EFF;
            color: white;
            border-radius: 5px;
        }
        
        .message-content:hover {
            /*box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);*/
        }
        

        
        .message-time {
            font-size: 9px;
            color: #9CA3AF;
            margin-top: 5px;
            font-weight: 400;
        }
        
        .message.own .message-time {
            text-align: right;
            color: #9CA3AF;
        }
        
        .message.other .message-time {
            text-align: left;
        }
        
        /* 群助手消息 */
        .message.helper {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 16px;
        }
        
        .helper-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
            border: 1px solid #E5E7EB;
        }
        

        
        .welfare-content {
            flex: 1;
            font-size: 14px;
            color: #92400E;
            font-weight: 500;
        }
        
        .welfare-actions {
            display: flex;
            gap: 10px;
        }
        
        .welfare-btn {
            background: linear-gradient(135deg, #FFFFFF 0%, #FFFBE6 100%);
            border: 1px solid #FFD700;
            color: #92400E;
            padding: 6px 16px;
            border-radius: 16px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 6px rgba(255, 193, 7, 0.1);
        }
        
        .welfare-btn:hover {
            background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.2);
        }
        
        .welfare-btn:active {
            transform: translateY(0);
        }
        
        /* 消息内容样式 */
        .message-text {
            word-wrap: break-word;
            font-size: 15px;
            line-height: 1.4;
        }
        
        /* 图片消息样式 - 移动端优化 */
        .message-image {
            max-width: 100%;
            max-height: 200px;
            border-radius: px;
            margin: 8px 0;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            object-fit: cover;
            position: relative;
            overflow: hidden;
        }
        
        /* 图片加载动画 */
        .message-image::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 32px;
            height: 32px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top: 3px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            opacity: 0;
            z-index: 1;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .message-image.loading::before {
            opacity: 1;
        }
        
        /* 图片加载遮罩 */
        .message-image::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, transparent 0%, rgba(0, 0, 0, 0.05) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1;
        }
        
        .message-image:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }
        
        .message-image:hover::after {
            opacity: 1;
        }
        
        /* 适配不同屏幕尺寸的图片大小 */
        @media (max-width: 400px) {
            .message-image {
                max-height: 180px;
            }
        }
        
        @media (min-width: 600px) {
            .message-image {
                max-height: 280px;
            }
        }
        
        /* 图片消息容器效果 */
        .message-content:has(.message-image) {
            padding: 8px;
        }
        
        /* 添加图片边框高光效果 */
        .message-image {
            box-shadow: 
                0 4px 12px rgba(0, 0, 0, 0.1),
                inset 0 0 0 1px rgba(255, 255, 255, 0.5);
        }
        
        /* 视频消息样式 - 与图片样式保持一致 */
        .message-video {
            max-width: 80%;
            max-height: 200px;
            border-radius: 12px;
            margin: 8px 0;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            object-fit: cover;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .message-video:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }
        
        /* 适配不同屏幕尺寸的视频大小 */
        @media (max-width: 400px) {
            .message-video {
                max-height: 180px;
                max-width: 70%;
            }
        }
        
        @media (min-width: 600px) {
            .message-video {
                max-height: 250px;
                max-width: 60%;
            }
        }
        
        /* 视频消息容器效果 */
        .message-content:has(.message-video) {
            padding: 8px;
        }
        
        .message-file {
            display: block;
            padding: 12px;
            background-color: rgba(0, 0, 0, 0.1);
            color: var(--text-color);
            text-decoration: none;
            border-radius: var(--radius-sm);
            margin: 8px 0;
            font-size: 14px;
            transition: background-color 0.2s ease;
        }
        
        .message-file:hover {
            background-color: rgba(0, 0, 0, 0.15);
        }
        
        .message-url {
            color:#2DE88D;
            /*text-decoration: none;*/
            word-break: break-all;
        }
        
        .message-url:hover {
            text-decoration: underline;
        }
        
        /* 快捷功能区域 - 修复移动端不显示问题 */
        .quick-actions {
            display: flex !important;
            gap: 12px;
            padding: 8px 10px;
            background-color: #F9FAFB;
            overflow-x: auto !important;
            position: static !important;
            z-index: 100 !important;
            /* 隐藏横向滚动条 */
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
            flex-shrink: 0 !important;
            opacity: 1 !important;
            visibility: visible !important;
            width: 100% !important;
            height: auto !important;
            min-height: 48px !important;
            box-sizing: border-box !important;
            left: 0 !important;
            right: 0 !important;
            margin: 0 !important;
            align-items: center !important;
            order: 0 !important;
            flex-wrap: nowrap !important;
        }
        
        /* Chrome, Safari and Opera */
        .quick-actions::-webkit-scrollbar {
            display: none;
        }
        
        .quick-action-item {
            display: flex;
            align-items: center;
            gap: 6px;
            min-width: auto;
            cursor: pointer;
            transition: all 0.2s ease;
            padding: 8px 16px;
            border-radius: 5px;
            position: relative;
            z-index: 2;
            background: #fff;
        }
        
        .quick-action-item:hover {
            /*background: linear-gradient(135deg, #E2E8F0 0%, #CBD5E1 100%);*/
            /*transform: translateY(-1px);*/
            /*box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);*/
        }
        
        .quick-action-item:active {
            transform: translateY(0);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
        
        .quick-action-icon {
            font-size: 12px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            position: relative;
        }
        
        .quick-action-text {
            font-size: 10px;
            color: var(--text-color);
            text-align: center;
            white-space: nowrap;
            font-weight: 500;
            letter-spacing: 0px;
            transition: all 0.2s ease;
        }
        
        /* 数字徽章样式 */
        .quick-action-item::before {
            content: attr(data-count);
            position: absolute;
            top: -6px;
            right: -6px;
            background: var(--danger-color);
            color: white;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 8px;
            min-width: 18px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            border: 2px solid white;
            display: none;
        }
        
        .quick-action-item.has-count::before {
            display: block;
        }
        
        /* 输入区域 */
        .input-area {
            background: white;
            position: sticky;
            bottom: 0;
            z-index: 100;
            display: flex;
            flex-direction: column;
        }
        
        .input-row {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #FFFFFF;
                padding: 8px 10px;
            height: auto;
            min-height: 40px;
            transition: all 0.2s ease;
        }
        
        

        
        #messageInput {
            flex: 1;
            padding: 8px 16px;
            border: 1px solid var(--border-light);
            border-radius: 3px;
            resize: none;
            font-size: 13px;
            max-height: 40px;
            overflow-y: auto;
            background-color: #F5F7FA;
            font-family: inherit;
            outline: none;
            line-height: 1.5;
            color: var(--text-color);
            font-weight: 400;
            letter-spacing: normal;
        }
        
        #messageInput:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(7, 193, 96, 0.1);
        }
        
        #messageInput::placeholder {
            color: var(--text-tertiary);
            font-weight: 400;
            opacity: 1;
            font-style: normal;
        }
        
        #messageInput::-webkit-scrollbar {
            width: 4px;
        }
        
        #messageInput::-webkit-scrollbar-track {
            background: transparent;
            border-radius: 2px;
        }
        
        #messageInput::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 2px;
        }
        
        #sendBtn {
            display: none;
        }
        
        /* 隐藏语音音频元素 */
        .voice-audio {
            display: none;
        }
        
        /* 语音消息样式 - 适配移动端 */
        .voice-message {
            min-width: 120px;
            max-width: 240px;
        }
        
        /* 移动端优化 - 确保语音消息在小屏幕上显示正常 */
        @media (max-width: 600px) {
            .voice-message {
                max-width: 70vw;
            }
            
            .voice-play-btn {
                font-size: 20px !important;
            }
            
            .voice-duration {
                font-size: 11px !important;
            }
            
            /* 修复底部标签在移动端不显示问题 */
            .quick-actions {
                display: flex !important;
                gap: 12px !important;
                padding: 10px 10px !important;
                overflow-x: auto !important;
                position: static !important;
                z-index: 100 !important;
                min-height: 55px !important;
                flex-shrink: 0 !important;
                opacity: 1 !important;
                box-sizing: border-box !important;
                background-color: #F9FAFB;
                visibility: visible !important;
                width: 100% !important;
                left: 0 !important;
                right: 0 !important;
                margin: 0 !important;
                align-items: center !important;
                order: 0 !important;
                flex-wrap: nowrap !important;
                box-sizing: border-box !important;
            }
            
            .quick-action-item {
                display: flex !important;
                align-items: center !important;
                gap: 6px !important;
                padding: 8px 16px !important;
                border-radius: 5px !important;
                background: white !important;
                min-width: auto !important;
                cursor: pointer !important;
                transition: all 0.2s ease !important;
            }
            
            .quick-action-icon {
                font-size: 12px !important;
            }
            
            .quick-action-text {
                font-size: 10px !important;
                margin-right: 3px;
            }
        }
        

        
        /* 隐藏的文件输入 */
        .hidden-input {
            display: none;
        }
        
        /* 隐藏滚动条 */
        .messages-area {
            /* 隐藏滚动条，但仍可滚动 */
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
        
        .messages-area::-webkit-scrollbar {
            /* Chrome, Safari and Opera */
            display: none;
        }
        
        /* 群公告样式 - 输入框上方标签 */
        .announcement-bar {
            background: linear-gradient(135deg, #FFF3E0 0%, #FFE0B2 100%);
            color: #E65100;
            padding: 6px 14px;
            margin: 0 10px 10px;
            border-radius: 25px;
            border: 1px solid #FFCC80;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            position: relative;
            z-index: 99;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(255, 167, 38, 0.3);
            animation: announcementSlideIn 0.3s ease;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 500;
        }
        
        .announcement-bar:hover {
            background: linear-gradient(135deg, #FFE0B2 0%, #FFCC80 100%);
            box-shadow: 0 3px 8px rgba(255, 167, 38, 0.4);
            transform: translateY(-1px);
        }
        
        .announcement-bar:active {
            transform: translateY(0);
            box-shadow: 0 1px 4px rgba(255, 167, 38, 0.3);
        }
        
        @keyframes announcementSlideIn {
            from {
                opacity: 0;
                transform: translateY(-10px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .announcement-content {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
            overflow: hidden;
        }
        
        .announcement-icon {
            color: #FF8C00;
            flex-shrink: 0;
            font-weight: 600;
        }
        
        .announcement-text {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 11px;
            color: #E65100;
            max-width: 100%;
        }
        
        /* 模态框样式 - 现代手机APP风格 */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            animation: modalFadeIn 0.25s ease;
            backdrop-filter: blur(10px);
        }
        
        .modal.active {
            display: flex;
            align-items: flex-end;
            justify-content: center;
        }
        
        @keyframes modalFadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        .modal-content {
            background: #FFFFFF;
            border-radius: 24px 24px 0 0;
            width: 100%;
            max-width: 600px;
            max-height: 85vh;
            overflow: hidden;
            animation: modalSlideUp 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.15);
            position: relative;
            transform-origin: bottom center;
        }
        
        @keyframes modalSlideUp {
            from {
                opacity: 0;
                transform: translateY(100%) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        /* 关闭动画 */
        @keyframes modalSlideDown {
            from {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
            to {
                opacity: 0;
                transform: translateY(100%) scale(0.95);
            }
        }
        
        @keyframes modalFadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
            }
        }
        
        /* 关闭动画类 */
        .modal.closing {
            animation: modalFadeOut 0.3s ease forwards;
        }
        
        .modal-content.closing {
            animation: modalSlideDown 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }
        
        /* 为公告模态框添加单独的关闭动画 */
        .announcement-modal.closing {
            animation: modalZoomOut 0.3s ease forwards;
        }
        
        /* 公告弹窗关闭动画 - 缩放淡出 */
        @keyframes modalZoomOut {
            from {
                opacity: 1;
                transform: scale(1);
            }
            to {
                opacity: 0;
                transform: scale(0.9);
            }
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            background: #F8FAFC;
            border-bottom: 1px solid #E2E8F0;
        }
        
        .modal-title {
            font-size: 18px;
            font-weight: 600;
            color: #1E293B;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .modal-title::before {
            content: "📢";
            font-size: 20px;
        }
        
        .modal-close {
            background: #E2E8F0;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #64748B;
            padding: 0;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s ease;
        }
        
        .modal-close:hover {
            background: #CBD5E1;
            color: #475569;
            transform: scale(1.1);
        }
        
        .modal-close:active {
            transform: scale(0.95);
        }
        
        .modal-content::-webkit-scrollbar {
            width: 5px;
        }
        
        .modal-content::-webkit-scrollbar-track {
            background: #F1F5F9;
            border-radius: 2.5px;
        }
        
        .modal-content::-webkit-scrollbar-thumb {
            background: #94A3B8;
            border-radius: 2.5px;
        }
        
        .modal-content::-webkit-scrollbar-thumb:hover {
            background: #64748B;
        }
        
        .modal-body {
            padding: 24px;
            font-size: 15px;
            line-height: 1.6;
            color: #334155;
            background: #FFFFFF;
            overflow-y: auto;
        }
        
        /* 公告内容模态框样式 */
        .announcement-modal {
            width: 100%;
            background: white;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .announcement-header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .announcement-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }
        
        .announcement-main {
            text-align: center;
            padding: 20px 0;
        }
        
        .announcement-icon {
            font-size: 60px;
            margin-bottom: 20px;
            display: block;
        }
        
        .announcement-text {
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            margin-bottom: 20px;
            white-space: pre-wrap;
            word-break: break-word;
        }
        
        .announcement-footer {
            text-align: center;
        }
        
        .announcement-button {
            background: #1677FF;
            color: white;
            border: none;
            padding: 12px 40px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            width: 100%;
            max-width: 300px;
        }
        
        .announcement-button:hover {
            background: #4096FF;
        }
        
        .announcement-button:active {
            background: #0958D9;
        }
        
        /* 公告内容样式 */
        #announcementModalContent {
            white-space: pre-wrap;
            word-break: break-word;
        }
        
        #announcementModalContent h1, 
        #announcementModalContent h2, 
        #announcementModalContent h3 {
            font-size: 16px;
            font-weight: 600;
            color: #1E293B;
            margin: 16px 0 8px 0;
            line-height: 1.4;
        }
        
        #announcementModalContent p {
            margin: 10px 0;
        }
        
        #announcementModalContent p:first-child {
            margin-top: 0;
        }
        
        #announcementModalContent ul, 
        #announcementModalContent ol {
            margin: 10px 0 10px 20px;
            padding: 0;
        }
        
        #announcementModalContent li {
            margin: 6px 0;
            padding-left: 8px;
        }
        
        #announcementModalContent strong {
            color: #1E293B;
            font-weight: 600;
        }
        
        #announcementModalContent em {
            color: #64748B;
            font-style: italic;
        }
        
        #announcementModalContent a {
            color: #3B82F6;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        #announcementModalContent a:hover {
            color: #2563EB;
            text-decoration: underline;
        }
        

        
        /* 输入提示 */
        .typing-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: 56px;
            margin-bottom: 8px;
        }
        
        .typing-dots {
            display: flex;
            gap: 4px;
        }
        
        .typing-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: var(--text-secondary);
            animation: typing 1.4s infinite ease-in-out;
        }
        
        .typing-dot:nth-child(1) {
            animation-delay: -0.32s;
        }
        
        .typing-dot:nth-child(2) {
            animation-delay: -0.16s;
        }
        
        @keyframes typing {
            0%, 80%, 100% {
                transform: scale(0);
                opacity: 0.5;
            }
            40% {
                transform: scale(1);
                opacity: 1;
            }
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
        

    
        /* ======= 消息卡片及转发历史记录专属样式 ======= */
        .message-card { display: flex; flex-direction: column; background: #FFFFFF; border: 1px solid rgba(0, 0, 0, 0.06); border-radius: 12px; width: 250px; text-decoration: none; cursor: pointer; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03); transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); overflow: hidden; position: relative; }
        .message.own .message-content:has(.message-card), .message.other .message-content:has(.message-card) { background: transparent !important; padding: 0 !important; box-shadow: none !important; border: none !important; }
        .message-card:active { transform: scale(0.98); background: #F9FAFB; }
        .message-card-body { display: flex; padding: 14px; gap: 12px; align-items: flex-start; }
        .message-card-info { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .message-card-title { font-size: 15px; font-weight: 500; color: #111827; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 6px; word-break: break-all; }
        .message-card-desc { font-size: 12px; color: #6B7280; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; word-break: break-all; }
        .message-card-thumb { width: 52px; height: 52px; border-radius: 8px; object-fit: cover; flex-shrink: 0; border: 1px solid rgba(0,0,0,0.05); background-color: #F3F4F6; }
        .message-card-footer { padding: 8px 14px; font-size: 11px; color: #9CA3AF; border-top: 1px solid rgba(0,0,0,0.04); background: rgba(249, 250, 251, 0.6); display: flex; align-items: center; gap: 6px; }

        /* 聊天记录转发卡片 */
        .message-history-card { display: flex; flex-direction: column; background: #FFFFFF; border: 1px solid rgba(0, 0, 0, 0.06); border-radius: 12px; width: 250px; text-decoration: none; cursor: pointer; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03); transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); padding: 12px 14px 10px 14px; box-sizing: border-box; }
        .message.own .message-content:has(.message-history-card), .message.other .message-content:has(.message-history-card) { background: transparent !important; padding: 0 !important; box-shadow: none !important; border: none !important; }
        .message-history-card:active { transform: scale(0.98); background: #F9FAFB; }
        .message-history-title { font-size: 14px; font-weight: 500; color: #111827; margin-bottom: 8px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .message-history-list { display: flex; flex-direction: column; gap: 4px; margin-bottom: 10px; }
        .message-history-item { font-size: 12px; color: #6B7280; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.4; }
        .message-history-footer { font-size: 11px; color: #9CA3AF; padding-top: 8px; border-top: 1px solid rgba(0,0,0,0.04); display: flex; align-items: center; justify-content: space-between; }

        /* 微信原生全屏聊天记录弹窗 */
        .wx-history-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #EDEDED; z-index: 2000; animation: wxSlideIn 0.3s cubic-bezier(0.25, 1, 0.5, 1); }
        .wx-history-modal.active { display: flex; flex-direction: column; }
        .wx-history-modal.closing { animation: wxSlideOut 0.3s cubic-bezier(0.25, 1, 0.5, 1) forwards; }
        @keyframes wxSlideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }
        @keyframes wxSlideOut { from { transform: translateX(0); } to { transform: translateX(100%); } }
        .wx-header { height: 50px; min-height: 50px; display: flex; align-items: center; justify-content: space-between; background: #EDEDED; padding: 0 16px; position: relative; border-bottom: 0.5px solid rgba(0,0,0,0.08); padding-top: env(safe-area-inset-top); }
        .wx-close { font-size: 16px; color: #181818; background: none; border: none; display: flex; align-items: center; cursor: pointer; padding: 10px 10px 10px 0; }
        .wx-close::before { content: ''; width: 10px; height: 10px; border-left: 2px solid #181818; border-bottom: 2px solid #181818; transform: rotate(45deg); margin-right: 6px; }
        .wx-title { font-size: 17px; font-weight: 500; color: #181818; position: absolute; left: 50%; transform: translateX(-50%); }
        .wx-body { flex: 1; overflow-y: auto; background: #FFFFFF; padding-bottom: 30px; }
        .wx-item { display: flex; padding: 16px 20px; border-bottom: 0.5px solid rgba(0,0,0,0.04); }
        .wx-avatar { width: 42px; height: 42px; border-radius: 6px; margin-right: 12px; background: #E5E7EB; object-fit: cover; }
        .wx-content { flex: 1; min-width: 0; }
        .wx-name { font-size: 14px; color: #888888; margin-bottom: 4px; }
        .wx-text { font-size: 16px; color: #111111; line-height: 1.5; word-wrap: break-word; }

        /* 多选模式样式 */
        body.selection-mode .input-area { display: none !important; }
        body.selection-mode #selectionBottomBar { display: flex; }
        .message { transition: padding 0.25s ease; position: relative; }
        body.selection-mode .message { padding-left: 45px; }
        body.selection-mode .message.own { padding-right: 10px; } 
        .msg-checkbox { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 24px; height: 24px; border-radius: 50%; border: 1px solid #C9C9C9; background: #FFFFFF; display: none; align-items: center; justify-content: center; z-index: 10; box-sizing: border-box; }
        body.selection-mode .msg-checkbox { display: flex; }
        .message.selected .msg-checkbox { background: #07C160; border-color: #07C160; }
        .message.selected .msg-checkbox::after { content: ''; width: 6px; height: 11px; border: solid white; border-width: 0 2px 2px 0; transform: rotate(45deg); margin-bottom: 2px; }

        /* 底部操作菜单 (Action Sheet) */
        .action-sheet-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 3000; opacity: 0; transition: opacity 0.3s; }
        .action-sheet-backdrop.active { opacity: 1; }
        .action-sheet { position: fixed; bottom: 0; left: 0; width: 100%; background: #F2F2F2; z-index: 3001; transform: translateY(100%); transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); border-radius: 16px 16px 0 0; overflow: hidden; }
        .action-sheet.active { transform: translateY(0); }
        .action-sheet-menu { background: #FFFFFF; margin-bottom: 8px; border-radius: 16px 16px 0 0; }
        .action-sheet-item { padding: 16px; text-align: center; font-size: 16px; color: #333; border-bottom: 1px solid rgba(0,0,0,0.05); cursor: pointer; background: #FFFFFF; }
        .action-sheet-item:active { background: #E5E5E5; }
        .action-sheet-item.primary { color: #07C160; font-weight: 500; }
        .action-sheet-cancel { padding: 16px; text-align: center; font-size: 16px; color: #333; background: #FFFFFF; cursor: pointer; font-weight: 500; padding-bottom: calc(16px + env(safe-area-inset-bottom)); }
        .action-sheet-cancel:active { background: #E5E5E5; }

        #selectionBottomBar { display: none; position: fixed; bottom: 0; left: 0; width: 100%; height: 70px; background: #F7F7F7; border-top: 0.5px solid #EBEBEB; z-index: 1000; justify-content: space-around; align-items: center; padding-bottom: env(safe-area-inset-bottom); }
        .sel-btn { display: flex; flex-direction: column; align-items: center; gap: 6px; background: none; border: none; color: #333; font-size: 12px; cursor: pointer; }
        .sel-icon { font-size: 22px; color: #333; }

</style>
</head>
<body>
    <div class="chat-container">
        <!-- 顶部导航栏 -->
        
        
        <!-- 消息区域 -->
        <div class="messages-area" id="messagesArea">
            <div class="loading" style="font-size:12px;">加载中...</div>
        </div>
        
        <!-- 新消息提示 -->
            <div id="newMessageNotification" class="new-message-notification" style="display: none; position: fixed; bottom: 120px; left: 50%; transform: translateX(-50%); background-color: rgba(0, 0, 0, 0.8); color: white; padding: 12px 24px; border-radius: 25px; cursor: pointer; z-index: 9999; font-size: 16px; font-weight: bold; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);">
                🔔 有新消息，点击查看
            </div>
        
        <!-- 输入区域 -->
        <div class="input-area">
            <!-- 快捷功能区域 -->
            <div class="quick-actions" id="quickActionsContainer">
                <!-- 底部标签将动态加载 -->
            </div>
            
            <div class="input-row" id="textInputRow">
                <textarea id="messageInput" placeholder="想对TA说点什么呢?" oninput="autoResize(this)"></textarea>
            </div>
        </div>
        
        <script>
            // 禁止双击放大缩小页面
            document.addEventListener('dblclick', function(e) {
                e.preventDefault();
            });
            
            // 禁止触摸缩放
            document.addEventListener('touchstart', function(e) {
                if (e.touches.length > 1) {
                    e.preventDefault();
                }
            }, { passive: false });
            
            // 禁止捏合缩放
            document.addEventListener('gesturestart', function(e) {
                e.preventDefault();
            });
        </script>
        
        <!-- 群聊设置模态框 -->
        <div class="modal" id="chatSettingsModal">
            <div class="modal-content" style="max-width: 500px;">
                <div class="modal-header">
                    <button class="back-btn" onclick="closeChatSettingsModal()">&larr;</button>
                    <h3 class="modal-title">群聊设置</h3>
                    <button class="modal-close" onclick="closeChatSettingsModal()">&times;</button>
                </div>
                <div class="modal-body" style="padding: 0;">                    
                    <!-- 群聊信息 -->
                    <div style="padding: 16px; border-bottom: 1px solid var(--border-color);">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                            <img id="settingGroupAvatar" src="https://picsum.photos/id/1/80/80" alt="群聊头像" style="width: 80px; height: 80px; border-radius: 12px; object-fit: cover;">
                            <div style="flex: 1;">
                                <div style="font-size: 18px; font-weight: 600; margin-bottom: 4px;">群聊名称</div>
                                <div style="font-size: 14px; color: var(--text-secondary);">群聊ID: 123456789</div>
                            </div>
                            <button style="background: none; border: none; font-size: 18px; color: var(--primary-color); cursor: pointer;">›</button>
                        </div>
                    </div>
                    
                    <!-- 群聊成员 -->
                    <div style="padding: 16px; border-bottom: 1px solid var(--border-color);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                            <div style="font-size: 16px; font-weight: 600;">群聊成员</div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 14px; color: var(--text-secondary);">查看105名群成员</span>
                                <button style="background: none; border: none; font-size: 16px; color: var(--primary-color); cursor: pointer;">›</button>
                            </div>
                        </div>
                        
                        <!-- 成员列表 -->
                        <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                            <!-- 显示部分成员 -->
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 4px; width: 60px;">
                                <img src="https://picsum.photos/id/1005/60/60" alt="成员头像" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover;">
                                <div style="font-size: 12px; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">成员1</div>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 4px; width: 60px;">
                                <img src="https://picsum.photos/id/1006/60/60" alt="成员头像" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover;">
                                <div style="font-size: 12px; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">成员2</div>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 4px; width: 60px;">
                                <img src="https://picsum.photos/id/1007/60/60" alt="成员头像" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover;">
                                <div style="font-size: 12px; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">成员3</div>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 4px; width: 60px;">
                                <img src="https://picsum.photos/id/1008/60/60" alt="成员头像" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover;">
                                <div style="font-size: 12px; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">成员4</div>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 4px; width: 60px;">
                                <div style="width: 60px; height: 60px; border-radius: 50%; background-color: #f0f0f0; display: flex; align-items: center; justify-content: center; font-size: 24px; color: var(--primary-color);">+</div>
                                <div style="font-size: 12px; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">邀请</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 群聊信息设置 -->
                    <div style="padding: 16px;">
                        <div style="margin-bottom: 16px;">
                            <div style="font-size: 16px; font-weight: 600; margin-bottom: 12px;">群聊信息</div>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--border-color);">
                                <div style="font-size: 14px;">群聊名称</div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="font-size: 14px; color: var(--text-secondary);">群聊名称</span>
                                    <button style="background: none; border: none; font-size: 16px; color: var(--primary-color); cursor: pointer;">›</button>
                                </div>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--border-color);">
                                <div style="font-size: 14px;">群号和二维码</div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="font-size: 14px; color: var(--text-secondary);">@12345678</span>
                                    <button style="background: none; border: none; font-size: 16px; color: var(--primary-color); cursor: pointer;">›</button>
                                </div>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--border-color);">
                                <div style="font-size: 14px;">群公告</div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="font-size: 14px; color: var(--text-secondary);">未设置</span>
                                    <button style="background: none; border: none; font-size: 16px; color: var(--primary-color); cursor: pointer;">›</button>
                                </div>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--border-color);">
                                <div style="font-size: 14px;">我的本群昵称</div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="font-size: 14px; color: var(--text-secondary);">未设置</span>
                                    <button style="background: none; border: none; font-size: 16px; color: var(--primary-color); cursor: pointer;">›</button>
                                </div>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--border-color);">
                                <div style="font-size: 14px;">群聊备注</div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="font-size: 14px; color: var(--text-secondary);">未设置</span>
                                    <button style="background: none; border: none; font-size: 16px; color: var(--primary-color); cursor: pointer;">›</button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 群内功能 -->
                        <div>
                            <div style="font-size: 16px; font-weight: 600; margin-bottom: 12px;">群内功能</div>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--border-color);">
                                <div style="font-size: 14px;">群应用中心</div>
                                <button style="background: none; border: none; font-size: 16px; color: var(--primary-color); cursor: pointer;">›</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        
        <!-- 修改头像模态框 -->
        <div class="modal" id="changeAvatarModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">修改头像</h3>
                    <button class="modal-close" onclick="closeChangeAvatarModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <div style="display: flex; flex-direction: column; align-items: center; gap: 16px;">
                        <div style="position: relative;">
                            <img src="${userData.avatar || 'https://picsum.photos/id/1005/120/120'}" alt="当前头像" id="currentAvatar" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover;">
                            <div style="position: absolute; bottom: 0; right: 0; background-color: var(--primary-color); color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                <label for="avatarUpload" style="cursor: pointer; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">📷</label>
                                <input type="file" id="avatarUpload" accept="image/*" class="hidden-input" onchange="handleAvatarUpload(this)">
                            </div>
                        </div>
                        <div style="text-align: center;">
                            <p style="color: var(--text-secondary); font-size: 14px;">点击相机图标选择新头像</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 图片预览模态框 -->
        <div class="modal" id="imagePreviewModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.95); z-index: 2000; align-items: center; justify-content: center; backdrop-filter: blur(15px);">
            <div style="position: relative; width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px;">
                <!-- 图片容器 -->
                <div style="flex: 1; display: flex; align-items: center; justify-content: center; width: 100%; max-width: 90vw; max-height: 90vh; padding: 10px;">
                    <img id="previewImage" style="max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 12px; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5); background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.1) 100%); backdrop-filter: blur(5px);">
                </div>
                
                <!-- 加载动画 -->
                <div id="previewLoading" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 60px; height: 60px; border: 4px solid rgba(255, 255, 255, 0.1); border-top: 4px solid var(--primary-color); border-radius: 50%; animation: spin 1s linear infinite; display: none; z-index: 2001;"></div>
                
                <!-- 错误提示 -->
                <div id="previewError" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(255, 87, 34, 0.9); color: white; padding: 15px 25px; border-radius: 8px; font-size: 16px; display: none; z-index: 2001; backdrop-filter: blur(10px); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);"></div>
                
                <!-- 底部操作栏 -->
                <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(0, 0, 0, 0.9)); padding: 25px 15px 15px; display: flex; justify-content: center; gap: 8px; backdrop-filter: blur(10px);">
                    <button id="previewZoomOut" style="background: rgba(255, 255, 255, 0.15); color: white; border: 1px solid rgba(255, 255, 255, 0.3); padding: 6px; border-radius: 6px; cursor: pointer; font-size: 16px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(15px); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M11 4a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V4.5A.5.5 0 0 1 11 4z"/>
                        </svg>
                    </button>
                    <button id="previewReset" style="background: rgba(255, 255, 255, 0.15); color: white; border: 1px solid rgba(255, 255, 255, 0.3); padding: 6px; border-radius: 6px; cursor: pointer; font-size: 16px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(15px); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                            <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                        </svg>
                    </button>
                    <button id="previewZoomIn" style="background: rgba(255, 255, 255, 0.15); color: white; border: 1px solid rgba(255, 255, 255, 0.3); padding: 6px; border-radius: 6px; cursor: pointer; font-size: 16px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(15px); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 4a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V4.5A.5.5 0 0 1 8 4z"/>
                            <path d="M4 8a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6A.5.5 0 0 1 4 8z"/>
                        </svg>
                    </button>
                    <button id="previewDownload" style="background: rgba(255, 255, 255, 0.15); color: white; border: 1px solid rgba(255, 255, 255, 0.3); padding: 6px; border-radius: 6px; cursor: pointer; font-size: 16px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(15px); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1z"/>
                            <path d="M7 11.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5z"/>
                            <path d="M7 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5z"/>
                            <path d="M6 2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5z"/>
                        </svg>
                    </button>
                    <button id="closePreviewBtn" style="background: rgba(255, 255, 255, 0.15); color: white; border: 1px solid rgba(255, 255, 255, 0.3); padding: 6px; border-radius: 6px; cursor: pointer; font-size: 16px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(15px); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                        </svg>
                    </button>
                </div>
                
                <!-- 顶部操作栏 -->
                <div style="position: absolute; top: 0; left: 0; right: 0; background: linear-gradient(rgba(0, 0, 0, 0.9), transparent); padding: 20px; display: flex; justify-content: flex-end; backdrop-filter: blur(10px);">
                    <!-- 可以在这里添加更多顶部操作按钮 -->
                </div>
            </div>
        </div>
        
        <!-- 公告内容模态框 -->
        <div class="modal" id="announcementModal">
            <div class="modal-content announcement-modal">
                <div class="announcement-header">
                    <div class="announcement-title">群公告</div>
                </div>
                <div class="announcement-main">
                    <div id="announcementModalContent" class="announcement-text"></div>
                </div>
                <div class="announcement-footer">
                    <button class="announcement-button" onclick="closeAnnouncementModal()">我知道了哇</button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // 获取URL参数
        const urlParams = new URLSearchParams(window.location.search);
        let groupId = urlParams.get('group_id');
        let userId = urlParams.get('user_id');
        
        let userData = null;
        let groupData = null;
        let pollingInterval = null;
        let lastMessageTimestamp = null;
        
        // 生成匿名昵称
        function generateAnonymousNickname(userId) {
            // 从用户ID中提取数字部分
            const numericPart = userId.replace(/[^0-9]/g, '');
            // 使用用户ID的最后6个字符
            const randomChars = numericPart.substr(-6);
            return '匿名' + randomChars;
        }

        // 随机生成昵称（保留函数但不再使用）
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

        // 初始化
        function init() {
            // 处理URL参数
            let currentGroupId = groupId;
            let currentUserId = userId;
            
            // 1. 确保用户数据存在（无论是否有userId参数）
            let storedUser = localStorage.getItem('user');
            
            if (storedUser) {
                userData = JSON.parse(storedUser);
                // 总是使用本地用户ID，无论URL中是否有user_id
                currentUserId = userData.id;
                // 更新昵称为匿名格式
                userData.nickname = generateAnonymousNickname(userData.id);
                // 保存到localStorage
                localStorage.setItem('user', JSON.stringify(userData));
                // 自动保存用户到服务器
                saveUserToServer(userData);
            } else {
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
                // 更新currentUserId
                currentUserId = newUserId;
                
                // 自动保存用户到服务器
                saveUserToServer(userData);
            }
            
            // 2. 确保有群聊ID
            const initializeApp = (groupIdToUse) => {
                // 更新全局变量
                groupId = groupIdToUse;
                userId = currentUserId;
                
                // 检查URL是否已经包含有效的user_id参数
                const url = new URL(window.location);
                const urlUserId = url.searchParams.get('user_id');
                const hasValidUserId = urlUserId && urlUserId !== 'null';
                const hasGroupId = url.searchParams.has('group_id');
                
                if (!hasValidUserId || !hasGroupId) {
                    // 如果URL参数不完整或user_id无效，重定向到包含完整参数的URL
                    url.searchParams.set('user_id', currentUserId);
                    url.searchParams.set('group_id', groupIdToUse);
                    window.location.href = url.toString();
                    return;
                }
                
                // 加载群聊信息
                loadGroupInfo();
                // 加载历史消息
                loadMessages();
                // 开始轮询新消息
                startPolling();
                // 初始化加载底部标签
                loadQuickActions();
                
                // 定时刷新在线人数，每30秒更新一次
                setInterval(() => {
                    // 先调用updateOnlineStatus更新当前用户在线状态
                    updateOnlineStatus();
                    // 然后重新加载群聊信息获取最新在线人数
                    loadGroupInfo();
                }, 30000);
            };
            
            // 3. 如果有groupId，直接初始化
            if (currentGroupId) {
                initializeApp(currentGroupId);
            } else {
                // 没有groupId，获取第一个群聊ID
                fetch('api/admin/groups.php')
                    .then(response => response.json())
                    .then(groups => {
                        if (groups && groups.length > 0) {
                            // 使用第一个群聊的ID
                            initializeApp(groups[0].id);
                        } else {
                            // 如果没有群聊，显示错误信息
                            const messagesArea = document.getElementById('messagesArea');
                            messagesArea.innerHTML = `
                                <div class="empty-state">
                                    <div class="empty-state-icon">❌</div>
                                    <h3 class="empty-state-title">加载失败</h3>
                                    <p class="empty-state-desc">没有找到任何群聊</p>
                                </div>
                            `;
                        }
                    })
                    .catch(error => {
                        console.error('获取群聊列表失败:', error);
                        const messagesArea = document.getElementById('messagesArea');
                        messagesArea.innerHTML = `
                            <div class="empty-state">
                                <div class="empty-state-icon">❌</div>
                                <h3 class="empty-state-title">加载失败</h3>
                                <p class="empty-state-desc">获取群聊列表失败: ${error.message}</p>
                            </div>
                        `;
                    });
            }
        }
        
        // 将用户信息保存到服务器
        function saveUserToServer(userData) {
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

                } else {
                    console.error('用户注册失败:', data.message);
                }
            })
            .catch(error => {
                console.error('保存用户到服务器失败:', error);
            });
        }
        
        // 返回上一页
        function goBack() {
            window.location.href = 'login.php';
        }
        
        // 更新禁言状态
        function updateMuteStatus() {
            const messageInput = document.getElementById('messageInput');
            
            // 默认为允许发言，除非明确被禁言
            const isGroupMuted = groupData && groupData.allow_speak !== 1 && groupData.allow_speak !== true;
            const isUserMuted = userData && userData.allow_speak !== 1 && userData.allow_speak !== true;
            
            if (isGroupMuted) {
                messageInput.placeholder = '群聊已关闭全体发言';
                messageInput.disabled = true;
            } else if (isUserMuted) {
                messageInput.placeholder = '您已被禁止发言';
                messageInput.disabled = true;
            } else {
                messageInput.placeholder = '想对TA说点什么呢?';
                messageInput.disabled = false;
            }
        }
        

        



        

        

        
        // 发送语音消息
        function sendVoiceMessage() {
            if (!recordedAudioBlob) return;
            
            // 保存录制的音频Blob，防止在异步操作中被重置
            const audioBlobToSend = recordedAudioBlob;
            
            // 创建临时音频对象计算时长
            const audio = new Audio();
            const reader = new FileReader();
            
            reader.onload = function(e) {
                audio.src = e.target.result;
                audio.onloadedmetadata = function() {
                    // 获取音频时长
                    const duration = Math.floor(audio.duration);
                    
                    const formData = new FormData();
                    formData.append('file', audioBlobToSend, 'voice_message.webm');
                    formData.append('group_id', groupId);
                    formData.append('user_id', userId);
                    formData.append('user_nickname', userData.nickname);
                    formData.append('user_avatar', userData.avatar);
                    formData.append('type', 'voice');
                    formData.append('duration', duration); // 添加时长信息
                    
                    // 上传语音文件到服务器
                    fetch('api/chat/upload_file.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // 切换回文本输入
                            toggleVoiceInput();
                            // 重新加载消息
                            loadMessages();
                        } else {
                            alert('发送失败: ' + (data.message || '未知错误'));
                        }
                        // 在上传完成后重置状态
                        resetVoiceRecording();
                    })
                    .catch(error => {
                        console.error('上传语音失败:', error);
                        alert('上传失败，请检查网络连接');
                        // 在上传失败后也重置状态
                        resetVoiceRecording();
                    });
                };
            };
            
            reader.readAsDataURL(audioBlobToSend);
        }
        
        // 处理语音文件上传
        function handleVoiceFileUpload(input) {
            const file = input.files[0];
            if (!file) return;
            
            // 创建临时音频对象计算时长
            const audio = new Audio();
            const reader = new FileReader();
            
            reader.onload = function(e) {
                audio.src = e.target.result;
                audio.onloadedmetadata = function() {
                    // 获取音频时长
                    const duration = Math.floor(audio.duration);
                    
                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('group_id', groupId);
                    formData.append('user_id', userId);
                    formData.append('user_nickname', userData.nickname);
                    formData.append('user_avatar', userData.avatar);
                    formData.append('type', 'voice');
                    formData.append('duration', duration); // 添加时长信息
                    
                    // 上传文件到服务器
                    fetch('api/chat/upload_file.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // 重新加载消息
                            loadMessages();
                        } else {
                            alert('发送失败: ' + (data.message || '未知错误'));
                        }
                    })
                    .catch(error => {
                        console.error('上传文件失败:', error);
                        alert('上传失败，请检查网络连接');
                    });
                };
            };
            
            reader.readAsDataURL(file);
            
            // 重置输入框
            input.value = '';
        }
        
        // 重置语音录制状态
        function resetVoiceRecording() {
            recordedAudioBlob = null;
            audioChunks = [];
            recordingStartTime = null;
            isRecording = false;
            clearInterval(recordingTimer);
            
            // 重置UI
            document.getElementById('voiceDuration').textContent = '00:00';
            document.getElementById('voiceSendBtn').disabled = true;
            document.getElementById('voiceRecordBtn').classList.remove('recording');
            document.getElementById('voiceWave').classList.remove('recording');
            
            // 重置波形
            initVoiceWave();
        }
        
        // 播放语音消息
        let currentVoiceMessage = null;
        let currentProgressInterval = null;
        
        function playVoiceMessage(btn, audioUrl) {

            
            // 停止当前播放的语音
            if (currentVoiceMessage) {
                const currentAudio = currentVoiceMessage.querySelector('.voice-audio');
                const currentPlayBtn = currentVoiceMessage.querySelector('.voice-play-btn');
                const currentProgressBar = currentVoiceMessage.querySelector('.voice-progress-bar');
                
                // 移除播放状态类，停止动画
                currentVoiceMessage.classList.remove('playing');
                
                if (currentAudio) {
                    currentAudio.pause();
                    currentAudio.currentTime = 0;
                }
                if (currentPlayBtn) {
                    currentPlayBtn.textContent = '▶️';
                    currentPlayBtn.style.fontSize = '24px';
                }
                if (currentProgressBar) {
                    currentProgressBar.style.width = '0%';
                }
                if (currentProgressInterval) {
                    clearInterval(currentProgressInterval);
                    currentProgressInterval = null;
                }
                
                // 如果点击的是当前播放的语音，则返回
                if (currentVoiceMessage === btn.closest('.voice-message')) {
                    currentVoiceMessage = null;
                    return;
                }
            }
            
            // 获取当前消息的voice-message元素
            const voiceMessage = btn.closest('.voice-message');
            if (!voiceMessage) {
                console.error('找不到voice-message元素');
                return;
            }
            
            // 获取相关元素
            const audio = voiceMessage.querySelector('.voice-audio');
            const progressBar = voiceMessage.querySelector('.voice-progress-bar');
            const durationDisplay = voiceMessage.querySelector('.voice-duration');
            
            if (!audio || !progressBar || !durationDisplay) {
                console.error('找不到音频元素、进度条或时长显示元素');
                return;
            }
            
            // 更新当前播放的语音消息
            currentVoiceMessage = voiceMessage;
            
            // 为语音消息添加播放状态类，触发动画
            voiceMessage.classList.add('playing');
            
            // 更新播放按钮状态
            btn.textContent = '⏸️';
            btn.style.fontSize = '24px';
            
            // 检查并更新音频源（解决其他用户音频无法播放的问题）
            if (audio.src !== audioUrl) {
    
                audio.src = audioUrl;
                
                // 移除所有现有的事件监听器，避免重复添加
                audio.removeEventListener('loadedmetadata', updateDuration);
                audio.removeEventListener('canplay', updateDuration);
                audio.removeEventListener('play', updateDuration);
                audio.removeEventListener('progress', updateDuration);
                audio.removeEventListener('canplaythrough', updateDuration);
                audio.removeEventListener('error', audioErrorHandler);
                
                // 错误处理函数
                function audioErrorHandler() {
                    console.error('音频加载失败:', audio.src);
                    durationDisplay.textContent = '该语音已失效';
                    btn.disabled = true;
                    btn.style.color = voiceMessage.closest('.message.own') ? 'rgba(255, 255, 255, 0.5)' : 'var(--text-tertiary)';
                    btn.style.cursor = 'not-allowed';
                    voiceMessage.style.opacity = '0.7';
                }
                
                // 更新时长显示的函数
                function updateDuration() {

                    // 检查duration是否有效且大于0
                    if (!isNaN(audio.duration) && isFinite(audio.duration) && audio.duration > 0) {
                        // 格式化时长
                        const duration = Math.floor(audio.duration);
                        const minutes = Math.floor(duration / 60);
                        const seconds = duration % 60;
                        durationDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                    } else if (audio.readyState >= 3) { // HAVE_FUTURE_DATA
                        // 即使duration为0，如果音频已加载到一定程度，也尝试显示
                        durationDisplay.textContent = '0:00';
                    }
                }
                
                // 直接检查当前状态
                if (audio.readyState >= 1) { // HAVE_METADATA
                    updateDuration();
                }
                
                // 只添加一次事件监听器
                audio.addEventListener('loadedmetadata', updateDuration, { once: true });
                audio.addEventListener('canplay', updateDuration, { once: true });
                audio.addEventListener('error', audioErrorHandler, { once: true });
                
                // 重新加载音频
                audio.load();
                
                // 立即检查状态
                updateDuration();
                
                // 100ms后再次检查，确保duration已更新
                setTimeout(() => {
                    updateDuration();
                }, 100);
                
                // 500ms后最终检查
                setTimeout(() => {
                    updateDuration();
                }, 500);
            }
            
            // 监听时间更新，更新进度条
            function updateProgress() {
                // 确保音频duration有效
                if (!audio.duration || isNaN(audio.duration) || !isFinite(audio.duration)) {
                    return;
                }
                const progress = (audio.currentTime / audio.duration) * 100;
                // 确保进度值有效
                const validProgress = isNaN(progress) || !isFinite(progress) ? 0 : progress;
                progressBar.style.width = `${validProgress}%`;
            }
            
            // 使用定时器实现平滑的进度条更新
            currentProgressInterval = setInterval(updateProgress, 100);
            
            // 监听播放结束
            audio.onended = () => {

                btn.textContent = '▶️';
                btn.style.fontSize = '24px';
                progressBar.style.width = '100%';
                if (currentProgressInterval) {
                    clearInterval(currentProgressInterval);
                    currentProgressInterval = null;
                }
                // 移除播放状态类，停止动画
                voiceMessage.classList.remove('playing');
                currentVoiceMessage = null;
            };
            
            // 监听播放暂停
            audio.onpause = () => {
                if (currentVoiceMessage === voiceMessage && currentProgressInterval) {
                    clearInterval(currentProgressInterval);
                    currentProgressInterval = null;
                    // 移除播放状态类，停止动画
                    voiceMessage.classList.remove('playing');
                }
            };
            
            // 监听播放错误
            audio.onerror = (error) => {
                console.error('音频播放错误:', error);
                console.error('错误代码:', error.code);
                console.error('音频URL:', audio.src);
                
                // 更新UI显示语音已失效
                durationDisplay.textContent = '该语音已失效';
                btn.textContent = '▶️';
                btn.style.fontSize = '24px';
                btn.disabled = true;
                btn.style.color = voiceMessage.closest('.message.own') ? 'rgba(255, 255, 255, 0.5)' : 'var(--text-tertiary)';
                btn.style.cursor = 'not-allowed';
                // 添加失效样式
                voiceMessage.style.opacity = '0.7';
                
                // 移除播放状态类，停止动画
                voiceMessage.classList.remove('playing');
                if (currentProgressInterval) {
                    clearInterval(currentProgressInterval);
                    currentProgressInterval = null;
                }
                currentVoiceMessage = null;
            };
            
            // 开始播放
            audio.play().catch(error => {
                console.error('播放失败:', error);
                
                // 只有当是浏览器自动播放策略问题时才显示提示，其他情况显示语音已失效
                if (error.name === 'NotAllowedError') {
                    // 浏览器自动播放策略问题，仅更新按钮状态，不显示失效
                    btn.textContent = '▶️';
                    btn.style.fontSize = '24px';
                } else {
                    // 其他播放错误，显示语音已失效
                    durationDisplay.textContent = '该语音已失效';
                    btn.textContent = '▶️';
                    btn.style.fontSize = '24px';
                    btn.disabled = true;
                    btn.style.color = voiceMessage.closest('.message.own') ? 'rgba(255, 255, 255, 0.5)' : 'var(--text-tertiary)';
                    btn.style.cursor = 'not-allowed';
                    // 添加失效样式
                    voiceMessage.style.opacity = '0.7';
                }
                
                // 移除播放状态类，停止动画
                voiceMessage.classList.remove('playing');
                if (currentProgressInterval) {
                    clearInterval(currentProgressInterval);
                    currentProgressInterval = null;
                }
                currentVoiceMessage = null;
            });
        }
        

        
        // 显示群公告
        function updateAnnouncement() {
            // 重新渲染快捷功能区域，包括公告标签
            loadQuickActions();
        }
        
        // 打开群公告模态框
        function openAnnouncement() {
            if (groupData && groupData.announcement) {
                // 设置公告内容
                document.getElementById('announcementModalContent').innerHTML = groupData.announcement;
                // 显示模态框
                document.getElementById('announcementModal').classList.add('active');
            }
        }
        
        // 关闭群公告模态框
        function closeAnnouncementModal() {
            const modal = document.getElementById('announcementModal');
            const modalContent = modal.querySelector('.modal-content');
            
            // 添加关闭动画类
            modal.classList.add('closing');
            modalContent.classList.add('closing');
            
            // 动画结束后完全隐藏模态框
            setTimeout(() => {
                modal.classList.remove('active');
                modal.classList.remove('closing');
                modalContent.classList.remove('closing');
            }, 300);
        }
        
        // 移除点击模态框背景关闭弹窗的功能，用户只能通过按钮关闭
        
        // 页面加载完成后添加事件监听器
        document.addEventListener('DOMContentLoaded', function() {
            // 阻止点击模态框内容时事件冒泡到背景
            const announcementModalContent = document.querySelector('.announcement-modal');
            if (announcementModalContent) {
                announcementModalContent.addEventListener('click', function(event) {
                    event.stopPropagation();
                });
            }
        });
        
        // 加载群聊信息
        function loadGroupInfo() {
            fetch(`api/admin/groups.php?group_id=${groupId}`)
                .then(response => response.json())
                .then(data => {
                    // 兼容不同的数据格式
                    let group = null;
                    if (data && typeof data === 'object' && !Array.isArray(data) && data.id) {
                        group = data;
                    } else if (data && data.success && data.data && data.data.id) {
                        group = data.data;
                    }
                    
                    if (group) {
                        groupData = group;
                        // 更新禁言状态
                        updateMuteStatus();
                        // 更新群公告
                        updateAnnouncement();
                        // 加载底部标签
                        loadQuickActions();
                    }
                })
                .catch(error => {
                    console.error('加载群聊信息失败:', error);
                });
        }
        
        // 更新用户在线状态
        function updateOnlineStatus() {
            fetch('api/chat/update_online_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `group_id=${groupId}&user_id=${userId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // 更新群总人数显示
                }
            })
            .catch(error => {
                console.error('更新在线状态失败:', error);
            });
        }
        
        // 加载消息
        function loadMessages() {
            const messagesArea = document.getElementById('messagesArea');
            
            // 首次加载时显示加载状态
            if (messagesArea.innerHTML.trim() === '') {
                messagesArea.innerHTML = '<div class="loading">加载中...</div>';
            }
            
            // 实际从服务器获取消息
            fetch(`api/chat/get_messages.php?group_id=${groupId}`)
                .then(response => {

                    if (!response.ok) {
                        throw new Error('HTTP 错误！状态: ' + response.status);
                    }
                    return response.text().then(text => {

                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            throw new Error('无效的JSON响应: ' + text);
                        }
                    });
                })
                .then(messages => {
                    // 获取服务器返回的所有消息ID
                    const serverMessageIds = new Set(messages.map(msg => msg.id));
                    
                    // 移除被撤回的消息（不在服务器返回列表中的消息）
                    const messageElements = messagesArea.querySelectorAll('.message');
                    const existingMessageMap = new Map();
                    
                    // 收集现有消息元素
                    messageElements.forEach(element => {
                        const messageId = element.dataset.messageId;
                        if (messageId) {
                            existingMessageMap.set(messageId, element);
                        }
                    });
                    
                    // 移除被撤回的消息
                    messageElements.forEach(element => {
                        const messageId = element.dataset.messageId;
                        if (messageId && !serverMessageIds.has(messageId)) {
                            element.remove();
                            existingMessageMap.delete(messageId);
                        }
                    });
                    
                    // 遍历所有消息，更新或添加
                    let hasNewMessages = false;
                    messages.forEach(message => {
                        if (existingMessageMap.has(message.id)) {
                            // 更新现有消息
                            const existingElement = existingMessageMap.get(message.id);
                            
                            // 检查是否是管理员消息
                            const isAdminMessage = message.is_admin || message.user_id === 'admin' || message.user_id === 'system'; // 增加对system用户的支持
                            
                            if (isAdminMessage) {
                                // 更新昵称
                                let senderElement = existingElement.querySelector('.message-sender');
                                if (senderElement) {
                                    senderElement.textContent = message.user_nickname;
                                } else {
                                    // 如果没有昵称元素，添加一个
                                    const contentWrapper = existingElement.querySelector('.message-content-wrapper');
                                    if (contentWrapper) {
                                        senderElement = document.createElement('div');
                                        senderElement.className = 'message-sender';
                                        senderElement.textContent = message.user_nickname;
                                        contentWrapper.insertBefore(senderElement, contentWrapper.firstChild);
                                    }
                                }
                                
                                // 更新头像
                                let avatarElement = existingElement.querySelector('.message-avatar');
                                if (avatarElement) {
                                    avatarElement.src = message.user_avatar || `https://picsum.photos/id/${Math.abs(generateHashCode(message.user_id)) % 1000}/36/36`;
                                } else {
                                    // 如果没有头像元素，添加一个
                                    const isOwn = message.user_id === userId;
                                    avatarElement = document.createElement('img');
                                    avatarElement.src = message.user_avatar || `https://picsum.photos/id/${Math.abs(generateHashCode(message.user_id)) % 1000}/36/36`;
                                    avatarElement.alt = message.user_nickname;
                                    avatarElement.className = 'message-avatar';
                                    
                                    if (isOwn) {
                                        // 自己的消息，添加到右侧
                                        existingElement.appendChild(avatarElement);
                                    } else {
                                        // 他人的消息，添加到左侧
                                        existingElement.insertBefore(avatarElement, existingElement.firstChild);
                                    }
                                }
                            }
                            
                            // 从映射中移除已处理的消息
                            existingMessageMap.delete(message.id);
                        } else {
                            // 添加新消息
                            addMessageToDOM(message);
                            hasNewMessages = true;
                            // 更新最后一条消息的时间戳
                            lastMessageTimestamp = message.timestamp;
                        }
                    });
                    
                    // 检查是否是"暂无信息"状态
                    const hasNoMessagesPrompt = messagesArea.innerHTML.includes('暂无消息，快来发送第一条消息吧');
                    
                    // 如果有消息，并且当前是"暂无信息"状态，清除提示
                    if (messages.length > 0 && hasNoMessagesPrompt) {
                        messagesArea.innerHTML = '';
                        // 重新添加所有消息
                        messages.forEach(message => {
                            addMessageToDOM(message);
                            // 更新最后一条消息的时间戳
                            lastMessageTimestamp = message.timestamp;
                        });
                    } 
                    // 如果是首次加载且有消息
                    else if (messagesArea.innerHTML.includes('加载中') && messages.length > 0) {
                        // 清空加载状态，然后添加所有消息
                        messagesArea.innerHTML = '';
                        messages.forEach(message => {
                            addMessageToDOM(message);
                            // 更新最后一条消息的时间戳
                            lastMessageTimestamp = message.timestamp;
                        });
                    } 
                    // 如果首次加载但没有消息，显示提示
                    else if (messagesArea.innerHTML.includes('加载中') && messages.length === 0) {
                        // 首次加载但没有消息，显示提示
                        messagesArea.innerHTML = `
                            <div class="no-messages-prompt" style="text-align: center; font-size:12px;color: var(--text-secondary); padding: 20px;">
                                暂无消息，快来发送第一条消息吧！
                            </div>
                        `;
                    }
                    // 如果没有消息了，添加"暂无信息"提示
                    else if (messages.length === 0 && !hasNoMessagesPrompt && !messagesArea.innerHTML.includes('加载中')) {
                        messagesArea.innerHTML = `
                            <div class="no-messages-prompt" style="text-align: center; font-size:12px;color: var(--text-secondary); padding: 20px;">
                                暂无消息，快来发送第一条消息吧！
                            </div>
                        `;
                    }
                    
                    // 滚动到底部，首次加载时强制滚动
                    // 检查是否是首次加载（消息区域为空或只有加载状态）
                    const isInitialLoad = messagesArea.innerHTML.trim() === '' || messagesArea.innerHTML.includes('加载中');
                    
                    // 根据是否为首次加载或是否有新消息决定是否强制滚动
                    // 首次加载或有新消息时强制滚动到底部，后续轮询根据用户滚动位置决定
                    // 立即执行滚动，确保快速加载时也能正确滚动
                    scrollToBottom(isInitialLoad || hasNewMessages);
                    // 首次加载完成后，设置isFirstLoad为false
                    if (isInitialLoad) {
                        isFirstLoad = false;
                    }
                })
                .catch(error => {
                    console.error('加载消息失败:', error);
                    // 只有在首次加载时才显示错误，否则保持现有消息
                    if (messagesArea.innerHTML.includes('加载中')) {
                        messagesArea.innerHTML = `
                            <div class="empty-state">
                                <div class="empty-state-icon">❌</div>
                                <h3 class="empty-state-title">加载失败</h3>
                                <p class="empty-state-desc">错误: ${error.message}</p>
                                <p class="empty-state-desc">请检查网络连接或刷新页面重试</p>
                                <p class="empty-state-desc">控制台有详细错误信息</p>
                            </div>
                        `;
                    }
                });
        }
        
        // 检查是否允许播放音频
        let canPlayAudio = false;
        
        // 监听用户交互事件，获得音频播放权限
        function enableAudioPlayback() {
            canPlayAudio = true;

        }
        
        // 添加事件监听器，等待用户交互，不要使用once: true，确保一直监听
        document.addEventListener('click', enableAudioPlayback);
        document.addEventListener('touchstart', enableAudioPlayback);
        document.addEventListener('keydown', enableAudioPlayback);
        
        // 预加载提示音
        const newMessageAudio = new Audio();
        // 使用一个简单的提示音，你可以替换为自己的音频文件
        newMessageAudio.src = 'data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEARKwAAIhYAQACABAAZGF0YQAAAAA=';
        newMessageAudio.volume = 0.5;
        
        // 添加新消息声音提示
        function playNewMessageSound() {
            // 首次加载时不播放提示音
            if (isFirstLoad) return;
            
            // 尝试播放音频，不需要严格检查canPlayAudio
            newMessageAudio.currentTime = 0; // 重置音频到开始位置
            newMessageAudio.play().catch(error => {

                // 尝试启用音频播放
                enableAudioPlayback();
            });
        }
        
        // 添加消息到DOM
        function addMessageToDOM(message) {
            const messagesArea = document.getElementById('messagesArea');
            const isOwn = message.user_id === userId;
            
            let messageContent = '';
            
            // 检查是否是管理员消息
            const isAdminMessage = message.is_admin || message.user_id === 'admin' || message.user_id === 'system'; // 增加对system用户的支持
            
            switch (message.type) {
                case 'history':
                case 'card':
                    messageContent = message.content; // 直接渲染HTML
                    break;
                case 'text':
                    messageContent = `${formatMessageContent(message.content)}`;
                    break;
                case 'image':
                    messageContent = `<img 
                        src="${message.content}" 
                        alt="图片" 
                        class="message-image loading" 
                        onclick="openImagePreview('${message.content}')"
                        onload="this.classList.remove('loading')"
                        onerror="this.classList.remove('loading'); this.src='https://picsum.photos/id/1050/200/200'; this.alt='图片加载失败'"
                    >`;
                    break;
                case 'video':
                    messageContent = `<video src="${message.content}" controls class="message-video" style="max-width: 100%; border-radius: 12px; margin: 4px 0;"></video>`;
                    break;
                case 'file':
                    const fileName = message.content.split('/').pop();
                    messageContent = `<a href="${message.content}" download class="message-file" style="display: block; padding: 8px 12px; background-color: rgba(0, 0, 0, 0.05); color: var(--text-color); text-decoration: none; border-radius: 12px; margin: 4px 0; font-size: 14px;">📄 ${fileName}</a>`;
                    break;
                case 'voice':
                    // 确保音频URL是完整的，添加base URL或处理相对路径
                    const audioUrl = message.content.startsWith('http') ? message.content : `/${message.content.replace(/^\//, '')}`;
                    
                    // 从消息中获取时长，如果没有则使用默认值0
                    const voiceDuration = message.duration || 0;
                    const minutes = Math.floor(voiceDuration / 60);
                    const seconds = voiceDuration % 60;
                    const formattedDuration = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                    
                    // 简化语音消息结构
                    messageContent = `
                        <div class="voice-message" style="display: flex; align-items: center; gap: 12px; padding: 8px 0;">
                            <button class="voice-play-btn" onclick="playVoiceMessage(this, '${audioUrl}')" style="background: none; border: none; font-size: 24px; cursor: pointer; color: ${isOwn ? 'white' : 'var(--primary-color)'};">
                                ▶️
                            </button>
                            <div class="voice-progress" style="flex: 1; height: 4px; background-color: ${isOwn ? 'rgba(255, 255, 255, 0.3)' : 'var(--border-color)'};
                                border-radius: 2px; overflow: hidden;">
                                <div class="voice-progress-bar" style="height: 100%; width: 0%; background-color: ${isOwn ? 'white' : 'var(--primary-color)'};
                                    transition: width 0.1s ease;"></div>
                            </div>
                            <div class="voice-duration" style="font-size: 12px; color: ${isOwn ? 'rgba(255, 255, 255, 0.8)' : 'var(--text-secondary)'};">
                                ${formattedDuration}
                            </div>
                            <audio class="voice-audio" src="${audioUrl}" preload="metadata" crossOrigin="anonymous"></audio>
                        </div>
                    `;
                    break;
                default:
                    messageContent = `${formatMessageContent(message.content)}`;
            }
            
            // 构建头像URL
            let avatarUrl = '';
            if (isAdminMessage) {
                // 管理员消息，使用消息中保存的头像
                avatarUrl = message.user_avatar || `https://picsum.photos/id/${Math.abs(generateHashCode(message.user_id || 'admin')) % 1000}/36/36`;
            } else if (isOwn) {
                // 自己的消息，使用本地用户数据中的头像
                avatarUrl = userData.avatar || 'https://picsum.photos/id/1005/36/36';
            } else {
                // 他人的消息，使用消息中保存的头像
                avatarUrl = message.user_avatar || `https://picsum.photos/id/${Math.abs(generateHashCode(message.user_id)) % 1000}/36/36`;
            }
            
            // 确保管理员消息显示头像和昵称，无论是否是自己的消息
            const showAvatarAndSender = isAdminMessage || !isOwn;
            
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${isOwn ? 'own' : 'other'} ${isAdminMessage ? 'admin' : ''}`;
            // 添加消息ID到data属性，用于识别已存在的消息
            messageDiv.dataset.messageId = message.id;
            
            messageDiv.innerHTML = `
                ${showAvatarAndSender ? `<img src="${avatarUrl}" alt="${message.user_nickname || '管理员'}" class="message-avatar">` : ''}
                <div class="message-content-wrapper">
                    ${showAvatarAndSender ? `<div class="message-sender">${message.user_nickname || '管理员'}</div>` : ''}
                    <div class="message-content">
                        ${messageContent}
                    </div>
                </div>
                ${isOwn && !isAdminMessage ? `<img src="${avatarUrl}" alt="${userData.nickname}" class="message-avatar">` : ''}
            `;
            
            messagesArea.appendChild(messageDiv);
            
            // 等待浏览器更新滚动高度后再处理通知
            setTimeout(() => {
                // 非首次加载时才处理新消息提示和声音
                if (!isFirstLoad) {
                    // 先检查用户是否在底部附近（在调用scrollToBottom之前）
                    const nearBottom = isNearBottom();
                    
                    // 如果是新消息且不是自己发送的，播放提示音
                    if (!isOwn) {
                        playNewMessageSound();
                    }
                    
                    // 如果用户不在底部，显示新消息提示
                    if (!nearBottom) {
                        hasUnreadMessages = true;
                        showNewMessageNotification();
                    }
                }
                
                // 最后调用scrollToBottom，根据用户当前滚动位置决定是否滚动
                scrollToBottom(false);
            }, 50);
            
            // 如果是语音消息，初始化音频元素并获取时长
            if (message.type === 'voice') {
                const voiceMessage = messageDiv.querySelector('.voice-message');
                const audio = voiceMessage.querySelector('.voice-audio');
                const durationDisplay = voiceMessage.querySelector('.voice-duration');
                const playBtn = voiceMessage.querySelector('.voice-play-btn');
                
                // 更新时长显示的函数
                function updateDuration() {

                    // 检查duration是否有效且大于0
                    if (!isNaN(audio.duration) && isFinite(audio.duration) && audio.duration > 0) {
                        // 格式化时长
                        const duration = Math.floor(audio.duration);
                        const minutes = Math.floor(duration / 60);
                        const seconds = duration % 60;
                        durationDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                    } else if (audio.readyState >= 1) { // HAVE_METADATA
                        // 即使duration为0，如果音频已加载到一定程度，也尝试显示
                        durationDisplay.textContent = '0:00';
                    }
                }
                
                // 错误处理函数
                function audioErrorHandler() {
                    console.error('音频加载失败:', audio.src);
                    durationDisplay.textContent = '该语音已失效';
                    playBtn.disabled = true;
                    playBtn.style.color = isOwn ? 'rgba(255, 255, 255, 0.5)' : 'var(--text-tertiary)';
                    playBtn.style.cursor = 'not-allowed';
                    voiceMessage.style.opacity = '0.7';
                }
                
                // 监听音频元数据加载完成事件
                audio.addEventListener('loadedmetadata', updateDuration, { once: true });
                
                // 监听音频可以播放事件，此时也能获取到duration
                audio.addEventListener('canplay', updateDuration, { once: true });
                
                // 监听音频数据加载进度，可能在这个阶段能获取到duration
                audio.addEventListener('progress', updateDuration, { once: true });
                
                // 加载失败时的处理
                audio.addEventListener('error', audioErrorHandler, { once: true });
                
                // 监听播放错误
                audio.addEventListener('stalled', audioErrorHandler, { once: true });
                
                // 设置crossOrigin属性，解决跨域问题
                audio.crossOrigin = 'anonymous';
                
                // 直接检查当前状态
                updateDuration();
                
                // 尝试加载音频元数据
                try {
                    audio.load();
                    
                    // 立即检查状态
                    updateDuration();
                    
                    // 100ms后再次检查，确保duration已更新
                    setTimeout(() => {
                        updateDuration();
                    }, 100);
                    
                    // 500ms后最终检查
                    setTimeout(() => {
                        updateDuration();
                    }, 500);
                } catch (e) {
                    console.error('音频加载异常:', e);
                    audioErrorHandler();
                }
            }
        }
        
        // 格式化消息内容
        function formatMessageContent(content) {
            // 格式化链接
            return content.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" class="message-url">$1</a>');
        }
        
        // 发送消息
        function sendMessage() {
            const messageInput = document.getElementById('messageInput');
            const content = messageInput.value.trim();
            
            if (!content) return;
            
            const messageData = {
                group_id: groupId,
                user_id: userId,
                user_nickname: userData.nickname,
                user_avatar: userData.avatar,
                type: 'text',
                content: content
            };
            
            // 发送到服务器
            fetch('api/chat/send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(messageData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // 清空输入框
                    messageInput.value = '';
                    autoResize(messageInput);
                    // 重新加载消息
                    loadMessages();
                    // 发送成功后，直接滚动到底部，确保用户看到自己发送的消息
                    setTimeout(() => {
                        scrollToBottom(false, true);
                    }, 100);
                } else {
                    alert('发送失败: ' + (data.message || '未知错误'));
                }
            })
            .catch(error => {
                console.error('发送消息失败:', error);
                alert('发送失败，请检查网络连接');
            });
        }
        
        // 处理图片上传按钮点击
        function handleImageUploadClick() {
            // 检查群聊是否允许图片上传
            if (groupData && groupData.allow_image_upload === false) {
                alert('该群已被禁止上传图片');
                return;
            }
            // 如果允许，打开文件选择器
            document.getElementById('imageInput').click();
        }
        
        // 处理文件上传
        function handleFileUpload(input, type) {
            const file = input.files[0];
            if (!file) return;
            
            const formData = new FormData();
            formData.append('file', file);
            formData.append('group_id', groupId);
            formData.append('user_id', userId);
            formData.append('user_nickname', userData.nickname);
            formData.append('user_avatar', userData.avatar);
            formData.append('type', type);
            
            // 上传文件到服务器
            fetch('api/chat/upload_file.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // 重新加载消息
                    loadMessages();
                    // 上传成功后，直接滚动到底部，确保用户看到自己发送的文件
                    setTimeout(() => {
                        scrollToBottom(false, true);
                    }, 100);
                } else {
                    alert('发送失败: ' + (data.message || '未知错误'));
                }
            })
            .catch(error => {
                console.error('上传文件失败:', error);
                alert('上传失败，请检查网络连接');
            });
            
            // 重置输入框
            input.value = '';
        }
        
        // 自动调整输入框高度
        function autoResize(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
        }
        
        // 独立的哈希函数，可处理字符串和数字，用于生成头像URL
        function generateHashCode(input) {
            const str = String(input);
            let hash = 0;
            for (let i = 0; i < str.length; i++) {
                const char = str.charCodeAt(i);
                hash = ((hash << 5) - hash) + char;
                hash = hash & hash; // Convert to 32bit integer
            }
            return hash;
        }
        
        // 新消息提示相关变量
        let hasUnreadMessages = false;
        let isFirstLoad = true;
        
        // 显示新消息提示
        function showNewMessageNotification() {
            // 首次加载时不显示新消息提示
            if (isFirstLoad) return;
            
            const notification = document.getElementById('newMessageNotification');
            if (notification) {
                notification.style.display = 'block';
                notification.style.opacity = '1';
                // 移除动画效果，直接显示
                notification.style.transition = 'none';
            }
        }
        
        // 隐藏新消息提示
        function hideNewMessageNotification() {
            const notification = document.getElementById('newMessageNotification');
            if (notification) {
                notification.style.opacity = '0';
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 300);
            }
        }
        
        // 初始化新消息提示点击事件
        function initNewMessageNotification() {
            const notification = document.getElementById('newMessageNotification');
            if (notification) {
                notification.addEventListener('click', function() {
                    // 滚动到底部
                    scrollToBottom(false, true);
                    // 隐藏提示
                    hideNewMessageNotification();
                });
            }
        }
        
        // 检查是否在底部附近
        function isNearBottom() {
            const messagesArea = document.getElementById('messagesArea');
            if (!messagesArea) return true;
            
            const threshold = 100;
            const scrollHeight = messagesArea.scrollHeight;
            const scrollTop = messagesArea.scrollTop;
            const clientHeight = messagesArea.clientHeight;
            const distanceFromBottom = scrollHeight - scrollTop - clientHeight;
            return distanceFromBottom < threshold;
        }
        
        // 滚动到底部
        // 优化的滚动到底部函数，确保每次都能正确滚动
        function scrollToBottom(isInitialLoad = false, forceScroll = false) {
            const messagesArea = document.getElementById('messagesArea');
            if (messagesArea) {
                // 强制滚动到底部的条件：
                // 1. 是首次加载
                // 2. 或者用户当前已经在底部附近
                // 3. 或者明确要求强制滚动
                // 4. 或者有新消息
                // 立即滚动，减少延迟，确保快速加载时也能正确滚动
                setTimeout(() => {
                    // 计算当前位置
                    const distanceFromBottom = messagesArea.scrollHeight - messagesArea.scrollTop - messagesArea.clientHeight;
                    const isNearBottom = distanceFromBottom < 100;
                    
                    // 强制滚动到底部的条件
                    if (isInitialLoad || forceScroll || isNearBottom) {
                        messagesArea.scrollTop = messagesArea.scrollHeight;
                        
                        // 滚动到底部后，隐藏新消息提示
                        hideNewMessageNotification();
                        hasUnreadMessages = false;
                        
                        // 再执行一次滚动，确保在快速加载时能滚动到底部
                        setTimeout(() => {
                            messagesArea.scrollTop = messagesArea.scrollHeight;
                        }, 50);
                    }
                }, 0);
            }
        }
        
        // 获取当前时间
        function getCurrentTime() {
            const now = new Date();
            return now.toLocaleTimeString('zh-CN', { hour: '2-digit', minute: '2-digit' });
        }
        
        // 格式化消息时间，只显示月日时间
        function formatMessageTime(timestamp) {
            const date = new Date(timestamp);
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${month}-${day} ${hours}:${minutes}`;
        }
        
        
        
        // 开始轮询新消息
        function startPolling() {
            pollingInterval = setInterval(() => {
                // 实际项目中从服务器获取新消息
    
                
                // 更新用户在线状态
                updateOnlineStatus();
                
                // 定期检查群聊信息，更新禁言状态和图片上传权限
                fetch(`api/admin/groups.php?group_id=${groupId}`)
                    .then(response => response.json())
                    .then(group => {
                        if (group && groupData) {
                            // 当禁言状态或图片上传权限发生变化时更新
                            if (group.allow_speak !== groupData.allow_speak || group.allow_image_upload !== groupData.allow_image_upload) {
                                groupData = group;
                                updateMuteStatus();
                            }
                            // 更新群总人数显示
                        }
                    })
                    .catch(error => {
                        console.error('轮询群聊信息失败:', error);
                    });
                
                // 定期获取新消息 - 直接调用loadMessages函数，确保消息处理的一致性
                loadMessages();
            }, 3000); // 缩短轮询间隔为3秒，提高实时性
        }
        
        // 打开成员列表
        function openMemberList() {
            alert('成员列表功能开发中');
        }
        
        // 打开修改头像模态框
        function openChangeAvatarModal() {
            const modal = document.getElementById('changeAvatarModal');
            const currentAvatar = document.getElementById('currentAvatar');
            // 更新当前头像显示
            currentAvatar.src = userData.avatar || 'https://picsum.photos/id/1005/120/120';
            modal.classList.add('active');
        }
        
        // 关闭修改头像模态框
        function closeChangeAvatarModal() {
            document.getElementById('changeAvatarModal').classList.remove('active');
        }
        
        // 处理头像上传
        function handleAvatarUpload(input) {
            const file = input.files[0];
            if (!file) return;
            
            const formData = new FormData();
            formData.append('user_id', userId);
            formData.append('userAvatar', file);
            
            // 上传头像到服务器
            fetch('api/chat/save_user.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // 更新本地用户数据
                    userData.avatar = data.user.avatar;
                    // 保存到localStorage
                    localStorage.setItem('user', JSON.stringify(userData));
                    // 更新当前头像显示
                    const currentAvatar = document.getElementById('currentAvatar');
                    currentAvatar.src = userData.avatar || 'https://picsum.photos/id/1005/120/120';
                    // 关闭模态框
                    closeChangeAvatarModal();
                    // 重新加载消息，更新所有消息中的头像
                    loadMessages();
                    alert('头像修改成功');
                } else {
                    alert('头像修改失败: ' + (data.message || '未知错误'));
                }
            })
            .catch(error => {
                console.error('上传头像失败:', error);
                alert('上传头像失败，请检查网络连接');
            });
        }
        
        // 打开聊天设置
        function openChatSettings() {
            // 跳转到群聊设置页面
            window.location.href = `chat_settings.php?group_id=${groupId}&user_id=${userId}`;
        }
        
        // 回车键发送消息
        document.getElementById('messageInput').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
        
        // 页面加载时初始化
        // 确保页面完全加载后初始化，同时兼容不同浏览器
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                init();
                // 初始化新消息提示
                initNewMessageNotification();
                // DOM加载完成后滚动到底部
                scrollToBottom(true);
            });
        } else {
            // DOM已经加载完成，直接初始化
            init();
            // 初始化新消息提示
            initNewMessageNotification();
            // 立即滚动到底部
            scrollToBottom(true);
        }
        
        // 在页面加载完成后再次尝试加载底部标签，确保移动端能显示
        window.addEventListener('load', function() {
            setTimeout(loadQuickActions, 100);
            // 页面完全加载后再滚动一次，确保显示最新消息
            scrollToBottom(true);
        });
        
        // 页面卸载时清理
        window.addEventListener('beforeunload', function() {
            if (pollingInterval) {
                clearInterval(pollingInterval);
            }
        });
        
        // 图片预览功能
        function openImagePreview(imageUrl) {
            const modal = document.getElementById('imagePreviewModal');
            const previewImage = document.getElementById('previewImage');
            const closePreviewBtn = document.getElementById('closePreviewBtn');
            const loading = document.getElementById('previewLoading');
            const error = document.getElementById('previewError');
            const zoomInBtn = document.getElementById('previewZoomIn');
            const zoomOutBtn = document.getElementById('previewZoomOut');
            const resetBtn = document.getElementById('previewReset');
            const downloadBtn = document.getElementById('previewDownload');
            
            // 初始化状态
            previewImage.style.transform = 'scale(1)';
            previewImage.style.opacity = '0';
            previewImage.src = '';
            loading.style.display = 'block';
            error.style.display = 'none';
            
            // 设置图片源并监听加载完成
            previewImage.onload = function() {
                loading.style.display = 'none';
                previewImage.style.opacity = '1';
            };
            
            previewImage.onerror = function() {
                loading.style.display = 'none';
                error.style.display = 'block';
                error.textContent = '图片加载失败，请稍后重试';
            };
            
            previewImage.src = imageUrl;
            modal.style.display = 'flex';
            
            // 禁止背景滚动
            document.body.style.overflow = 'hidden';
            
            // 关闭按钮点击事件
            closePreviewBtn.onclick = closeImagePreview;
            
            // 下载按钮点击事件
            downloadBtn.onclick = function() {
                const a = document.createElement('a');
                a.href = imageUrl;
                a.download = `image_${Date.now()}.jpg`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            };
            
            // 缩放功能
            let currentScale = 1;
            const scaleStep = 0.5;
            const minScale = 0.5;
            const maxScale = 3;
            
            // 放大按钮点击事件
            zoomInBtn.onclick = function() {
                if (currentScale < maxScale) {
                    currentScale += scaleStep;
                    previewImage.style.transform = `scale(${currentScale})`;
                }
            };
            
            // 缩小按钮点击事件
            zoomOutBtn.onclick = function() {
                if (currentScale > minScale) {
                    currentScale -= scaleStep;
                    previewImage.style.transform = `scale(${currentScale})`;
                }
            };
            
            // 重置按钮点击事件
            resetBtn.onclick = function() {
                currentScale = 1;
                previewImage.style.transform = `scale(${currentScale})`;
            };
            
            // 双击放大缩小
            let lastTap = 0;
            previewImage.onclick = function(event) {
                const currentTime = new Date().getTime();
                const tapLength = currentTime - lastTap;
                
                // 双击检测（300ms内）
                if (tapLength < 300 && tapLength > 0) {
                    event.preventDefault();
                    
                    // 切换放大状态
                    if (currentScale === 1) {
                        currentScale = 2;
                    } else {
                        currentScale = 1;
                    }
                    previewImage.style.transform = `scale(${currentScale})`;
                    previewImage.style.transformOrigin = 'center center';
                }
                
                lastTap = currentTime;
            };
        }
        
        // 关闭图片预览
        function closeImagePreview() {
            const modal = document.getElementById('imagePreviewModal');
            modal.style.display = 'none';
            
            // 恢复背景滚动
            document.body.style.overflow = 'auto';
            
            // 重置状态
            const previewImage = document.getElementById('previewImage');
            const loading = document.getElementById('previewLoading');
            const error = document.getElementById('previewError');
            previewImage.style.opacity = '0';
            loading.style.display = 'none';
            error.style.display = 'none';
        }
        
        // 点击模态框背景关闭预览
        document.getElementById('imagePreviewModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeImagePreview();
            }
        });
        
        // 键盘事件支持
        document.addEventListener('keydown', function(event) {
            const modal = document.getElementById('imagePreviewModal');
            if (modal.style.display === 'flex') {
                const previewImage = document.getElementById('previewImage');
                let currentScale = parseFloat(previewImage.style.transform.match(/scale\(([0-9.]+)\)/)?.[1] || 1);
                const scaleStep = 0.5;
                
                switch(event.key) {
                    case 'Escape':
                        closeImagePreview();
                        break;
                    case '+':
                    case '=':
                        if (currentScale < 3) {
                            currentScale += scaleStep;
                            previewImage.style.transform = `scale(${currentScale})`;
                        }
                        break;
                    case '-':
                    case '_':
                        if (currentScale > 0.5) {
                            currentScale -= scaleStep;
                            previewImage.style.transform = `scale(${currentScale})`;
                        }
                        break;
                    case '0':
                        currentScale = 1;
                        previewImage.style.transform = `scale(${currentScale})`;
                        break;
                }
            }
        });
        
        // 触摸手势支持 - 滑动关闭预览
        let startY = 0;
        let startX = 0;
        let isSwiping = false;
        
        document.getElementById('imagePreviewModal').addEventListener('touchstart', function(event) {
            startY = event.touches[0].clientY;
            startX = event.touches[0].clientX;
            isSwiping = false;
        });
        
        document.getElementById('imagePreviewModal').addEventListener('touchmove', function(event) {
            if (event.touches.length > 1) return;
            
            const currentY = event.touches[0].clientY;
            const currentX = event.touches[0].clientX;
            const diffY = currentY - startY;
            const diffX = Math.abs(currentX - startX);
            
            // 检测是否为向下滑动手势
            if (diffY > 50 && diffY > diffX) {
                isSwiping = true;
            }
        });
        
        document.getElementById('imagePreviewModal').addEventListener('touchend', function(event) {
            if (isSwiping) {
                closeImagePreview();
            }
        });
        
        // 为功能按钮添加悬停效果
        const previewButtons = document.querySelectorAll('#imagePreviewModal button');
        previewButtons.forEach(button => {
            button.addEventListener('mouseenter', function() {
                this.style.background = 'rgba(255, 255, 255, 0.25)';
                this.style.transform = 'scale(1.1)';
                this.style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.3)';
            });
            
            button.addEventListener('mouseleave', function() {
                this.style.background = 'rgba(255, 255, 255, 0.15)';
                this.style.transform = 'scale(1)';
                this.style.boxShadow = 'none';
            });
        });
        
        // 底部标签相关功能
        
        // 加载底部标签
        // 加载底部标签，带重试机制
        let quickActionsRetryCount = 0;
        const MAX_QUICK_ACTIONS_RETRIES = 3;
        
        function loadQuickActions() {
            fetch(`api/admin/group_quick_actions.php?group_id=${groupId}`)
                .then(res => res.json())
                .then(data => {
                    // 兼容不同的数据格式：可能直接返回数组，或者包含success字段
                    let quickActionsData = [];
                    if (data && data.success && Array.isArray(data.data)) {
                        quickActionsData = data.data;
                    } else if (Array.isArray(data)) {
                        quickActionsData = data;
                    }
                    renderQuickActions(quickActionsData);
                    // 重置重试计数
                    quickActionsRetryCount = 0;
                })
                .catch(error => {
                    console.error('加载底部标签失败:', error);
                    // 如果重试次数未达上限，重试加载
                    if (quickActionsRetryCount < MAX_QUICK_ACTIONS_RETRIES) {
                        quickActionsRetryCount++;
                        setTimeout(loadQuickActions, 1000 * quickActionsRetryCount); // 递增延迟重试
                    } else {
                        // 重试失败后，至少显示公告标签（如果有）
                        renderQuickActions([]);
                    }
                });
        }
        
        // 渲染底部标签
        function renderQuickActions(quickActions) {

            const container = document.getElementById('quickActionsContainer');
            
            let html = '';
            
            // 添加公告标签（如果有公告内容）
            if (groupData && groupData.announcement) {
                html += `
                    <div class="quick-action-item" onclick="openAnnouncement()">
                        <span class="quick-action-icon">📢</span>
                        <span class="quick-action-text">公告</span>
                    </div>
                `;
            }
            
            // 添加其他快捷功能标签
            if (quickActions && quickActions.length > 0) {
                html += quickActions.map(action => {
                    // 检查是否有数量信息
                    const hasCount = action.count && action.count > 0;
                    const countClass = hasCount ? 'has-count' : '';
                    const countAttr = hasCount ? `data-count="${action.count}"` : '';
                    
                    return `
                        <div class="quick-action-item ${countClass}" ${countAttr} onclick="handleQuickActionClick('${action.id}', '${action.url}')">
                            <span class="quick-action-icon">${action.icon}</span>
                            <span class="quick-action-text">${action.title}</span>
                        </div>
                    `;
                }).join('');
            }
            

            container.innerHTML = html;
            // 确保容器可见
            container.style.display = 'flex';
        }
        
        // 处理底部标签点击
        function handleQuickActionClick(actionId, url) {
            // 统计点击次数
            fetch('api/chat/track_quick_action_click.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    group_id: groupId,
                    action_id: actionId
                })
            })
            .catch(error => {
                console.error('统计点击次数失败:', error);
            });
            
            // 跳转到指定URL
            window.open(url, '_blank');
        }
        
        // 底部标签在loadGroupInfo()中加载
    </script>

    <!-- 转发选择底部弹窗 -->
    <div class="action-sheet-backdrop" id="forwardBackdrop" onclick="closeForwardSheet()"></div>
    <div class="action-sheet" id="forwardSheet">
        <div class="action-sheet-menu">
            <div class="action-sheet-item primary" onclick="doActualForward('current')">转发到本群</div>
            <div class="action-sheet-item" onclick="doActualForward('other')">转发到其他群聊...</div>
        </div>
        <div class="action-sheet-cancel" onclick="closeForwardSheet()">取消</div>
    </div>

    <!-- 底部多选操作栏 -->
    <div id="selectionBottomBar">
        <button class="sel-btn" onclick="forwardSelectedAsCard()">
            <div class="sel-icon">📑</div>
            <span>合并转发</span>
        </button>
        <button class="sel-btn" onclick="alert('逐条转发开发中...')">
            <div class="sel-icon">➡️</div>
            <span>逐条转发</span>
        </button>
        <button class="sel-btn" onclick="alert('收藏成功')">
            <div class="sel-icon">⭐</div>
            <span>收藏</span>
        </button>
        <button class="sel-btn" onclick="exitSelectionMode()">
            <div class="sel-icon">❌</div>
            <span>取消</span>
        </button>
    </div>

    <!-- 动态弹窗容器 -->
    <div id="dynamicModalsContainer"></div>

</body>
</html>