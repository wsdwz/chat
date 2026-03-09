<!DOCTYPE html>

<html lang="zh-CN">
	<head>
		<meta charset="utf-8" />
		<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
		<title>聊天室</title>
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
				background-color: #F8F8F8;
				padding: 10px 16px;
				display: flex;
				align-items: center;
				justify-content: space-between;
				position: sticky;
				top: 0;
				z-index: 100;
				border-bottom: 0.5px solid #E5E5E5;
				height: 50px;
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
				gap: 16px;
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
				width: 40px;
				height: 40px;
				border-radius: 50%;
				/* QQ经典圆头像 */
				object-fit: cover;
				flex-shrink: 0;
				box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
			}

			.message.other .message-avatar {
				margin-right: 12px;
			}

			.message.own .message-avatar {
				margin-left: 12px;
			}

			.message-content-wrapper {
				max-width: 72%;
				display: flex;
				flex-direction: column;
			}

			.message.own .message-content-wrapper {
				align-items: flex-end;
			}

			.message.other .message-content-wrapper {
				align-items: flex-start;
			}

			/* 昵称 */
			.message-sender {
				font-size: 12px;
				color: var(--text-secondary);
				margin-bottom: 4px;
				margin-left: 4px;
				margin-right: 4px;
			}

			/* 气泡本体 */
			.message-content {
				padding: 10px 14px;
				word-wrap: break-word;
				line-height: 1.5;
				font-size: 15px;
				position: relative;
				min-height: 40px;
				display: flex;
				align-items: center;
			}

			/* 别人发的消息气泡 (带小尾巴) */
			.message.other .message-content {
				background: var(--other-message-bg);
				color: var(--text-color);
				border-radius: 12px;
				border-top-left-radius: 4px;
				box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
			}

			/* 自己发的消息气泡 (带小尾巴) */
			.message.own .message-content {
				background: var(--own-message-bg);
				color: #FFFFFF;
				border-radius: 12px;
				border-top-right-radius: 4px;
				box-shadow: 0 1px 2px rgba(18, 183, 245, 0.15);
			}

			/* 消息时间 (隐藏，QQ一般不在气泡下显示时间，只在消息间隙显示，这里保留原功能并弱化) */
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
				border-radius: 12px !important;
				width: 240px !important;
				box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06) !important;
				padding: 0 !important;
				display: flex;
				flex-direction: column;
				overflow: hidden;
				text-decoration: none;
				border: 0.5px solid rgba(0, 0, 0, 0.05);
			}

			.message.own .message-content:has(.message-card),
			.message.other .message-content:has(.message-card),
			.message.own .message-content:has(.message-history-card),
			.message.other .message-content:has(.message-history-card),
			.message.own .message-content:has(.message-image),
			.message.other .message-content:has(.message-image),
			.message.own .message-content:has(.message-video),
			.message.other .message-content:has(.message-video) {
				background: transparent !important;
				padding: 0 !important;
				box-shadow: none !important;
				border-radius: 0 !important;
			}

			/* ------------------ QQ风格卡片 ------------------ */
			.message-card-body {
				padding: 12px;
				display: flex;
				gap: 10px;
				text-align: left;
				align-items: flex-start;
			}

			.message-card-info {
				flex: 1;
				display: flex;
				flex-direction: column;
				min-width: 0;
			}

			.message-card-title {
				font-size: 15px;
				font-weight: 500;
				color: #000;
				line-height: 1.4;
				display: -webkit-box;
				-webkit-line-clamp: 2;
				-webkit-box-orient: vertical;
				overflow: hidden;
			}

			.message-card-desc {
				font-size: 12px;
				color: #878B99;
				margin-top: 4px;
				display: -webkit-box;
				-webkit-line-clamp: 2;
				-webkit-box-orient: vertical;
				overflow: hidden;
			}

			.message-card-thumb {
				width: 48px;
				height: 48px;
				border-radius: 8px;
				object-fit: cover;
				flex-shrink: 0;
				background-color: #F3F4F6;
			}

			.message-card-footer {
				padding: 6px 12px;
				font-size: 11px;
				color: #878B99;
				border-top: 0.5px solid #F0F0F0;
				text-align: left;
				display: flex;
				align-items: center;
				background: #FAFAFA;
			}

			/* ------------------ QQ风格 合并转发卡片 ------------------ */
			.message-history-card {
				background: #FFFFFF !important;
				border-radius: 12px !important;
				width: 250px !important;
				padding: 12px 14px !important;
				text-align: left;
				box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06) !important;
				border: 0.5px solid rgba(0, 0, 0, 0.05);
				cursor: pointer;
			}

			.message-history-title {
				font-size: 14px;
				font-weight: 500;
				color: #000000;
				margin-bottom: 8px;
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
			}

			.message-history-list {
				display: flex;
				flex-direction: column;
				gap: 4px;
				margin-bottom: 8px;
			}

			.message-history-item {
				font-size: 12px;
				color: #878B99;
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
				line-height: 1.4;
			}

			.message-history-footer {
				font-size: 11px;
				color: #B0B3BF;
				padding-top: 8px;
				border-top: 0.5px solid #F0F0F0;
			}

			/* ------------------ 嵌套预览模态框 ------------------ */
			.qq-history-modal {
				display: none;
				position: fixed;
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
				background: #EBEDF0;
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
				height: 50px;
				min-height: 50px;
				background: #F8F8F8;
				display: flex;
				align-items: center;
				justify-content: space-between;
				padding: env(safe-area-inset-top) 16px 0;
				border-bottom: 0.5px solid #EBEBEB;
			}

			.qq-close {
				font-size: 15px;
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
				font-size: 17px;
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
				padding: 16px;
				border-bottom: 0.5px solid rgba(0, 0, 0, 0.04);
			}

			.wx-avatar {
				width: 40px;
				height: 40px;
				border-radius: 50%;
				margin-right: 12px;
				object-fit: cover;
			}

			.wx-content {
				flex: 1;
				min-width: 0;
			}

			.wx-name {
				font-size: 13px;
				color: #878B99;
				margin-bottom: 4px;
			}

			.wx-text {
				font-size: 15px;
				color: #000;
				line-height: 1.5;
				word-wrap: break-word;
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
				border-radius: 12px;
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
				max-width: 140px;
				max-height: 200px;
				border-radius: 8px;
				cursor: pointer;
				object-fit: cover;
				border: 0.5px solid rgba(0, 0, 0, 0.05);
			}

			.message-video {
				max-width: 140px;
				max-height: 200px;
				border-radius: 8px;
				cursor: pointer;
				object-fit: cover;
				background: #000;
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
				width: 220px;
			}

			.message.own .message-file {
				background: #FFFFFF;
				color: #000;
			}

			.message-content:has(.message-image),
			.message-content:has(.message-video) {
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
				background: #F8F8F8;
				position: sticky;
				bottom: 0;
				z-index: 100;
				display: flex;
				flex-direction: column;
				border-top: 0.5px solid #EBEBEB;
				padding-bottom: env(safe-area-inset-bottom);
			}

			.quick-actions {
				display: flex;
				gap: 16px;
				padding: 10px 16px 6px;
				overflow-x: auto;
				scrollbar-width: none;
				align-items: center;
			}

			.quick-actions::-webkit-scrollbar {
				display: none;
			}

			.quick-action-item {
				display: flex;
				align-items: center;
				justify-content: center;
				padding: 4px 12px;
				border-radius: 12px;
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
				gap: 10px;
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
				padding: 10px 14px;
				border: none;
				border-radius: 4px;
				background-color: #FFFFFF;
				font-size: 15px;
				outline: none;
				resize: none;
				max-height: 100px;
				overflow-y: auto;
				line-height: 1.4;
				box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
			}

			#messageInput::placeholder {
				color: #B0B3BF;
			}

			.send-btn {
				background: var(--primary-color);
				color: #fff;
				border: none;
				border-radius: 4px;
				padding: 0 16px;
				height: 36px;
				font-size: 14px;
				font-weight: 500;
				white-space: nowrap;
				cursor: pointer;
				display: flex;
				align-items: center;
				justify-content: center;
			}

			.send-btn:active {
				background: var(--primary-dark);
			}

			.send-btn:disabled {
				background: #A0DFFB;
				cursor: not-allowed;
			}

			/* 多选模式样式 */
			body.selection-mode .input-area {
				display: none !important;
			}

			body.selection-mode #selectionBottomBar {
				display: none;
				position: fixed;
				bottom: 0;
				left: 0;
				width: 100%;
				height: 52px;
				background: #FFFFFF;
				border-top: 0.5px solid #EBEBEB;
				z-index: 1000;
				display: flex;
				justify-content: space-around;
				align-items: center;
				padding-bottom: env(safe-area-inset-bottom);
			}

			.sel-btn {
				flex: 1;
				display: flex;
				flex-direction: column;
				align-items: center;
				justify-content: center;
				gap: 2px;
				background: none;
				border: none;
				color: #333;
				font-size: 11px;
				cursor: pointer;
			}

			.sel-icon {
				font-size: 20px;
			}

			.message {
				transition: transform 0.25s ease;
			}

			.msg-checkbox {
				position: absolute;
				/* 以头像为基准：头像宽 40px，左右外边距 12px */
				left: -4px;
				/* 根据实际效果微调，例如 -6px/-8px */
				top: 50%;
				transform: translateY(-50%);
				width: 16px;
				height: 16px;
				border-radius: 50%;
				border: 1px solid #D5D5D5;
				background: #FFFFFF;
				display: none;
				align-items: center;
				justify-content: center;
				z-index: 10;
			}

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

			/* 移动端预览通常点背景退出，隐藏关闭按钮 */

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
				font-size: 15px;
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
				<div style="text-align: center; color: #999; font-size: 12px; padding: 20px;">加载中...</div>
			</div>
			<div class="input-area">
				<div class="quick-actions" id="quickActionsContainer">
					<div class="quick-action-item" onclick="toggleEmojiPanel()">😀 表情</div>
				</div>
				<div class="input-row" id="textInputRow">
					<textarea id="messageInput" placeholder="发消息..."></textarea>
					<button class="send-btn" onclick="sendMessage()">发送</button>
				</div>
				<div class="emoji-panel" id="emojiPanel" style="display:none;"><span class="emoji-item"
						onclick="insertEmoji('😀')">😀</span><span class="emoji-item"
						onclick="insertEmoji('😁')">😁</span><span class="emoji-item"
						onclick="insertEmoji('😂')">😂</span><span class="emoji-item"
						onclick="insertEmoji('🤣')">🤣</span><span class="emoji-item"
						onclick="insertEmoji('😊')">😊</span><span class="emoji-item"
						onclick="insertEmoji('😍')">😍</span><span class="emoji-item"
						onclick="insertEmoji('😘')">😘</span><span class="emoji-item"
						onclick="insertEmoji('😜')">😜</span><span class="emoji-item"
						onclick="insertEmoji('😎')">😎</span><span class="emoji-item"
						onclick="insertEmoji('😢')">😢</span><span class="emoji-item"
						onclick="insertEmoji('😭')">😭</span><span class="emoji-item"
						onclick="insertEmoji('😡')">😡</span><span class="emoji-item"
						onclick="insertEmoji('👍')">👍</span><span class="emoji-item"
						onclick="insertEmoji('👎')">👎</span><span class="emoji-item"
						onclick="insertEmoji('👌')">👌</span><span class="emoji-item"
						onclick="insertEmoji('🙏')">🙏</span><span class="emoji-item"
						onclick="insertEmoji('🎉')">🎉</span><span class="emoji-item"
						onclick="insertEmoji('❤️')">❤️</span><span class="emoji-item"
						onclick="insertEmoji('💔')">💔</span><span class="emoji-item"
						onclick="insertEmoji('⭐')">⭐</span></div>
			</div>
		</div>
		<!-- 底部多选操作栏 -->
		<div id="selectionBottomBar"><button class="sel-btn" onclick="alert('单条转发功能待接入');">
				<div class="sel-icon">🔁</div><span>转发</span>
			</button><button class="sel-btn" onclick="alert('收藏功能待接入');">
				<div class="sel-icon">⭐</div><span>收藏</span>
			</button><button class="sel-btn" onclick="forwardSelectedAsCard()">
				<div class="sel-icon">📑</div><span>合并转发</span>
			</button><button class="sel-btn" onclick="alert('删除功能待接入');">
				<div class="sel-icon">🗑️</div><span>删除</span>
			</button><button class="sel-btn" onclick="alert('更多功能待接入');">
				<div class="sel-icon">⋯</div><span>更多</span>
			</button></div>
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

			// 预加载提示音，动态使用当前域名
			const newMessageAudio = new Audio();
			newMessageAudio.src = window.location.origin + '/mp3/xm3143.mp3';

			let lastMessageCount = 0;
			let isFirstLoad = true;
			let currentGroupId = groupId;

			function sendMessage() {
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

						// 重新渲染的条件：
						// 1. 消息数量增加
						// 2. 群聊ID改变
						// 3. 首次加载
						if (messages.length > lastMessageCount || currentGroupId !== groupId || isFirstLoad) {
							console.log('重新渲染消息，原因:',
								messages.length > lastMessageCount ? '消息数量增加' :
								currentGroupId !== groupId ? '群聊ID改变' : '首次加载');
							area.innerHTML = '';
							messages.forEach(msg => addMessageToDOM(msg, area));

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

			let longPressTimer;

			function handleLongPress(element) {
				toggleSelectionMode();
				toggleMsgSelect(element);
			}

			function addMessageToDOM(message, area) {
				const isOwn = message.user_id === userId;
				let contentHtml = message.content || '';

				// 如果内容里包含写死的 lvba3 域名，自动替换为当前域名
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
							if (payload.items && payload.items.length) {
								itemsHtml = payload.items.slice(0, 4).map(it => {
									if (it.type === 'image')
									return `<div class=\"message-history-item\">${it.from}: [图片]</div>`;
									if (it.type === 'video')
									return `<div class=\"message-history-item\">${it.from}: [视频]</div>`;
									if (it.type === 'voice')
									return `<div class=\"message-history-item\">${it.from}: [语音]</div>`;
									if (it.type === 'file')
									return `<div class=\"message-history-item\">${it.from}: [文件]</div>`;
									if (it.type === 'history')
									return `<div class=\"message-history-item\">${it.from}: [聊天记录]</div>`;
									return `<div class=\"message-history-item\">${it.from}: ${it.text}</div>`;
								}).join('');
							}
							const encodedPayload = encodeURIComponent(JSON.stringify(payload));
							// 安全处理，避免onclick事件中的语法错误
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
					div.innerHTML = `<span>${contentHtml}</span>`;
					area.appendChild(div);
					return;
				}

				// 补充遗漏的语音、文件和视频、图片处理
				if (message.type === 'video') {
					contentHtml =
						`<video src="${contentHtml}" class="message-video" onclick="enterVideoFullscreen(this)" preload="metadata"></video>`;
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
				// 注入复选框用于多选合并转发
				const checkboxHtml = `<div class="msg-checkbox" onclick="toggleMsgSelect(this.parentElement)"></div>`;

				// 格式化消息时间
				const timestamp = message.timestamp || new Date().toISOString();
				const date = new Date(timestamp);
				const timeStr = date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2,
				'0');

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

				// 添加长按事件监听
				div.addEventListener('mousedown', function(e) {
					longPressTimer = setTimeout(() => {
						handleLongPress(div);
					}, 500);
				});

				div.addEventListener('mouseup', function() {
					clearTimeout(longPressTimer);
				});

				div.addEventListener('mouseleave', function() {
					clearTimeout(longPressTimer);
				});

				// 触摸设备支持
				div.addEventListener('touchstart', function(e) {
					longPressTimer = setTimeout(() => {
						handleLongPress(div);
					}, 500);
				});

				div.addEventListener('touchend', function() {
					clearTimeout(longPressTimer);
				});

				div.addEventListener('touchcancel', function() {
					clearTimeout(longPressTimer);
				});

				area.appendChild(div);
			}

			// 视频全屏API调用
			window.enterVideoFullscreen = function(videoEl) {
				if (videoEl.requestFullscreen) videoEl.requestFullscreen();
				else if (videoEl.webkitRequestFullscreen) videoEl.webkitRequestFullscreen();
				else if (videoEl.webkitEnterFullscreen) videoEl.webkitEnterFullscreen();
				videoEl.play();
			};

			function scrollToBottom(force = false) {
				const area = document.getElementById('messagesArea');
				if (area) area.scrollTop = area.scrollHeight;
			}

			setInterval(loadMessages, 3000);
			window.onload = loadMessages;

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

					let text = contentEl ? contentEl.innerText.substring(0, 50) : '[复杂消息]';
					let type = 'text';
					let url = '';

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
				fetch('api/admin/groups.php').then(r => r.json()).then(groups => {
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
						alert('转发成功！');
						closeGroupSelectorModal();
						exitSelectionMode();
						if (targetGroupId == groupId) {
							// 强制重新加载当前群聊的消息
							lastMessageCount = 0;
							loadMessages();
						} else {
							// 提示用户切换到目标群聊查看转发的消息
							alert('转发成功！请切换到群聊 "' + targetGroupId + '" 查看转发的消息');
						}
					} else alert('失败: ' + res.message);
				}).catch(error => {
					console.error('转发请求失败:', error);
					alert('转发请求失败，请检查网络连接');
				});
			};

			// 聊天记录层级栈
			window.historyModalStack = [];

			window.openQQHistoryModal = function(payloadStr) {
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
						// 普通消息
						return `
                        <div class="wx-item">
                            <img src="${it.avatar || 'https://picsum.photos/id/1005/60/60'}" class="wx-avatar">
                            <div class="wx-content">
                                <div class="wx-name">${it.from}</div>
                                <div class="wx-text">${it.text}</div>
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

			// 关闭聊天记录模态框（支持层级返回）
			window.closeQQHistoryModal = function() {
				console.log('关闭聊天记录模态框，当前栈:', window.historyModalStack);

				// 弹出当前payload
				window.historyModalStack.pop();

				if (window.historyModalStack.length > 0) {
					// 如果栈不为空，显示上一层聊天记录
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
							return `
                            <div class="wx-item">
                                <img src="${it.avatar || 'https://picsum.photos/id/1005/60/60'}" class="wx-avatar">
                                <div class="wx-content">
                                    <div class="wx-name">${it.from}</div>
                                    <div class="wx-text">${it.text}</div>
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
					// 如果栈为空，关闭模态框
					const modalElement = document.getElementById('qqHistoryModalContainer');
					if (modalElement) {
						modalElement.classList.remove('active');
					}
				}
			};

			// 图片预览功能
			window.openImagePreview = function(imageUrl) {
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


			// 简单的表情插入函数（基础支持）
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
					idEl.textContent = '群号：' + (typeof groupId !== 'undefined' ? groupId : '--');
				}
			}

			function closeGroupInfoPage() {
				var page = document.getElementById('groupInfoPage');
				if (!page) return;
				page.classList.remove('active');
			}

			function openCallPanel() {
				alert('这里可以接语音/视频通话面板（预留）');
			}


			function updateSelectedCount() {
				var count = document.querySelectorAll('.message.selected').length;
				var el = document.getElementById('selectedCount');
				if (el) el.textContent = count;
			}
		</script>
	</body>
</html>