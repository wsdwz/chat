<!DOCTYPE html>

<html lang="zh-CN">
	<head>
		<meta charset="utf-8" />
		<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
		<style>
			* {
				margin: 0;
				padding: 0;
				box-sizing: border-box;
				-webkit-tap-highlight-color: transparent;
			}

			:root {
				/* 纯正 QQ 配色 */
				--primary-color: #12B7F5;
				/* 经典QQ蓝 */
				--primary-light: rgba(18, 183, 245, 0.1);
				--primary-dark: #0A9CE0;
				--text-color: #000000;
				--text-secondary: #878B99;
				/* 昵称等次要文字 */
				--text-tertiary: #B0B3BF;
				--background-color: #EBEDF0;
				/* QQ专属高级灰底色 */
				--card-background: #FFFFFF;

				--own-message-bg: #12B7F5;
				--other-message-bg: #FFFFFF;

				--font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
			}

			body {
				font-family: var(--font-family);
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
				background-color: var(--background-color);
				box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
				position: relative;
			}

			/* 顶部导航栏 - QQ风格 */
			.chat-header {
				background-color: #FAFAFA;
				/* QQ顶部偏白 */
				padding: 8px 16px;
				display: flex;
				align-items: center;
				justify-content: space-between;
				position: sticky;
				top: 0;
				z-index: 100;
				border-bottom: 0.5px solid rgba(0, 0, 0, 0.05);
				height: 54px;
			}

			.back-btn {
				background: none;
				border: none;
				font-size: 24px;
				cursor: pointer;
				color: #000;
				display: flex;
				align-items: center;
				padding: 0 8px 0 0;
			}

			.header-info {
				flex: 1;
				text-align: center;
				display: flex;
				flex-direction: column;
				align-items: center;
				justify-content: center;
			}

			.chat-title {
				font-size: 17px;
				font-weight: 500;
				color: #000;
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
				max-width: 200px;
			}

			.header-right {
				display: flex;
				gap: 8px;
			}

			.header-btn {
				background: none;
				border: none;
				font-size: 24px;
				cursor: pointer;
				color: #000;
				display: flex;
				align-items: center;
				justify-content: center;
			}

			/* 消息区域 */
			.messages-area {
				flex: 1;
				padding: 16px 12px;
				overflow-y: auto;
				display: flex;
				flex-direction: column;
				gap: 8px;
				overflow-x: hidden;
				background-color: var(--background-color);
				-webkit-overflow-scrolling: touch;
				scrollbar-width: none;
				-ms-overflow-style: none;
			}

			.messages-area::-webkit-scrollbar {
				display: none;
			}

			/* 消息行 */
			.message {
				display: flex;
				position: relative;
				animation: messageFadeIn 0.25s ease-out;
				width: 100%;
				margin-bottom: 15px;
			}

			@keyframes messageFadeIn {
				from {
					opacity: 0;
					transform: translateY(8px);
				}

				to {
					opacity: 1;
					transform: translateY(0);
				}
			}

			.message.own {
				justify-content: flex-end;
			}

			.message.other {
				justify-content: flex-start;
			}

			/* 头像 */
			.message-avatar {
				width: 35px;
				height: 35px;
				border-radius: 50%;
				/* QQ经典圆头像 */
				object-fit: cover;
				flex-shrink: 0;
				box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
			}

			.message.other .message-avatar {
				margin-right: 10px;
			}

			.message.own .message-avatar {
				margin-left: 10px;
			}

			.message-content-wrapper {
				max-width: 60%;
				display: flex;
				flex-direction: column;
			}

			.message.own .message-content-wrapper {
				align-items: flex-end;
			}

			.message.own .message-sender {
				display: none;
			}

			.message.other .message-content-wrapper {
				align-items: flex-start;
			}

			/* 昵称 */
			.message-sender {
				display: block;
				font-size: 11px;
				color: var(--text-secondary);
				margin-bottom: 4px;
				margin-left: 4px;
				margin-right: 4px;
			}

			/* 气泡本体 */
			.message-content {
				padding: 8px 12px;
				word-wrap: break-word;
				word-break: break-all;
				overflow-wrap: anywhere;
				line-height: 1.4;
				font-size: 13px;
				position: relative;
				display: block;
				/* 移除 flex 避免在某些浏览器中挤破容器 */
			}

			/* 别人发的消息气泡 (带小尾巴) */
			.message.other .message-content {
				background: var(--other-message-bg);
				color: var(--text-color);
				border-radius: 5px;
				border-top-left-radius: 4px;
				box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
			}

			.message.other .message-content::before {
				content: '';
				position: absolute;
				top: 0px;
				left: -6px;
				width: 10px;
				height: 10px;
				background: var(--other-message-bg);
				clip-path: polygon(100% 0, 100% 100%, 0 0);
				border-radius: 0 0 0 2px;
			}

			/* 自己发的消息气泡 (带小尾巴) */
			.message.own .message-content {
				background: var(--own-message-bg);
				color: #FFFFFF;
				border-radius: 5px;
				border-top-right-radius: 4px;
				box-shadow: 0 1px 2px rgba(18, 183, 245, 0.15);
			}

			.message.own .message-content::after {
				content: '';
				position: absolute;
				top: 0px;
				right: -6px;
				width: 10px;
				height: 10px;
				background: var(--own-message-bg);
				clip-path: polygon(0 0, 0 100%, 100% 0);
				border-radius: 0 0 2px 0;
			}


			/* 消息时间 (隐藏,QQ一般不在气泡下显示时间,只在消息间隙显示,这里保留原功能并弱化) */
			.message-time {
				font-size: 10px;
				color: var(--text-tertiary);
				margin-top: 4px;
				margin-left: 4px;
				margin-right: 4px;
			}

			/* @全体成员 高亮 */
			.mention-all-tag {
				display: inline-block;
				background: rgba(255, 140, 0, 0.1);
				color: #FF8C00;
				padding: 0 4px;
				border-radius: 4px;
				font-size: 14px;
				margin-right: 4px;
			}

			/* 卡片消息去除气泡底色 */
			.message-card {
				background: #FFFFFF !important;
				border-radius: 5px !important;
				width: 210px !important;
				padding: 0 !important;
				display: flex;
				flex-direction: column;
				overflow: hidden;
				text-decoration: none;
				border: 0.5px solid rgba(0, 0, 0, 0.08);
			}


			.message.own .message-content:has(.message-card)::after,
			.message.other .message-content:has(.message-card)::before,
			.message.own .message-content:has(.message-history-card)::after,
			.message.other .message-content:has(.message-history-card)::before,
			.message.own .message-content:has(.message-image)::after,
			.message.other .message-content:has(.message-image)::before,
			.message.own .message-content:has(.video-wrapper)::after,
			.message.other .message-content:has(.video-wrapper)::before {
				display: none !important;
			}

			.message.own .message-content:has(.message-card),
			.message.other .message-content:has(.message-card),
			.message.own .message-content:has(.message-history-card),
			.message.other .message-content:has(.message-history-card),
			.message.own .message-content:has(.message-image),
			.message.other .message-content:has(.message-image),
			.message.own .message-content:has(.video-wrapper),
			.message.other .message-content:has(.video-wrapper) {
				background: transparent !important;
				padding: 0 !important;
				box-shadow: none !important;
				border-radius: 0 !important;
			}

			/* ------------------ QQ风格卡片 ------------------ */
			.message-card-body {
				padding: 12px 14px;
				display: flex;
				gap: 12px;
				text-align: left;
				align-items: center;
			}

			.message-card-info {
				flex: 1;
				display: flex;
				flex-direction: column;
				justify-content: center;
				min-width: 0;
			}

			.message-card-title {
				font-size: 14px;
				font-weight: 500;
				color: #111111;
				line-height: 1.4;
				display: -webkit-box;
				-webkit-line-clamp: 1;
				-webkit-box-orient: vertical;
				overflow: hidden;
				margin-bottom: 2px;
			}

			.message-card-desc {
				font-size: 11px;
				color: #878B99;
				display: -webkit-box;
				-webkit-line-clamp: 2;
				-webkit-box-orient: vertical;
				overflow: hidden;
				line-height: 1.4;
			}

			.message-card-thumb {
				width: 45px;
				height: 45px;
				border-radius: 4px;
				object-fit: cover;
				flex-shrink: 0;
				background-color: #F3F4F6;
				border: 0.5px solid rgba(0, 0, 0, 0.04);
			}

			.message-card-footer {
				padding: 6px 14px;
				font-size: 11px;
				color: #878B99;
				border-top: 0.5px solid rgba(0, 0, 0, 0.05);
				text-align: left;
				display: flex;
				align-items: center;
				background: #FFFFFF;
			}

			.message-card-footer::before {
				content: '🔗';
				font-size: 10px;
				margin-right: 4px;
				opacity: 0.5;
			}

			/* ------------------ QQ风格 合并转发卡片 ------------------ */
			.message-history-card {
				background: #FFFFFF !important;
				border-radius: 5px !important;
				width: 210px !important;
				padding: 12px 14px !important;
				text-align: left;
				border: 0.5px solid rgba(0, 0, 0, 0.08);
				cursor: pointer;
				display: flex;
				flex-direction: column;
			}

			.message-history-title {
				font-size: 13px;
				font-weight: 500;
				color: #111111;
				margin-bottom: 10px;
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
			}

			.message-history-list {
				display: flex;
				flex-direction: column;
				gap: 6px;
				margin-bottom: 10px;
			}

			.message-history-item {
				font-size: 11px;
				color: #878B99;
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
				line-height: 1.4;
			}

			.message-history-footer {
				font-size: 10px;
				color: #B0B3BF;
				padding-top: 8px;
				border-top: 0.5px solid rgba(0, 0, 0, 0.05);
			}

			/* ------------------ 嵌套预览模态框 ------------------ */
			.qq-history-modal {
				display: none;
				position: fixed;
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
				background: #FFFFFF;
				/* QQ聊天记录背景通常是纯白 */
				z-index: 2000;
				flex-direction: column;
				transform: translateX(100%);
				transition: transform 0.3s cubic-bezier(0.25, 1, 0.5, 1);
			}

			.qq-history-modal.active {
				display: flex;
				transform: translateX(0);
			}

			.qq-header {
				height: 54px;
				min-height: 54px;
				background: #FAFAFA;
				display: flex;
				align-items: center;
				justify-content: space-between;
				padding: env(safe-area-inset-top) 16px 0;
				border-bottom: 0.5px solid rgba(0, 0, 0, 0.05);
			}

			.qq-close {
				font-size: 14px;
				color: #000;
				background: transparent;
				border: none;
				cursor: pointer;
				display: flex;
				align-items: center;
			}

			.qq-close::before {
				content: '‹';
				font-size: 26px;
				margin-right: 4px;
				margin-top: -2px;
				font-weight: 300;
			}

			.qq-title {
				font-size: 16px;
				font-weight: 500;
				position: absolute;
				left: 50%;
				transform: translateX(-50%);
				color: #000;
			}

			.qq-body {
				flex: 1;
				overflow-y: auto;
				-webkit-overflow-scrolling: touch;
				padding-bottom: 30px;
				background: #FFFFFF;
				scrollbar-width: none;
				-ms-overflow-style: none;
			}

			.qq-body::-webkit-scrollbar {
				display: none;
			}

			.wx-item {
				display: flex;
				padding: 12px 16px;
				/* QQ的内边距略小 */
				border-bottom: 0.5px solid rgba(0, 0, 0, 0.04);
			}

			.wx-avatar {
				width: 32px;
				/* 和外面保持一致 */
				height: 32px;
				border-radius: 50%;
				margin-right: 10px;
				object-fit: cover;
			}

			.wx-content {
				flex: 1;
				min-width: 0;
				display: flex;
				flex-direction: column;
			}

			.wx-name {
				font-size: 12px;
				color: #878B99;
				margin-bottom: 4px;
			}

			.wx-text {
				font-size: 14px;
				color: #111111;
				line-height: 1.4;
				word-wrap: break-word;
				word-break: break-all;
			}

			/* 聊天记录预览中的图片/视频样式 */
			.wx-text img {
				max-width: 140px;
				max-height: 200px;
				border-radius: 8px;
				cursor: pointer;
				object-fit: cover;
				border: 0.5px solid rgba(0, 0, 0, 0.05);
				display: block;
				margin: 4px 0;
			}

			.wx-text video {
				max-width: 140px;
				max-height: 200px;
				border-radius: 8px;
				cursor: pointer;
				object-fit: cover;
				background: #000;
				display: block;
				margin: 4px 0;
			}

			/* ------------------ 转发群聊选择器 ------------------ */
			.qq-group-selector-modal {
				display: none;
				position: fixed;
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
				background: #EBEDF0;
				z-index: 2005;
				flex-direction: column;
				transform: translateY(100%);
				transition: transform 0.3s cubic-bezier(0.25, 1, 0.5, 1);
			}

			.qq-group-selector-modal.active {
				display: flex;
				transform: translateY(0);
			}

			.qq-group-list {
				flex: 1;
				overflow-y: auto;
				padding: 16px;
				scrollbar-width: none;
				-ms-overflow-style: none;
			}

			.qq-group-list::-webkit-scrollbar {
				display: none;
			}

			.qq-group-item {
				background: #FFFFFF;
				border-radius: 5px;
				padding: 16px;
				margin-bottom: 12px;
				display: flex;
				align-items: center;
				justify-content: space-between;
				box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
				cursor: pointer;
			}

			.qq-group-item:active {
				background: #F9F9F9;
			}

			.qq-group-info {
				font-size: 16px;
				color: #000;
				font-weight: 500;
			}

			.qq-group-item.is-current::after {
				content: '当前群聊';
				font-size: 11px;
				color: var(--primary-color);
				background: var(--primary-light);
				padding: 2px 6px;
				border-radius: 4px;
			}

			/* 媒体内容 */
			.message-image {
				max-width: 120px;
				max-height: 160px;
				border-radius: 8px;
				cursor: pointer;
				object-fit: cover;
				border: 0.5px solid rgba(0, 0, 0, 0.05);
			}

			.video-wrappers {
				position: relative;
				display: inline-block;
				max-width: 100%;
				max-height: 320px;
				border-radius: 5px;
				overflow: hidden;
				cursor: pointer;
				background: #000;
				border: 0.5px solid rgba(0, 0, 0, 0.05);
			}

			.video-wrapper {
				position: relative;
				display: inline-block;
				max-width: 180px;
				max-height: 240px;
				border-radius: 5px;
				overflow: hidden;
				cursor: pointer;
				background: #000;
				border: 0.5px solid rgba(0, 0, 0, 0.05);
			}

			.video-play-btn {
				position: absolute;
				top: 50%;
				left: 50%;
				transform: translate(-50%, -50%);
				width: 36px;
				height: 36px;
				background: rgba(0, 0, 0, 0.4);
				border-radius: 50%;
				display: flex;
				align-items: center;
				justify-content: center;
				pointer-events: none;
				z-index: 2;
			}

			.video-play-btn::after {
				content: '';
				width: 0;
				height: 0;
				border-style: solid;
				border-width: 8px 0 8px 12px;
				border-color: transparent transparent transparent #fff;
				margin-left: 4px;
			}

			.message-video {
				width: 100%;
				height: 100%;
				max-width: 180px;
				max-height: 240px;
				object-fit: cover;
				display: block;
			}

			.message-file {
				display: flex;
				align-items: center;
				gap: 10px;
				padding: 10px 14px;
				background: #FFFFFF;
				border-radius: 8px;
				text-decoration: none;
				color: #000;
				border: 0.5px solid rgba(0, 0, 0, 0.05);
				width: 180px;
			}

			.message.own .message-file {
				background: #FFFFFF;
				color: #000;
			}

			.message-content:has(.message-image),
			.message-content:has(.video-wrapper) {
				padding: 0 !important;
				background: transparent !important;
				box-shadow: none !important;
			}

			/* 系统消息 (QQ灰色居中小字) */
			.system-message {
				text-align: center;
				margin: 16px 0;
			}

			.system-message span {
				background: rgba(0, 0, 0, 0.06);
				color: #878B99;
				padding: 4px 10px;
				border-radius: 4px;
				font-size: 11px;
				display: inline-block;
				max-width: 80%;
			}

			/* 底部输入区 QQ风格 */
			.input-area {
				background: #F4F5F7;
				position: sticky;
				bottom: 0;
				z-index: 100;
				display: flex;
				flex-direction: column;
				border-top: 0.5px solid #EBEBEB;
				padding-bottom: env(safe-area-inset-bottom);
			}

			/* 多选模式时隐藏输入区但保留占位 */
			body.selection-mode .input-area {
				visibility: hidden !important;
			}

			.quick-actions {
				display: flex;
				gap: 12px;
				padding: 10px 16px 2px;
				overflow-x: auto;
				scrollbar-width: none;
				align-items: center;
				background: #F4F5F7;
			}

			.quick-actions::-webkit-scrollbar {
				display: none;
			}

			.quick-action-item {
				display: flex;
				align-items: center;
				justify-content: center;
				padding: 4px 12px;
				border-radius: 5px;
				background: #FFFFFF;
				cursor: pointer;
				font-size: 12px;
				color: #333;
				white-space: nowrap;
				box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
				border: 0.5px solid rgba(0, 0, 0, 0.02);
			}

			.input-row {
				display: flex;
				align-items: flex-end;
				padding: 8px 12px 12px;
				gap: 12px;
				background: #F4F5F7;
			}

			.input-btn {
				background: none;
				border: none;
				font-size: 22px;
				cursor: pointer;
				color: #878B99;
				padding: 0;
				width: 34px;
				height: 34px;
				display: flex;
				align-items: center;
				justify-content: center;
				border-radius: 50%;
			}

			#messageInput {
				flex: 1;
				padding: 10px 16px;
				border: none;
				border-radius: 5px;
				/* QQ 圆角输入框 */
				background-color: #FFFFFF;
				font-size: 13px;
				outline: none;
				resize: none;
				max-height: 100px;
				overflow-y: auto;
				line-height: 1.4;
				box-shadow: none;
			}

			#messageInput::placeholder {
				color: #B0B3BF;
			}

			.send-btn {
				background: var(--primary-color);
				color: #FFFFFF;
				border: none;
				border-radius: 5px;
				/* QQ发送按钮也是圆角的 */
				padding: 0 18px;
				height: 30px;
				font-size: 12px;
				font-weight: 500;
				white-space: nowrap;
				cursor: pointer;
				display: flex;
				align-items: center;
				justify-content: center;
				margin-bottom: 2px;
			}

			.send-btn:active {
				background: var(--primary-dark);
			}

			.send-btn:disabled {
				background: #A0DFFB;
				cursor: not-allowed;
			}

			/* 多选模式底部操作栏 */
			#selectionBottomBar {
				display: none;
				position: fixed;
				bottom: 0;
				left: 0;
				width: 100%;
				height: 56px;
				background: #F8F8F8;
				border-top: 0.5px solid #EBEBEB;
				z-index: 1000;
				justify-content: space-around;
				align-items: center;
				padding-bottom: env(safe-area-inset-bottom);
			}

			body.selection-mode #selectionBottomBar {
				display: flex;
			}

			.sel-btn {
				display: flex;
				flex-direction: column;
				align-items: center;
				gap: 4px;
				background: none;
				border: none;
				color: #333;
				font-size: 10px;
				cursor: pointer;
			}

			.sel-icon {
				font-size: 20px;
				color: #878B99;
			}

			.message {
				transition: transform 0.25s ease;
			}

			/* 多选气泡 - 在消息对面垂直居中 */
			.msg-checkbox {
				position: absolute;
				width: 18px;
				height: 18px;
				border-radius: 50%;
				border: 1.5px solid #D5D5D5;
				background: #FFFFFF;
				display: none;
				align-items: center;
				justify-content: center;
				z-index: 10;
				top: 50%;
				transform: translateY(-50%);
			}

			/* 别人的消息:复选框在右侧(消息对面) */
			.message.other .msg-checkbox {
				right: 8px;
			}

			/* 自己的消息:复选框在左侧(消息对面) */
			.message.own .msg-checkbox {
				left: 8px;
			}

			/* 多选模式时显示气泡 */
			body.selection-mode .msg-checkbox {
				display: flex;
			}

			.message.selected .msg-checkbox {
				background: var(--primary-color);
				border-color: var(--primary-color);
			}

			.message.selected .msg-checkbox::after {
				content: '';
				width: 4px;
				height: 8px;
				border: solid white;
				border-width: 0 1.5px 1.5px 0;
				transform: rotate(45deg);
				margin-bottom: 2px;
			}

			/* 多选模式禁用交互 */
			body.selection-mode .message-history-card,
			body.selection-mode .message-image,
			body.selection-mode .message-video,
			body.selection-mode .video-wrapper,
			body.selection-mode .message-file,
			body.selection-mode a {
				pointer-events: none !important;
				cursor: default !important;
			}

			/* 公告条 */
			.announcement-bar {
				background: rgba(255, 140, 0, 0.1);
				color: #FF8C00;
				padding: 8px 14px;
				margin: 0 12px 10px;
				border-radius: 4px;
				font-size: 13px;
				display: flex;
				align-items: center;
				gap: 6px;
				overflow: hidden;
				white-space: nowrap;
				text-overflow: ellipsis;
			}

			/* 图片预览模态框 */
			.image-preview-modal {
				display: none;
				position: fixed;
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
				background: rgba(0, 0, 0, 1);
				z-index: 3000;
				justify-content: center;
				align-items: center;
			}

			.image-preview-modal.active {
				display: flex;
			}

			.image-preview-content {
				max-width: 100%;
				max-height: 100%;
				object-fit: contain;
			}

			.image-preview-close {
				display: none;
			}

			/* 移动端预览通常点背景退出,隐藏关闭按钮 */

			/* 表情面板 QQ 风格 */
			.emoji-panel {
				padding: 6px 10px 4px;
				display: flex;
				flex-wrap: wrap;
				gap: 6px;
				background: #F8F8F8;
				border-top: 0.5px solid #EBEBEB;
				max-height: 150px;
				overflow-y: auto;
			}

			.emoji-panel::-webkit-scrollbar {
				width: 4px;
			}

			.emoji-panel::-webkit-scrollbar-thumb {
				background: rgba(0, 0, 0, 0.15);
				border-radius: 2px;
			}

			.emoji-item {
				font-size: 24px;
				cursor: pointer;
				width: 32px;
				height: 32px;
				display: flex;
				align-items: center;
				justify-content: center;
				border-radius: 4px;
			}

			.emoji-item:active {
				background: rgba(0, 0, 0, 0.06);
			}

			/* 群信息右侧面板 */
			.group-info-panel {
				position: fixed;
				top: 0;
				right: 0;
				bottom: 0;
				width: 260px;
				background: #FFFFFF;
				box-shadow: -2px 0 8px rgba(0, 0, 0, 0.08);
				z-index: 1500;
				transform: translateX(100%);
				transition: transform 0.25s ease-out;
				display: flex;
				flex-direction: column;
			}

			.group-info-panel.active {
				transform: translateX(0);
			}

			.group-info-header {
				padding: 14px 16px;
				border-bottom: 0.5px solid #EBEBEB;
				display: flex;
				align-items: center;
				justify-content: space-between;
			}

			.group-info-title {
				font-size: 16px;
				font-weight: 500;
			}

			.group-info-close {
				background: none;
				border: none;
				font-size: 20px;
				cursor: pointer;
			}

			.group-info-body {
				padding: 12px 16px;
				font-size: 14px;
				color: #333;
			}

			.group-info-row {
				display: flex;
				justify-content: space-between;
				padding: 8px 0;
				border-bottom: 0.5px solid #F3F3F3;
			}

			.group-info-row span:first-child {
				color: #878B99;
			}

			/* 顶部 QQ 群聊导航 */
			.chat-header {
				background-color: #F8F8F8;
				padding: 6px 10px;
				display: flex;
				align-items: center;
				display: none;
				justify-content: space-between;
				border-bottom: 0.5px solid #E5E5E5;
				height: 52px;
			}

			.header-left {
				display: flex;
				align-items: center;
				gap: 6px;
			}

			.group-avatar {
				width: 32px;
				height: 32px;
				border-radius: 8px;
				object-fit: cover;
			}

			.header-center {
				flex: 1;
				display: flex;
				flex-direction: column;
				justify-content: center;
				align-items: flex-start;
				padding-left: 8px;
			}

			.chat-title {
				font-size: 16px;
				font-weight: 500;
				max-width: 160px;
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
			}

			.chat-subtitle {
				font-size: 11px;
				color: #878B99;
				margin-top: 2px;
			}

			.header-right {
				display: flex;
				align-items: center;
				gap: 4px;
			}

			.header-btn {
				background: none;
				border: none;
				font-size: 20px;
				cursor: pointer;
				padding: 4px;
				width: 32px;
				height: 32px;
				display: flex;
				align-items: center;
				justify-content: center;
			}

			/* 群资料整页 */
			.group-info-page {
				position: fixed;
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
				background: #EBEDF0;
				z-index: 2000;
				transform: translateX(100%);
				transition: transform 0.3s ease-out;
				display: flex;
				flex-direction: column;
			}

			.group-info-page.active {
				transform: translateX(0);
			}

			.group-info-topbar {
				height: 50px;
				background: #F8F8F8;
				display: flex;
				align-items: center;
				padding: 0 12px;
				border-bottom: 0.5px solid #EBEBEB;
			}

			.gi-back {
				background: none;
				border: none;
				font-size: 26px;
				margin-right: 8px;
			}

			.gi-title {
				font-size: 17px;
				font-weight: 500;
			}

			.group-info-content {
				flex: 1;
				overflow-y: auto;
				-webkit-overflow-scrolling: touch;
				padding: 10px 12px 20px;
			}

			.gi-section {
				background: #FFFFFF;
				border-radius: 10px;
				margin-bottom: 10px;
				padding: 10px 12px;
			}

			.gi-header-section {
				display: flex;
				align-items: center;
				gap: 10px;
			}

			.gi-group-avatar {
				width: 54px;
				height: 54px;
				border-radius: 10px;
				object-fit: cover;
			}

			.gi-header-text {
				display: flex;
				flex-direction: column;
			}

			.gi-group-name {
				font-size: 16px;
				font-weight: 500;
			}

			.gi-group-id {
				font-size: 12px;
				color: #878B99;
				margin-top: 4px;
			}

			.gi-row-title {
				font-size: 13px;
				color: #878B99;
				margin-bottom: 8px;
			}

			.gi-members-row {
				display: flex;
				gap: 10px;
			}

			.gi-member {
				width: 52px;
				display: flex;
				flex-direction: column;
				align-items: center;
				font-size: 11px;
				color: #555;
			}

			.gi-member-avatar {
				width: 40px;
				height: 40px;
				border-radius: 50%;
				object-fit: cover;
				margin-bottom: 4px;
			}

			.gi-member-add {
				background: #F5F5F5;
				display: flex;
				align-items: center;
				justify-content: center;
				font-size: 20px;
			}

			.gi-row {
				display: flex;
				justify-content: space-between;
				align-items: center;
				padding: 10px 0;
				border-bottom: 0.5px solid #F3F3F3;
				font-size: 14px;
			}

			.gi-row:last-child {
				border-bottom: none;
			}

			.gi-row-right {
				color: #878B99;
				font-size: 13px;
			}

			.gi-row-switch {
				align-items: center;
			}

			.gi-switch {
				position: relative;
				display: inline-block;
				width: 40px;
				height: 22px;
			}

			.gi-switch input {
				opacity: 0;
				width: 0;
				height: 0;
			}

			.gi-slider {
				position: absolute;
				cursor: pointer;
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
				background-color: #ccc;
				transition: .2s;
				border-radius: 22px;
			}

			.gi-slider:before {
				position: absolute;
				content: "";
				height: 18px;
				width: 18px;
				left: 2px;
				top: 2px;
				background-color: white;
				transition: .2s;
				border-radius: 50%;
			}

			.gi-switch input:checked+.gi-slider {
				background-color: #12B7F5;
			}

			.gi-switch input:checked+.gi-slider:before {
				transform: translateX(18px);
			}

			/* 多选模式顶部导航 */
			.selection-header {
				height: 52px;
				background: #F8F8F8;
				display: flex;
				align-items: center;
				justify-content: space-between;
				padding: 0 12px;
				border-bottom: 0.5px solid #EBEBEB;
			}

			.sel-top-cancel {
				background: none;
				border: none;
				font-size: 16px;
				color: #007AFF;
			}

			.sel-top-title {
				font-size: 14px;
				font-weight: 500;
			}

			.sel-top-search {
				background: none;
				border: none;
				font-size: 20px;
			}

			body.selection-mode .chat-header {
				display: none;
			}

			body.selection-mode #selectionHeader {
				display: flex !important;
			}

			/* MuiPlayer 视频预览模态框 */
			.video-preview-modal {
				display: none;
				position: fixed;
				top: 0;
				left: 0;
				width: 100vw;
				height: 100vh;
				background: #000000;
				z-index: 4000;
				flex-direction: column;
			}

			.video-preview-modal.active {
				display: flex;
				animation: modalFadeIn 0.2s ease-out;
			}

			@keyframes modalFadeIn {
				from {
					opacity: 0;
					transform: scale(0.95);
				}

				to {
					opacity: 1;
					transform: scale(1);
				}
			}

			.video-preview-close {
				position: absolute;
				top: 20px;
				left: 15px;
				color: #fff;
				background: rgba(0, 0, 0, 0.4);
				/* border-radius: 50%; */
				width: 32px;
				height: 32px;
				border: 1px solid rgba(255, 255, 255, 0.2);
				font-size: 26px;
				z-index: 4001;
				cursor: pointer;
				display: flex;
				align-items: center;
				justify-content: center;
				padding-bottom: 3px;
			}

			.video-preview-close::before {
				content: '‹';
			}

			#mui-player-wrap {
				flex: 1;
				width: 100%;
				display: flex;
				align-items: center;
				justify-content: center;
			}

			/* 隐藏 MuiPlayer 自带的顶部返回按钮和标题栏 */
			.mplayer-header {
				display: none !important;
			}

			.mplayer-mask {
				background: transparent !important;
			}

			/* ====== 强制彻底居中 ====== */
			#mui-player-wrap,
			#mui-player-container,
			.mplayer {
				width: 100vw !important;
				height: 100vh !important;
				background: #000000 !important;
			}

			/* 接管内部所有的包裹层，强制铺满屏幕 */
			.mplayer .mplayer-video-wrap {
				position: absolute !important;
				top: 0 !important;
				left: 0 !important;
				width: 100% !important;
				height: 100% !important;
				display: flex !important;
				align-items: center !important;
				justify-content: center !important;
				margin: 0 !important;
				padding: 0 !important;
			}

			/* 针对 video 标签彻底重置它的宽高和定位，完全交给 object-fit */
			.mplayer video {
				position: static !important;
				width: 100% !important;
				height: 100% !important;
				max-width: 100% !important;
				max-height: 100% !important;
				object-fit: contain !important;
				top: auto !important;
				left: auto !important;
				transform: none !important;
				margin: 0 !important;
			}

			.mplayer-mask {
				background: transparent !important;
			}
		</style>

		<!-- 引入 MuiPlayer 框架 -->
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/mui-player/dist/mui-player.min.css">
		<script src="https://cdn.jsdelivr.net/npm/mui-player/dist/mui-player.min.js"></script>

		<style>
			/* 强制隐藏多选相关UI */
			#selectionHeader,
			#selectionBottomBar,
			.msg-checkbox {
				display: none !important;
			}

			body.selection-mode .input-area {
				visibility: visible !important;
			}

			body.selection-mode .chat-header {
				display: flex !important;
			}
		</style>

		<script type="text/javascript" src="uni.webview.js"></script>




		<style>
			/* QQ风格弹窗样式 */
			.qq-alert-modal {
				display: none;
				position: fixed;
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
				background: rgba(0, 0, 0, 0.4);
				z-index: 9999;
				align-items: center;
				justify-content: center;
				animation: fadeIn 0.2s ease-out;
			}

			.qq-alert-modal.active {
				display: flex;
			}

			.qq-alert-box {
				background: #FFFFFF;
				border-radius: 12px;
				display: flex;
				padding-left: 20px;
				padding-right: 20px;
				flex-direction: column;
				overflow: hidden;
				animation: scaleUp 0.2s ease-out;
			}

			.qq-alert-content {
				padding: 20px 20px;
				text-align: center;
				font-size: 12px;
				color: #000000;
				line-height: 1.5;
			}

			.qq-alert-btn {
				padding: 12px 0;
				text-align: center;
				font-size: 12px;
				color: #12B7F5;
				font-weight: 500;
				cursor: pointer;
				background: #FFFFFF;
				border: none;
				border-top: 0.5px solid #EBEBEB;
				outline: none;
			}

			.qq-alert-btn:active {
				background: #F5F5F5;
			}

			@keyframes fadeIn {
				from {
					opacity: 0;
				}

				to {
					opacity: 1;
				}
			}

			@keyframes scaleUp {
				from {
					transform: scale(0.9);
				}

				to {
					transform: scale(1);
				}
			}
		</style>

	</head>

	<body>
		<div class="chat-container">
			<!-- 顶部导航栏 -->
			<div class="chat-header">
				<div class="header-left"><button class="back-btn" onclick="window.history.back()">‹</button><img
						class="group-avatar" src="/group_avatar.png" /></div>
				<div class="header-center">
					<div class="chat-title" id="chatTitle">群聊</div>
					<div class="chat-subtitle" id="chatSubTitle">3人在线 | 共20人</div>
				</div>
			</div>
			<div class="selection-header" id="selectionHeader" style="display:none;">
				<button class="sel-top-cancel" onclick="exitSelectionMode()">取消</button>
				<div class="sel-top-title">已选择 <span id="selectedCount">0</span> 条消息</div>
				<button class="sel-top-search">🔍</button>
			</div>
			<div class="messages-area" id="messagesArea">
				<div id="historyLoader"
					style="text-align: center; color: #999; font-size: 12px; padding: 10px; display: none;">下拉加载更多...
				</div>
				<div id="messageListContainer">
					<div style="text-align: center; color: #999; font-size: 12px; padding: 20px;">加载中...</div>
				</div>
			</div>
			<div class="input-area">
				<div class="quick-actions" id="quickActionsContainer">

				</div>
				<div class="input-row" id="textInputRow">
					<textarea id="messageInput" placeholder="发消息..."></textarea>
					<button class="send-btn" onclick="sendMessage()">发送</button>
				</div>
			</div>
		</div>

		<!-- 视频预览模态框 (MuiPlayer) -->
		<div class="video-preview-modal" id="videoPreviewModal">
			<button class="video-preview-close" onclick="closeVideoPreview()"></button>
			<div id="mui-player-wrap" style="width: 100%;max-width: 100%;"></div>
		</div>

		<!-- 图片预览模态框 -->
		<div class="image-preview-modal" id="imagePreviewModal">
			<button class="image-preview-close" onclick="closeImagePreview()">×</button>
			<img class="image-preview-content" id="imagePreviewContent" src="" />
		</div>
		<script>
			const urlParams = new URLSearchParams(window.location.search);
			let groupId = urlParams.get('group_id') || '1';
			let userId = urlParams.get('user_id') || 'guest_' + Date.now();
			let userData = {
				id: userId,
				nickname: '用户_' + userId.slice(-4),
				avatar: 'https://picsum.photos/id/' + Math.floor(Math.random() * 1000) + '/60/60'
			};

			// 预加载提示音,动态使用当前域名
			const newMessageAudio = new Audio();
			newMessageAudio.src = window.location.origin + '/mp3/xm3143.mp3';

			let lastMessageCount = 0;
			let isFirstLoad = true;
			let currentGroupId = groupId;

			function sendMessage() {
				const btn = document.querySelector('.send-btn');
				if (btn && btn.disabled) return;

				const input = document.getElementById('messageInput');
				const content = input.value.trim();
				if (!content) return;


				fetch('api/chat/send_message.php', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json'
					},
					body: JSON.stringify({
						group_id: groupId,
						user_id: userId,
						user_nickname: userData.nickname,
						user_avatar: userData.avatar,
						type: 'text',
						content: content
					})
				}).then(r => r.json()).then(data => {
					if (data.success) {
						input.value = '';
						loadMessages();
						setTimeout(() => scrollToBottom(true), 100);
					} else {
						alert(data.message);
					}
				});
			}

			function loadMessages() {
				console.log('加载群聊消息:', groupId);
				console.log('当前群聊ID:', currentGroupId);
				console.log('最后消息数量:', lastMessageCount);
				fetch(`api/chat/get_messages.php?group_id=${groupId}`)
					.then(r => r.json())
					.then(messages => {
						console.log('获取到的消息:', messages);
						const area = document.getElementById('messagesArea');

						// 重新渲染的条件:
						// 1. 消息数量增加
						// 2. 群聊ID改变
						// 3. 首次加载
						if (currentGroupId !== groupId) {
							renderedMessageIds.clear();
							currentOffset = 0;
							hasMoreHistory = true;
							const loader = document.getElementById('historyLoader');
							if (loader) loader.style.display = 'none';
						}

						// 新增：比对已渲染的消息，如果接口返回的数组里没有了，说明已被撤回/删除，需要从DOM和集合中移除
						const newMsgIds = new Set(messages.map(m => {
							const msgContent = typeof m.content === 'string' ? m.content.substring(0, 20) : 'obj';
							return m.id || `${m.timestamp}_${m.user_id}_${msgContent}`;
						}));
						let hasDeleted = false;
						for (const id of renderedMessageIds) {
							if (!newMsgIds.has(id)) {
								const el = document.getElementById('msg-' + id);
								if (el) el.remove();
								renderedMessageIds.delete(id);
								hasDeleted = true;
							}
						}

						if (messages.length > lastMessageCount || hasDeleted || currentGroupId !== groupId || isFirstLoad) {
							console.log('重新渲染消息,原因:',
								hasDeleted ? '消息被撤回/删除' :
								messages.length > lastMessageCount ? '消息数量增加' :
								currentGroupId !== groupId ? '群聊ID改变' : '首次加载');
							const container = document.getElementById('messageListContainer');

							// 【修复：首次加载时清空“加载中...”默认占位符】
							if (isFirstLoad || currentGroupId !== groupId) {
								container.innerHTML = '';
							}

							// 【修复：如果是空群聊，显示暂无消息】
							if (messages.length === 0) {
								container.innerHTML =
									'<div style="text-align: center; color: #999; font-size: 12px; padding: 20px;">暂无消息</div>';
							} else {
								if (container.innerHTML.includes('暂无消息')) {
									container.innerHTML = '';
								}
							}

							// 依赖 renderedMessageIds 进行严格去重，不论后端返回什么，只追加新消息
							messages.forEach(msg => {
								addMessageToDOM(msg, container, false);
							});

							// 播放提示音 (排除第一次加载和群聊切换)
							if (!isFirstLoad && currentGroupId === groupId) {
								// 检查最后一条消息是否是别人发的
								const lastMsg = messages[messages.length - 1];
								if (lastMsg.user_id !== userId) {
									newMessageAudio.play().catch(e => console.log("播放提示音失败: ", e));
								}
							}

							setTimeout(() => scrollToBottom(true), 100);
							lastMessageCount = messages.length;
							currentGroupId = groupId;
						}
						isFirstLoad = false;
					}).catch(error => {
						console.error('加载消息失败:', error);
					});
			}


			let currentMuiPlayer = null;

			function openVideoPreview(src) {
				if (document.body.classList.contains('selection-mode')) return;

				const modal = document.getElementById('videoPreviewModal');
				modal.classList.add('active');
				document.body.style.overflow = 'hidden'; // 防止移动端背景穿透滚动

				const wrap = document.getElementById('mui-player-wrap');
				// 每次打开重新生成挂载点，防止 mui-player 销毁不干净
				wrap.innerHTML = '<div id="mui-player-container"></div>';

				currentMuiPlayer = new MuiPlayer({
					container: '#mui-player-container',
					title: '',
					src: src,
					autoplay: true,
					loop: false,
					themeColor: '#12B7F5',
					lang: 'zh-cn',
					objectFit: 'contain' // 让框架自身也尝试走contain逻辑
				});

				// 监听视频元数据加载完毕后，暴力纠正DOM（防框架内部JS延时重写）
				currentMuiPlayer.on('ready', function() {
					setTimeout(() => {
						const v = document.querySelector('.mplayer video');
						if (v) {
							v.style.setProperty('width', '100%', 'important');
							v.style.setProperty('height', '100%', 'important');
							v.style.setProperty('top', '0', 'important');
							v.style.setProperty('left', '0', 'important');
							v.style.setProperty('transform', 'none', 'important');
							v.style.setProperty('position', 'relative', 'important');
						}
					}, 100);
				});
			}

			function closeVideoPreview() {
				const modal = document.getElementById('videoPreviewModal');
				modal.classList.remove('active');
				document.body.style.overflow = ''; // 恢复背景滚动

				if (currentMuiPlayer) {
					try {
						currentMuiPlayer.destroy();
					} catch (e) {}
					currentMuiPlayer = null;
				}
				document.getElementById('mui-player-wrap').innerHTML = '';
			}

			let longPressTimer;

			function handleLongPress(element) {
				/* 多选功能已取消 */
			}

			// 用于全局记录已渲染的消息ID，防止重复加载
			const renderedMessageIds = new Set();

			function addMessageToDOM(message, area, isPrepend = false) {
				// 1. 生成唯一标识(优先使用真实ID，如果没有则通过时间+用户+内容生成哈希)
				const msgContent = typeof message.content === 'string' ? message.content.substring(0, 20) : 'obj';
				const msgId = message.id || `${message.timestamp}_${message.user_id}_${msgContent}`;

				// 2. 如果页面上已经有这条消息，直接跳过不渲染
				if (renderedMessageIds.has(msgId)) {
					return false;
				}
				renderedMessageIds.add(msgId);

				const isOwn = message.user_id === userId;
				let contentHtml = message.content || '';

				// 防止XSS攻击:转义普通文本
				if (message.type === 'text') {
					contentHtml = String(contentHtml).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(
						/"/g, '&quot;');
				}

				// 如果内容里包含写死的 lvba3 域名,自动替换为当前域名
				if (typeof contentHtml === 'string' && contentHtml.includes('lvba3.tyxcu.shop')) {
					contentHtml = contentHtml.replace(/https?:\/\/lvba3\.tyxcu\.shop/g, window.location.origin);
				}

				// 解析 @全体成员
				if (message.type === 'text' && typeof contentHtml === 'string' && contentHtml.includes('@全体成员')) {
					contentHtml = contentHtml.replace(/@全体成员/g, '<span class="mention-all-tag">@全体成员</span>');
				}

				// 解析卡片与合并转发
				if (message.type === 'card' || message.type === 'history') {
					try {
						const payload = typeof message.content === 'string' ? JSON.parse(message.content) : message.content;
						if (message.type === 'card') {
							let clickHandler = '';
							clickHandler =
								`onclick="uni.webView.navigateTo({url: '/pages/${payload.type || ''}/${payload.type || ''}?id=${payload.id || ''}'})" style="cursor: pointer;"`;

							contentHtml = `
                            <div class="message-card" ${clickHandler}>
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
							if (payload.items && payload.items.length) {
								itemsHtml = payload.items.slice(0, 4).map(it => {
									if (it.type === 'image')
										return `<div class="message-history-item">${it.from}: [图片]</div>`;
									if (it.type === 'video')
										return `<div class="message-history-item">${it.from}: [视频]</div>`;
									if (it.type === 'voice')
										return `<div class="message-history-item">${it.from}: [语音]</div>`;
									if (it.type === 'file')
										return `<div class="message-history-item">${it.from}: [文件]</div>`;
									if (it.type === 'history')
										return `<div class="message-history-item">${it.from}: [聊天记录]</div>`;
									return `<div class="message-history-item">${it.from}: ${it.text}</div>`;
								}).join('');
							}
							const encodedPayload = encodeURIComponent(JSON.stringify(payload));
							// 安全处理,避免onclick事件中的语法错误
							contentHtml = `
                            <div class="message-history-card" data-payload="${encodedPayload}" onclick="openQQHistoryModal(this.getAttribute('data-payload'))">
                                <div class="message-history-title">${payload.title || '群聊的聊天记录'}</div>
                                <div class="message-history-list">${itemsHtml}</div>
                                <div class="message-history-footer">查看${payload.items ? payload.items.length : 0}条转发消息</div>
                            </div>
                        `;
						}
					} catch (e) {
						console.error("解析卡片失败", e);
					}
				}

				// 系统消息
				if (message.type === 'system') {
					const div = document.createElement('div');
					div.className = 'system-message';
					div.id = 'msg-' + msgId;
					div.innerHTML = `<span>${contentHtml}</span>`;
					if (isPrepend) {
						area.prepend(div);
					} else {
						area.appendChild(div);
					}
					return;
				}

				// 补充遗漏的语音、文件和视频、图片处理
				if (message.type === 'video') {
					contentHtml = `<div class="video-wrapper" onclick="openVideoPreview(this.querySelector('video').src)">
						<video src="${contentHtml}" class="message-video" preload="metadata" playsinline></video>
						<div class="video-play-btn"></div>
					</div>`;
				} else if (message.type === 'image') {
					contentHtml =
						`<img src="${contentHtml}" class="message-image" onclick="openImagePreview('${contentHtml}')">`;
				} else if (message.type === 'voice') {
					contentHtml = `<audio src="${contentHtml}" controls style="max-width: 100%; height: 40px;"></audio>`;
				} else if (message.type === 'file') {
					const fileName = contentHtml.split('/').pop() || '文件';
					contentHtml = `<a href="${contentHtml}" target="_blank" class="message-file">
                    <span style="font-size: 20px;">📄</span>
                    <span style="word-break: break-all;">${fileName}</span>
                </a>`;
				}

				const avatarUrl = message.user_avatar || `/user.jpg`;
				const senderName = message.user_nickname || '用户';

				const div = document.createElement('div');
				div.className = `message ${isOwn ? 'own' : 'other'}`;
				div.id = 'msg-' + msgId;

				// 注入复选框用于多选合并转发
				const checkboxHtml = '';

				// 格式化消息时间
				const timestamp = message.timestamp || new Date().toISOString();
				const date = new Date(timestamp);
				const now = new Date();
				const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
				const targetDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());
				const diffDays = Math.floor((today - targetDate) / (1000 * 60 * 60 * 24));

				const hh = date.getHours().toString().padStart(2, '0');
				const mm = date.getMinutes().toString().padStart(2, '0');
				let timeStr = hh + ':' + mm;

				if (diffDays === 1) {
					timeStr = '昨天 ' + timeStr;
				} else if (diffDays === 2) {
					timeStr = '前天 ' + timeStr;
				} else if (diffDays > 2) {
					const MM = (date.getMonth() + 1).toString().padStart(2, '0');
					const DD = date.getDate().toString().padStart(2, '0');
					timeStr = date.getFullYear() + '-' + MM + '-' + DD + ' ' + timeStr;
				}

				div.innerHTML = `
					${checkboxHtml}
					${!isOwn ? `<img src="${avatarUrl}" class="message-avatar">` : ''}
					<div class="message-content-wrapper">
						${!isOwn ? `<div class="message-sender">${senderName}</div>` : ''}
						<div class="message-content">${contentHtml}</div>
						<div class="message-time">${timeStr}</div>
					</div>
					${isOwn ? `<img src="${avatarUrl}" class="message-avatar">` : ''}
				`;

				// 点击消息时,如果在多选模式则切换选中状态
				div.addEventListener('click', function(e) {
					// 防止点击内部链接时触发
					if (e.target.tagName === 'A' || e.target.closest('a')) return;

					if (document.body.classList.contains('selection-mode')) {
						toggleMsgSelect(div);
					}
				});

				// 添加长按事件监听
				div.addEventListener('mousedown', function(e) {
					longPressTimer = setTimeout(() => {
						handleLongPress(div);
					}, 500);
				});





				// 触摸设备支持
				div.addEventListener('touchstart', function(e) {
					longPressTimer = setTimeout(() => {
						handleLongPress(div);
					}, 500);
				});





				if (isPrepend) {
					area.prepend(div);
				} else {
					area.appendChild(div);
				}
				return true;
			}

			function scrollToBottom(force = false) {
				const area = document.getElementById('messagesArea');
				if (area) area.scrollTop = area.scrollHeight;
			}


			// removed: setInterval(loadMessages, 3000);


			let isGroupBanned = false; // 全局变量记录禁言状态

			function loadGroupInfo(isInitialLoad = false) {
				if (!groupId) {
					if (isInitialLoad) showQQAlert('无法获取当前群信息');
					return;
				}

				// Construct the absolute URL
				var currentUrl = new URL(window.location.href);
				var apiUrl = currentUrl.origin + currentUrl.pathname.replace(/\/[^\/]*$/, '') +
					'/api/chat/groups.php?group_id=' + groupId;

				fetch(apiUrl)
					.then(function(response) {
						if (!response.ok) throw new Error('Network error');
						return response.json();
					})
					.then(function(group) {
						if (!group || group.error) throw new Error('无法获取群信息');

						// 1. 处理禁言状态 (allow_speak: 0 表示禁言，1表示允许)
						var messageInput = document.getElementById('messageInput');
						var sendBtn = document.querySelector('.send-btn');

						// 如果返回的数据包含 allow_speak 字段
						if (group.allow_speak !== undefined) {
							if (group.allow_speak == 0) {
								isGroupBanned = true;
								if (messageInput) {
									messageInput.placeholder = "全体禁言中..";
									messageInput.disabled = true;
									messageInput.style.backgroundColor = "#EBEBEB";
									messageInput.value = ""; // 清空可能已有的输入
								}
								if (sendBtn) {
									sendBtn.disabled = true;
									sendBtn.style.backgroundColor = "#A0DFFB";
									sendBtn.style.cursor = "not-allowed";
								}
							} else {
								isGroupBanned = false;
								if (messageInput) {
									messageInput.placeholder = "说点什么吧...";
									messageInput.disabled = false;
									messageInput.style.backgroundColor = "#FFFFFF";
								}
								if (sendBtn) {
									sendBtn.disabled = false;
									sendBtn.style.backgroundColor = ""; // 恢复默认
									sendBtn.style.cursor = "pointer";
								}
							}
						}

						// 2. 处理 quick_actions
						var container = document.getElementById('quickActionsContainer');
						if (container) {
							// 只有当有实际变化时才重新渲染，避免频繁重绘导致闪烁
							var currentActionsStr = JSON.stringify(group.quick_actions || []);
							if (container.dataset.lastActions !== currentActionsStr) {
								container.innerHTML = '';
								container.dataset.lastActions = currentActionsStr;

								if (group.quick_actions && group.quick_actions.length > 0) {
									group.quick_actions.forEach(function(action) {
										var btn = document.createElement('div');
										btn.className = 'quick-action-item';

										var iconText = action.icon ? action.icon + ' ' : '';
										btn.textContent = iconText + action.title;

										btn.onclick = function() {
											if (action.url) {
												try {
													if (window.uni && window.uni.postMessage) {
														// alert("准备发送 postMessage: " + action.url);
														window.uni.postMessage({
															data: {
																action: 'openSchema',
																url: action.url
															}
														});
														// 发送成功
													} else {
														alert("window.uni 不存在或无 postMessage 方法");
														window.open(action.url, '_blank');
													}
												} catch (e) {
													alert("发送消息报错: " + e.message);
												}
											} else {
												alert("后台未配置跳转链接(url为空)");
											}
										};
										container.appendChild(btn);
									});
									container.style.display = 'flex';
								} else {
									container.style.display = 'none';
								}
							}
						}

						// --- 核心修改：如果首次加载成功，则开启拉取消息和轮询 ---
						if (isInitialLoad) {
							loadMessages();
							setInterval(loadMessages, 3000);
							setInterval(function() {
								loadGroupInfo(false);
							}, 5000);
						}

					})
					.catch(function(error) {
						console.error('获取群信息失败:', error);
						let errorMsg = 'groups.php 加载失败: ' + error.message;
						typeof showQQAlert === 'function' ? showQQAlert(errorMsg) : alert(errorMsg);
					});
			}

			// 拦截发送函数，防止被绕过
			const originalSendMessage = window.sendMessage;
			window.sendMessage = function() {
				if (isGroupBanned) {
					alert("该群当前处于全体禁言状态！");
					return;
				}
				const input = document.getElementById('messageInput');
				if (input && input.disabled) return;

				const btn = document.querySelector('.send-btn');
				if (btn && btn.disabled) return;

				if (typeof originalSendMessage === 'function') {
					originalSendMessage();
				}
			};


			// 统一的初始化入口
			function initApp() {
				if (window.appInitialized) return;
				window.appInitialized = true;
				loadGroupInfo(true);
			}
			window.onload = initApp;
			document.addEventListener('DOMContentLoaded', initApp);




			// ---- 多选与转发逻辑 ----
			function toggleSelectionMode() {
				document.body.classList.toggle('selection-mode');
				updateSelectedCount();
			}

			function exitSelectionMode() {
				document.body.classList.remove('selection-mode');
				document.querySelectorAll('.message.selected').forEach(el => el.classList.remove('selected'));
				updateSelectedCount();
			}

			function toggleMsgSelect(el) {
				if (document.body.classList.contains('selection-mode')) {
					el.classList.toggle('selected');
					updateSelectedCount();
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

					const historyCard = contentEl ? contentEl.querySelector('.message-history-card') : null;
					if (historyCard) {
						const payloadStr = historyCard.getAttribute('data-payload');
						if (payloadStr) {
							try {
								const payload = JSON.parse(decodeURIComponent(payloadStr));
								items.push({
									from: from,
									avatar: avatar,
									text: '[聊天记录: ' + (payload.title || '群聊的聊天记录') + ']',
									type: 'history',
									history_payload: payload
								});
								return;
							} catch (e) {
								console.error('解析聊天记录payload失败:', e);
							}
						}
					}

					// 初始化变量
					let type = 'text';
					let url = '';
					let text = '[复杂消息]';

					// 先检测媒体类型(优先级高)
					if (contentEl) {
						const imgEl = contentEl.querySelector('img.message-image');
						const videoEl = contentEl.querySelector('video.message-video');
						const audioEl = contentEl.querySelector('audio');
						const fileEl = contentEl.querySelector('.message-file');

						if (imgEl) {
							type = 'image';
							url = imgEl.src;
							text = '[图片]';
						} else if (videoEl) {
							type = 'video';
							url = videoEl.src;
							text = '[视频]';
						} else if (audioEl) {
							type = 'voice';
							url = audioEl.src;
							text = '[语音]';
						} else if (fileEl) {
							type = 'file';
							url = fileEl.getAttribute('href') || '';
							text = '[文件]';
						} else {
							// 如果不是媒体类型,才使用 innerText
							text = contentEl.innerText.substring(0, 50) || '[复杂消息]';
						}
					}

					items.push({
						from,
						avatar,
						text,
						type,
						url
					});
				});

				window.pendingForwardPayload = {
					title: "群聊的聊天记录",
					items: items
				};
				openGroupSelectorModal();
			};

			window.openGroupSelectorModal = function() {
				if (!document.getElementById('qqGroupSelectorModal')) {
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

				console.log('获取群聊列表...');
				fetch('api/chat/groups.php').then(r => r.json()).then(groups => {
					console.log('获取到的群聊列表:', groups);
					let html = '';
					if (Array.isArray(groups)) {
						groups.forEach(g => {
							const isCur = (g.id == groupId) ? 'is-current' : '';
							console.log('添加群聊选项:', g.id, g.name);
							html +=
								`<div class="qq-group-item ${isCur}" onclick="executeForwardToGroup('${g.id}')"><div class="qq-group-info">${g.name || '群聊'}</div></div>`;
						});
					} else {
						console.error('群聊列表格式错误:', groups);
						html = '群聊列表格式错误';
					}
					document.getElementById('qqGroupListContainer').innerHTML = html;
				}).catch(error => {
					console.error('获取群聊列表失败:', error);
					document.getElementById('qqGroupListContainer').innerHTML = '获取群聊列表失败';
				});
			};

			window.closeGroupSelectorModal = function() {
				const modal = document.getElementById('qqGroupSelectorModal');
				if (modal) modal.classList.remove('active');
			};

			window.executeForwardToGroup = function(targetGroupId) {
				if (!window.pendingForwardPayload) return;
				console.log('转发到群聊:', targetGroupId);
				console.log('转发内容:', window.pendingForwardPayload);
				fetch('api/chat/send_message.php', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json'
					},
					body: JSON.stringify({
						group_id: targetGroupId,
						user_id: userId,
						user_nickname: userData.nickname,
						user_avatar: userData.avatar,
						type: 'history',
						content: JSON.stringify(window.pendingForwardPayload)
					})
				}).then(r => r.json()).then(res => {
					console.log('转发结果:', res);
					if (res.success) {
						alert('转发成功!');
						closeGroupSelectorModal();
						exitSelectionMode();
						if (targetGroupId == groupId) {
							// 强制重新加载当前群聊的消息
							lastMessageCount = 0;
							loadMessages();
						} else {
							// 提示用户切换到目标群聊查看转发的消息
							alert('转发成功!请切换到群聊 "' + targetGroupId + '" 查看转发的消息');
						}
					} else alert('失败: ' + res.message);
				}).catch(error => {
					console.error('转发请求失败:', error);
					alert('转发请求失败,请检查网络连接');
				});
			};

			// 聊天记录层级栈
			window.historyModalStack = [];

			window.openQQHistoryModal = function(payloadStr) {
				// 多选模式下禁用
				if (document.body.classList.contains('selection-mode')) {
					console.log('多选模式下禁用聊天记录查看');
					return;
				}

				const payload = JSON.parse(decodeURIComponent(payloadStr));
				console.log('打开聊天记录模态框:', payload);

				// 将当前payload压入栈
				window.historyModalStack.push(payload);
				console.log('聊天记录栈:', window.historyModalStack);

				if (!document.getElementById('qqHistoryModalContainer')) {
					const modalHtml = `
                    <div class="qq-history-modal" id="qqHistoryModalContainer">
                        <div class="qq-header">
                            <button class="qq-close" onclick="closeQQHistoryModal()">返回</button>
                            <div class="qq-title" id="qqHistoryModalTitle">聊天记录</div>
                            <div style="width: 32px;"></div>
                        </div>
                        <div class="qq-body wx-body" id="qqHistoryModalBody"></div>
                    </div>
                `;
					document.body.insertAdjacentHTML('beforeend', modalHtml);
				}

				// 更新标题
				const titleElement = document.getElementById('qqHistoryModalTitle');
				if (titleElement) {
					titleElement.textContent = payload.title || '聊天记录';
				}

				let itemsHtml = (payload.items || []).map(it => {
					// 检查是否是嵌套的聊天记录
					if (it.history_payload) {
						// 为嵌套的聊天记录创建可点击的卡片
						const nestedPayloadStr = encodeURIComponent(JSON.stringify(it.history_payload));
						return `
                        <div class="wx-item">
                            <img src="${it.avatar || 'https://picsum.photos/id/1005/60/60'}" class="wx-avatar">
                            <div class="wx-content">
                                <div class="wx-name">${it.from}</div>
                                <div class="wx-text">
                                    <div class="message-history-card" data-payload="${nestedPayloadStr}" onclick="openQQHistoryModal(this.getAttribute('data-payload'))">
                                        <div class="message-history-title">${it.history_payload.title || '群聊的聊天记录'}</div>
                                        <div class="message-history-list">
                                            ${(it.history_payload.items || []).slice(0, 2).map(nestedIt => `<div class="message-history-item">${nestedIt.from}: ${nestedIt.text}</div>`).join('')}
                                            ${(it.history_payload.items || []).length > 2 ? '<div class="message-history-item">...</div>' : ''}
                                        </div>
                                        <div class="message-history-footer">查看${it.history_payload.items ? it.history_payload.items.length : 0}条转发消息</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
					} else {
						// 根据消息类型渲染实际内容
						let contentHtml = '';

						if (it.type === 'image' && it.url) {
							// 显示实际图片
							contentHtml =
								`<img src="${it.url}" onclick="openImagePreview('${it.url}')" style="max-width: 120px; max-height: 160px; border-radius: 8px; cursor: pointer; object-fit: cover; border: 0.5px solid rgba(0, 0, 0, 0.05); display: block; margin: 4px 0;">`;
						} else if (it.type === 'video' && it.url) {
							// 显示实际视频
							contentHtml = `<div class="video-wrapper" onclick="openVideoPreview(this.querySelector('video').src)" style="margin: 4px 0;">
								<video src="${it.url}" class="message-video" preload="metadata" playsinline></video>
								<div class="video-play-btn"></div>
							</div>`;
						} else if (it.type === 'voice' && it.url) {
							// 显示音频播放器
							contentHtml =
								`<audio src="${it.url}" controls style="max-width: 100%; height: 40px;"></audio>`;
						} else if (it.type === 'file' && it.url) {
							// 显示文件链接
							const fileName = it.url.split('/').pop() || '文件';
							contentHtml =
								`<a href="${it.url}" target="_blank" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; background: #F5F5F5; border-radius: 8px; text-decoration: none; color: #000; border: 0.5px solid rgba(0, 0, 0, 0.05);"><span style="font-size: 18px;">📄</span><span style="word-break: break-all;">${fileName}</span></a>`;
						} else {
							// 普通文本消息
							contentHtml = it.text || '';
						}

						return `
                        <div class="wx-item">
                            <img src="${it.avatar || 'https://picsum.photos/id/1005/60/60'}" class="wx-avatar">
                            <div class="wx-content">
                                <div class="wx-name">${it.from}</div>
                                <div class="wx-text">${contentHtml}</div>
                            </div>
                        </div>
                    `;
					}
				}).join('');

				console.log('生成的聊天记录HTML:', itemsHtml);
				const bodyElement = document.getElementById('qqHistoryModalBody');
				if (bodyElement) {
					bodyElement.innerHTML = itemsHtml;
				} else {
					console.error('聊天记录模态框 body 元素不存在');
				}

				const modalElement = document.getElementById('qqHistoryModalContainer');
				if (modalElement) {
					modalElement.classList.add('active');
				} else {
					console.error('聊天记录模态框元素不存在');
				}
			};

			// 关闭聊天记录模态框(支持层级返回)
			window.closeQQHistoryModal = function() {
				console.log('关闭聊天记录模态框,当前栈:', window.historyModalStack);

				// 弹出当前payload
				window.historyModalStack.pop();

				if (window.historyModalStack.length > 0) {
					// 如果栈不为空,显示上一层聊天记录
					const previousPayload = window.historyModalStack[window.historyModalStack.length - 1];
					console.log('返回到上一层聊天记录:', previousPayload);

					// 更新标题
					const titleElement = document.getElementById('qqHistoryModalTitle');
					if (titleElement) {
						titleElement.textContent = previousPayload.title || '聊天记录';
					}

					// 重新生成内容
					let itemsHtml = (previousPayload.items || []).map(it => {
						if (it.history_payload) {
							const nestedPayloadStr = encodeURIComponent(JSON.stringify(it.history_payload));
							return `
                            <div class="wx-item">
                                <img src="${it.avatar || 'https://picsum.photos/id/1005/60/60'}" class="wx-avatar">
                                <div class="wx-content">
                                    <div class="wx-name">${it.from}</div>
                                    <div class="wx-text">
                                        <div class="message-history-card" data-payload="${nestedPayloadStr}" onclick="openQQHistoryModal(this.getAttribute('data-payload'))">
                                            <div class="message-history-title">${it.history_payload.title || '群聊的聊天记录'}</div>
                                            <div class="message-history-list">
                                                ${(it.history_payload.items || []).slice(0, 2).map(nestedIt => `<div class="message-history-item">${nestedIt.from}: ${nestedIt.text}</div>`).join('')}
                                                ${(it.history_payload.items || []).length > 2 ? '<div class="message-history-item">...</div>' : ''}
                                            </div>
                                            <div class="message-history-footer">查看${it.history_payload.items ? it.history_payload.items.length : 0}条转发消息</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
						} else {
							// 根据消息类型渲染实际内容
							let contentHtml = '';

							if (it.type === 'image' && it.url) {
								contentHtml =
									`<img src="${it.url}" onclick="openImagePreview('${it.url}')" style="max-width: 120px; max-height: 160px; border-radius: 8px; cursor: pointer; object-fit: cover; border: 0.5px solid rgba(0, 0, 0, 0.05); display: block; margin: 4px 0;">`;
							} else if (it.type === 'video' && it.url) {
								contentHtml = `<div class="video-wrapper" onclick="openVideoPreview(this.querySelector('video').src)" style="margin: 4px 0;">
								<video src="${it.url}" class="message-video" preload="metadata" playsinline></video>
								<div class="video-play-btn"></div>
							</div>`;
							} else if (it.type === 'voice' && it.url) {
								contentHtml =
									`<audio src="${it.url}" controls style="max-width: 100%; height: 40px;"></audio>`;
							} else if (it.type === 'file' && it.url) {
								const fileName = it.url.split('/').pop() || '文件';
								contentHtml =
									`<a href="${it.url}" target="_blank" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; background: #F5F5F5; border-radius: 8px; text-decoration: none; color: #000; border: 0.5px solid rgba(0, 0, 0, 0.05);"><span style="font-size: 18px;">📄</span><span style="word-break: break-all;">${fileName}</span></a>`;
							} else {
								contentHtml = it.text || '';
							}

							return `
                            <div class="wx-item">
                                <img src="${it.avatar || 'https://picsum.photos/id/1005/60/60'}" class="wx-avatar">
                                <div class="wx-content">
                                    <div class="wx-name">${it.from}</div>
                                    <div class="wx-text">${contentHtml}</div>
                                </div>
                            </div>
                        `;
						}
					}).join('');

					const bodyElement = document.getElementById('qqHistoryModalBody');
					if (bodyElement) {
						bodyElement.innerHTML = itemsHtml;
					}
				} else {
					// 如果栈为空,关闭模态框
					const modalElement = document.getElementById('qqHistoryModalContainer');
					if (modalElement) {
						modalElement.classList.remove('active');
					}
				}
			};

			// 图片预览功能
			window.openImagePreview = function(imageUrl) {
				// 多选模式下禁用
				if (document.body.classList.contains('selection-mode')) {
					console.log('多选模式下禁用图片预览');
					return;
				}

				console.log('打开图片预览:', imageUrl);
				const modal = document.getElementById('imagePreviewModal');
				const image = document.getElementById('imagePreviewContent');
				if (modal && image) {
					image.src = imageUrl;
					modal.classList.add('active');
					// 禁止背景滚动
					document.body.style.overflow = 'hidden';
				}
			};

			window.closeImagePreview = function() {
				const modal = document.getElementById('imagePreviewModal');
				if (modal) {
					modal.classList.remove('active');
					// 恢复背景滚动
					document.body.style.overflow = '';
				}
			};

			// 点击模态框背景关闭预览
			document.getElementById('imagePreviewModal')?.addEventListener('click', function(e) {
				if (e.target === this) {
					closeImagePreview();
				}
			});


			// 简单的表情插入函数(基础支持)
			function insertEmoji(emoji) {
				var input = document.getElementById('messageInput');
				if (!input) return;
				var start = input.selectionStart || input.value.length;
				var end = input.selectionEnd || input.value.length;
				var value = input.value;
				input.value = value.substring(0, start) + emoji + value.substring(end);
				input.focus();
				// 将光标移动到表情后面
				var pos = start + emoji.length;
				input.selectionStart = input.selectionEnd = pos;
			}


			function toggleEmojiPanel() {
				var panel = document.getElementById('emojiPanel');
				if (!panel) return;
				if (panel.style.display === 'none' || panel.style.display === '') {
					panel.style.display = 'flex';
				} else {
					panel.style.display = 'none';
				}
			}


			function openGroupInfo() {
				var panel = document.getElementById('groupInfoPanel');
				if (!panel) return;
				panel.classList.add('active');
				// 利用已有的变量 groupId / chatTitle 填充信息
				var nameEl = document.getElementById('groupInfoName');
				var idEl = document.getElementById('groupInfoId');
				if (nameEl) {
					var titleEl = document.getElementById('chatTitle');
					nameEl.textContent = titleEl ? titleEl.textContent : '群聊';
				}
				if (idEl) {
					idEl.textContent = typeof groupId !== 'undefined' ? groupId : '';
				}
			}

			function closeGroupInfo() {
				var panel = document.getElementById('groupInfoPanel');
				if (!panel) return;
				panel.classList.remove('active');
			}


			function openGroupInfoPage() {
				var page = document.getElementById('groupInfoPage');
				if (!page) return;
				page.classList.add('active');
				var nameEl = document.getElementById('giGroupName');
				var idEl = document.getElementById('giGroupId');
				if (nameEl) {
					var titleEl = document.getElementById('chatTitle');
					nameEl.textContent = titleEl ? titleEl.textContent : '群聊';
				}
				if (idEl) {
					idEl.textContent = '群号:' + (typeof groupId !== 'undefined' ? groupId : '--');
				}
			}

			function closeGroupInfoPage() {
				var page = document.getElementById('groupInfoPage');
				if (!page) return;
				page.classList.remove('active');
			}

			function openCallPanel() {
				alert('这里可以接语音/视频通话面板(预留)');
			}


			function updateSelectedCount() {
				var count = document.querySelectorAll('.message.selected').length;
				var el = document.getElementById('selectedCount');
				if (el) el.textContent = count;
			}
		</script>

		<script>
			// ==== 修复手机端返回键关闭弹窗逻辑 ====
			(function() {
				if (window.openQQHistoryModal) {
					const _openQQHistoryModal = window.openQQHistoryModal;
					window.openQQHistoryModal = function(payloadStr) {
						_openQQHistoryModal(payloadStr);
						window.history.pushState({
							modal: 'qqHistory'
						}, '', '#qqHistory');
					};
				}

				if (window.closeQQHistoryModal) {
					const _closeQQHistoryModal = window.closeQQHistoryModal;
					window.closeQQHistoryModal = function(fromPopState) {
						_closeQQHistoryModal();
						if (fromPopState !== true && (!window.historyModalStack || window.historyModalStack.length ===
								0)) {
							window.history.back();
						}
					};
				}

				if (typeof openImagePreview !== 'undefined') {
					const _openImagePreview = window.openImagePreview || openImagePreview;
					window.openImagePreview = function(src) {
						_openImagePreview(src);
						window.history.pushState({
							modal: 'imagePreview'
						}, '', '#imagePreview');
					};
					const _closeImagePreview = window.closeImagePreview || closeImagePreview;
					window.closeImagePreview = function(fromPopState) {
						_closeImagePreview();
						if (fromPopState !== true) window.history.back();
					};
				}

				if (typeof openVideoPreview !== 'undefined') {
					const _openVideoPreview = window.openVideoPreview || openVideoPreview;
					window.openVideoPreview = function(src) {
						_openVideoPreview(src);
						window.history.pushState({
							modal: 'videoPreview'
						}, '', '#videoPreview');
					};
					const _closeVideoPreview = window.closeVideoPreview || closeVideoPreview;
					window.closeVideoPreview = function(fromPopState) {
						_closeVideoPreview();
						if (fromPopState !== true) window.history.back();
					};
				}

				if (window.openGroupSelectorModal) {
					const _openGroupSelectorModal = window.openGroupSelectorModal;
					window.openGroupSelectorModal = function() {
						_openGroupSelectorModal();
						window.history.pushState({
							modal: 'groupSelector'
						}, '', '#groupSelector');
					};
				}

				if (window.closeGroupSelectorModal) {
					const _closeGroupSelectorModal = window.closeGroupSelectorModal;
					window.closeGroupSelectorModal = function(fromPopState) {
						_closeGroupSelectorModal();
						if (fromPopState !== true) window.history.back();
					};
				}

				window.addEventListener('popstate', function(event) {
					const qqHistoryModal = document.getElementById('qqHistoryModalContainer');
					if (qqHistoryModal && qqHistoryModal.classList.contains('active')) {
						if (window.closeQQHistoryModal) window.closeQQHistoryModal(true);
						return;
					}

					const videoModal = document.getElementById('videoPreviewModal');
					if (videoModal && videoModal.classList.contains('active')) {
						if (window.closeVideoPreview) window.closeVideoPreview(true);
						return;
					}

					const imageModal = document.getElementById('imagePreviewModal');
					if (imageModal && imageModal.classList.contains('active')) {
						if (window.closeImagePreview) window.closeImagePreview(true);
						return;
					}

					const groupSelectorModal = document.getElementById('qqGroupSelectorModal');
					if (groupSelectorModal && groupSelectorModal.classList.contains('active')) {
						if (window.closeGroupSelectorModal) window.closeGroupSelectorModal(true);
						return;
					}
				});
			})();
		</script>


		<!-- QQ风格提示弹窗 -->
		<div class="qq-alert-modal" id="qqAlertModal">
			<div class="qq-alert-box">
				<div class="qq-alert-content" id="qqAlertMessage">无法获取当前群信息</div>
				<button class="qq-alert-btn" onclick="closeQQAlert()">确定</button>
			</div>
		</div>

		<script>
			function showQQAlert(message) {
				var modal = document.getElementById('qqAlertModal');
				var msgEl = document.getElementById('qqAlertMessage');
				if (msgEl && message) msgEl.textContent = message;
				if (modal) modal.classList.add('active');
			}

			function closeQQAlert() {
				var modal = document.getElementById('qqAlertModal');
				if (modal) modal.classList.remove('active');
			}
		</script>
	</body>

	<style>
		.m-player .player-wrapper {
			height: 100%;
			width: 100%;
			display: flex !important;
			position: relative;
			flex-direction: row !important;
			justify-content: center !important;
			align-content: center !important;
			flex-wrap: nowrap !important;
			align-items: center !important;
		}

		#mplayer-media-wrapper .video-wrapper {
			max-width: 100%;
		}
	</style>
</html>