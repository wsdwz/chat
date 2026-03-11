
<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit;
}
?>


<!DOCTYPE html>
<html lang="zh-CN">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Chat</title>
		<style>

/* ================= 卡片消息美化 (Card Message) ================= */
.admin-msg-card {
    background: #FFFFFF;
    border: 1px solid #EAEAEA;
    border-radius: 8px;
    padding: 12px 14px;
    min-width: 240px;
    max-width: 280px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    display: flex;
    flex-direction: column;
    text-align: left;
    cursor: pointer;
}
.admin-msg-card:hover {
    background: #FAFAFA;
}
.admin-msg-card-body {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 10px;
    border-bottom: 1px solid #F0F0F0;
    padding-bottom: 10px;
}
.admin-msg-card-info {
    flex: 1;
    min-width: 0;
}
.admin-msg-card-title {
    font-size: 15px;
    font-weight: 500;
    color: #1A1A1A;
    margin-bottom: 6px;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.admin-msg-card-desc {
    font-size: 12px;
    color: #8C8C8C;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.admin-msg-card-thumb {
    width: 48px;
    height: 48px;
    border-radius: 6px;
    object-fit: cover;
    flex-shrink: 0;
    border: 1px solid #F0F0F0;
}
.admin-msg-card-footer {
    font-size: 11px;
    color: #999;
    display: flex;
    align-items: center;
    gap: 4px;
}
.admin-msg-card-footer::before {
    content: "🔗";
    font-size: 10px;
}

			/* 使用系统默认字体，确保在国内正常显示 */
			/* 如需使用PingFang SC，可考虑使用国内CDN或本地字体 */
			* {
				margin: 0;
				padding: 0;
				box-sizing: border-box;
				outline: none;
			}

			body {
				font-family: -apple-system, BlinkMacSystemFont, 'PingFang SC', 'Helvetica Neue', Arial, sans-serif;
				background-color: #F6F7F9;
				color: #1A1A1A;
				font-size: 14px;
				height: 100vh;
				overflow: hidden;
			}

			/* App Layout */
			.app-container {
				display: flex;
				height: 100vh;
			}

			.sidebar {
				width: 200px !important;
				background-color: #FBFBFB !important;
				border-right: 1px solid #EBEBEB !important;
				display: flex;
				flex-direction: column;
				z-index: 100;
			}

			.sidebar-header {
				padding: 24px 20px !important;
				border-bottom: none !important;
				display: flex;
				align-items: center;
				gap: 12px;
			}

			.sidebar-header::before {
				content: '';
				display: block;
				width: 24px;
				height: 24px;
				background-color: #FA5151;
				border-radius: 50%;
				flex-shrink: 0;
				background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='white'%3E%3Cpath d='M12 2C6.48 2 2 6.03 2 11c0 2.84 1.48 5.37 3.82 7.03-.32 1.34-1.07 2.78-1.12 2.92-.06.16.03.34.19.4.05.02.1.02.15.02.11 0 .22-.06.28-.15 0 0 2.22-2.91 5.38-3.03.43.05.86.08 1.3.08 5.52 0 10-4.03 10-9S17.52 2 12 2z'/%3E%3C/svg%3E");
				background-size: 16px;
				background-position: center;
				background-repeat: no-repeat;
			}

			.sidebar-title,
			.sidebar-header h2 {
				font-size: 16px !important;
				font-weight: 600 !important;
				color: #1A1A1A !important;
				margin: 0 !important;
			}

			.sidebar-menu {
				padding: 10px 12px !important;
				flex: 1;
			}

			.menu-item {
				display: flex !important;
				align-items: center !important;
				padding: 12px 14px !important;
				margin-bottom: 2px !important;
				border-radius: 6px !important;
				color: #595959 !important;
				text-decoration: none !important;
				font-size: 14px !important;
				border: none !important;
				background: transparent !important;
				transform: none !important;
				position: relative;
			}

			.menu-item::before,
			.menu-item::after {
				display: none !important;
			}

			.menu-item:hover {
				background-color: #F2F2F2 !important;
			}

			.menu-item.active {
				background-color: #E7F8EE !important;
				color: #07C160 !important;
				font-weight: 500 !important;
			}

			.menu-item.active::after {
				display: none !important;
			}

			.menu-item.active .menu-item-icon,
			.menu-item.active .menu-item-text {
				color: #07C160 !important;
			}

			.menu-item-icon {
				font-size: 16px !important;
				margin-right: 10px !important;
			}

			.sidebar-footer {
				padding: 16px 20px;
				border-top: 1px solid #EBEBEB;
				display: flex;
				flex-direction: column;
				gap: 16px;
			}

			.sidebar-footer-links {
				display: flex;
				gap: 24px;
				color: #595959;
				font-size: 13px;
			}

			.sidebar-footer-links span {
				display: flex;
				align-items: center;
				gap: 6px;
				cursor: pointer;
			}

			.sidebar-footer-user {
				display: flex;
				justify-content: space-between;
				align-items: center;
			}

			.sidebar-avatar {
				width: 24px;
				height: 24px;
				border-radius: 50%;
				background: #333;
				display: flex;
				align-items: center;
				justify-content: center;
				color: white;
				font-size: 12px;
			}

			/* Main Content */
			.main-panel {
				flex: 1;
				display: flex;
				flex-direction: column;
				background-color: #F6F7F9 !important;
				overflow: hidden;
			}

			.top-nav {
				background-color: #F6F7F9 !important;
				padding: 24px 32px 16px !important;
				height: auto !important;
				border-bottom: none !important;
				display: flex !important;
				align-items: center !important;
				justify-content: flex-start !important;
			}

			.nav-left {
				display: flex;
				align-items: center;
			}

			.nav-left::before {
				content: '🤖';
				font-size: 24px;
				margin-right: 12px;
			}

			.nav-title {
				font-size: 20px !important;
				font-weight: 600 !important;
				color: #1A1A1A !important;
				margin: 0 !important;
			}

			.nav-subtitle,
			.nav-actions {
				display: none !important;
			}

			.content-wrapper {
				flex: 1;
				padding: 0 32px 32px !important;
				overflow-y: auto;
			}

			/* Basic Card overrides */
			.card {
				background-color: #FFFFFF !important;
				border-radius: 4px !important;
				padding: 24px !important;
				margin-bottom: 16px !important;
				box-shadow: none !important;
				transform: none !important;
				position: static !important;
			}

			.card::before,
			.card::after {
				display: none !important;
			}

			.card:hover {
				box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04) !important;
			}

			.card-header {
				padding-bottom: 16px !important;
				border-bottom: 1px solid #F0F0F0 !important;
				display: flex;
				justify-content: space-between;
				align-items: center;
			}

			.card-title {
				font-size: 16px !important;
				font-weight: 600 !important;
				color: #1A1A1A !important;
				margin: 0 !important;
			}

			/* Homepage Elements */
			.alert-bar {
				background-color: white;
				border-radius: 4px;
				padding: 16px 24px;
				margin-bottom: 24px;
				display: flex;
				justify-content: space-between;
				align-items: center;
				border: 1px solid #EBEBEB;
			}

			.alert-bar .text {
				font-size: 13px;
				color: #595959;
				display: flex;
				align-items: center;
				gap: 8px;
			}

			.alert-bar .text::before {
				content: '🔊';
				font-size: 16px;
			}

			.text-brand {
				color: #07C160;
				cursor: pointer;
				font-size: 13px;
				font-weight: 500;
			}

			.flex-between {
				display: flex;
				align-items: center;
				justify-content: space-between;
			}

			.grid-2 {
				display: grid;
				grid-template-columns: repeat(2, 1fr);
				gap: 20px;
				margin-bottom: 24px;
			}

			.grid-3 {
				display: grid;
				grid-template-columns: repeat(3, 1fr);
				gap: 20px;
				margin-bottom: 24px;
			}

			.dashed-card {
				background: white;
				border: 1px dashed #E0E0E0;
				border-radius: 4px;
				padding: 32px 24px;
				display: flex;
				align-items: flex-start;
				gap: 20px;
				cursor: pointer;
				transition: 0.2s;
			}

			.dashed-card:hover {
				border-color: #07C160;
			}

			.dashed-icon {
				width: 56px;
				height: 56px;
				border-radius: 12px;
				background: #595959;
				display: flex;
				align-items: center;
				justify-content: center;
				font-size: 24px;
				color: white;
				flex-shrink: 0;
			}

			.dashed-content h4 {
				font-size: 16px;
				font-weight: 600;
				color: #1A1A1A;
				margin-bottom: 8px;
			}

			.dashed-content p {
				font-size: 13px;
				color: #8C8C8C;
				line-height: 1.6;
			}

			.solid-card {
				background: white;
				border-radius: 4px;
				padding: 24px;
				border: 1px solid #EBEBEB;
				display: flex;
				flex-direction: column;
			}

			.solid-header {
				display: flex;
				justify-content: space-between;
				align-items: center;
				margin-bottom: 24px;
			}

			.solid-title {
				font-size: 16px;
				font-weight: 500;
				color: #1A1A1A;
			}

			.solid-body {
				flex: 1;
				font-size: 13px;
				color: #595959;
				line-height: 1.6;
			}

			.solid-footer {
				margin-top: 24px;
			}

			.status-pill {
				display: inline-flex;
				align-items: center;
				gap: 6px;
				padding: 4px 12px;
				border: 1px solid #EBEBEB;
				border-radius: 20px;
				font-size: 12px;
				color: #8C8C8C;
				background: #FAFAFA;
			}

			.status-pill::before {
				content: '';
				width: 6px;
				height: 6px;
				border-radius: 50%;
				background: #D9D9D9;
			}

			.status-pill.active::before {
				background: #07C160;
			}

			.app-panel {
				background: white;
				border-radius: 4px;
				padding: 24px;
				border: 1px solid #EBEBEB;
			}

			.app-title {
				font-size: 16px;
				font-weight: 500;
				color: #1A1A1A;
				margin-bottom: 24px;
			}

			.app-grid {
				display: grid;
				grid-template-columns: repeat(6, 1fr);
				gap: 16px;
			}

			.app-item {
				border: 1px dashed #E0E0E0;
				border-radius: 4px;
				padding: 32px 16px;
				display: flex;
				flex-direction: column;
				align-items: center;
				gap: 16px;
				cursor: pointer;
				transition: 0.2s;
			}

			.app-item:hover {
				border-color: #07C160;
			}

			.app-name {
				font-size: 13px;
				color: #1A1A1A;
			}

			.floating-tools {
				position: fixed;
				right: 24px;
				bottom: 48px;
				display: flex;
				flex-direction: column;
				gap: 12px;
				z-index: 1000;
			}

			.float-btn {
				width: 48px;
				height: 48px;
				border-radius: 24px;
				background: white;
				box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
				display: flex;
				flex-direction: column;
				align-items: center;
				justify-content: center;
				cursor: pointer;
				border: 1px solid #F0F0F0;
			}

			.float-btn.green {
				color: #07C160;
			}

			.float-btn.blue {
				color: #1677FF;
			}

			.float-icon {
				font-size: 18px;
				margin-bottom: 2px;
			}

			.float-text {
				font-size: 10px;
			}

			/* Group Info Page / Operations */
			.group-info-grid {
				display: grid;
				grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
				gap: 16px;
			}

			.group-info-card {
				background: #FFFFFF;
				border: 1px solid #EBEBEB;
				border-radius: 4px;
				padding: 20px;
				cursor: pointer;
				transition: box-shadow 0.15s, border-color 0.15s;
			}

			.group-info-card:hover {
				box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
				border-color: #D9D9D9;
			}

			.group-info-header {
				display: flex;
				align-items: center;
				gap: 12px;
				margin-bottom: 16px;
			}

			.group-info-avatar {
				width: 56px;
				height: 56px;
				border-radius: 8px;
				object-fit: cover;
				border: 1px solid #F0F0F0;
				flex-shrink: 0;
			}

			.group-info-name {
				font-size: 15px;
				font-weight: 600;
				color: #1A1A1A;
				margin-bottom: 4px;
			}

			.group-info-desc {
				font-size: 12px;
				color: #999;
				text-overflow: ellipsis;
				white-space: nowrap;
				overflow: hidden;
			}

			.group-info-stats {
				display: grid;
				grid-template-columns: 1fr 1fr 1fr 1fr 1fr;
				gap: 8px;
				margin-bottom: 16px;
			}

			.stat-item {
				text-align: center;
			}

			.stat-value {
				font-size: 18px;
				font-weight: 600;
				color: #1A1A1A;
			}

			.stat-label {
				font-size: 11px;
				color: #999;
				margin-top: 2px;
			}

			.group-info-footer {
				display: flex;
				gap: 8px;
				justify-content: flex-end;
				border-top: 1px solid #F5F5F5;
				padding-top: 12px;
			}

			/* Stats Page */
			.stats-header {
				margin-bottom: 24px;
			}

			.stats-title {
				font-size: 20px;
				font-weight: 600;
				color: #1A1A1A;
				margin-bottom: 4px;
			}

			.stats-sub {
				font-size: 13px;
				color: #999;
			}

			.stats-grid {
				display: grid;
				grid-template-columns: repeat(4, 1fr);
				gap: 16px;
				margin-bottom: 24px;
			}

			.stat-card {
				background: white;
				border-radius: 4px;
				padding: 24px;
				border: 1px solid #F0F0F0;
			}

			.stat-card-label {
				font-size: 13px;
				color: #999;
				margin-bottom: 8px;
			}

			.stat-card-value {
				font-size: 32px;
				font-weight: 600;
				color: #1A1A1A;
			}

			.stat-card-value.green {
				color: #07C160;
			}

			.stat-card-value.blue {
				color: #1677FF;
			}

			.stat-card-value.orange {
				color: #FAAD14;
			}

			.stat-card-value.cyan {
				color: #13C2C2;
			}

			.stats-table {
				background: white;
				border-radius: 4px;
				border: 1px solid #F0F0F0;
				overflow: hidden;
			}

			.stats-table table {
				width: 100%;
				border-collapse: collapse;
			}

			.stats-table th {
				padding: 14px 20px;
				text-align: left;
				font-size: 12px;
				font-weight: 500;
				color: #999;
				background: #FAFAFA;
				border-bottom: 1px solid #F0F0F0;
			}

			.stats-table td {
				padding: 16px 20px;
				font-size: 14px;
				color: #1A1A1A;
				border-bottom: 1px solid #F5F5F5;
				vertical-align: middle;
			}

			.stats-table tr:hover td {
				background: #FAFAFA;
			}

			/* Chat Configuration Page */
			.chat-layout {
				display: flex;
				gap: 0;
				height: calc(100vh - 128px);
				border: 1px solid #EBEBEB;
				border-radius: 4px;
				overflow: hidden;
			}

			.chat-sidebar {
				width: 260px;
				background: #FFFFFF;
				border-right: 1px solid #EBEBEB;
				display: flex;
				flex-direction: column;
				flex-shrink: 0;
			}

			.chat-sidebar-header {
				padding: 16px 20px;
				border-bottom: 1px solid #F0F0F0;
			}

			.chat-sidebar-title {
				font-size: 15px;
				font-weight: 600;
				color: #1A1A1A;
			}

			.chat-search {
				padding: 12px 16px;
				border-bottom: 1px solid #F0F0F0;
			}

			.chat-search input {
				width: 100%;
				padding: 7px 12px;
				background: #F6F7F9;
				border: 1px solid #EBEBEB;
				border-radius: 4px;
				font-size: 13px;
				color: #1A1A1A;
				outline: none;
			}

			.chat-search input:focus {
				border-color: #07C160;
				background: white;
			}

			.chat-list {
				flex: 1;
				overflow-y: auto;
			}

			.chat-group-item {
				display: flex;
				align-items: center;
				gap: 12px;
				padding: 14px 16px;
				border-bottom: 1px solid #F5F5F5;
				cursor: pointer;
				transition: background 0.15s;
			}

			.chat-group-item:hover {
				background-color: #F6F7F9;
			}

			.chat-group-item.selected {
				background-color: #E7F8EE;
			}

			.chat-group-item img {
				width: 44px;
				height: 44px;
				border-radius: 6px;
				object-fit: cover;
				flex-shrink: 0;
			}

			.chat-group-name {
				font-size: 14px;
				font-weight: 500;
				color: #1A1A1A;
				margin-bottom: 3px;
			}

			.chat-group-meta {
				font-size: 12px;
				color: #999;
			}

			.chat-badge {
				background: #FF4D4F;
				color: white;
				font-size: 11px;
				padding: 1px 5px;
				border-radius: 8px;
				margin-left: auto;
				flex-shrink: 0;
			}

			.chat-main {
				flex: 1;
				display: flex;
				flex-direction: column;
				background: #FFFFFF;
			}

			.chat-header {
				padding: 16px 24px;
				border-bottom: 1px solid #F0F0F0;
				background: #FAFAFA;
				display: flex;
				justify-content: space-between;
				align-items: center;
			}

			.chat-header-title {
				font-size: 15px;
				font-weight: 600;
				color: #1A1A1A;
			}

			.chat-header-sub {
				font-size: 12px;
				color: #999;
				margin-top: 2px;
			}

			.chat-header-actions {
				display: flex;
				gap: 8px;
			}

			.chat-messages {
				flex: 1;
				overflow-y: auto;
				padding: 20px 24px;
				background: #F6F7F9;
			}

			.chat-empty {
				display: flex;
				flex-direction: column;
				align-items: center;
				justify-content: center;
				height: 100%;
				background-color: #F6F7F9;
				animation: slideUpFade 0.4s ease-out;
			}

			.chat-empty-icon-wrapper {
				position: relative;
				width: 88px;
				height: 88px;
				background: linear-gradient(145deg, #ffffff 0%, #f5f5f5 100%);
				border-radius: 50%;
				display: flex;
				align-items: center;
				justify-content: center;
				box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06), inset 0 2px 4px rgba(255, 255, 255, 0.9);
				margin-bottom: 20px;
				z-index: 1;
			}

			.chat-empty-icon-wrapper::after {
				content: '';
				position: absolute;
				inset: -4px;
				border-radius: 50%;
				border: 2px solid rgba(7, 193, 96, 0.15);
				animation: ripplePulse 2.5s infinite;
				z-index: -1;
			}

			.chat-empty-title {
				font-size: 16px;
				font-weight: 600;
				color: #1A1A1A;
				margin: 0 0 8px 0;
				letter-spacing: 0.5px;
			}

			.chat-empty-sub {
				font-size: 13px;
				color: #999999;
				margin: 0;
				max-width: 260px;
				text-align: center;
				line-height: 1.6;
			}

			@keyframes slideUpFade {
				from {
					opacity: 0;
					transform: translateY(12px);
				}

				to {
					opacity: 1;
					transform: translateY(0);
				}
			}

			@keyframes ripplePulse {
				0% {
					transform: scale(1);
					opacity: 0.8;
				}

				100% {
					transform: scale(1.3);
					opacity: 0;
				}
			}

			.chat-empty-icon {
				font-size: 48px;
				opacity: 0.3;
			}

			.chat-footer {
				padding: 16px 24px;
				border-top: 1px solid #F0F0F0;
				background: #FAFAFA;
			}

			.chat-tabs {
				display: flex;
				gap: 0;
				border-bottom: 1px solid #F0F0F0;
				margin-bottom: 12px;
			}

			.chat-tab {
				padding: 6px 16px;
				font-size: 13px;
				color: #999;
				cursor: pointer;
				border-bottom: 2px solid transparent;
			}

			.chat-tab.active {
				color: #07C160;
				border-bottom-color: #07C160;
			}

			.chat-input-area {
				display: flex;
				gap: 10px;
				align-items: flex-end;
			}

			.chat-input {
				flex: 1;
				padding: 10px 14px;
				border: 1px solid #EBEBEB;
				border-radius: 4px;
				font-size: 14px;
				resize: vertical;
				background: white;
				font-family: inherit;
				height: 120px;
				transition: border-color 0.2s;
				box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.02);
			}

			.chat-input:focus {
				border-color: #07C160;
				outline: none;
			}


			/* 高级聊天气泡样式 */
			.chat-msg-item {
				display: flex;
				flex-direction: column;
				margin-bottom: 24px;
				position: relative;
				width: 100%;
			}

			.chat-msg-item.is-admin {
				align-items: flex-end;
			}

			.chat-msg-item.is-user {
				align-items: flex-start;
			}

			.chat-msg-info {
				display: flex;
				align-items: center;
				gap: 8px;
				margin-bottom: 6px;
				font-size: 12px;
				color: #999;
			}

			.chat-msg-item.is-admin .chat-msg-info {
				flex-direction: row-reverse;
			}

			.chat-msg-name {
				font-weight: 500;
				font-size: 13px;
			}

			.chat-msg-item.is-admin .chat-msg-name {
				color: #595959;
			}

			.chat-msg-item.is-user .chat-msg-name {
				color: #595959;
			}

			.chat-msg-bubble-wrap {
				display: flex;
				align-items: flex-end;
				max-width: 65%;
				gap: 12px;
			}

			.chat-msg-item.is-admin .chat-msg-bubble-wrap {
				flex-direction: row-reverse;
			}

			.chat-msg-bubble {
				padding: 10px 14px;
				border-radius: 8px 12px 12px 12px;
				font-size: 15px;
				line-height: 1.5;
				word-break: break-word;
				box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
				border: 1px solid rgba(0, 0, 0, 0.03);
			}

			.chat-msg-item.is-admin .chat-msg-bubble {
				background: #95EC69;
				color: #111;
				border-radius: 12px 8px 12px 12px;
				border-color: rgba(0, 0, 0, 0.05);
				box-shadow: none;
			}

			.chat-msg-item.is-user .chat-msg-bubble {
				background: #FFFFFF;
				color: #111;
			}

			.chat-msg-actions {
				opacity: 0;
				transition: opacity 0.2s;
				display: flex;
				align-items: center;
			}

			.chat-msg-item:hover .chat-msg-actions {
				opacity: 1;
			}

			.btn-withdraw {
				width: 50px;
				background: #FF4D4F;
				color: white;
				border: none;
				border-radius: 4px;
				padding: 2px 8px;
				font-size: 11px;
				cursor: pointer;
				transition: 0.2s;
				box-shadow: 0 2px 4px rgba(255, 77, 79, 0.2);
			}

			.btn-withdraw:hover {
				background: #FF7875;
				transform: scale(1.05);
			}

			/* 图片气泡特殊处理 */
			.chat-msg-bubble:has(img) {
				padding: 0;
				background: transparent !important;
				border: none !important;
				box-shadow: none !important;
			}

			/* Msg Bubbles */
			.chat-msg-bubble:has(.admin-history-card) {
				padding: 0;
				background: transparent !important;
				border: none !important;
				box-shadow: none !important;
			}

			.msg-item {
				display: flex;
				margin-bottom: 20px;
			}

			.msg-item.is-admin {
				flex-direction: row-reverse;
			}

			.msg-avatar {
				width: 40px;
				height: 40px;
				border-radius: 50%;
				object-fit: cover;
				flex-shrink: 0;
				background: #EBEBEB;
				display: flex;
				align-items: center;
				justify-content: center;
				font-size: 16px;
			}

			.msg-body {
				max-width: 60%;
				margin: 0 10px;
			}

			.msg-meta {
				font-size: 11px;
				color: #BFBFBF;
				margin-bottom: 4px;
			}

			.msg-item.is-admin .msg-meta {
				text-align: right;
			}

			.msg-bubble {
				padding: 10px 14px;
				border-radius: 8px;
				font-size: 14px;
				line-height: 1.6;
				background: #FFFFFF;
				color: #1A1A1A;
				border: 1px solid #EBEBEB;
				display: inline-block;
			}

			.msg-item.is-admin .msg-bubble {
				background: #95EC69;
				border: none;
				color: #1A1A1A;
			}

			.msg-actions {
				margin-top: 4px;
				font-size: 10px;
			}

			.msg-item.is-admin .msg-actions {
				text-align: right;
			}

			/* Modals */
			.modal {
				display: none;
				position: fixed;
				inset: 0;
				z-index: 1000;
				overflow: hidden;
			}

			.modal-overlay {
				position: absolute;
				inset: 0;
				background: rgba(0, 0, 0, 0.4);
				z-index: 1;
				padding: 20px;
				overflow: hidden;
			}

			.modal.active {
				display: flex;
				align-items: center;
				justify-content: center;
				overflow: hidden;
			}

			.modal-content {
				background: white;
				border-radius: 6px;
				padding: 0;
				width: 90%;
				max-width: 560px;
				max-height: 88vh;
				position: relative;
				z-index: 2;
				box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
				animation: slideUp 0.2s ease;
				display: flex;
				flex-direction: column;
			}

			.modal-content>form,
			.modal-content>div.modal-body {
				padding: 80px 20px 20px;
				flex: 1;
				overflow-y: auto;
			}

			.modal-content>form {
				min-height: 0;
			}

			@keyframes slideUp {
				from {
					opacity: 0;
					transform: translateY(12px)
				}

				to {
					opacity: 1;
					transform: translateY(0)
				}
			}

			.modal-header {
				display: flex;
				justify-content: space-between;
				align-items: center;
				padding: 20px;
				position: absolute;
				top: 0;
				left: 0;
				right: 0;
				background: white;
				z-index: 10;
				border-radius: 6px 6px 0 0;
				flex-shrink: 0;
			}

			.modal-body {
				padding: 24px;
			}

			.modal-title {
				font-size: 16px;
				font-weight: 600;
				color: #1A1A1A;
				margin: 0;
			}

			.close-btn {
				background: none;
				border: none;
				font-size: 20px;
				cursor: pointer;
				color: #BFBFBF;
				line-height: 1;
			}

			.close-btn:hover {
				color: #FF4D4F;
			}

			.detail-section {
				margin-bottom: 24px;
			}

			.detail-section-title {
				font-size: 14px;
				font-weight: 600;
				color: #1A1A1A;
				margin-bottom: 12px;
				display: flex;
				align-items: center;
				gap: 8px;
			}

			.detail-section-title::before {
				content: '';
				width: 3px;
				height: 14px;
				background: #07C160;
				border-radius: 2px;
				flex-shrink: 0;
			}

			.detail-grid {
				display: grid;
				grid-template-columns: 100px 1fr;
				gap: 12px;
			}

			.detail-label {
				font-size: 13px;
				color: #999;
			}

			.detail-value {
				font-size: 13px;
				color: #1A1A1A;
			}

			.member-row {
				display: flex;
				align-items: center;
				gap: 12px;
				padding: 12px 0;
				border-bottom: 1px solid #F5F5F5;
			}

			.member-row:last-child {
				border-bottom: none;
			}

			.member-row img {
				width: 40px;
				height: 40px;
				border-radius: 50%;
				object-fit: cover;
				border: 1px solid #F0F0F0;
			}

			.member-name {
				font-size: 14px;
				font-weight: 500;
				color: #1A1A1A;
			}

			.member-id-text {
				font-size: 12px;
				color: #999;
			}

			.modal-footer {
				display: flex;
				justify-content: flex-end;
				gap: 8px;
				padding: 16px 24px;
				border-top: 1px solid #F0F0F0;
			}

			/* Buttons & Utils */
			.btn {
				padding: 7px 16px;
				border-radius: 4px;
				font-size: 13px;
				font-weight: 500;
				cursor: pointer;
				border: 1px solid transparent;
				display: inline-flex;
				align-items: center;
				gap: 6px;
				font-family: inherit;
			}

			.btn::before,
			.btn::after {
				display: none !important;
			}

			.btn:hover {
				transform: none !important;
				box-shadow: none !important;
			}

			.btn-primary {
				background: #07C160;
				color: white;
				border-color: #07C160;
			}

			.btn-primary:hover {
				background: #06AD56;
			}

			.btn-secondary {
				background: white;
				color: #595959;
				border-color: #EBEBEB;
			}

			.btn-secondary:hover {
				border-color: #BFBFBF;
			}

			.btn-danger {
				background: #FF4D4F;
				color: white;
				border-color: #FF4D4F;
			}

			.btn-sm {
				padding: 5px 12px;
				font-size: 12px;
			}

			.btn-xs {
				padding: 2px 8px;
				font-size: 11px;
			}

			.tag {
				display: inline-flex;
				align-items: center;
				padding: 2px 8px;
				border-radius: 3px;
				font-size: 12px;
				font-weight: 500;
			}

			.tag-success {
				background: #E7F8EE;
				color: #07C160;
			}

			.tag-warning {
				background: #FFFBE6;
				color: #FAAD14;
			}

			.tag-danger {
				background: #FFF2F0;
				color: #FF4D4F;
			}

			.form-group {
				margin-bottom: 16px;
			}

			.form-label {
				display: flex;
				align-items: center;
				gap: 6px;
				font-size: 14px;
				font-weight: 500;
				margin-bottom: 6px;
				color: #1A1A1A;
			}

			.form-label::before {
				content: '';
				width: 2px;
				height: 12px;
				background: #07C160;
				border-radius: 1px;
				flex-shrink: 0;
			}

			.form-input {
				width: 100%;
				padding: 9px 12px;
				border: 1px solid #EBEBEB;
				border-radius: 4px;
				font-size: 14px;
				background: white;
				color: #1A1A1A;
				outline: none;
				font-family: inherit;
			}

			.form-input:focus {
				border-color: #07C160;
			}

			.form-textarea {
				resize: vertical;
				min-height: 100px;
				line-height: 1.6;
			}

			::-webkit-scrollbar {
				width: 4px;
			}

			::-webkit-scrollbar-thumb {
				background: #E0E0E0;
				border-radius: 4px;
			}

			::-webkit-scrollbar-track {
				background: transparent;
			}

			.loading {
				display: flex;
				flex-direction: column;
				justify-content: center;
				align-items: center;
				padding: 60px 20px;
				color: #999;
				gap: 12px;
				font-size: 13px;
			}

			.loading::after {
				content: '';
				width: 22px;
				height: 22px;
				border: 2px solid #EBEBEB;
				border-top-color: #07C160;
				border-radius: 50%;
				animation: spin 0.7s linear infinite;
			}

			.empty-state {
				display: flex;
				flex-direction: column;
				align-items: center;
				justify-content: center;
				padding: 64px 20px;
				text-align: center;
				color: #BFBFBF;
			}

			.empty-state-icon {
				font-size: 44px;
				margin-bottom: 12px;
				opacity: 0.3;
			}

			.empty-state-title {
				font-size: 15px;
				font-weight: 600;
				margin-bottom: 6px;
				color: #8C8C8C;
			}

			.empty-state-desc {
				font-size: 13px;
				line-height: 1.6;
				max-width: 340px;
				color: #BFBFBF;
			}

			/* Toggle Switch */
			.toggle-switch {
				position: relative;
				display: inline-flex;
				align-items: center;
				cursor: pointer;
			}

			.toggle-switch input {
				opacity: 0;
				width: 0;
				height: 0;
				position: absolute;
			}

			.toggle-slider {
				position: relative;
				display: inline-block;
				width: 36px;
				height: 20px;
				background-color: #D9D9D9;
				border-radius: 10px;
				transition: background-color 0.2s;
				flex-shrink: 0;
			}

			.toggle-slider:before {
				content: "";
				position: absolute;
				height: 16px;
				width: 16px;
				left: 2px;
				bottom: 2px;
				background-color: white;
				border-radius: 50%;
				transition: transform 0.2s;
			}

			.toggle-switch input:checked+.toggle-slider {
				background-color: #07C160;
			}

			.toggle-switch input:checked+.toggle-slider:before {
				transform: translateX(16px);
			}

			/* Toast 弹窗样式 */
			.toast-container {
				position: fixed;
				top: 20px;
				right: 20px;
				z-index: 9999;
				display: flex;
				flex-direction: column;
				gap: 10px;
			}

			.toast {
				padding: 12px 20px;
				border-radius: 4px;
				box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
				color: #fff;
				font-size: 14px;
				font-weight: 500;
				animation: slideIn 0.3s ease-out forwards, fadeOut 0.3s ease-in forwards 2.7s;
				display: flex;
				align-items: center;
				gap: 8px;
				max-width: 300px;
			}

			.toast-success {
				background-color: #07C160;
			}

			.toast-error {
				background-color: #FA5151;
			}

			.toast-info {
				background-color: #1677FF;
			}

			.toast-warning {
				background-color: #FF9500;
			}

			@keyframes slideIn {
				from {
					transform: translateX(100%);
					opacity: 0;
				}

				to {
					transform: translateX(0);
					opacity: 1;
				}
			}

			@keyframes fadeOut {
				from {
					opacity: 1;
				}

				to {
					opacity: 0;
					transform: translateX(100%);
				}
			}

			@keyframes fadeInUp {
				from {
					opacity: 0;
					transform: translateY(20px);
				}

				to {
					opacity: 1;
					transform: translateY(0);
				}
			}

			@keyframes spin {
				from {
					transform: rotate(0deg);
				}

				to {
					transform: rotate(360deg);
				}
			}

			/* 预加载动画 */
			.preloader-spin {
				animation: spin 1s linear infinite;
			}

			/* 消息项动画 */
			.chat-msg-item {
				animation: fadeInUp 0.3s ease-out;
			}
		
/* ===== 聊天记录卡片新样式 ===== */
.qq-history-card {
    background: #FFFFFF;
    border: 1px solid #EAEAEA;
    border-radius: 8px;
    padding: 12px 14px;
    min-width: 240px;
    max-width: 280px;
    cursor: pointer;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    display: flex;
    flex-direction: column;
}

.qq-history-card:hover {
    background: #FAFAFA;
}

.qq-history-title {
    font-size: 14px;
    font-weight: 500;
    color: #1A1A1A;
    margin-bottom: 8px;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}

.qq-history-list {
    display: flex;
    flex-direction: column;
    gap: 4px;
    margin-bottom: 8px;
}

.qq-history-item {
    font-size: 12px;
    color: #888888;
    line-height: 1.4;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    display: flex;
}

.qq-history-item-name {
    color: #888888;
    flex-shrink: 0;
}

.qq-history-item-content {
    color: #888888;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.qq-history-footer {
    font-size: 12px;
    color: #888888;
    padding-top: 8px;
    border-top: 1px solid #F0F0F0;
}

			/* 针对聊天记录卡片的特殊外层包装 */
			.chat-msg-bubbles {
				padding: 0 !important;
				background: transparent !important;
				border: none !important;
				box-shadow: none !important;
			}
</style>
	</head>
	<body>
		<!-- Toast 弹窗容器 -->
		<div id="toastContainer" class="toast-container"></div>

		<div class="app-container">
			<!-- 左侧导航栏 -->
			<aside class="sidebar">
				<div class="sidebar-header">
					<h2 class="sidebar-title"
						style="font-size: 18px; font-weight: 600; color: var(--wechat-primary); margin: 0;">Chat</h2>
				</div>
				<nav class="sidebar-menu">
					<a href="#" class="menu-item active" onclick="loadGroups()">
						<span class="menu-item-icon">💬</span>
						<span class="menu-item-text">首页</span>
					</a>
					<a href="#" class="menu-item" onclick="loadChatMessages()">
						<span class="menu-item-icon">💭</span>
						<span class="menu-item-text">群聊列表</span>
					</a>
					<a href="#" class="menu-item" onclick="loadGroupInfo()">
						<span class="menu-item-icon">📋</span>
						<span class="menu-item-text">运营数据</span>
					</a>
					<a href="#" class="menu-item" onclick="loadAdminConfig()">
						<span class="menu-item-icon">⚙️</span>
						<span class="menu-item-text">网站配置</span>
					</a>
				</nav>
				<div class="sidebar-footer">
					<div class="sidebar-footer-links"><span>📄 文档</span><span>💬 社区</span></div>
					<div class="sidebar-footer-user">
						<div style="display:flex;align-items:center;gap:8px;">
							<div class="sidebar-avatar">w</div><span style="font-size:12px;color:#595959;">w.</span>
						</div><span style="color:#8C8C8C;">📱</span>
					</div>
				</div>
			</aside>

			<!-- 右侧主内容 -->
			<main class="main-panel">
				<!-- 顶部导航栏 -->
				<header class="top-nav">
					<div class="nav-left">
						<h1 class="nav-title">首页</h1>
					</div>
					<div class="nav-actions">
						<span class="nav-info">2026-01-17</span>
					</div>
				</header>

				<!-- 内容区域 -->
				<div class="content-wrapper" id="mainContent">
					<div class="loading">加载中...</div>
				</div>
			</main>
		</div>

		<!-- 创建群聊模态框 -->
		<div id="createGroupModal" class="modal">
			<div class="modal-overlay"></div>
			<div class="modal-content">
				<div class="modal-header">
					<h2 class="modal-title">创建群聊</h2>
					<button class="close-btn" onclick="closeCreateGroupModal()">×</button>
				</div>
				<form id="createGroupForm" enctype="multipart/form-data">
					<div class="form-group">
						<label class="form-label" for="groupName">群名称</label>
						<input type="text" class="form-input" id="groupName" name="groupName" required>
					</div>
					<div class="form-group">
						<label class="form-label" for="groupDesc">群介绍</label>
						<textarea class="form-input form-textarea" id="groupDesc" name="groupDesc"></textarea>
					</div>
					<div class="form-group">
						<label class="form-label" for="groupAnnouncement">群公告</label>
						<textarea class="form-input form-textarea" id="groupAnnouncement" name="groupAnnouncement"
							placeholder="输入群公告内容"></textarea>
					</div>
					<div class="form-group">
						<label class="form-label" for="groupAvatar">群头像</label>
						<input type="file" class="form-input" id="groupAvatar" name="groupAvatar" accept="image/*">
					</div>

					<div class="form-group">
						<label class="form-label" for="groupMemberLimit">群人数限制</label>
						<select class="form-input" id="groupMemberLimit" name="groupMemberLimit">
							<option value="10">10人</option>
							<option value="100">100人</option>
							<option value="500">500人</option>
							<option value="1000">1000人</option>
							<option value="2000">2000人</option>
							<option value="3000">3000人</option>
							<option value="5000">5000人</option>
							<option value="0">无限制</option>
						</select>
					</div>
					<div class="form-group">
						<label class="form-label" for="groupTag">群聊标签</label>
						<input type="text" class="form-input" id="groupTag" name="groupTag" placeholder="输入群聊专属标签">
					</div>

					<div style="display: flex; gap: 12px;">
						<button type="button" class="btn btn-secondary" onclick="closeCreateGroupModal()"
							style="flex: 1;">取消</button>
						<button type="submit" class="btn btn-primary" style="flex: 1;">创建</button>
					</div>
				</form>
			</div>
		</div>

		<!-- 群聊详情模态框 -->
		<div id="groupDetailModal" class="modal">
			<div class="modal-overlay"></div>
			<div class="modal-content">
				<div class="modal-header">
					<h2 class="modal-title" id="modalGroupName">群聊详情</h2>
					<button class="close-btn" onclick="closeGroupDetailModal()">×</button>
				</div>
				<div id="groupDetailContent" class="modal-body">
					<div class="loading">加载中...</div>
				</div>
			</div>
		</div>

		<!-- 编辑群聊模态框 -->
		<div id="editGroupModal" class="modal">
			<div class="modal-overlay"></div>
			<div class="modal-content">
				<div class="modal-header">
					<h2 class="modal-title">编辑群聊</h2>
					<button class="close-btn" onclick="closeEditGroupModal()">×</button>
				</div>
				<form id="editGroupForm" enctype="multipart/form-data">
					<input type="hidden" id="editGroupId" name="groupId">
					<div class="form-group">
						<label class="form-label" for="editGroupName">群名称</label>
						<input type="text" class="form-input" id="editGroupName" name="groupName" required>
					</div>
					<div class="form-group">
						<label class="form-label" for="editGroupDesc">群介绍</label>
						<textarea class="form-input form-textarea" id="editGroupDesc" name="groupDesc"></textarea>
					</div>
					<div class="form-group">
						<label class="form-label" for="editGroupAnnouncement">群公告</label>
						<textarea class="form-input form-textarea" id="editGroupAnnouncement"
							name="groupAnnouncement"></textarea>
					</div>
					<div class="form-group">
						<label class="form-label" for="editGroupAvatar">群头像</label>
						<input type="file" class="form-input" id="editGroupAvatar" name="groupAvatar" accept="image/*">
					</div>

					<div class="form-group">
						<label class="form-label" for="editCustomGroupId">自定义群ID（5-10位数字）</label>
						<input type="text" class="form-input" id="editCustomGroupId" name="customGroupId"
							placeholder="留空则使用系统生成的ID">
					</div>

					<div class="form-group">
						<label class="form-label" for="editGroupMemberLimit">群人数限制</label>
						<select class="form-input" id="editGroupMemberLimit" name="groupMemberLimit">
							<option value="10">10人</option>
							<option value="100">100人</option>
							<option value="500">500人</option>
							<option value="1000">1000人</option>
							<option value="2000">2000人</option>
							<option value="3000">3000人</option>
							<option value="5000">5000人</option>
							<option value="0">无限制</option>
						</select>
					</div>
					<div class="form-group">
						<label class="form-label" for="editGroupTag">群聊标签</label>
						<input type="text" class="form-input" id="editGroupTag" name="groupTag" placeholder="输入群聊专属标签">
					</div>
					<div style="display: flex; gap: 12px;">
						<button type="button" class="btn btn-secondary" onclick="closeEditGroupModal()"
							style="flex: 1;">取消</button>
						<button type="submit" class="btn btn-primary" style="flex: 1;">保存</button>
					</div>
				</form>
			</div>
		</div>

		<!-- 违禁词管理模态框 -->
		<div id="bannedWordsModal" class="modal">
			<div class="modal-overlay"></div>
			<div class="modal-content">
				<div class="modal-header">
					<h2 class="modal-title">违禁词管理</h2>
					<button class="close-btn" onclick="closeBannedWordsModal()">×</button>
				</div>
				<div id="bannedWordsContent" style="padding: 80px 20px 20px; flex: 1; overflow-y: auto;">
					<!-- 群选择界面 -->
					<div id="groupSelectSection">
						<div class="form-group">
							<label class="form-label">选择群聊</label>
							<div id="groupList"
								style="max-height: 400px; overflow-y: auto; padding: 16px; background-color: #F6F7F9; border-radius: 6px; margin-top: 8px;">
								<div style="text-align: center; color: #999;">加载中...</div>
							</div>
						</div>
					</div>

					<!-- 违禁词管理界面 -->
					<div id="bannedWordsSection" style="display: none;">
						<div class="form-group">
							<label class="form-label">添加违禁词</label>
							<div style="display: flex; gap: 8px; margin-top: 8px;">
								<input type="text" id="newBannedWord" class="form-input" placeholder="输入违禁词">
								<button type="button" class="btn btn-primary" onclick="addBannedWord()">添加</button>
							</div>
						</div>
						<div class="form-group" style="margin-top: 20px;">
							<label class="form-label">当前违禁词列表</label>
							<div id="bannedWordsList"
								style="max-height: 300px; overflow-y: auto; padding: 16px; background-color: #F6F7F9; border-radius: 6px; margin-top: 8px;">
								<div style="text-align: center; color: #999;">暂无违禁词</div>
							</div>
						</div>
						<div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
							<button type="button" class="btn btn-secondary" onclick="backToGroupSelect()">返回群选择</button>
							<button type="button" class="btn btn-primary" onclick="saveBannedWords()">保存</button>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- 引入二维码生成库 -->

		<script>
			// Toast 弹窗函数
			function toast(message, type = 'info') {
				const toastContainer = document.getElementById('toastContainer');
				if (!toastContainer) {
					console.error('toastContainer not found');
					return;
				}

				const toast = document.createElement('div');
				toast.className = `toast toast-${type}`;

				// 添加图标
				let icon = '';
				switch (type) {
					case 'success':
						icon = '✅';
						break;
					case 'error':
						icon = '❌';
						break;
					case 'warning':
						icon = '⚠️';
						break;
					case 'info':
					default:
						icon = 'ℹ️';
						break;
				}

				toast.innerHTML = `${icon} ${message}`;
				toastContainer.appendChild(toast);

				// 3秒后自动移除
				setTimeout(() => {
					toast.remove();
				}, 3000);
			}

			// 转义函数 - 用于处理模板字符串中的特殊字符
			function escapeAttr(str) {
				if (str == null) return '';
				return String(str).replace(/'/g, "\\'").replace(/\\/g, '\\\\');
			}

			// 优化模态框滑动行为
			document.addEventListener('DOMContentLoaded', function() {
				// 为模态框内容添加触摸事件处理
				document.querySelectorAll('.modal-content').forEach(function(modalContent) {
					modalContent.addEventListener('touchstart', function(e) {
						// 阻止事件冒泡到背景
						e.stopPropagation();
					});

					modalContent.addEventListener('touchmove', function(e) {
						// 允许模态框内部正常滚动
						// 只阻止冒泡，不阻止默认行为
						e.stopPropagation();
					});
				});

				// 为模态框覆盖层添加触摸事件处理，阻止背景滚动
				document.querySelectorAll('.modal-overlay').forEach(function(overlay) {
					overlay.addEventListener('touchmove', function(e) {
						// 阻止覆盖层的触摸移动，防止背景滚动
						e.preventDefault();
					});
				});
			});

			// 性能优化：前端缓存系统
			const apiCache = {
				data: {},
				timestamp: {},
				maxAge: 30000, // 缓存有效期30秒

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

				invalidate(key) {
					delete this.data[key];
					delete this.timestamp[key];
				},

				clear() {
					this.data = {};
					this.timestamp = {};
				}
			};

			// 性能优化：DOM缓存
			const domCache = {
				elements: {},

				get(id) {
					if (!this.elements[id]) {
						this.elements[id] = document.getElementById(id);
					}
					return this.elements[id];
				}
			};

			// 性能优化：防抖函数
			function debounce(func, wait) {
				let timeout;
				return function executedFunction(...args) {
					const later = () => {
						clearTimeout(timeout);
						func(...args);
					};
					clearTimeout(timeout);
					timeout = setTimeout(later, wait);
				};
			}

			// 性能优化：节流函数
			function throttle(func, limit) {
				let inThrottle;
				return function() {
					const args = arguments;
					const context = this;
					if (!inThrottle) {
						func.apply(context, args);
						inThrottle = true;
						setTimeout(() => inThrottle = false, limit);
					}
				};
			}

			// 初始化页面
			function init() {
				loadGroups();
				// 启动全局新消息检查定时器
				startGlobalMessagesCheck();
			}

			// 全局新消息检查函数 - 优化版本
			function checkGlobalMessages() {
				// 先尝试从缓存获取群聊列表
				const cachedGroups = apiCache.get('groups');

				// 获取群聊列表（使用缓存或请求新数据）
				const groupsPromise = cachedGroups ? Promise.resolve(cachedGroups) :
					fetch('api/admin/groups.php')
					.then(res => res.json())
					.then(groups => {
						if (Array.isArray(groups)) {
							apiCache.set('groups', groups);
						}
						return Array.isArray(groups) ? groups : [];
					})
					.catch(error => {
						console.error('获取群聊列表失败:', error);
						return [];
					});

				groupsPromise.then(groups => {
					// 优化：批量检查新消息，而不是逐个请求
					const groupIds = groups.map(group => group.id);
					if (groupIds.length > 0) {
						// 构建批量请求URL
						const lastTimestamps = groupIds.map(id => lastMessageTimestamps[id] || 0).join(',');
						const groupIdsParam = groupIds.join(',');

						// 使用单个请求获取所有群的新消息
						fetch(`api/chat/get_messages.php?group_ids=${groupIdsParam}&last_timestamps=${lastTimestamps}`)
							.then(res => res.json())
							.then(allMessages => {
								// 处理返回的消息数据
								if (allMessages && typeof allMessages === 'object') {
									Object.entries(allMessages).forEach(([groupId, messages]) => {
										processGroupMessages(groupId, messages);
									});
								}
							})
							.catch(error => {
								console.error('批量检查新消息失败:', error);
								// 失败时回退到逐个请求
								fallbackToIndividualRequests(groups);
							});
					}
				});
			}

			// 处理单个群聊的消息
			function processGroupMessages(groupId, messages) {
				// 确保messages是一个数组
				if (!Array.isArray(messages)) {
					console.warn('Messages is not an array for group', groupId, messages);
					messages = [];
				}

				// 初始化该群聊的最后消息时间戳（如果尚未初始化）
				if (!lastMessageTimestamps[groupId] && messages.length > 0) {
					lastMessageTimestamps[groupId] = messages[messages.length - 1].timestamp;
				}

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

						// 播放新消息提示音
						playAdminNewMessageSound();

						// 优化：只在需要时更新DOM
						updateUIForNewMessages(groupId);
					}
				} else {
					// 如果该群聊没有消息，初始化时间戳为当前时间
					if (!lastMessageTimestamps[groupId]) {
						lastMessageTimestamps[groupId] = Date.now();
					}
				}
			}

			// 更新UI以反映新消息
			function updateUIForNewMessages(groupId) {
				const mainContent = domCache.get('mainContent');
				if (!mainContent) return;

				const currentContent = mainContent.innerHTML;

				// 如果在群聊消息页面，更新左侧群聊列表
				if (currentContent.includes('chatGroupName')) {
					loadGroupsForChat();
				}

				// 如果在群聊信息页面，更新群聊信息列表
				if (currentContent.includes('groupInfoList')) {
					const groupInfoList = document.getElementById('groupInfoList');
					if (groupInfoList) {
						// 只更新未读消息计数，不重新加载整个列表
						const groupCards = groupInfoList.querySelectorAll('[onclick^="openGroupDetail"]');
						groupCards.forEach(card => {
							if (card.innerHTML.includes(`openGroupDetail('${groupId}')`)) {
								// 更新未读消息计数
								const unreadCountElement = card.querySelector(
								'span[style*="background-color: #FF3B30"]');
								const unreadCount = unreadMessageCounts[groupId] || 0;

								if (unreadCount > 0) {
									if (unreadCountElement) {
										unreadCountElement.textContent = unreadCount;
									} else {
										// 如果没有未读消息元素，添加一个
										const nameElement = card.querySelector('h4');
										if (nameElement) {
											nameElement.insertAdjacentHTML('afterend',
												`<span style="background-color: #FF3B30; color: white; font-size: 11px; padding: 2px 6px; border-radius: 10px; font-weight: 600;">${unreadCount}</span>`
												);
										}
									}
								} else {
									// 如果未读消息数为0，移除未读消息元素
									if (unreadCountElement) {
										unreadCountElement.remove();
									}
								}
							}
						});
					}
				}
			}

			// 回退到逐个请求（当批量请求失败时）
			function fallbackToIndividualRequests(groups) {
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
							processGroupMessages(groupId, messages);
						})
						.catch(error => {
							console.error(`检查群聊 ${groupId} 新消息失败:`, error);
						});
				});
			}

			// 启动全局新消息检查定时器
			function startGlobalMessagesCheck() {
				if (!globalMessagesCheckInterval) {
					// 每10秒检查一次新消息（保持合理频率）
					globalMessagesCheckInterval = setInterval(checkGlobalMessages, 10000);
				}
			}

			// 停止全局新消息检查定时器
			function stopGlobalMessagesCheck() {
				if (globalMessagesCheckInterval) {
					clearInterval(globalMessagesCheckInterval);
					globalMessagesCheckInterval = null;
				}
			}

			// 加载所有群聊 - 优化版本
			function loadGroups() {
				setActiveMenuItem('loadGroups');
				// 确保全局消息检查定时器正在运行
				startGlobalMessagesCheck();
				const mainContent = domCache.get('mainContent');
				if (!mainContent) return;

				mainContent.innerHTML = '<div class="loading">加载中...</div>';

				// 尝试从缓存获取群聊数据
				const cachedGroups = apiCache.get('groups');
				if (cachedGroups) {
					renderGroupList(cachedGroups);
				}

				// 无论是否有缓存，都请求最新数据
				fetch('api/admin/groups.php')
					.then(response => response.json())
					.then(groups => {
						if (Array.isArray(groups)) {
							apiCache.set('groups', groups);
							renderGroupList(groups);
						}
					})
					.catch(error => {
						console.error('加载群聊失败:', error);
						if (mainContent) {
							mainContent.innerHTML = `
                            <div class="empty-state">
                                <div class="empty-state-icon">❌</div>
                                <h3 class="empty-state-title">加载失败</h3>
                                <p class="empty-state-desc">请检查网络连接或刷新页面重试</p>
                            </div>
                        `;
						}
					});
			}

			// 渲染群聊列表 - 优化版本
			function renderGroupList(groups) {
				const mainContent = domCache.get('mainContent');
				if (!mainContent) return;

				const totalGroups = groups.length;
				const totalMembers = groups.reduce((sum, group) => sum + (group.members?.length || 0), 0);
				const totalTodayActive = groups.reduce((sum, group) => sum + (group.today_active_users || 0), 0);
				const totalActive = groups.reduce((sum, group) => sum + (group.total_active_users || 0), 0);

				let html = `
                <div class="alert-bar">
                    <div class="text">小众的一个聊天功能的程序</div>
                    <div style="font-size: 13px;"><span style="color: #8C8C8C; margin-right: 12px;">2025-12-10</span><span class="text-brand">查看</span></div>
                </div>
                <div class="grid-2">
                    <div class="dashed-card" onclick="openCreateGroupModal()">
                        <div class="dashed-icon">Q</div>
                        <div class="dashed-content"><h4>创建新群聊</h4><p>点击此处立刻创建新的聊天室房间。</p></div>
                    </div>
                    <div class="dashed-card" onclick="openBannedWordsModal()">
                        <div class="dashed-icon">📚</div>
                        <div class="dashed-content"><h4>违禁词设置</h4><p>点击此处管理群聊的敏感词拦截功能。</p></div>
                    </div>
                </div>

                <div class="app-panel">
                    <div class="app-title">管理的群聊列表</div>
                    <div class="app-grid">
                        ${groups.length === 0 ? '<div style="grid-column: 1/-1; padding: 40px; text-align: center; color: #999;">暂无群聊</div>' : groups.map(group => `
                        <div class="app-item" onclick="openGroupDetail('${escapeAttr(group.id)}')" title="群ID: ${escapeAttr(group.id)}">
                            <img src="${group.avatar || '/user.jpg'}" alt="${group.name}" style="width:48px; height:48px; border-radius:8px; object-fit:cover;">
                            <div class="app-name" style="font-weight:500; text-align:center; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; width:100%;">${group.name}</div>
                            <div style="font-size:11px; color:#8C8C8C;">👥 ${group.members?.length || 0}人</div>
                        </div>`).join('')}
                    </div>
                </div>
            `;

				// 性能优化：使用requestAnimationFrame减少重排
				requestAnimationFrame(() => {
					if (mainContent) {
						mainContent.innerHTML = html;
					}
				});
			}

			// 成员管理
			function loadMembers() {
				// 不激活任何菜单项，保持成员管理页面独立
				const mainContent = document.getElementById('mainContent');

				mainContent.innerHTML = `
                <div class="card">
                    <h3 class="card-title">成员管理</h3>
                    <div style="margin: 20px 0;">
                        <div id="groupInfoList" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px;">
                            <!-- 群聊信息列表将动态加载 -->
                            <div style="grid-column: 1 / -1; text-align: center; color: #999999; padding: 40px 0;">加载成员信息中...</div>
                        </div>
                    </div>
                </div>
            `;

				// 复用群聊信息加载函数，因为成员管理也需要显示群聊列表
				loadGroupInfo();
			}



			// 打开群聊详情
			function openGroupDetail(groupId) {
				const modalGroupName = document.getElementById('modalGroupName');
				const groupDetailContent = document.getElementById('groupDetailContent');

				// 清除之前的详情刷新定时器
				if (groupDetailRefreshInterval) {
					clearInterval(groupDetailRefreshInterval);
					groupDetailRefreshInterval = null;
				}

				// 显示模态框
				modalGroupName.textContent = '加载中...';
				document.getElementById('groupDetailModal').classList.add('active');

				// 显示初始加载状态
				groupDetailContent.innerHTML = '<div class="loading">加载中...</div>';

				// 标志变量，用于判断是否是首次加载
				let isFirstLoad = true;

				// 获取群聊详情的函数
				function fetchGroupDetail() {
					fetch(`api/admin/groups.php?group_id=${groupId}`)
						.then(res => res.json())
						.then(group => {
							modalGroupName.textContent = group.name;

							// 计算统计信息
							const createdDate = new Date(group.created_at);
							const now = new Date();
							const daysSinceCreation = Math.floor((now - createdDate) / (1000 * 60 * 60 * 24));

							// 构建完整的HTML内容
							const newContent = `
    <!-- 头部信息 -->
    <div style="text-align: center; padding-bottom: 24px; border-bottom: 1px solid #F0F0F0; margin-bottom: 24px;">
        <div style="display: inline-block; position: relative; margin-bottom: 16px;">
            <img src="${group.avatar || '/user.jpg'}" alt="${group.name}" style="width: 80px; height: 80px; border-radius: 12px; object-fit: cover; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
            ${group.online_count > 0 ? `<div style="position: absolute; bottom: -4px; right: -4px; width: 20px; height: 20px; background: #07C160; border-radius: 50%; border: 3px solid #FFF;"></div>` : ''}
        </div>
        <h3 style="font-size: 20px; font-weight: 600; color: #1A1A1A; margin: 0 0 8px 0">${group.name}</h3>
        <p style="font-size: 13px; color: #8C8C8C; margin: 0;">${group.members?.length || 0} 人群成员 · ${group.today_active_users || 0} 今日活跃</p>
    </div>

    <!-- 数据统计区 -->
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

    <!-- 基本信息与管理区 -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
        <!-- 左侧：基本信息 -->
        <div>
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                <div style="width: 3px; height: 14px; background: #07C160; border-radius: 2px;"></div>
                <div style="font-size: 14px; font-weight: 600; color: #1A1A1A;">基本信息</div>
            </div>
            <div style="background: #FFF; border: 1px solid #EBEBEB; border-radius: 8px; padding: 16px; height: calc(100% - 26px);">
                <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 12px; border-bottom: 1px solid #F5F5F5; margin-bottom: 12px;">
                    <div style="color: #8C8C8C; font-size: 13px;">群ID</div>
                    <div style="display: flex; align-items: center; gap: 8px;">
    <div style="color: #1A1A1A; font-size: 13px; font-family: monospace; background: #F6F7F9; padding: 4px 8px; border-radius: 4px;">${escapeAttr(group.id)}</div>
    <span onclick="copyShareLink(window.location.href.split('?')[0].replace('admin.php', '') + '?group_id=${escapeAttr(group.id)}')" style="cursor:pointer; color:#07C160; font-size:12px; font-weight:500; background: #E7F8EE; padding: 4px 8px; border-radius: 4px; transition: 0.2s;">🔗 复制链接</span>
</div>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 12px; border-bottom: 1px solid #F5F5F5; margin-bottom: 12px;">
                    <div style="color: #8C8C8C; font-size: 13px;">群人数限制</div>
                    <div style="color: #1A1A1A; font-size: 13px;">${group.member_limit ? group.member_limit + '人' : '无限制'}</div>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 12px; border-bottom: 1px solid #F5F5F5; margin-bottom: 12px;">
                    <div style="color: #8C8C8C; font-size: 13px;">群聊标签</div>
                    <div style="color: #1A1A1A; font-size: 13px;">${group.tag || '无'}</div>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="color: #8C8C8C; font-size: 13px;">创建时间</div>
                    <div style="color: #1A1A1A; font-size: 13px;">${new Date(group.created_at).toLocaleString()}</div>
                </div>
            </div>
        </div>

        <!-- 右侧：群管理 -->
        <div>
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                <div style="width: 3px; height: 14px; background: #07C160; border-radius: 2px;"></div>
                <div style="font-size: 14px; font-weight: 600; color: #1A1A1A;">群管理</div>
            </div>
            <div style="background: #FFF; border: 1px solid #EBEBEB; border-radius: 8px; padding: 16px; height: calc(100% - 26px);">
                <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 12px; border-bottom: 1px solid #F5F5F5; margin-bottom: 12px;">
                    <div style="font-size: 13px; color: #1A1A1A;">全员禁言</div>
                    <label class="toggle-switch" style="display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" ${!group.allow_speak ? 'checked' : ''} onchange="toggleGroupSpeak('${escapeAttr(group.id)}', !this.checked)">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="font-size: 13px; color: #1A1A1A;">上传图片</div>
                    <label class="toggle-switch" style="display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" ${group.allow_image_upload !== false ? 'checked' : ''} onchange="toggleGroupImageUpload('${escapeAttr(group.id)}', this.checked)">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- 群简介与公告 -->
    <div style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
            <div style="width: 3px; height: 14px; background: #07C160; border-radius: 2px;"></div>
            <div style="font-size: 14px; font-weight: 600; color: #1A1A1A;">群公告与介绍</div>
        </div>
        <div style="background: #FFF; border: 1px solid #EBEBEB; border-radius: 8px; padding: 16px;">
        <div style="font-size: 13px; color: #595959; line-height: 1.6;">
                <strong style="color:#1A1A1A;">公告：</strong>${group.announcement || '暂无公告'}
            </div>
            <div style="font-size: 13px; color: #595959; line-height: 1.6; margin-bottom: 12px; padding-bottom: 12px; margin-top:5px;">
                <strong style="color:#1A1A1A;">介绍：</strong>${group.desc || '暂无介绍'}
            </div>
        </div>
    </div>

    <!-- 底部标签管理 -->
    <div style="margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <div style="width: 3px; height: 14px; background: #07C160; border-radius: 2px;"></div>
                <div style="font-size: 14px; font-weight: 600; color: #1A1A1A;">底部标签管理</div>
            </div>
            <button class="btn btn-primary btn-sm" onclick="openAddQuickActionModal('${escapeAttr(group.id)}')" style="padding: 4px 12px; font-size: 12px; height: auto;">
                <span style="margin-right: 4px;">+</span>添加标签
            </button>
        </div>
        <div id="quickActionsList_${group.id}" style="background: #FFF;  border-radius: 8px; min-height: 50px;"></div>
    </div>

    <!-- 底部操作按钮 -->
    <div style="display: flex; gap: 12px; justify-content: flex-end; padding-top: 16px; border-top: 1px solid #F0F0F0; margin-top: 24px;">
        <button class="btn btn-primary" onclick="editGroup('${escapeAttr(group.id)}')">编辑信息</button>
        <button class="btn btn-danger" onclick="deleteGroup('${escapeAttr(group.id)}')">解散群聊</button>
    </div>
`;

							// 首次加载时，直接替换内容并生成二维码
							groupDetailContent.innerHTML = newContent;
							loadQuickActions(group.id);
						})
						.catch(error => {
							console.error('加载群聊详情失败:', error);
							// 只在首次加载失败时显示错误，后续刷新失败不影响用户体验
							if (groupDetailContent.innerHTML.includes('加载中...')) {
								groupDetailContent.innerHTML = `
                            <div class="empty-state">
                                <div class="empty-state-icon">❌</div>
                                <h3 class="empty-state-title">加载失败</h3>
                                <p class="empty-state-desc">请检查网络连接或刷新页面重试</p>
                            </div>
                        `;
							}
						});
				}

				// 立即加载一次数据
				fetchGroupDetail();

				// 设置定时器，每10秒刷新一次数据，减少闪烁
				groupDetailRefreshInterval = setInterval(fetchGroupDetail, 10000);
			}

			// 切换群聊全体发言
			function toggleGroupSpeak(groupId, allowSpeak) {
				fetch('api/admin/toggle_group_speak.php', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
						},
						body: JSON.stringify({
							group_id: groupId,
							allow_speak: allowSpeak
						})
					})
					.then(response => response.json())
					.then(data => {
						if (data.success) {
							// 显示成功提示
							console.log('发言权限已' + (allowSpeak ? '开启' : '关闭'));

							// 只更新当前群聊的状态，不重新加载整个列表
							// 更新群聊列表中的状态
							const groupCards = document.querySelectorAll('.group-card');
							groupCards.forEach(card => {
								if (card.dataset.groupId === groupId) {
									// 更新卡片中的状态显示
									const allowSpeakElement = card.querySelector(
										'[onchange*="toggleGroupSpeak"] + span');
									if (allowSpeakElement) {
										allowSpeakElement.textContent = allowSpeak ? '允许' : '禁止';
										allowSpeakElement.style.color = allowSpeak ? 'var(--wechat-success)' :
											'var(--wechat-text-tertiary)';
									}
								}
							});

							// 更新群聊详情中的状态
							const modal = document.getElementById('groupDetailModal');
							if (modal.classList.contains('active') && modal.dataset.groupId === groupId) {
								const allowSpeakElement = document.querySelector(
									'[onchange*="toggleGroupSpeak"] + span + span');
								if (allowSpeakElement) {
									allowSpeakElement.textContent = allowSpeak ? '允许' : '禁止';
									allowSpeakElement.style.color = allowSpeak ? 'var(--wechat-success)' :
										'var(--wechat-text-tertiary)';
								}
							}
						} else {
							// 显示失败提示
							console.error('切换发言权限失败:', data.message || '未知错误');
						}
					})
					.catch(error => {
						console.error('切换发言权限失败:', error);
					});
			}

			// 设置成员头衔
			function setMemberTitle(groupId, userId, title) {
				fetch('api/admin/set_member_title.php', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
						},
						body: JSON.stringify({
							group_id: groupId,
							user_id: userId,
							title: title
						})
					})
					.then(response => response.json())
					.then(data => {
						if (data.success) {
							// 重新加载群聊列表
							loadGroups();
						}
					})
					.catch(error => {
						console.error('设置成员头衔失败:', error);
					});
			}

			// 切换群聊图片上传权限
			function toggleGroupImageUpload(groupId, allowImageUpload) {
				fetch('api/admin/toggle_group_image_upload.php', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
						},
						body: JSON.stringify({
							group_id: groupId,
							allow_image_upload: allowImageUpload
						})
					})
					.then(response => response.json())
					.then(data => {
						if (data.success) {
							// 显示成功提示
							console.log('图片上传权限已' + (allowImageUpload ? '开启' : '关闭'));

							// 只更新当前群聊的状态，不重新加载整个列表
							// 更新群聊列表中的状态
							const groupCards = document.querySelectorAll('.group-card');
							groupCards.forEach(card => {
								if (card.dataset.groupId === groupId) {
									// 更新卡片中的状态显示
									const allowImageElement = card.querySelector(
										'[onchange*="toggleGroupImageUpload"] + span');
									if (allowImageElement) {
										allowImageElement.textContent = allowImageUpload ? '允许' : '禁止';
										allowImageElement.style.color = allowImageUpload ? 'var(--wechat-success)' :
											'var(--wechat-text-tertiary)';
									}
								}
							});

							// 更新群聊详情中的状态
							const modal = document.getElementById('groupDetailModal');
							if (modal.classList.contains('active') && modal.dataset.groupId === groupId) {
								const allowImageElement = document.querySelector(
									'[onchange*="toggleGroupImageUpload"] + span + span');
								if (allowImageElement) {
									allowImageElement.textContent = allowImageUpload ? '允许' : '禁止';
									allowImageElement.style.color = allowImageUpload ? 'var(--wechat-success)' :
										'var(--wechat-text-tertiary)';
								}
							}
						} else {
							// 显示失败提示
							console.error('切换图片上传权限失败:', data.message || '未知错误');
						}
					})
					.catch(error => {
						console.error('切换图片上传权限失败:', error);
					});
			}

			// 切换成员发言权限
			function toggleMemberSpeak(userId, allowSpeak) {
				fetch('api/admin/toggle_member_speak.php', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
						},
						body: JSON.stringify({
							user_id: userId,
							allow_speak: allowSpeak
						})
					})
					.then(response => response.json())
					.then(data => {
						if (data.success) {
							// 重新加载群聊详情
							const modal = document.getElementById('groupDetailModal');
							if (modal.classList.contains('active')) {
								const groupId = modal.dataset.groupId;
								if (groupId) {
									openGroupDetail(groupId);
								}
							}
						}
					})
					.catch(error => {
						console.error('切换成员发言权限失败:', error);
					});
			}

			// 移除成员
			function kickMember(groupId, userId) {
				if (confirm('确定要移除该成员吗？')) {
					fetch('api/admin/remove_member.php', {
							method: 'POST',
							headers: {
								'Content-Type': 'application/json',
							},
							body: JSON.stringify({
								group_id: groupId,
								user_id: userId
							})
						})
						.then(response => response.json())
						.then(data => {
							if (data.success) {
								// 重新加载群聊详情
								openGroupDetail(groupId);
								// 重新加载群聊列表
								loadGroups();
								toast('成员移除成功', 'success');
							} else {
								toast('移除失败: ' + (data.message || '未知错误'), 'error');
							}
						})
						.catch(error => {
							console.error('移除成员失败:', error);
							toast('移除失败，请检查网络连接', 'error');
						});
				}
			}

			// 编辑群聊
			function editGroup(groupId) {
				// 获取群聊信息
				fetch(`api/admin/groups.php?group_id=${groupId}`)
					.then(response => response.json())
					.then(group => {
						if (group) {
							// 填充表单
							document.getElementById('editGroupName').value = group.name;
							document.getElementById('editGroupDesc').value = group.desc;
							document.getElementById('editGroupAnnouncement').value = group.announcement || '';
							document.getElementById('editCustomGroupId').value = group.custom_group_id || '';
							// 设置群人数限制
							document.getElementById('editGroupMemberLimit').value = group.member_limit || 0;
							// 设置群聊标签
							document.getElementById('editGroupTag').value = group.tag || '';
							// 保存群聊ID到表单
							document.getElementById('editGroupId').value = groupId;
							// 显示编辑模态框
							document.getElementById('editGroupModal').classList.add('active');
						}
					})
					.catch(error => {
						console.error('加载群聊信息失败:', error);
						toast('加载群聊信息失败', 'error');
					});
			}

			// 删除群聊
			function deleteGroup(groupId) {
				if (confirm('确定要删除该群聊吗？此操作不可恢复。')) {
					fetch(`api/admin/group.php?group_id=${groupId}`, {
							method: 'DELETE'
						})
						.then(response => response.json())
						.then(data => {
							if (data.success) {
								// 关闭模态框
								closeGroupDetailModal();
								// 重新加载群聊列表
								loadGroups();
								toast('群聊删除成功', 'success');
							} else {
								toast('删除失败', 'error');
							}
						})
						.catch(error => {
							console.error('删除群聊失败:', error);
							toast('删除失败', 'error');
						});
				}
			}

			// 打开搜索
			function openSearch() {
				toast('搜索功能开发中', 'info');
			}

			// 群聊消息管理功能
			let currentMessageType = 'text';
			let chatGroups = [];

			// 加载群聊消息管理界面
			function loadChatMessages() {
				setActiveMenuItem('loadChatMessages');
				startGlobalMessagesCheck();
				loadAdminConfigData();
				document.getElementById('mainContent').innerHTML = `
        <div class="chat-layout">
            <div class="chat-sidebar">
                <div class="chat-sidebar-header"><span class="chat-sidebar-title">群聊列表</span></div>
                <div class="chat-search"><input type="text" id="groupSearch" placeholder="搜索群聊..."></div>
                <div id="groupList" class="chat-list"><div class="chat-empty"><div class="chat-empty-icon">💬</div>加载中...</div></div>
            </div>
            <div class="chat-main">
                <div id="chatHeader" class="chat-header">
                    <div>
                        <div id="chatGroupName" class="chat-header-title">请选择群聊</div>
                        <div id="chatGroupInfo" class="chat-header-sub"></div>
                    </div>
                    <div class="chat-header-actions">
                        <button class="btn btn-sm btn-secondary" onclick="toggleMultiSelect()" id="btnMultiSelect">多选</button>
							<button class="btn btn-sm btn-primary" onclick="openForwardModal()" id="btnForward" style="display:none;">合并转发</button>
							<button class="btn btn-sm btn-secondary" onclick="refreshMessages()">刷新</button>
                        <button class="btn btn-sm btn-danger" onclick="withdrawAllMessages()">撤回全部</button>
                    </div>
                </div>
                <div id="messagesList" class="chat-messages">
                    <div class="chat-empty"><div class="chat-empty-icon-wrapper"><div class="chat-empty-icon">👈</div></div><h3 class="chat-empty-title">未选择任何群聊</h3><p class="chat-empty-sub">请在左侧群组列表中选择一个群聊以查看和发送消息</p></div>
                </div>
                <div id="chatFooter" class="chat-footer" style="display: none;">
                    <div class="chat-tabs">
                        <div class="chat-tab active" onclick="switchMessageType('text')">文本</div>
                        <div class="chat-tab" onclick="switchMessageType('image')">图片</div>
                        <div class="chat-tab" onclick="switchMessageType('video')">视频</div>
                        <div class="chat-tab" onclick="openResourceModal()">资源</div>
                    </div>
                    <div class="chat-input-area">
                        <textarea id="messageInput" class="chat-input" placeholder="输入消息，按 Enter 发送"></textarea>
                        <button class="btn btn-primary" onclick="sendMessage()">发送</button>
                    </div>
                    <div id="mediaUploadContainer" style="display: none; margin-top: 10px;">
                        <input type="file" id="mediaUpload" class="form-input" style="font-size: 13px; padding: 6px;">
                    </div>

                </div>
            </div>
        </div>
    `;
				domCache.elements = {};
				loadGroupsForChat();

				document.getElementById('messageInput').addEventListener('keypress', function(e) {
					if (e.key === 'Enter' && !e.shiftKey) {
						e.preventDefault();
						sendMessage();
					}
				});

				// 使用防抖函数优化搜索功能
				const debouncedSearch = debounce(function(searchText) {
					document.querySelectorAll('.chat-group-item').forEach(card => {
						const name = card.querySelector('.chat-group-name').textContent.toLowerCase();
						card.style.display = name.includes(searchText) ? 'flex' : 'none';
					});
				}, 300);

				document.getElementById('groupSearch').addEventListener('input', function(e) {
					const searchText = e.target.value.toLowerCase();
					debouncedSearch(searchText);
				});
			}

			// 加载群聊列表用于聊天选择
			function loadGroupsForChat() {
				// 尝试从缓存获取群聊数据
				const cachedGroups = apiCache.get('groups');
				if (cachedGroups) {
					renderGroupsForChat(cachedGroups);
				} else {
					// 如果没有缓存，显示加载状态
					const groupList = document.getElementById('groupList');
					if (groupList) {
						groupList.innerHTML = '<div class="chat-loading">加载中...</div>';
					}
				}

				// 无论是否有缓存，都请求最新数据
				fetch('api/admin/groups.php')
					.then(r => {
						if (!r.ok) {
							throw new Error('网络请求失败: ' + r.status);
						}
						return r.json();
					})
					.then(groups => {
						// 再次检查groupList元素是否存在，因为DOM可能已经被重新创建
						const groupList = document.getElementById('groupList');
						if (!groupList) {
							console.error('无法获取groupList元素，DOM可能已经被重新创建');
							return;
						}

						if (Array.isArray(groups)) {
							apiCache.set('groups', groups);
							renderGroupsForChat(groups);
						} else {
							console.error('加载群聊失败: 返回数据不是数组', groups);
							// 使用缓存数据作为 fallback
							const cachedGroups = apiCache.get('groups');
							if (cachedGroups) {
								renderGroupsForChat(cachedGroups);
								toast('加载群聊列表失败，使用缓存数据', 'warning');
							} else {
								// 如果没有缓存，显示空状态
								groupList.innerHTML = '<div class="chat-empty">加载失败，请刷新页面重试</div>';
								toast('加载群聊列表失败，请刷新页面重试', 'error');
							}
						}
					})
					.catch(e => {
						console.error('加载群聊失败:', e);
						// 再次检查groupList元素是否存在
						const groupList = document.getElementById('groupList');
						if (!groupList) {
							console.error('无法获取groupList元素，DOM可能已经被重新创建');
							return;
						}

						// 错误时使用缓存数据
						const cachedGroups = apiCache.get('groups');
						if (cachedGroups) {
							renderGroupsForChat(cachedGroups);
							toast('网络错误，使用缓存的群聊数据', 'warning');
						} else {
							// 如果没有缓存，显示错误状态
							groupList.innerHTML = '<div class="chat-empty">加载失败，请刷新页面重试</div>';
							toast('加载群聊列表失败，请检查网络连接', 'error');
						}
					});
			}

			function renderGroupsForChat(groups) {
				// 每次都直接从DOM中获取groupList元素，而不是使用缓存
				// 因为loadChatMessages函数会重新创建整个页面结构
				const groupList = document.getElementById('groupList');
				if (!groupList) {
					console.error('无法获取groupList元素');
					return;
				}

				if (groups.length === 0) {
					groupList.innerHTML = '<div class="chat-empty">暂无群聊</div>';
					return;
				}

				const groupHtml = groups.map(group => {
					const unreadCount = unreadMessageCounts[group.id] || 0;
					return `
        <div class="chat-group-item" onclick="selectGroupForChat('${escapeAttr(group.id)}', '${escapeAttr(group.name)}', ${group.members?.length || 0})" data-group-id="${group.id}">
            <img src="${group.avatar || '/user.jpg'}">
            <div style="flex:1;min-width:0;">
                <div class="chat-group-name">${group.name}</div>
                <div class="chat-group-meta">👥 ${group.members?.length || 0}人</div>
            </div>
            ${unreadCount > 0 ? `<span class="chat-badge">${unreadCount}</span>` : ''}
        </div>`;
				}).join('');

				// 使用requestAnimationFrame优化DOM更新
				requestAnimationFrame(() => {
					groupList.innerHTML = groupHtml;
				});
			}

			// 选择群聊
			function selectGroupForChat(groupId, groupName, memberCount) {
				document.querySelectorAll('.chat-group-item').forEach(c => c.classList.remove('selected'));
				const currentCard = document.querySelector(`.chat-group-item[data-group-id="${groupId}"]`);
				if (currentCard) {
					currentCard.classList.add('selected');
					const badge = currentCard.querySelector('.chat-badge');
					if (badge) badge.remove();
				}
				unreadMessageCounts[groupId] = 0;
				document.getElementById('chatGroupName').textContent = groupName;
				document.getElementById('chatGroupInfo').textContent = `${memberCount}名成员 | ID: ${groupId}`;
				document.getElementById('chatFooter').style.display = 'block';
				currentSelectedGroupId = groupId;
				if (window._msgPollInterval) {
					clearInterval(window._msgPollInterval);
					window._msgPollInterval = null;
				}
				const messagesList = document.getElementById('messagesList');
				messagesList.innerHTML =
					'<div class="chat-empty"><div class="chat-empty-icon-wrapper" style="animation: spin 1.5s linear infinite;"><div class="chat-empty-icon">⏳</div></div><h3 class="chat-empty-title">正在同步数据</h3><p class="chat-empty-sub">正在拉取云端聊天记录，请稍候...</p></div>';
				loadGroupMessages(groupId, true);
			}

			// 加载群聊消息
			function loadGroupMessages(groupId, isInitialLoad = true) {
				const messagesList = domCache.get('messagesList');
				if (!messagesList) return;

				// 如果有正在进行的请求，取消它
				if (currentMessageRequest) {
					currentMessageRequest.abort();
				}

				// 首次加载时显示加载状态
				if (isInitialLoad) {
					messagesList.innerHTML = '<div style="text-align: center; color: #999999; padding: 20px;">加载消息中...</div>';
				}

				// 构建请求URL，对于增量加载，我们获取完整消息列表以检测删除
				let url = `api/chat/get_messages.php?group_id=${groupId}`;

				// 尝试从缓存获取消息数据
				const cacheKey = `messages_${groupId}`;
				const cachedMessages = apiCache.get(cacheKey);

				if (cachedMessages && isInitialLoad) {
					renderMessagesForGroup(cachedMessages, groupId, isInitialLoad, messagesList);
				}

				// 创建新的请求控制器
				const controller = new AbortController();
				const signal = controller.signal;
				currentMessageRequest = controller;

				fetch(url, {
						signal
					})
					.then(response => response.json())
					.then(messages => {
						// 清除当前请求
						currentMessageRequest = null;

						if (Array.isArray(messages)) {
							// 缓存消息数据
							apiCache.set(cacheKey, messages);
							renderMessagesForGroup(messages, groupId, isInitialLoad, messagesList);
						}
					})
					.catch(error => {
						// 清除当前请求
						currentMessageRequest = null;

						// 忽略中止错误
						if (error.name === 'AbortError') {
							return;
						}

						console.error('加载消息失败:', error);
						// 只在首次加载失败时显示错误
						if (isInitialLoad) {
							messagesList.innerHTML =
								'<div style="text-align: center; color: #FF3B30; padding: 20px;">加载消息失败</div>';
						}
					});
			}

			function renderMessagesForGroup(messages, groupId, isInitialLoad, messagesList) {
				// 首次加载
				if (isInitialLoad) {
					if (messages.length === 0) {
						messagesList.innerHTML =
							`<div class="chat-empty"><div class="chat-empty-icon-wrapper"><div class="chat-empty-icon">📭</div></div><h3 class="chat-empty-title">暂无消息记录</h3><p class="chat-empty-sub">这里静悄悄的，快在下方输入框发送第一条消息开启对话吧～</p></div>`;
						return;
					}

					const messagesHtml = messages.map(message => `
            <div class="chat-msg-item ${message.is_admin ? 'is-admin' : 'is-user'}" data-message-id="${message.id || 'unknown'}" data-group-id="${message.group_id}" style="position: relative; width: 100%; padding: 6px; transition: background-color 0.2s; border-radius: 8px; margin-bottom: 12px;" onclick="handleMsgRowClick(event, this)">

    <!-- 核心修复1：添加一个绝对定位的顶层透明遮罩。在多选模式下激活，物理拦截针对图片/视频的所有点击 -->
    <div class="msg-click-shield" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 9999; border-radius: 8px;"></div>

    <!-- flex 布局：对立面 + 垂直水平居中 -->
    <div style="display: flex; width: 100%; flex-direction: ${message.is_admin ? 'row-reverse' : 'row'}; justify-content: space-between; align-items: center; position: relative; z-index: 1;">

        <!-- 消息主体 -->
        <div class="msg-content-wrapper" style="display: flex; flex-direction: column; max-width: 85%; align-items: ${message.is_admin ? 'flex-end' : 'flex-start'};">
            <div class="chat-msg-info">
                <span class="chat-msg-name">${message.user_nickname || (message.is_admin ? '管理员' : '匿名用户')}</span>
                <span>${message.timestamp}</span>
            </div>
            <div class="chat-msg-bubble-wrap" style="align-items: flex-end;">
                <div class="${message.type === 'history' ? 'chat-msg-bubbles' : 'chat-msg-bubble'}">
                    ${renderMessageContent(message)}
                </div>
                <div class="chat-msg-actions">
                    <button class="btn-withdraw" onclick="withdrawMessage('${message.id || 'unknown'}', '${message.group_id || ''}')">撤回</button>
                </div>
            </div>
        </div>

        <!-- 多选框：始终在信息的对面 -->
        <div class="msg-select-checkbox" style="display: ${typeof isMultiSelectMode !== 'undefined' && isMultiSelectMode ? 'flex' : 'none'}; align-items: center; justify-content: center; width: 40px; flex-shrink: 0; z-index: 2;">
            <input type="checkbox" class="message-checkbox" value="${encodeURIComponent(JSON.stringify(message))}" style="width: 22px; height: 22px; pointer-events: none; margin: 0; accent-color: #07C160;">
        </div>

    </div>
</div>
        `).join('');

					// 使用requestAnimationFrame优化DOM更新
					requestAnimationFrame(() => {
						messagesList.innerHTML = messagesHtml;

						// 记录最后一条消息的时间戳
						if (messages.length > 0) {
							lastMessageTimestamps[groupId] = messages[messages.length - 1].timestamp;
						}

						// 滚动到底部
						messagesList.scrollTop = messagesList.scrollHeight;

						// 清除未读消息计数
						if (unreadMessageCounts[groupId]) {
							delete unreadMessageCounts[groupId];
							// 更新群聊列表
							const groupList = domCache.get('groupList');
							if (groupList) {
								loadGroupsForChat();
							}
						}
					});
				}
				// 增量加载（同步新消息和删除消息）
				else {
					// 保存当前滚动位置
					const currentScrollHeight = messagesList.scrollHeight;
					const currentScrollTop = messagesList.scrollTop;
					const clientHeight = messagesList.clientHeight;
					const isAtBottom = currentScrollHeight - currentScrollTop - clientHeight < 100; // 100px以内视为在底部

					// 获取当前DOM中的所有消息项
					const existingMessages = messagesList.querySelectorAll('.chat-msg-item');
					const existingMessageIds = new Set();
					existingMessages.forEach(msg => {
						existingMessageIds.add(msg.getAttribute('data-message-id'));
					});

					// 获取服务器返回的所有消息ID
					const serverMessageIds = new Set(messages.map(msg => msg.id || 'unknown'));

					// 1. 移除已删除的消息（存在于DOM但不存在于服务器返回的消息）
					existingMessages.forEach(msg => {
						const msgId = msg.getAttribute('data-message-id');
						if (!serverMessageIds.has(msgId)) {
							msg.remove();
						}
					});

					// 2. 获取新消息（时间戳大于上次记录的时间戳）
					const newMessages = messages.filter(msg => {
						return msg.timestamp > (lastMessageTimestamps[groupId] || 0);
					});

					if (newMessages.length > 0) {
						// 构建新消息HTML
						const newMessagesHtml = newMessages.map(message => `
            <div class="chat-msg-item ${message.is_admin ? 'is-admin' : 'is-user'}" data-message-id="${message.id || 'unknown'}" data-group-id="${message.group_id}" style="position: relative; width: 100%; padding: 6px; transition: background-color 0.2s; border-radius: 8px; margin-bottom: 12px;" onclick="handleMsgRowClick(event, this)">

    <!-- 核心修复1：添加一个绝对定位的顶层透明遮罩。在多选模式下激活，物理拦截针对图片/视频的所有点击 -->
    <div class="msg-click-shield" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 9999; border-radius: 8px;"></div>

    <!-- flex 布局：对立面 + 垂直水平居中 -->
    <div style="display: flex; width: 100%; flex-direction: ${message.is_admin ? 'row-reverse' : 'row'}; justify-content: space-between; align-items: center; position: relative; z-index: 1;">

        <!-- 消息主体 -->
        <div class="msg-content-wrapper" style="display: flex; flex-direction: column; max-width: 85%; align-items: ${message.is_admin ? 'flex-end' : 'flex-start'};">
            <div class="chat-msg-info">
                <span class="chat-msg-name">${message.user_nickname || (message.is_admin ? '管理员' : '匿名用户')}</span>
                <span>${message.timestamp}</span>
            </div>
            <div class="chat-msg-bubble-wrap" style="align-items: flex-end;">
                <div class="${message.type === 'history' ? 'chat-msg-bubbles' : 'chat-msg-bubble'}">
                    ${renderMessageContent(message)}
                </div>
                <div class="chat-msg-actions">
                    <button class="btn-withdraw" onclick="withdrawMessage('${message.id || 'unknown'}', '${message.group_id || ''}')">撤回</button>
                </div>
            </div>
        </div>

        <!-- 多选框：始终在信息的对面 -->
        <div class="msg-select-checkbox" style="display: ${typeof isMultiSelectMode !== 'undefined' && isMultiSelectMode ? 'flex' : 'none'}; align-items: center; justify-content: center; width: 40px; flex-shrink: 0; z-index: 2;">
            <input type="checkbox" class="message-checkbox" value="${encodeURIComponent(JSON.stringify(message))}" style="width: 22px; height: 22px; pointer-events: none; margin: 0; accent-color: #07C160;">
        </div>

    </div>
</div>
        `).join('');

						// 动态添加新消息到列表末尾
						messagesList.insertAdjacentHTML('beforeend', newMessagesHtml);

						// 播放新消息提示音
						playAdminNewMessageSound();
					}

					// 更新最后消息时间戳
					if (messages.length > 0) {
						lastMessageTimestamps[groupId] = messages[messages.length - 1].timestamp;
					}

					// 恢复滚动位置
					setTimeout(() => {
						const newScrollHeight = messagesList.scrollHeight;
						const scrollDiff = newScrollHeight - currentScrollHeight;
						if (isAtBottom) {
							messagesList.scrollTop = newScrollHeight;
						} else {
							messagesList.scrollTop = currentScrollTop + scrollDiff;
						}
					}, 0);
				}
			}

			// 渲染消息内容
			function escapeHtml(str) {
				if (typeof str !== 'string') return '';
				return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
			}

			function renderMessageContent(message) {
				let type = message.type || 'text';
				let content = message.content || '';

				// 尝试兼容: 若 type 为 text，但 content 是 JSON 字符串且包含 title/items，则转为 history/card
				if (type === 'text') {
					try {
						const parsed = JSON.parse(content);
						if (parsed && typeof parsed === 'object') {
							if (parsed.items && Array.isArray(parsed.items)) {
								type = 'history';
								content = parsed;
							} else if (parsed.title && parsed.appName) {
								type = 'card';
								content = parsed;
							}
						}
					} catch (e) {}
				}

				// 兼容：后端把合并转发存成纯文本的情况，长得像：
				// 群聊的聊天记录
				// 管理员: [图片]
				// 我: xxx
				// 查看 3 条转发消息
				if (type === 'text' && typeof content === 'string' && content.includes('群聊的聊天记录')) {
					const lines = content.split('\n').map(l => l.trim()).filter(l => l);
					if (lines.length >= 2) {
						const title = lines[0];
						const footer = lines[lines.length - 1];
						const middleLines = lines.slice(1, lines.length - 1);

						const itemsHtml = middleLines.map(line => `<div class="admin-history-line">${escapeHtml(line)}</div>`)
							.join('');

						return `<div class="admin-history-card">
                <div class="admin-history-title">${escapeHtml(title)}</div>
                ${itemsHtml}
                <div class="admin-history-footer">${escapeHtml(footer)}</div>
            </div>`;
					}
				}

				// 替换旧域名
				if (typeof content === 'string' && content.includes('lvba3.tyxcu.shop')) {
					content = content.replace(/https?:\/\/lvba3\.tyxcu\.shop/g, window.location.origin);
				}

				// ---------- 系统消息 ----------
				if (type === 'system') {
					return `<span class="admin-msg-system">${escapeHtml(content)}</span>`;
				}

				// ---------- 图片 ----------
				if (type === 'image') {
					let imgUrl = content;
					if (imgUrl && !imgUrl.startsWith('http') && !imgUrl.startsWith('data:') && !imgUrl.startsWith('/')) {
						imgUrl = '/' + imgUrl;
					}
					const safeUrl = escapeHtml(imgUrl);
					return `<img src="${safeUrl}" style="max-width:200px;max-height:240px;border-radius:8px;display:block;cursor:zoom-in;object-fit:cover;border:0.5px solid rgba(0,0,0,0.06);" onerror="this.onerror=null;this.style.display='none';var s=document.createElement('span');s.style='color:#999;font-size:12px;';s.textContent='[图片加载失败]';this.parentNode.insertBefore(s,this.nextSibling);" onclick="window.open('${safeUrl}','_blank')">`;
				}

				// ---------- 视频 ----------
				if (type === 'video') {
					let vidUrl = content;
					if (vidUrl && !vidUrl.startsWith('http') && !vidUrl.startsWith('data:') && !vidUrl.startsWith('/')) {
						vidUrl = '/' + vidUrl;
					}
					const safeUrl = escapeHtml(vidUrl);
					return `<div style="position:relative; width: 180px; height: 240px; cursor:pointer; border-radius:8px; overflow:hidden; background:#1A1A1A; border:0.5px solid rgba(0,0,0,0.06); margin: 2px 0;" onclick="previewVideo('${safeUrl}')">
						<video src="${safeUrl}" preload="metadata" style="width:100%; height:100%; object-fit:cover; display:block; opacity:0.85;"></video>
						<div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); width:44px; height:44px; background:rgba(0,0,0,0.4); border-radius:50%; display:flex; align-items:center; justify-content:center; pointer-events:none; border:2px solid rgba(255,255,255,0.7); backdrop-filter:blur(2px); transition:all 0.2s;">
							<div style="width:0; height:0; border-style:solid; border-width:8px 0 8px 12px; border-color:transparent transparent transparent #fff; margin-left:4px;"></div>
						</div>
					</div>`;
				}

				// ---------- 语音 ----------
				if (type === 'voice') {
					const safeUrl = escapeHtml(content);
					return `<audio src="${safeUrl}" controls style="max-width:220px;height:40px;"></audio>`;
				}

				// ---------- 文件 ----------
				if (type === 'file') {
					const safeUrl = escapeHtml(content);
					const fileName = content.split('/').pop() || '文件';
					return `<a href="${safeUrl}" target="_blank" class="admin-msg-file"><span style="font-size:18px;">📄</span><span style="word-break:break-all;font-size:13px;">${escapeHtml(fileName)}</span></a>`;
				}

				// ---------- 卡片 / 合并转发 ----------
				if (type === 'card' || type === 'history') {
					try {
						const payload = typeof content === 'string' ? JSON.parse(content) : content;

						if (type === 'card') {
							const thumbHtml = payload.thumbUrl ?
								`<img src="${escapeHtml(payload.thumbUrl)}" class="admin-msg-card-thumb">` :
								'';
							
							// 兼容提取资源类型角标
							const resType = payload.resType || payload.appName || '资源';
							const finalBadgeText = (resType === '内部资源' || resType === '来自外部应用') ? '资源' : resType;
							const typeBadgeHtml = `<span style="font-size:10px; background:#E7F8EE; color:#07C160; padding:1px 4px; border-radius:4px; margin-right:4px; vertical-align:middle; flex-shrink:0; font-weight:normal;">${escapeHtml(finalBadgeText)}</span>`;

							return `<div class="admin-msg-card" onclick="if('${escapeHtml(payload.url || '')}') window.open('${escapeHtml(payload.url || '')}', '_blank')">
                    <div class="admin-msg-card-body">
                        <div class="admin-msg-card-info">
                            <div class="admin-msg-card-title">
                                ${typeBadgeHtml}
                                <span style="vertical-align:middle;">${escapeHtml(payload.title || '应用推荐')}</span>
                            </div>
                            <div class="admin-msg-card-desc">${escapeHtml(payload.desc || '')}</div>
                        </div>
                        ${thumbHtml}
                    </div>
                    <div class="admin-msg-card-footer">${escapeHtml(resType)}卡片</div>
                </div>`;
						}

						if (type === 'history') {
							const items = payload.items || [];
							const itemsHtml = items.slice(0, 4).map(it => {
								let preview = it.text || '';
								if (it.type === 'image') preview = '[图片]';
								else if (it.type === 'video') preview = '[视频]';
								else if (it.type === 'voice') preview = '[语音]';
								else if (it.type === 'file') preview = '[文件]';
								else if (it.type === 'history') preview = '[聊天记录]';
								return `<div class="qq-history-item"><span class="qq-history-item-name">${escapeHtml(it.from || '')}: </span><span class="qq-history-item-content">${escapeHtml(preview)}</span></div>`;
							}).join('');
							const encodedPayload = encodeURIComponent(JSON.stringify(payload));
							return `<div class="qq-history-card" data-payload="${encodedPayload}" onclick="event.stopPropagation();adminOpenHistoryModal(this.getAttribute('data-payload'),event)">
                    <div class="qq-history-title">${escapeHtml(payload.title || '群聊的聊天记录')}</div>
                    <div class="qq-history-list">${itemsHtml}</div>
                    <div class="qq-history-footer">查看 ${items.length} 条转发消息</div>
                </div>`;
						}
					} catch (e) {
						// JSON 解析失败，回退显示原文
						return `<span style="color:#FF4D4F;font-size:12px;">[解析失败] </span>${escapeHtml(typeof content === 'string' ? content : JSON.stringify(content))}`;
					}
				}

				// ---------- 文本（默认，含 @全体成员 高亮）----------
				let html = escapeHtml(content);
				if (type === 'text' && typeof content === 'string') {
					html = content.replace(/\n/g, '<br>');
					const urlRegex = /(https?:\/\/[^\s<]+[^<.,:;"')\]\s])/g;
					html = html.replace(urlRegex,
						'<a href="$1" target="_blank" style="color: #07C160; text-decoration: none;">$1</a>');
					if (html.includes('@全体成员')) {
						html = html.replace(/@全体成员/g,
							'<span style="background:rgba(255,140,0,0.1);color:#FF8C00;padding:0 4px;border-radius:4px;">@全体成员</span>'
							);
					}
				}
				return `<div style="animation: fadeInUp 0.3s ease-out; word-break: break-word;">${html}</div>`;
			}
			// ==================== 合并转发查看弹窗（admin端）====================
			window.adminOpenHistoryModal = function(payloadStr, event) {
				if (event) { event.stopPropagation(); }
				const payload = JSON.parse(decodeURIComponent(payloadStr));
				const items = payload.items || [];

				const itemsHtml = items.map(it => {
					let contentHtml = '';
					// 兼容不同的字段名
					const mediaUrl = it.url || it.content || '';
					const previewText = it.text || (typeof it.content === 'string' ? it.content : '') || '';
					const msgType = it.type || 'text';

					if (it.history_payload) {
						const nested = encodeURIComponent(JSON.stringify(it.history_payload));
						contentHtml = `<div class="qq-history-card" data-payload="${nested}" onclick="event.stopPropagation();adminOpenHistoryModal(this.getAttribute('data-payload'),event)" style="margin-top:4px; max-width: 220px; box-shadow: none; border: 1px solid #EBEBEB;">
                <div class="qq-history-title" style="font-size:13px; margin-bottom: 4px;">${escapeHtml(it.history_payload.title || '群聊的聊天记录')}</div>
                <div class="qq-history-footer" style="padding-top:4px;">查看 ${(it.history_payload.items||[]).length} 条转发消息</div>
            </div>`;
					} else if (msgType === 'image' && mediaUrl) {
						contentHtml =
							`<img src="${escapeHtml(mediaUrl)}" style="max-width:140px;max-height:180px;border-radius:8px;display:block;margin-top:4px;cursor:zoom-in;object-fit:cover;border:1px solid rgba(0,0,0,0.05);" onclick="window.open('${escapeHtml(mediaUrl)}','_blank')">`;
					} else if (msgType === 'video' && mediaUrl) {
						let vidUrl = mediaUrl;
						if (vidUrl && !vidUrl.startsWith('http') && !vidUrl.startsWith('data:') && !vidUrl.startsWith('/')) vidUrl = '/' + vidUrl;
						contentHtml =
							`<div style="position:relative; width: 140px; height: 180px; cursor:pointer; border-radius:8px; overflow:hidden; background:#1A1A1A; border:0.5px solid rgba(0,0,0,0.06); margin-top:4px;" onclick="previewVideo('${escapeHtml(vidUrl)}')">
								<video src="${escapeHtml(vidUrl)}" preload="metadata" style="width:100%; height:100%; object-fit:cover; display:block; opacity:0.85;"></video>
								<div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); width:36px; height:36px; background:rgba(0,0,0,0.4); border-radius:50%; display:flex; align-items:center; justify-content:center; pointer-events:none; border:2px solid rgba(255,255,255,0.7); backdrop-filter:blur(2px);">
									<div style="width:0; height:0; border-style:solid; border-width:6px 0 6px 10px; border-color:transparent transparent transparent #fff; margin-left:3px;"></div>
								</div>
							</div>`;
					} else if (msgType === 'voice' && mediaUrl) {
						contentHtml =
							`<audio src="${escapeHtml(mediaUrl)}" controls style="max-width:200px;height:36px;margin-top:4px;"></audio>`;
					} else if (msgType === 'file' && mediaUrl) {
						const fn = mediaUrl.split('/').pop() || '文件';
						contentHtml =
							`<a href="${escapeHtml(mediaUrl)}" target="_blank" style="display:flex;align-items:center;gap:6px;padding:8px 12px;background:#F5F5F5;border-radius:6px;text-decoration:none;color:#1A1A1A;margin-top:4px;border:1px solid rgba(0,0,0,0.05);"><span>📄</span><span style="word-break:break-all;font-size:13px;">${escapeHtml(fn)}</span></a>`;
					} else if (msgType === 'card') {
						let cardData = null;
						try {
							cardData = typeof it.content === 'string' ? JSON.parse(it.content) : it.content;
						} catch(e) {}
						if (cardData) {
							const resType = cardData.resType || cardData.appName || '资源';
							const finalBadgeText = (resType === '内部资源' || resType === '来自外部应用') ? '资源' : resType;
							const typeBadgeHtml = `<span style="font-size:10px; background:#E7F8EE; color:#07C160; padding:1px 4px; border-radius:4px; margin-right:4px; vertical-align:middle; flex-shrink:0; font-weight:normal;">${escapeHtml(finalBadgeText)}</span>`;
							const thumbHtml = cardData.thumbUrl ? `<img src="${escapeHtml(cardData.thumbUrl)}" class="admin-msg-card-thumb">` : '';
							contentHtml = `<div class="admin-msg-card" onclick="if('${escapeHtml(cardData.url || '')}') window.open('${escapeHtml(cardData.url || '')}', '_blank')" style="margin-top:4px;">
								<div class="admin-msg-card-body">
									<div class="admin-msg-card-info">
										<div class="admin-msg-card-title" style="display:flex; align-items:center;">${typeBadgeHtml}<span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${escapeHtml(cardData.title || '应用推荐')}</span></div>
										<div class="admin-msg-card-desc">${escapeHtml(cardData.desc || '')}</div>
									</div>
									${thumbHtml}
								</div>
								<div class="admin-msg-card-footer">${escapeHtml(resType)}卡片</div>
							</div>`;
						} else {
							contentHtml = `<span style="color:#999;font-size:13px;">[卡片消息解析失败]</span>`;
						}
					} else {
						// 普通文本，如果是 [图片] 之类的占位符（且缺少URL），稍微变灰
						let formattedText = escapeHtml(previewText).replace(/\n/g, '<br>');
						if (formattedText === '[图片]' || formattedText === '[视频]' || formattedText === '[语音]') {
							formattedText =
								`<span style="color:#999;font-size:13px;">${formattedText} (原数据丢失)</span>`;
						}
						contentHtml =
							`<span style="font-size:14px;color:#1A1A1A;line-height:1.5;word-break:break-word;display:inline-block;margin-top:2px;">${formattedText}</span>`;
					}

					return `<div style="display:flex;padding:12px 20px;gap:12px;align-items:flex-start;border-bottom:1px solid #F0F0F0;">
            <img src="${escapeHtml(it.avatar || '/user.jpg')}" onerror="this.src='/user.jpg'" style="width:36px;height:36px;border-radius:50%;object-fit:cover;flex-shrink:0;border:1px solid #F0F0F0;">
            <div style="flex:1;min-width:0;">
                <div style="font-size:12px;color:#888;margin-bottom:2px;display:flex;align-items:baseline;gap:8px;">
                    <span style="font-weight:500;">${escapeHtml(it.from || '用户')}</span>
                    ${it.time ? `<span style="font-size:11px;color:#C0C0C0;">${escapeHtml(it.time)}</span>` : ''}
                </div>
                <div>${contentHtml}</div>
            </div>
        </div>`;
				}).join('');

				
                if (!window.historyModalStack) window.historyModalStack = [];
                let modal = document.getElementById('adminHistoryModal');
				if (!modal) {
					modal = document.createElement('div');
					modal.id = 'adminHistoryModal';
					modal.style.cssText =
						'position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:9000;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(2px);';
					modal.innerHTML = `<div style="background:#F5F6FA;border-radius:8px;width:90%;max-width:520px;max-height:80vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.1);">
            <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 20px;background:#FFFFFF;border-bottom:1px solid #EBEBEB;flex-shrink:0;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <button id="adminHistoryModalBack" onclick="popHistoryModal()" style="display:none; background:none; border:none; font-size:14px; cursor:pointer; color:#1677FF; padding:0; display:flex; align-items:center;">
                        <svg viewBox="0 0 1024 1024" width="16" height="16" style="margin-right:2px"><path d="M724 218.3V141c0-6.7-7.7-10.4-12.9-6.3L260.3 486.8c-16.4 12.8-16.4 37.5 0 50.3l450.8 352.1c5.3 4.1 12.9 0.4 12.9-6.3v-77.3c0-4.9-2.3-9.6-6.1-12.6l-360-281 360-281.1c3.8-3 6.1-7.7 6.1-12.6z" fill="#1677FF"/></svg>返回
                    </button>
                    <span id="adminHistoryModalTitle" style="font-size:15px;font-weight:600;color:#000;"></span>
                </div>
                <button onclick="closeAllHistoryModals()" style="background:none;border:none;font-size:22px;cursor:pointer;color:#999;line-height:1;padding:0 4px;border-radius:4px;">&times;</button>
            </div>
            <div id="adminHistoryModalBody" style="flex:1;overflow-y:auto;background:#FFFFFF;padding:10px 0;"></div>
        </div>`;
					document.body.appendChild(modal);

                    window.popHistoryModal = function() {
                        if (window.historyModalStack.length > 1) {
                            window.historyModalStack.pop(); // 移除当前层
                            const prev = window.historyModalStack[window.historyModalStack.length - 1]; // 获取上一层
                            document.getElementById('adminHistoryModalTitle').textContent = prev.title;
                            document.getElementById('adminHistoryModalBody').innerHTML = prev.html;
                            document.getElementById('adminHistoryModalBack').style.display = window.historyModalStack.length > 1 ? 'flex' : 'none';
                        }
                    };

                    window.closeAllHistoryModals = function() {
                        document.getElementById('adminHistoryModal').style.display = 'none';
                        window.historyModalStack = [];
                    };
				}

                const titleText = payload.title || '聊天记录';
                window.historyModalStack.push({ title: titleText, html: itemsHtml });

				document.getElementById('adminHistoryModalTitle').textContent = titleText;
				document.getElementById('adminHistoryModalBody').innerHTML = itemsHtml;
                document.getElementById('adminHistoryModalBack').style.display = window.historyModalStack.length > 1 ? 'flex' : 'none';

				modal.style.display = 'flex';
				modal.onclick = function(e) {
					if (e.target === modal) window.closeAllHistoryModals();
				};
			};


			function handleImageSelection(input) {
				if (!input.files || input.files.length === 0) return;
				const file = input.files[0];

				const reader = new FileReader();
				reader.onload = function(e) {
					document.getElementById('imagePreview').src = e.target.result;
					document.getElementById('imageConfirmModal').classList.add('active');

					document.getElementById('confirmSendImageBtn').onclick = function() {
						const messageData = {
							group_id: currentSelectedGroupId,
							user_id: adminConfig.userId,
							user_nickname: adminConfig.nickname,
							type: 'image',
							content: e.target.result,
							is_admin: true
						};

						document.getElementById('imageConfirmModal').classList.remove('active');
						document.getElementById('confirmSendImageBtn').innerText = '确定发送';
						input.value = '';

						sendToAPI(messageData);
					};
				};
				reader.readAsDataURL(file);
			}

			// 添加假消息预加载
			function appendTempMessage(messageData) {
				const messagesList = document.getElementById('messagesList');
				if (messagesList.innerHTML.includes('chat-empty')) {
					messagesList.innerHTML = '';
				}

				const tempId = 'temp_' + Date.now();
				const timeStr = new Date().toLocaleString();
				
				// 直接复用统一的渲染函数来生成预览内容，这样不管发送什么类型都能正常显示
				let contentHtml = renderMessageContent(messageData);

				const tempHtml = `
            <div class="chat-msg-item is-admin temp-msg" id="${tempId}">
                <div class="chat-msg-info">
                    <span class="chat-msg-name">${messageData.user_nickname}</span>
                    <span>${timeStr}</span>
                </div>
                <div class="chat-msg-bubble-wrap" style="align-items: center;">
                    <!-- 旋转加载动画 -->
                    <div id="status_${tempId}" style="width: 14px; height: 14px; border: 2px solid rgba(7, 193, 96, 0.2); border-top-color: #07C160; border-radius: 50%; animation: spin 0.8s linear infinite; margin-right: 8px; flex-shrink: 0;"></div>
                    <div class="chat-msg-bubble" style="opacity: 0.8;">
                        ${contentHtml}
                    </div>
                </div>
            </div>`;

				messagesList.insertAdjacentHTML('beforeend', tempHtml);
				messagesList.scrollTop = messagesList.scrollHeight;
				return tempId;
			}

			// 创建隐藏的文件输入元素
			const hiddenFileInput = document.createElement('input');
			hiddenFileInput.type = 'file';
			hiddenFileInput.accept = 'image/*';
			hiddenFileInput.style.display = 'none';
			document.body.appendChild(hiddenFileInput);

			// 存储当前选择的媒体类型
			let currentMediaFileType = 'image';

			// 创建媒体确认发送模态框
			function createImageConfirmModal() {
				const modalHTML = `
                <div id="imageConfirmModal" class="modal">
                    <div class="modal-overlay"></div>
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title" id="confirmModalTitle">确认发送图片</h2>
                            <button class="close-btn" onclick="document.getElementById('imageConfirmModal').classList.remove('active')">×</button>
                        </div>
                        <div style="padding: 80px 20px 20px; flex: 1; overflow-y: auto; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                            <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                                <img id="imagePreview" src="" alt="图片预览" style="max-width: 100%; max-height: 300px; object-fit: contain; border-radius: 8px;">
                            </div>
                            <div id="confirmModalMessage" style="margin-bottom: 20px;">确定要发送这张图片吗？</div>
                            <div style="display: flex; gap: 12px; justify-content: center;">
                                <button class="btn btn-secondary" onclick="document.getElementById('imageConfirmModal').classList.remove('active')">取消</button>
                                <button id="confirmSendImageBtn" class="btn btn-primary">确定发送</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
				document.body.insertAdjacentHTML('beforeend', modalHTML);
			}

			// 初始化图片确认发送模态框
			createImageConfirmModal();

			// 处理文件选择
			hiddenFileInput.addEventListener('change', function(e) {
				if (!this.files || this.files.length === 0) return;

				const file = this.files[0];

				// 创建预览
				const previewElement = document.getElementById('imagePreview');
				const modal = document.getElementById('imageConfirmModal');
				const confirmBtn = document.getElementById('confirmSendImageBtn');
				const modalTitle = document.getElementById('confirmModalTitle');
				const modalMessage = document.getElementById('confirmModalMessage');

				// 使用FileReader仅用于预览，不用于上传
				const reader = new FileReader();
				reader.onload = function(e) {
					// 根据媒体类型设置预览和弹窗内容
					if (currentMediaFileType === 'image') {
						// 图片预览
						previewElement.style.display = 'block';
						previewElement.src = e.target.result;
						previewElement.alt = '图片预览';

						// 隐藏视频预览
						const videoPreview = document.getElementById('videoPreview');
						if (videoPreview) {
							videoPreview.style.display = 'none';
						}

						// 设置弹窗标题和内容
						modalTitle.textContent = '确认发送图片';
						modalMessage.textContent = '确定要发送这张图片吗？';
					} else if (currentMediaFileType === 'video') {
						// 视频预览
						previewElement.style.display = 'none';

						// 创建视频预览元素
						let videoPreview = document.getElementById('videoPreview');
						if (!videoPreview) {
							videoPreview = document.createElement('video');
							videoPreview.id = 'videoPreview';
							videoPreview.style.maxWidth = '100%';
							videoPreview.style.maxHeight = '300px';
							videoPreview.style.objectFit = 'contain';
							videoPreview.style.marginBottom = '20px';
							videoPreview.style.borderRadius = '8px';
							videoPreview.controls = true;
							previewElement.parentNode.insertBefore(videoPreview, previewElement.nextSibling);
						} else {
							videoPreview.style.display = 'block';
						}
						videoPreview.src = e.target.result;
						videoPreview.alt = '视频预览';

						// 设置弹窗标题和内容
						modalTitle.textContent = '确认发送视频';
						modalMessage.textContent = '确定要发送这段视频吗？';
					}

					// 显示确认弹窗
					modal.classList.add('active');

					// 绑定确认发送按钮的点击事件
					confirmBtn.onclick = function() {
						// 创建FormData对象，用于上传文件
						const formData = new FormData();
						formData.append('group_id', currentSelectedGroupId);
						formData.append('user_id', adminConfig.userId);
						formData.append('user_nickname', adminConfig.nickname);
						formData.append('user_avatar', adminConfig.avatar || '/user.jpg');
						formData.append('type', currentMediaFileType);
						formData.append('is_admin', true);
						formData.append('file', file);

						// 关闭模态框
						modal.classList.remove('active');

						// 发送媒体文件
						sendFileToAPI(formData);

						// 重置文件输入
						hiddenFileInput.value = '';
					};
				};
				reader.readAsDataURL(file);
			});

			function switchMessageType(type) {
				// 当用户点击"图片"或"视频"标签时，直接触发文件选择对话框，不改变布局
				if (type === 'image' || type === 'video') {
					if (!currentSelectedGroupId) {
						toast('请先选择群聊', 'warning');
						return;
					}
					// 根据类型设置文件输入的accept属性
					hiddenFileInput.accept = type === 'image' ? 'image/*' : 'video/*';
					// 存储当前选择的媒体类型
					currentMediaFileType = type;
					hiddenFileInput.click();
					return; // 直接返回，不执行后续的布局变化代码
				}

				// 对于其他类型（文本），正常处理布局变化
				currentMessageType = type;

				document.querySelectorAll('.chat-tab').forEach(tab => {
					tab.classList.remove('active');
					if (tab.getAttribute('onclick') && tab.getAttribute('onclick').includes(`('${type}')`)) {
						tab.classList.add('active');
					}
				});

				document.getElementById('messageInput').style.display = type === 'text' ? 'block' : 'none';
				document.getElementById('mediaUploadContainer').style.display = 'none'; // 始终隐藏图片上传容器
			}

			function sendToAPI(messageData) {
				console.log('sendToAPI called:', messageData);
				// 立即插一条假消息到屏幕
				const tempId = appendTempMessage(messageData);

				fetch('api/chat/send_message.php', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json'
						},
						body: JSON.stringify(messageData)
					})
					.then(response => {
						console.log('Response received:', response);
						return response.json();
					})
					.then(result => {
						console.log('Result received:', result);
						if (result.success) {
							console.log('Message sent successfully');
							document.getElementById('messageInput').value = '';
							const mediaUpload = document.getElementById('mediaUpload');
							if (mediaUpload) mediaUpload.value = '';
							const linkT = document.getElementById('linkTitle');
							if (linkT) linkT.value = '';
							const linkU = document.getElementById('linkUrl');
							if (linkU) linkU.value = '';

							// 发送成功，停止转圈并显示对号
							const statusEl = document.getElementById(`status_${tempId}`);
							if (statusEl) {
								statusEl.style.animation = 'none';
								statusEl.style.border = 'none';
								statusEl.innerHTML =
									'<span style="color:#07C160; font-size: 12px; margin-right: 4px;">✓</span>';

								// 3秒后自动隐藏成功状态
								setTimeout(() => {
									if (statusEl) {
										statusEl.style.display = 'none';
									}
								}, 3000);
							}

							// 显示发送成功的toast提示
							console.log('Calling toast for success');
							toast('消息发送成功', 'success');

							// 恢复图片亮度
							const tempItem = document.getElementById(tempId);
							if (tempItem) {
								const img = tempItem.querySelector('img');
								if (img) {
									img.style.filter = 'none';
									img.style.opacity = '1';
								}
							}

							// 后台静默刷新列表。因为列表里会有真实消息，必须防止重复显示。
							// 原先 loadGroupMessages 会盲目 append。
							// 解决方法：在这里不调用 loadGroupMessages(增量)，而是直接发起全量拉取并替换！
							// 或者我们把 temp 消息标记为 "已经被真实消息替换"。
							// 最优雅的方法：静默增量拉取，如果拉取到了刚才发的消息，就把 temp 节点删掉。

							// 不需要重新加载整个消息列表，因为我们已经在发送前添加了临时消息
							// 临时消息会在用户看到后自动被真实消息替换
							// 这样可以显著提高发送速度，特别是在消息数量较多时

							// 只需要更新最后一条消息的时间戳
							if (result.message && result.message.timestamp) {
								lastMessageTimestamps[currentSelectedGroupId] = result.message.timestamp;
							}

							// 确保临时消息的ID被替换为真实消息的ID
							setTimeout(() => {
								const tempMsgItem = document.getElementById(tempId);
								if (tempMsgItem && result.message && result.message.id) {
									tempMsgItem.setAttribute('data-message-id', result.message.id);
								}
							}, 100);

						} else {
							console.log('Message sent failed:', result.message);
							const statusEl = document.getElementById(`status_${tempId}`);
							if (statusEl) {
								statusEl.style.animation = 'none';
								statusEl.style.border = 'none';
								statusEl.innerHTML =
									'<span style="color:#FF4D4F; font-size:12px; margin-right: 4px; cursor:pointer;" title="发送失败: ' +
									(result.message || '未知错误') + '">❗️</span>';
							}
							toast('发送消息失败: ' + (result.message || '未知错误'), 'error');
						}
					})
					.catch(error => {
						console.error('发送消息失败:', error);
						const statusEl = document.getElementById(`status_${tempId}`);
						if (statusEl) {
							statusEl.style.animation = 'none';
							statusEl.style.border = 'none';
							statusEl.innerHTML =
								'<span style="color:#FF4D4F; font-size:12px; margin-right: 4px; cursor:pointer;" title="网络错误">❗️</span>';
						}

						// 显示网络错误的toast提示
						toast('发送消息失败，请检查网络连接', 'error');
					});
			}

			// 存储当前的上传控制器，用于取消上传
			let currentUploadController = null;

			// 存储当前正在进行的消息加载请求，用于防抖和并发控制
			let currentMessageRequest = null;

			// 发送文件到API
			function sendFileToAPI(formData) {
				// 创建临时消息数据用于显示
				const tempMessageData = {
					group_id: currentSelectedGroupId,
					user_id: adminConfig.userId,
					user_nickname: adminConfig.nickname,
					type: currentMediaFileType,
					content: '', // 内容会在发送成功后更新
					is_admin: true
				};

				// 立即插一条假消息到屏幕
				const tempId = appendTempMessage(tempMessageData);

				// 获取临时消息元素
				const tempMsgItem = document.getElementById(tempId);
				if (tempMsgItem) {
					// 修改临时消息内容，添加视频预览和进度条
					const bubbleWrap = tempMsgItem.querySelector('.chat-msg-bubble-wrap');
					if (bubbleWrap) {
						const bubble = bubbleWrap.querySelector('.chat-msg-bubble');
						if (bubble) {
							// 创建视频预览和进度条
							let previewContent = '';
							if (currentMediaFileType === 'image') {
								previewContent =
									'<img src="" alt="图片" style="max-width: 100%; border-radius: 8px; opacity: 0.8;">';
							} else if (currentMediaFileType === 'video') {
								previewContent =
									'<video src="" controls style="max-width: 100%; border-radius: 8px; opacity: 0.8;"></video>';
							}

							bubble.innerHTML = `
                            ${previewContent}
                            <div style="margin-top: 10px; font-size: 12px; color: #1677FF;">
                                上传中... <span id="uploadProgress_${tempId}">0%</span>
                            </div>
                            <div style="margin-top: 5px; height: 4px; background-color: #E0E0E0; border-radius: 2px; overflow: hidden;">
                                <div id="uploadProgressBar_${tempId}" style="height: 100%; width: 0%; background-color: #1677FF; transition: width 0.3s ease;"></div>
                            </div>
                            <div style="margin-top: 8px; text-align: right;">
                                <span style="color:#1677FF; font-size: 12px; cursor:pointer;" onclick="cancelUpload()">取消</span>
                            </div>
                        `;
						}
					}
				}

				// 创建XMLHttpRequest用于上传文件（支持进度条）
				const xhr = new XMLHttpRequest();
				currentUploadController = xhr; // 存储xhr用于取消上传

				// 监听上传进度
				xhr.upload.addEventListener('progress', function(e) {
					if (e.lengthComputable) {
						const percentComplete = Math.round((e.loaded / e.total) * 100);
						// 更新进度显示
						const progressEl = document.getElementById(`uploadProgress_${tempId}`);
						const progressBarEl = document.getElementById(`uploadProgressBar_${tempId}`);
						if (progressEl) progressEl.textContent = `${percentComplete}%`;
						if (progressBarEl) progressBarEl.style.width = `${percentComplete}%`;
					}
				});

				// 监听请求完成
				xhr.addEventListener('load', function() {
					if (xhr.status === 200) {
						try {
							const result = JSON.parse(xhr.responseText);
							console.log('Result received:', result);
							if (result.success) {
								console.log('File sent successfully');

								// 发送成功，更新消息内容
								const tempMsgItem = document.getElementById(tempId);
								if (tempMsgItem) {
									const bubbleWrap = tempMsgItem.querySelector('.chat-msg-bubble-wrap');
									if (bubbleWrap) {
										const bubble = bubbleWrap.querySelector('.chat-msg-bubble');
										if (bubble) {
											// 更新为实际的消息内容
											bubble.innerHTML = renderMessageContent({
												type: currentMediaFileType,
												content: result.message.content
											});
										}

										// 发送成功，停止转圈并显示对号
										const statusEl = document.getElementById(`status_${tempId}`);
										if (statusEl) {
											statusEl.style.animation = 'none';
											statusEl.style.border = 'none';
											statusEl.innerHTML =
												'<span style="color:#07C160; font-size: 12px; margin-right: 4px;">✓</span>';

											// 3秒后自动隐藏成功状态
											setTimeout(() => {
												if (statusEl) {
													statusEl.style.display = 'none';
												}
											}, 3000);
										}
									}
								}

								// 显示发送成功的toast提示
								toast('文件发送成功', 'success');

								// 不需要重新加载整个消息列表，因为我们已经在发送前添加了临时消息
								// 临时消息会在用户看到后自动被真实消息替换
								// 这样可以显著提高发送速度，特别是在消息数量较多时

								// 只需要更新最后一条消息的时间戳
								if (result.message && result.message.timestamp) {
									lastMessageTimestamps[currentSelectedGroupId] = result.message.timestamp;
								}

								// 确保临时消息的ID被替换为真实消息的ID
								setTimeout(() => {
									const tempMsgItem = document.getElementById(tempId);
									if (tempMsgItem && result.message && result.message.id) {
										tempMsgItem.setAttribute('data-message-id', result.message.id);
									}
								}, 100);

							} else {
								console.log('File sent failed:', result.message);
								const tempMsgItem = document.getElementById(tempId);
								if (tempMsgItem) {
									const bubbleWrap = tempMsgItem.querySelector('.chat-msg-bubble-wrap');
									if (bubbleWrap) {
										const bubble = bubbleWrap.querySelector('.chat-msg-bubble');
										if (bubble) {
											bubble.innerHTML =
												`<div style="color: #FF4D4F; font-size: 12px;">发送失败: ${result.message || '未知错误'}</div>`;
										}
									}
								}
								toast('发送文件失败: ' + (result.message || '未知错误'), 'error');
							}
						} catch (error) {
							console.error('解析响应失败:', error);
							const tempMsgItem = document.getElementById(tempId);
							if (tempMsgItem) {
								const bubbleWrap = tempMsgItem.querySelector('.chat-msg-bubble-wrap');
								if (bubbleWrap) {
									const bubble = bubbleWrap.querySelector('.chat-msg-bubble');
									if (bubble) {
										bubble.innerHTML =
											'<div style="color: #FF4D4F; font-size: 12px;">发送失败: 解析响应失败</div>';
									}
								}
							}
							toast('发送文件失败，请检查网络连接', 'error');
						}
					} else {
						console.error('请求失败:', xhr.status);
						const tempMsgItem = document.getElementById(tempId);
						if (tempMsgItem) {
							const bubbleWrap = tempMsgItem.querySelector('.chat-msg-bubble-wrap');
							if (bubbleWrap) {
								const bubble = bubbleWrap.querySelector('.chat-msg-bubble');
								if (bubble) {
									bubble.innerHTML =
										`<div style="color: #FF4D4F; font-size: 12px;">发送失败: 网络错误 (${xhr.status})</div>`;
								}
							}
						}
						toast('发送文件失败，请检查网络连接', 'error');
					}
					currentUploadController = null;
				});

				// 监听请求错误
				xhr.addEventListener('error', function() {
					console.error('请求错误');
					const tempMsgItem = document.getElementById(tempId);
					if (tempMsgItem) {
						const bubbleWrap = tempMsgItem.querySelector('.chat-msg-bubble-wrap');
						if (bubbleWrap) {
							const bubble = bubbleWrap.querySelector('.chat-msg-bubble');
							if (bubble) {
								bubble.innerHTML = '<div style="color: #FF4D4F; font-size: 12px;">发送失败: 网络错误</div>';
							}
						}
					}
					toast('发送文件失败，请检查网络连接', 'error');
					currentUploadController = null;
				});

				// 监听请求中止
				xhr.addEventListener('abort', function() {
					console.log('请求已中止');
					const tempMsgItem = document.getElementById(tempId);
					if (tempMsgItem) {
						const bubbleWrap = tempMsgItem.querySelector('.chat-msg-bubble-wrap');
						if (bubbleWrap) {
							const bubble = bubbleWrap.querySelector('.chat-msg-bubble');
							if (bubble) {
								bubble.innerHTML = '<div style="color: #999; font-size: 12px;">上传已取消</div>';
							}
						}
					}
					toast('上传已取消', 'info');
					currentUploadController = null;
				});

				// 发送请求
				xhr.open('POST', 'api/chat/send_message.php');
				xhr.send(formData);
			}

			// 取消上传
			function cancelUpload() {
				if (currentUploadController) {
					// 检查currentUploadController是AbortController还是XMLHttpRequest
					if (currentUploadController instanceof XMLHttpRequest) {
						currentUploadController.abort();
					} else if (currentUploadController.abort) {
						currentUploadController.abort();
					}
					currentUploadController = null;
				}
			}

			function sendMessage() {
				if (!currentSelectedGroupId) {
					toast('请先选择群聊', 'warning');
					return;
				}

				let messageData = {
					group_id: currentSelectedGroupId,
					user_id: 'admin',
					user_nickname: '管理员',
					type: currentMessageType,
					is_admin: true
				};

				if (currentMessageType === 'text') {
					const textContent = document.getElementById('messageInput').value.trim();
					if (!textContent) {
						toast('请输入消息内容', 'warning');
						return;
					}
					messageData.content = textContent;
					sendToAPI(messageData);


				} else if (currentMessageType === 'image') {
					const mediaUpload = document.getElementById('mediaUpload');
					if (!mediaUpload || !mediaUpload.files || mediaUpload.files.length === 0) {
						toast('请选择要上传的图片', 'warning');
						return;
					}

					const file = mediaUpload.files[0];
					const reader = new FileReader();
					reader.onload = function(e) {
						messageData.content = e.target.result;
						sendToAPI(messageData);
					};
					reader.readAsDataURL(file);
				}
			}

			function previewVideo(src) {
				let videoModal = document.getElementById('videoModal');
				if (!videoModal) {
					videoModal = document.createElement('div');
					videoModal.id = 'videoModal';
					videoModal.className = 'modal';
					videoModal.style.cssText = 'z-index: 10000; background: rgba(0,0,0,0.9);';
					videoModal.innerHTML = `
						<div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; justify-content: center; align-items: center;" onclick="closeVideoModal()">
							<video id="lightboxVideo" controls autoplay style="max-width: 90vw; max-height: 90vh; border-radius: 4px; box-shadow: 0 8px 32px rgba(0,0,0,0.3); outline: none;" onclick="event.stopPropagation()"></video>
							<button style="position: absolute; top: 20px; right: 20px; background: rgba(255,255,255,0.2); border: none; color: white; font-size: 24px; width: 40px; height: 40px; border-radius: 50%; cursor: pointer;" onclick="closeVideoModal()">&times;</button>
						</div>
					`;
					document.body.appendChild(videoModal);
				}
				const videoEl = document.getElementById('lightboxVideo');
				videoEl.src = src;
				videoModal.classList.add('active');
			}

			function closeVideoModal() {
				const videoModal = document.getElementById('videoModal');
				if (videoModal) {
					videoModal.classList.remove('active');
					const videoEl = document.getElementById('lightboxVideo');
					if (videoEl) {
						videoEl.pause();
						videoEl.src = '';
					}
				}
			}

			function previewImage(src) {
				document.getElementById('lightboxImage').src = src;
				document.getElementById('lightboxModal').classList.add('active');
			}

			// 刷新消息
			function refreshMessages() {
				if (currentSelectedGroupId) {
					// 手动刷新时使用增量加载
					loadGroupMessages(currentSelectedGroupId, false);
				}
			}

			// 撤回单条消息
			function withdrawMessage(messageId, groupId) {
				console.log('撤回按钮点击', {
					messageId,
					groupId
				});

				if (messageId === 'unknown') {
					toast('无法撤回未知ID的消息', 'error');
					return;
				}

				if (confirm('确定要撤回这条消息吗？')) {
					// 检查是否存在该消息项
					const messageItem = document.querySelector(`[data-message-id="${messageId}"]`);
					if (!messageItem) {
						console.error('未找到该消息项', messageId);
						toast('未找到该消息', 'error');
						return;
					}

					// 直接在前端隐藏消息，不依赖API
					// 替换为撤回提示
					messageItem.innerHTML = `
                    <div style="max-width: 70%;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                            <span style="font-size: 12px; font-weight: 600; color: #07C160;">管理员</span>
                            <span style="font-size: 10px; color: #999999;">${new Date().toLocaleString()}</span>
                        </div>
                        <div style="background-color: #F5F7FA; padding: 12px; border-radius: 8px; border: 1px solid #E5E7EB;">
                            <span style="font-size: 12px; color: #999999; font-style: italic;">[消息已撤回]</span>
                        </div>
                    </div>
                `;

					// 发送API请求，用于在后端实际撤回消息
					fetch('api/chat/withdraw_message.php', {
							method: 'POST',
							headers: {
								'Content-Type': 'application/json'
							},
							body: JSON.stringify({
								message_id: messageId,
								group_id: groupId
							})
						})
						.then(response => response.json())
						.then(result => {
							if (!result.success) {
								console.error('撤回消息失败:', result.message);
								toast('撤回失败: ' + result.message, 'error');
							}
						})
						.catch(error => {
							console.error('撤回消息失败:', error);
							// 不显示错误提示，因为前端已经更新了UI
							// alert('撤回失败，请检查网络连接');
						});
				}
			}

			// 撤回全部消息
			function withdrawAllMessages() {
				if (!currentSelectedGroupId) {
					toast('请先选择群聊', 'warning');
					return;
				}

				if (confirm('确定要撤回当前群聊的所有消息吗？此操作不可恢复。')) {
					fetch('api/chat/withdraw_all_messages.php', {
							method: 'POST',
							headers: {
								'Content-Type': 'application/json'
							},
							body: JSON.stringify({
								group_id: currentSelectedGroupId
							})
						})
						.then(response => response.json())
						.then(result => {
							if (result.success) {
								// 清空消息列表，显示撤回提示
								const messagesList = document.getElementById('messagesList');
								messagesList.innerHTML = `
                            <div style="text-align: center; margin: 20px 0;">
                                <div style="display: inline-block; max-width: 70%;">
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px; justify-content: center;">
                                        <span style="font-size: 12px; font-weight: 600; color: #07C160;">管理员</span>
                                        <span style="font-size: 10px; color: #999999;">${new Date().toLocaleString()}</span>
                                    </div>
                                    <div style="background-color: #F5F7FA; padding: 12px; border-radius: 8px; border: 1px solid #E5E7EB;">
                                        <span style="font-size: 12px; color: #999999; font-style: italic;">[管理员撤回了全部消息]</span>
                                    </div>
                                </div>
                            </div>
                        `;
							} else {
								toast('撤回失败: ' + result.message, 'error');
							}
						})
						.catch(error => {
							console.error('撤回全部消息失败:', error);
							toast('撤回失败，请检查网络连接', 'error');
						});
				}
			}

			// 设置激活菜单项
			function setActiveMenuItem(activeFunction) {
				// 移除所有菜单项的active类
				document.querySelectorAll('.menu-item').forEach(item => {
					item.classList.remove('active');
				});

				// 根据函数名设置对应的菜单项为active
				const menuItems = document.querySelectorAll('.menu-item');
				menuItems.forEach(item => {
					const onclick = item.getAttribute('onclick');
					if (onclick && onclick.includes(activeFunction)) {
						item.classList.add('active');
					}
				});

				// 清除所有定时器，防止切换页面后仍有定时器运行
				if (groupInfoRefreshInterval) {
					clearInterval(groupInfoRefreshInterval);
					groupInfoRefreshInterval = null;
				}
				if (groupDetailRefreshInterval) {
					clearInterval(groupDetailRefreshInterval);
					groupDetailRefreshInterval = null;
				}
				if (messagesRefreshInterval) {
					clearInterval(messagesRefreshInterval);
					messagesRefreshInterval = null;
				}
			}

			// 过滤群聊
			function filterGroups(searchTerm) {
				if (!searchTerm) {
					loadGroupsForChat();
					return;
				}

				const filteredGroups = chatGroups.filter(group =>
					group.name.toLowerCase().includes(searchTerm.toLowerCase())
				);

				const groupList = document.getElementById('groupList');
				if (filteredGroups.length === 0) {
					groupList.innerHTML = '<div style="text-align: center; color: #999999; padding: 20px;">未找到匹配的群聊</div>';
					return;
				}

				groupList.innerHTML = filteredGroups.map(group => {
					const unreadCount = unreadMessageCounts[group.id] || 0;
					return `
                <div class="group-card" onclick="selectGroupForChat('${escapeAttr(group.id)}', '${escapeAttr(group.name)}', ${group.members?.length || 0})" style="cursor: pointer; margin: 0 0 8px 0;">
                    <img src="${group.avatar || '/user.jpg'}" alt="${group.name}" class="group-avatar" style="width: 48px; height: 48px;">
                    <div class="group-info">
                        <div class="group-name" style="font-size: 14px; margin-bottom: 2px; display: flex; justify-content: space-between; align-items: center;">
                            <span>${group.name}</span>
                            ${unreadCount > 0 ? `<span style="background-color: #FF3B30; color: white; font-size: 11px; padding: 2px 6px; border-radius: 10px; font-weight: 600;">${unreadCount}</span>` : ''}
                        </div>
                        <div class="group-meta" style="font-size: 12px; gap: 8px;">
                            <span class="group-meta-item">成员: ${group.members?.length || 0}</span>
                        </div>
                    </div>
                </div>
            `;
				}).join('');
			}

			// 全局定时器变量
			let groupInfoRefreshInterval = null;
			let groupDetailRefreshInterval = null;
			let messagesRefreshInterval = null;
			let globalMessagesCheckInterval = null;

			// 管理端消息提示音相关
			let canPlayAudio = false;

			// 预加载提示音
			const adminMessageAudio = new Audio();
			adminMessageAudio.src = window.location.origin + '/mp3/xm3143.mp3';
			adminMessageAudio.volume = 0.5;

			// 监听音频加载事件
			adminMessageAudio.addEventListener('loadeddata', () => {
				console.log('管理端提示音已加载');
			});

			// 监听音频加载错误事件
			adminMessageAudio.addEventListener('error', (error) => {
				console.error('管理端提示音加载失败:', error);
			});

			// 监听用户交互事件，获得音频播放权限
			function enableAdminAudioPlayback() {
				canPlayAudio = true;
				// 移除事件监听器，避免重复执行
				document.removeEventListener('click', enableAdminAudioPlayback);
				document.removeEventListener('touchstart', enableAdminAudioPlayback);
				document.removeEventListener('keydown', enableAdminAudioPlayback);
				console.log('管理端音频播放已启用');
			}

			// 添加事件监听器，等待用户交互
			document.addEventListener('click', enableAdminAudioPlayback, {
				once: true
			});
			document.addEventListener('touchstart', enableAdminAudioPlayback, {
				once: true
			});
			document.addEventListener('keydown', enableAdminAudioPlayback, {
				once: true
			});

			// 播放新消息提示音
			function playAdminNewMessageSound() {
				console.log('尝试播放管理端新消息提示音，canPlayAudio:', canPlayAudio);
				// 只有在用户交互后才允许播放音频
				if (canPlayAudio) {
					adminMessageAudio.currentTime = 0;
					adminMessageAudio.play().then(() => {
						console.log('管理端新消息提示音播放成功');
					}).catch(error => {
						console.error('无法播放管理端提示音:', error);
					});
				}
			}

			// 存储每个群聊的最后消息时间戳和未读消息数
			let lastMessageTimestamps = {};
			let unreadMessageCounts = {};

			// 页面加载完成后初始化
			window.onload = function() {
				init();
			};

			// 加载群聊信息页面
			function loadGroupInfo() {
				setActiveMenuItem('loadGroupInfo');
				startGlobalMessagesCheck();
				const mainContent = domCache.get('mainContent');
				if (!mainContent) return;

				// 尝试从缓存获取群聊数据
				const cachedGroups = apiCache.get('groups');

				// 根据是否有缓存数据，决定显示内容
				let groupInfoListContent =
					'<div style="grid-column: 1/-1; text-align: center; color: #999; padding: 40px 0;">加载中...</div>';
				if (cachedGroups) {
					// 如果有缓存数据，先构建群聊卡片HTML
					if (cachedGroups.length === 0) {
						groupInfoListContent =
							'<div style="grid-column: 1/-1; text-align: center; color: #999; padding: 40px 0;">暂无群聊</div>';
					} else {
						groupInfoListContent = cachedGroups.map(group => {
							const days = Math.floor((new Date() - new Date(group.created_at)) / 86400000);
							const unread = unreadMessageCounts[group.id] || 0;
							return `
                <div class="group-info-card" onclick="openGroupDetail('${escapeAttr(group.id)}')">
                    <div class="group-info-header">
                        <img src="${group.avatar || '/user.jpg'}" class="group-info-avatar">
                        <div style="flex: 1; min-width: 0;">
                            <div class="group-info-name">${group.name} ${unread>0 ? `<span class="tag tag-danger">${unread}条未读</span>`:''}</div>
                            <div class="group-info-desc">${group.desc || '暂无介绍'}</div>
                        </div>
                    </div>
                    <div class="group-info-stats">
                        <div class="stat-item"><div class="stat-value">${group.members?.length || 0}</div><div class="stat-label">总成员</div></div>
                        <div class="stat-item"><div class="stat-value">${group.online_count || 0}</div><div class="stat-label">在线数</div></div>
                        <div class="stat-item"><div class="stat-value">${group.today_active_users || 0}</div><div class="stat-label">今日活跃</div></div>
                        <div class="stat-item"><div class="stat-value">${group.today_new_members || 0}</div><div class="stat-label">今日新加入</div></div>
                        <div class="stat-item"><div class="stat-value">${group.yesterday_new_members || 0}</div><div class="stat-label">昨日新加入</div></div>
                    </div>

                </div>`;
						}).join('');
					}
				}

				mainContent.innerHTML = `
        <div class="card" style=" border: none; padding: 32px !important; background: transparent !important;">
            <div style="margin-bottom: 24px;">
                <h3 class="card-title" style="font-size: 20px !important;">运营分析与群聊信息</h3>
                <div style="font-size: 13px; color: #999; margin-top: 4px;">查看和管理所有群聊的详细数据与成员状态</div>
            </div>
            <div id="groupInfoList" class="group-info-grid">
                ${groupInfoListContent}
            </div>
        </div>
    `;
				if (groupInfoRefreshInterval) clearInterval(groupInfoRefreshInterval);

				function refreshGroupInfoData() {
					fetch('api/admin/groups.php').then(r => r.json()).then(groups => {
						if (Array.isArray(groups)) {
							apiCache.set('groups', groups);
							renderGroupInfoList(groups);
						}
					}).catch(e => console.error(e));
				}

				function renderGroupInfoList(groups) {
					const list = domCache.get('groupInfoList');
					if (!list) return;

					if (groups.length === 0) {
						list.innerHTML =
							'<div style="grid-column: 1/-1; text-align: center; color: #999; padding: 40px 0;">暂无群聊</div>';
						return;
					}

					const groupHtml = groups.map(group => {
						const days = Math.floor((new Date() - new Date(group.created_at)) / 86400000);
						const unread = unreadMessageCounts[group.id] || 0;
						return `
            <div class="group-info-card" onclick="openGroupDetail('${escapeAttr(group.id)}')">
                <div class="group-info-header">
                    <img src="${group.avatar || '/user.jpg'}" class="group-info-avatar">
                    <div style="flex: 1; min-width: 0;">
                        <div class="group-info-name">${group.name} ${unread>0 ? `<span class="tag tag-danger">${unread}条未读</span>`:''}</div>
                        <div class="group-info-desc">${group.desc || '暂无介绍'}</div>
                    </div>
                </div>
                <div class="group-info-stats">
                    <div class="stat-item"><div class="stat-value">${group.members?.length || 0}</div><div class="stat-label">总成员</div></div>
                    <div class="stat-item"><div class="stat-value">${group.online_count || 0}</div><div class="stat-label">在线数</div></div>
                    <div class="stat-item"><div class="stat-value">${group.today_active_users || 0}</div><div class="stat-label">今日活跃</div></div>
                    <div class="stat-item"><div class="stat-value">${group.today_new_members || 0}</div><div class="stat-label">今日新加入</div></div>
                    <div class="stat-item"><div class="stat-value">${group.yesterday_new_members || 0}</div><div class="stat-label">昨日新加入</div></div>
                </div>

            </div>`;
					}).join('');

					// 使用requestAnimationFrame优化DOM更新
					requestAnimationFrame(() => {
						list.innerHTML = groupHtml;
					});
				}

				refreshGroupInfoData();
				groupInfoRefreshInterval = setInterval(refreshGroupInfoData, 10000);
			}

			// 打开设置
			function openSettings() {
				toast('设置功能开发中', 'info');
			}

			// 打开创建群聊模态框
			function openCreateGroupModal() {
				document.getElementById('createGroupModal').classList.add('active');
			}

			// 关闭创建群聊模态框
			function closeCreateGroupModal() {
				document.getElementById('createGroupModal').classList.remove('active');
			}

			// 关闭群聊详情模态框
			function closeGroupDetailModal() {
				document.getElementById('groupDetailModal').classList.remove('active');

				// 清除群聊详情刷新定时器
				if (groupDetailRefreshInterval) {
					clearInterval(groupDetailRefreshInterval);
					groupDetailRefreshInterval = null;
				}
			}

			// 关闭编辑群聊模态框
			function closeEditGroupModal() {
				document.getElementById('editGroupModal').classList.remove('active');
			}

			// 表单提交处理
			document.getElementById('createGroupForm').addEventListener('submit', function(e) {
				e.preventDefault();
				const formData = new FormData(this);

				fetch('api/admin/groups.php', {
						method: 'POST',
						body: formData
					})
					.then(response => response.json())
					.then(data => {
						if (data.success) {
							// 关闭模态框
							closeCreateGroupModal();
							// 重置表单
							this.reset();
							// 清除群聊数据缓存，确保重新加载最新数据
							apiCache.invalidate('groups');
							// 重新加载群聊列表
							loadGroups();

							// 如果当前在群聊消息页面，重新加载群聊列表
							const currentContent = document.getElementById('mainContent').innerHTML;
							if (currentContent.includes('chatGroupName') || currentContent.includes(
								'messagesList')) {
								loadGroupsForChat();
							}

							// 如果当前在群聊信息页面，刷新群聊信息列表
							if (currentContent.includes('groupInfoList')) {
								refreshGroupInfoData();
							}

							toast('群聊创建成功', 'success');
						} else {
							toast('创建失败: ' + (data.message || '未知错误'), 'error');
						}
					})
					.catch(error => {
						console.error('创建群聊失败:', error);
						toast('创建失败，请检查网络连接', 'error');
					});
			});

			// 编辑群聊表单提交处理
			document.getElementById('editGroupForm').addEventListener('submit', function(e) {
				e.preventDefault();
				const formData = new FormData(this);
				const groupId = document.getElementById('editGroupId').value;

				fetch(`api/admin/group.php?group_id=${groupId}`, {
						method: 'POST',
						body: formData
					})
					.then(response => response.json())
					.then(data => {
						if (data.success) {
							// 关闭模态框
							closeEditGroupModal();
							// 重置表单
							this.reset();
							// 清除群聊数据缓存，确保重新加载最新数据
							apiCache.invalidate('groups');
							// 重新加载群聊列表
							loadGroups();
							// 重新加载当前打开的群聊详情
							const modal = document.getElementById('groupDetailModal');
							if (modal.classList.contains('active')) {
								openGroupDetail(groupId);
							}
							toast('群聊更新成功', 'success');
						} else {
							toast('更新失败: ' + (data.message || '未知错误'), 'error');
						}
					})
					.catch(error => {
						console.error('更新群聊失败:', error);
						toast('更新失败，请检查网络连接', 'error');
					});
			});

			// 点击模态框外部关闭
			window.onclick = function(event) {
				const modals = document.querySelectorAll('.modal');
				modals.forEach(modal => {
					if (event.target == modal) {
						modal.classList.remove('active');
					}
				});
			}

			// 底部标签管理功能
			let currentGroupId = '';

			// 加载底部标签列表
			function loadQuickActions(groupId) {
				currentGroupId = groupId;
				fetch(`api/admin/group_quick_actions.php?group_id=${groupId}`)
					.then(res => res.json())
					.then(data => {
						if (data.success) {
							renderQuickActions(groupId, data.data);
						} else {
							console.error('加载底部标签失败:', data.message);
						}
					})
					.catch(error => {
						console.error('加载底部标签失败:', error);
					});
			}

			// 渲染底部标签列表
			function renderQuickActions(groupId, quickActions) {
				const container = document.getElementById(`quickActionsList_${groupId}`);

				if (quickActions.length === 0) {
					container.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-state-icon">📌</div>
                        <p class="empty-state-desc">暂无底部标签</p>
                    </div>
                `;
					return;
				}

				container.innerHTML = quickActions.map(action => `
                <div class="card" style="margin-bottom: 8px; padding: 12px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 20px;">${action.icon}</span>
                            <div>
                                <div style="font-weight: 500;">${action.title}</div>
                                <div style="font-size: 12px; color: var(--text-secondary);">${action.type}</div>
                            </div>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <span style="font-size: 12px; color: var(--text-secondary);">点击: ${action.click_count}</span>
                        </div>
                    </div>
                    <div style="font-size: 13px; color: var(--text-secondary); margin-bottom: 12px; word-break: break-all;">${action.url}</div>
                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                        <button class="btn btn-secondary btn-sm" onclick="openEditQuickActionModal('${groupId}', ${JSON.stringify(action).replace(/"/g, '&quot;')})"></button>
                        <button class="btn btn-danger btn-sm" onclick="deleteQuickAction('${groupId}', '${action.id}')">删除</button>
                    </div>
                </div>
            `).join('');
			}

			// 打开添加底部标签模态框
			function openAddQuickActionModal(groupId) {
				currentGroupId = groupId;
				document.getElementById('quickActionModalTitle').textContent = '添加底部标签';
				document.getElementById('quickActionForm').reset();
				document.getElementById('quickActionId').value = '';
				document.getElementById('quickActionGroupId').value = groupId;
				document.getElementById('quickActionModal').classList.add('active');
			}

			// 打开编辑底部标签模态框
			function openEditQuickActionModal(groupId, action) {
				currentGroupId = groupId;
				document.getElementById('quickActionModalTitle').textContent = '编辑底部标签';
				document.getElementById('quickActionGroupId').value = groupId;
				document.getElementById('quickActionId').value = action.id;
				document.getElementById('quickActionType').value = action.type;
				document.getElementById('quickActionTitle').value = action.title;
				document.getElementById('quickActionUrl').value = action.url;
				document.getElementById('quickActionModal').classList.add('active');
			}

			// 关闭底部标签模态框
			function closeQuickActionModal() {
				document.getElementById('quickActionModal').classList.remove('active');
			}

			// 提交底部标签表单
			function submitQuickActionForm() {
				const groupId = document.getElementById('quickActionGroupId').value;
				const actionId = document.getElementById('quickActionId').value;
				const type = document.getElementById('quickActionType').value;
				const title = document.getElementById('quickActionTitle').value;
				const url = document.getElementById('quickActionUrl').value;

				if (!type || !title || !url) {
					toast('请填写完整信息', 'warning');
					return;
				}

				const data = {
					type: type,
					title: title,
					url: url
				};

				const isEdit = !!actionId;
				const method = isEdit ? 'PUT' : 'POST';

				if (isEdit) {
					data.id = actionId;
				}

				fetch(`api/admin/group_quick_actions.php?group_id=${groupId}`, {
						method: method,
						headers: {
							'Content-Type': 'application/json'
						},
						body: JSON.stringify(data)
					})
					.then(res => res.json())
					.then(data => {
						if (data.success) {
							closeQuickActionModal();
							loadQuickActions(groupId);
							toast(isEdit ? '标签更新成功' : '标签添加成功', 'success');
						} else {
							toast(data.message, 'error');
						}
					})
					.catch(error => {
						console.error('操作失败:', error);
						toast('操作失败，请重试', 'error');
					});
			}

			// 违禁词管理功能
			let currentBannedWords = [];
			let currentSelectedGroupId = '';
			let currentSelectedGroupName = '';

			// 打开违禁词管理模态框
			function openBannedWordsModal(groupId = '') {
				currentBannedWords = [];
				currentSelectedGroupId = '';
				currentSelectedGroupName = '';

				// 加载群列表
				loadGroupsForBannedWords();

				// 显示模态框
				document.getElementById('bannedWordsModal').classList.add('active');
			}

			// 关闭违禁词管理模态框
			function closeBannedWordsModal() {
				document.getElementById('bannedWordsModal').classList.remove('active');
				// 重置状态
				document.getElementById('groupSelectSection').style.display = 'block';
				document.getElementById('bannedWordsSection').style.display = 'none';
			}

			// 加载群列表用于违禁词管理
			function loadGroupsForBannedWords() {
				const groupListContainer = document.getElementById('groupList');

				groupListContainer.innerHTML = '<div style="text-align: center; color: #999;">加载中...</div>';

				// 从API获取群列表
				fetch('api/admin/groups.php')
					.then(response => response.json())
					.then(groups => {
						if (!Array.isArray(groups)) {
							groups = [];
						}

						// 更新群列表
						if (groups.length === 0) {
							groupListContainer.innerHTML =
								'<div style="text-align: center; color: #999; padding: 20px;">暂无群聊</div>';
							return;
						}

						groupListContainer.innerHTML = groups.map(group => `
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background-color: white; border-radius: 6px; margin-bottom: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); cursor: pointer;" onclick="selectGroupForBannedWords('${escapeAttr(group.id)}', '${escapeAttr(group.name)}')">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <img src="${group.avatar || '/user.jpg'}" alt="${group.name}" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 2px solid #EBEBEB;">
                                <div>
                                    <div style="font-weight: 600; color: #1A1A1A;">${group.name}</div>
                                    <div style="font-size: 12px; color: #999;">成员: ${group.members?.length || 0}</div>
                                </div>
                            </div>
                            <button class="btn btn-primary btn-sm" onclick="event.stopPropagation(); selectGroupForBannedWords('${escapeAttr(group.id)}', '${escapeAttr(group.name)}')">选择</button>
                        </div>
                    `).join('');
					})
					.catch(error => {
						console.error('加载群列表失败:', error);
						groupListContainer.innerHTML =
							'<div style="text-align: center; color: #FF4D4F; padding: 20px;">加载群列表失败</div>';
					});
			}

			// 选择群聊
			function selectGroupForBannedWords(groupId, groupName) {
				currentSelectedGroupId = groupId;
				currentSelectedGroupName = groupName;

				// 加载该群的违禁词
				loadGroupBannedWords(groupId, groupName);
			}

			// 加载群违禁词
			function loadGroupBannedWords(groupId, groupName) {
				if (!groupId) {
					return;
				}

				// 设置当前选中的群ID和群名
				currentSelectedGroupId = groupId;
				currentSelectedGroupName = groupName || '未知群聊';

				const bannedWordsList = document.getElementById('bannedWordsList');
				bannedWordsList.innerHTML = '<div style="text-align: center; color: #999; padding: 20px;">加载中...</div>';

				// 调用API获取违禁词
				fetch(`api/admin/get_group_banned_words.php?group_id=${groupId}`)
					.then(response => response.json())
					.then(data => {
						if (data.success) {
							currentBannedWords = data.banned_words || [];
						} else {
							toast(data.message || '加载违禁词失败', 'error');
							currentBannedWords = [];
						}
						renderBannedWords();

						// 切换到违禁词管理界面
						document.getElementById('groupSelectSection').style.display = 'none';
						document.getElementById('bannedWordsSection').style.display = 'block';
					})
					.catch(error => {
						console.error('加载违禁词失败:', error);
						toast('加载违禁词失败', 'error');
						currentBannedWords = [];
						renderBannedWords();

						// 切换到违禁词管理界面
						document.getElementById('groupSelectSection').style.display = 'none';
						document.getElementById('bannedWordsSection').style.display = 'block';
					});
			}

			// 返回群选择界面
			function backToGroupSelect() {
				document.getElementById('groupSelectSection').style.display = 'block';
				document.getElementById('bannedWordsSection').style.display = 'none';
				currentSelectedGroupId = '';
				currentSelectedGroupName = '';
				currentBannedWords = [];
			}

			// 渲染违禁词列表
			function renderBannedWords() {
				const container = document.getElementById('bannedWordsList');

				if (currentBannedWords.length === 0) {
					container.innerHTML = '<div style="text-align: center; color: #999; padding: 20px;">暂无违禁词</div>';
					return;
				}

				container.innerHTML = currentBannedWords.map((word, index) => `
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background-color: white; border-radius: 6px; margin-bottom: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.04);">
                    <span>${index + 1}. ${word}</span>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeBannedWord(${index})">删除</button>
                </div>
            `).join('');
			}

			// 添加违禁词
			function addBannedWord() {
				const input = document.getElementById('newBannedWord');
				const word = input.value.trim();

				if (!word) {
					toast('请输入违禁词', 'warning');
					return;
				}

				if (currentBannedWords.includes(word)) {
					toast('该违禁词已存在', 'warning');
					return;
				}

				currentBannedWords.push(word);
				input.value = '';
				renderBannedWords();
			}

			// 移除违禁词
			function removeBannedWord(index) {
				currentBannedWords.splice(index, 1);
				renderBannedWords();
			}

			// 保存违禁词
			function saveBannedWords() {
				if (!currentSelectedGroupId) {
					toast('请先选择群聊', 'warning');
					return;
				}

				// 调用API保存违禁词
				fetch(`api/admin/set_group_banned_words.php?group_id=${currentSelectedGroupId}`, {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json'
						},
						body: JSON.stringify({
							banned_words: currentBannedWords
						})
					})
					.then(response => response.json())
					.then(data => {
						if (data.success) {
							toast('违禁词保存成功', 'success');
							closeBannedWordsModal();
							// 刷新群列表，更新违禁词数量
							loadGroups();
						} else {
							toast(data.message || '违禁词保存失败', 'error');
						}
					})
					.catch(error => {
						console.error('保存违禁词失败:', error);
						toast('违禁词保存失败', 'error');
					});
			}

			// 删除底部标签
			function deleteQuickAction(groupId, actionId) {
				if (confirm('确定要删除这个底部标签吗？')) {
					fetch(`api/admin/group_quick_actions.php?group_id=${groupId}&action_id=${actionId}`, {
							method: 'DELETE'
						})
						.then(res => res.json())
						.then(data => {
							if (data.success) {
								loadQuickActions(groupId);
								toast('标签删除成功', 'success');
							} else {
								toast(data.message, 'error');
							}
						})
						.catch(error => {
							console.error('删除失败:', error);
							toast('删除失败，请重试', 'error');
						});
				}
			}

			// 在openGroupDetail函数中添加加载底部标签列表的调用
			// 注意：需要修改openGroupDetail函数

			// 复制分享链接函数
			function copyShareLink(link) {
				if (navigator.clipboard && window.isSecureContext) {
					navigator.clipboard.writeText(link).then(() => toast('链接已复制到剪贴板', 'success')).catch(err => {
						console.error('复制失败:', err);
						toast('复制失败，请手动复制', 'error');
					});
				} else {
					let textArea = document.createElement('textarea');
					textArea.value = link;
					textArea.style.position = 'fixed';
					textArea.style.left = '-999999px';
					document.body.appendChild(textArea);
					textArea.focus();
					textArea.select();
					try {
						document.execCommand('copy');
						toast('链接已复制到剪贴板', 'success');
					} catch (err) {
						console.error('复制失败:', err);
						toast('浏览器拦截了复制，请手动复制', 'error');
					}
					textArea.remove();
				}
			}

			// 下载二维码函数
			function downloadQRCode(qrcodeId, groupName) {
				const qrcodeElement = document.getElementById(qrcodeId);
				let canvas = qrcodeElement.querySelector('canvas');
				let img = qrcodeElement.querySelector('img');

				if (canvas) {
					// 将canvas转换为图片
					const dataURL = canvas.toDataURL('image/png');

					// 创建下载链接
					const link = document.createElement('a');
					link.href = dataURL;
					link.download = `${groupName}_群聊二维码.png`;

					// 触发下载
					document.body.appendChild(link);
					link.click();
					document.body.removeChild(link);
				} else if (img) {
					// 如果是img元素，直接使用其src属性
					const link = document.createElement('a');
					link.href = img.src;
					link.download = `${groupName}_群聊二维码.png`;

					// 触发下载
					document.body.appendChild(link);
					link.click();
					document.body.removeChild(link);
				} else {
					toast('二维码尚未生成或生成失败，请稍后再试', 'error');
				}
			}

			// 管理端配置相关功能
			let adminConfig = {
				nickname: '管理员',
				avatar: '',
				userId: 'admin',
				wechat: ''
			};

			// 加载管理端配置数据
			function loadAdminConfigData() {
				const savedConfig = localStorage.getItem('adminConfig');
				if (savedConfig) {
					adminConfig = JSON.parse(savedConfig);
					adminConfig.avatar = '/user.jpg'; // 强制清除本地超大Base64缓存
				}
			}

			// 保存管理端配置
			function saveAdminConfig() {
				// 直接使用全局adminConfig对象，因为昵称和头像已经在修改时更新到该对象
				if (!adminConfig.nickname) {
					toast('请输入昵称', 'warning');
					return;
				}

				if (!adminConfig.userId) {
					toast('请输入账号ID', 'warning');
					return;
				}

				localStorage.setItem('adminConfig', JSON.stringify(adminConfig));

				// 更新所有历史管理员消息
				fetch('api/admin/update_admin_messages.php', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json'
						},
						body: JSON.stringify({
							nickname: adminConfig.nickname,
							avatar: adminConfig.avatar
						})
					})
					.then(response => response.json())
					.then(result => {
						if (result.success) {
							console.log('历史管理员消息更新成功');
						} else {
							console.log('历史管理员消息更新失败:', result.message);
						}
					})
					.catch(error => {
						console.error('更新历史管理员消息失败:', error);
					})
					.finally(() => {
						toast('配置保存成功', 'success');
					});
			}

			// 处理管理员头像上传
			function handleAdminAvatarUpload(input) {
				if (input.files && input.files[0]) {
					const reader = new FileReader();

					reader.onload = function(e) {
						document.getElementById('currentAdminAvatar').src = e.target.result;
						// 直接更新adminConfig对象
						adminConfig.avatar = '/user.jpg';
						toast('已强制使用全局 /user.jpg', 'success');
						// 保存配置
						saveAdminConfig();
					}

					reader.readAsDataURL(input.files[0]);
				}
			}

			// 移除管理员头像
			function removeAdminAvatar() {
				document.getElementById('currentAdminAvatar').src = '/user.jpg';
				// 直接更新adminConfig对象
				adminConfig.avatar = '';
				// 保存配置
				saveAdminConfig();
			}

			// 打开管理端配置模态框
			function openAdminConfigModal() {
				// 此函数可能不再需要，因为我们已经在网站配置页面直接修改配置
				toast('配置功能已集成到网站配置页面', 'info');
			}

			// 加载网站配置页面
			function loadAdminConfig() {
				setActiveMenuItem('loadAdminConfig');
				const mainContent = domCache.get('mainContent');
				if (!mainContent) return;

				mainContent.innerHTML = `
                <div style="min-height: calc(100vh - 128px); background: #F6F7F9; padding: 0;">
                    <div style="background: white; border-radius: 8px; overflow: hidden; margin: 0;">

                        <!-- 头像区域 -->
                        <div style="padding: 28px 24px 20px; border-bottom: 1px solid #F5F5F5;">
                            <div style="position: relative; display: inline-block;">
                                <img id="currentAdminAvatar"
                                     src="${adminConfig.avatar || '/user.jpg'}"
                                     alt="头像"
                                     style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; display: block; border: 1px solid #EBEBEB;">
                                <label for="adminAvatarUpload"
                                       style="position: absolute; bottom: 0; right: 0;
                                              width: 26px; height: 26px;
                                              background: rgba(30,30,30,0.55);
                                              border-radius: 50%;
                                              display: flex; align-items: center; justify-content: center;
                                              cursor: pointer; border: 2px solid white;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="white">
                                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                    </svg>
                                </label>
                                <input type="file" id="adminAvatarUpload" accept="image/*"
                                       style="display: none;" onchange="handleAdminAvatarUpload(this)">
                            </div>
                        </div>

                        <!-- 昵称 -->
                        <div style="display: flex; align-items: center; padding: 15px 24px; border-bottom: 1px solid #F5F5F5;">
                            <span style="color: #999; font-size: 14px; width: 72px; flex-shrink: 0;">昵称：</span>
                            <span id="nicknameDisplay" style="flex: 1; color: #1A1A1A; font-size: 14px;">${adminConfig.nickname || 'w.'}</span>
                            <svg onclick="(function(){
                                    var cur = document.getElementById('nicknameDisplay');
                                    var val = prompt('修改昵称', cur.textContent);
                                    if(val !== null && val.trim()) {
                                        adminConfig.nickname = val.trim();
                                        cur.textContent = val.trim();
                                        saveAdminConfig();
                                    }
                                })()" style="cursor: pointer; flex-shrink: 0;" width="16" height="16" viewBox="0 0 24 24" fill="#BFBFBF">
                                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                            </svg>
                        </div>

                        <!-- 账号ID -->
                        <div style="display: flex; align-items: center; padding: 15px 24px; border-bottom: 1px solid #F5F5F5;">
                            <span style="color: #999; font-size: 14px; width: 72px; flex-shrink: 0;">账号ID：</span>
                            <span style="flex: 1; color: #1A1A1A; font-size: 14px; font-family: monospace;">${adminConfig.userId || ''}</span>
                            <svg onclick="(function(){
                                    var id = '${adminConfig.userId || ''}';
                                    if(navigator.clipboard){ navigator.clipboard.writeText(id).then(function(){ toast('已复制账号ID','success'); }); }
                                    else{ var ta=document.createElement('textarea'); ta.value=id; document.body.appendChild(ta); ta.select(); document.execCommand('copy'); document.body.removeChild(ta); toast('已复制账号ID','success'); }
                                })()" style="cursor: pointer; flex-shrink: 0;" width="16" height="16" viewBox="0 0 24 24" fill="#BFBFBF">
                                <path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/>
                            </svg>
                        </div>

                        <!-- 微信 -->
                        <div style="display: flex; align-items: center; padding: 15px 24px; border-bottom: 1px solid #F5F5F5;">
                            <span style="color: #999; font-size: 14px; width: 72px; flex-shrink: 0;">微信：</span>
                            <span style="flex: 1; color: #1A1A1A; font-size: 14px;">${adminConfig.wechat || 'w.'}</span>
                        </div>

                        <!-- 认证 -->
                        <div style="display: flex; align-items: center; padding: 15px 24px;">
                            <span style="color: #999; font-size: 14px; width: 72px; flex-shrink: 0;">认证：</span>
                            <span style="flex: 1; color: #999; font-size: 14px;">未实名</span>
                            <button onclick="toast('实名认证功能暂未开放','info')"
                                    style="background: #07C160; color: white; border: none;
                                           border-radius: 20px; padding: 5px 16px;
                                           font-size: 13px; cursor: pointer; flex-shrink: 0;
                                           font-family: inherit; line-height: 1.6;">
                                前往认证
                            </button>
                        </div>
                    </div>
                </div>
            `;
			}

			// 关闭管理端配置模态框
			function closeAdminConfigModal() {
				document.getElementById('adminConfigModal').classList.remove('active');
			}

			// 修改sendMessage函数以使用配置的值
			function sendMessage() {
				if (!currentSelectedGroupId) {
					toast('请先选择群聊', 'warning');
					return;
				}

				// 只处理文本消息
				const textContent = document.getElementById('messageInput').value.trim();
				if (!textContent) {
					toast('请输入消息内容', 'warning');
					return;
				}

				let messageData = {
					group_id: currentSelectedGroupId,
					user_id: adminConfig.userId,
					user_nickname: adminConfig.nickname,
					user_avatar: adminConfig.avatar,
					type: 'text',
					content: textContent,
					is_admin: true
				};

				fetch('api/chat/send_message.php', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json'
						},
						body: JSON.stringify(messageData)
					})
					.then(response => response.json())
					.then(result => {
						if (result.success) {
							// 清空输入
							document.getElementById('messageInput').value = '';

							// 检查是否有该群聊的消息记录
							const hasMessageHistory = lastMessageTimestamps[currentSelectedGroupId] !== undefined;

							// 如果没有消息记录（新群聊），使用全量加载；否则使用增量加载
							loadGroupMessages(currentSelectedGroupId, !hasMessageHistory);

							// 确保滚动到底部，显示新发送的消息
							setTimeout(() => {
								const messagesList = document.getElementById('messagesList');
								if (messagesList) {
									messagesList.scrollTop = messagesList.scrollHeight;
								}
							}, 100); // 延迟一段时间，确保消息已加载
						} else {
							toast('消息发送失败: ' + result.message, 'error');
						}
					})
					.catch(error => {
						console.error('消息发送失败:', error);
						toast('消息发送失败，请稍后重试', 'error');
					});
			}

			// 修改图片发送函数以使用配置的值
			function sendImage() {
				const groupId = currentSelectedGroupId;
				if (!groupId) {
					toast('请先选择群聊', 'warning');
					return;
				}

				const fileInput = document.getElementById('imageUpload');
				const file = fileInput.files[0];

				if (!file) {
					toast('请选择要上传的图片', 'warning');
					return;
				}

				// 检查文件大小（限制为5MB）
				const MAX_FILE_SIZE = 5 * 1024 * 1024;
				if (file.size > MAX_FILE_SIZE) {
					toast('图片大小不能超过5MB', 'warning');
					return;
				}

				// 检查文件类型
				const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
				if (!ALLOWED_TYPES.includes(file.type)) {
					toast('只允许上传JPG、PNG、GIF和WebP格式的图片', 'warning');
					return;
				}

				// 显示确认弹窗
				if (!confirm('确定要发送这张图片吗？')) {
					return;
				}

				const formData = new FormData();
				formData.append('group_id', groupId);
				formData.append('user_id', adminConfig.userId);
				formData.append('user_nickname', adminConfig.nickname);
				formData.append('user_avatar', adminConfig.avatar);
				formData.append('type', 'image');
				formData.append('file', file); // 将字段名从'image'改为'file'，与upload_file.php期望的一致
				formData.append('is_admin', true);

				// 将请求发送到upload_file.php而不是send_message.php
				fetch('api/chat/upload_file.php', {
						method: 'POST',
						body: formData
					})
					.then(response => response.json())
					.then(result => {
						if (result.success) {
							// 清空文件输入
							fileInput.value = '';

							// 检查是否有该群聊的消息记录
							const hasMessageHistory = lastMessageTimestamps[groupId] !== undefined;

							// 如果没有消息记录（新群聊），使用全量加载；否则使用增量加载
							loadGroupMessages(groupId, !hasMessageHistory);

							// 自动滚动到底部
							setTimeout(() => {
								const messagesList = document.getElementById('messagesList');
								if (messagesList) {
									messagesList.scrollTop = messagesList.scrollHeight;
								}
							}, 100);
						} else {
							toast('图片发送失败: ' + result.message, 'error');
						}
					})
					.catch(error => {
						console.error('图片发送失败:', error);
						toast('图片发送失败，请稍后重试', 'error');
					});
			}

			// 页面加载完成后初始化
			window.addEventListener('DOMContentLoaded', function() {
				init();
				loadAdminConfigData();
			});
		</script>

		<!-- 底部标签管理模态框 -->
		<div class="modal" id="quickActionModal">
			<div class="modal-overlay" onclick="closeQuickActionModal()"></div>
			<div class="modal-content" style="max-width: 400px;">
				<div class="modal-header">
					<h3 class="modal-title" id="quickActionModalTitle">添加底部标签</h3>
					<button class="modal-close" onclick="closeQuickActionModal()">&times;</button>
				</div>
				<div class="modal-body">
					<form id="quickActionForm">
						<input type="hidden" id="quickActionGroupId">
						<input type="hidden" id="quickActionId">

						<div class="form-group">
							<label for="quickActionType">标签类型</label>
							<select id="quickActionType" class="form-input" required>
								<option value="">请选择类型</option>
								<option value="welfare">福利</option>
								<option value="red_packet">红包</option>
								<option value="video">视频</option>
								<option value="image">图片</option>
								<option value="activity">活动</option>
							</select>
						</div>

						<div class="form-group">
							<label for="quickActionTitle">标签标题</label>
							<input type="text" id="quickActionTitle" class="form-input" placeholder="请输入标签标题" required>
						</div>

						<div class="form-group">
							<label for="quickActionUrl">跳转网址</label>
							<input type="url" id="quickActionUrl" class="form-input" placeholder="请输入跳转网址" required>
						</div>

						<div class="form-actions">
							<button type="button" class="btn btn-secondary"
								onclick="closeQuickActionModal()">取消</button>
							<button type="button" class="btn btn-primary" onclick="submitQuickActionForm()">保存</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- 管理端配置模态框 -->


		<div id="imageConfirmModal" class="modal" style="z-index: 9999;">
			<div class="modal-overlay"
				onclick="document.getElementById('imageConfirmModal').classList.remove('active'); document.getElementById('mediaUpload').value='';">
			</div>
			<div class="modal-content"
				style="max-width: 400px; text-align: center; border-radius: 12px; overflow: hidden; background: #fff;">
				<div class="modal-header" style="border-bottom: none; padding-bottom: 0;">
					<h2 class="modal-title" style="font-size: 16px; font-weight: 600;">发送图片</h2>
					<button class="close-btn"
						onclick="document.getElementById('imageConfirmModal').classList.remove('active'); document.getElementById('mediaUpload').value='';">&times;</button>
				</div>
				<div class="modal-body" style="padding: 24px;">
					<div style="background: #F6F7F9; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
						<img id="imagePreview" src=""
							style="max-width: 100%; max-height: 250px; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); object-fit: contain;">
					</div>
					<p style="color: #595959; font-size: 14px; margin: 0;">确定要发送这张图片到群聊吗？</p>
				</div>
				<div class="modal-footer"
					style="justify-content: center; gap: 16px; border-top: none; padding-top: 0; padding-bottom: 24px;">
					<button type="button" class="btn btn-secondary" style="padding: 8px 32px; border-radius: 20px;"
						onclick="document.getElementById('imageConfirmModal').classList.remove('active'); document.getElementById('mediaUpload').value='';">取消</button>
					<button type="button" class="btn btn-primary" style="padding: 8px 32px; border-radius: 20px;"
						id="confirmSendImageBtn">确定发送</button>
				</div>
			</div>
		</div>

		<div id="lightboxModal" class="modal" style="z-index: 10000; background: rgba(0,0,0,0.9);">
			<div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; justify-content: center; align-items: center;"
				onclick="document.getElementById('lightboxModal').classList.remove('active')">
				<img id="lightboxImage" src=""
					style="max-width: 90vw; max-height: 90vh; border-radius: 4px; box-shadow: 0 8px 32px rgba(0,0,0,0.3); object-fit: contain; cursor: zoom-out;"
					onclick="event.stopPropagation()">
				<button
					style="position: absolute; top: 24px; right: 32px; background: rgba(255,255,255,0.1); border: none; color: white; width: 40px; height: 40px; border-radius: 50%; font-size: 24px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background 0.2s;"
					onmouseover="this.style.background='rgba(255,255,255,0.2)'"
					onmouseout="this.style.background='rgba(255,255,255,0.1)'"
					onclick="document.getElementById('lightboxModal').classList.remove('active')">&times;</button>
			</div>
		</div>

	
<!-- ================= 新增：合并转发与多选增强逻辑 ================= -->
<style>

/* ================= 卡片消息美化 (Card Message) ================= */
.admin-msg-card {
    background: #FFFFFF;
    border: 1px solid #EAEAEA;
    border-radius: 8px;
    padding: 12px 14px;
    min-width: 240px;
    max-width: 280px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    display: flex;
    flex-direction: column;
    text-align: left;
    cursor: pointer;
}
.admin-msg-card:hover {
    background: #FAFAFA;
}
.admin-msg-card-body {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 10px;
    border-bottom: 1px solid #F0F0F0;
    padding-bottom: 10px;
}
.admin-msg-card-info {
    flex: 1;
    min-width: 0;
}
.admin-msg-card-title {
    font-size: 15px;
    font-weight: 500;
    color: #1A1A1A;
    margin-bottom: 6px;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.admin-msg-card-desc {
    font-size: 12px;
    color: #8C8C8C;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.admin-msg-card-thumb {
    width: 48px;
    height: 48px;
    border-radius: 6px;
    object-fit: cover;
    flex-shrink: 0;
    border: 1px solid #F0F0F0;
}
.admin-msg-card-footer {
    font-size: 11px;
    color: #999;
    display: flex;
    align-items: center;
    gap: 4px;
}
.admin-msg-card-footer::before {
    content: "🔗";
    font-size: 10px;
}

/* 选中消息时的明显背景高亮 */
.chat-msg-item.msg-selected {
    background-color: rgba(7, 193, 96, 0.12) !important;
}

/* 多选模式下：显示遮罩层以物理拦截图片的点击 */
.multi-select-active .msg-click-shield {
    display: block !important;
}

/* 强制覆盖图片/视频等内联 onclick 样式，使点击彻底移交给父级行 */
.multi-select-active img, 
.multi-select-active video, 
.multi-select-active .admin-history-card, 
.multi-select-active .qq-history-card {
    pointer-events: none !important;
}

/* 多选模式下：隐藏撤回按钮 */
.multi-select-active .chat-msg-actions {
    display: none !important;
}

.multi-select-active .chat-msg-item {
    cursor: pointer;
}
</style>
<script>
let isMultiSelectMode = false;

function handleMsgRowClick(e, msgItem) {
    if (!isMultiSelectMode) return;

    // 强制阻止原有的点击事件（如打开图片、播放视频等）
    e.preventDefault();
    e.stopPropagation();

    const checkbox = msgItem.querySelector('.message-checkbox');
    if (checkbox) {
        checkbox.checked = !checkbox.checked;
        if (checkbox.checked) {
            msgItem.classList.add('msg-selected');
        } else {
            msgItem.classList.remove('msg-selected');
        }
    }
}

if (typeof selectGroupForChat === 'function') {
    const originalSelectGroupForChat = selectGroupForChat;
    window.selectGroupForChat = function(groupId, groupName, memberCount) {
        currentSelectedGroupName = groupName;
        originalSelectGroupForChat(groupId, groupName, memberCount);
        if (isMultiSelectMode) toggleMultiSelect();
    };
}

function toggleMultiSelect() {
    isMultiSelectMode = !isMultiSelectMode;
    const btnForward = document.getElementById('btnForward');
    const btnMultiSelect = document.getElementById('btnMultiSelect');
    const messagesList = document.getElementById('messagesList');

    if (isMultiSelectMode) {
        if (messagesList) messagesList.classList.add('multi-select-active');
        document.querySelectorAll('.msg-select-checkbox').forEach(cb => cb.style.display = 'flex');

        btnForward.style.display = 'inline-block';
        btnMultiSelect.textContent = '取消多选';
        btnMultiSelect.classList.remove('btn-secondary');
        btnMultiSelect.classList.add('btn-primary');
    } else {
        if (messagesList) messagesList.classList.remove('multi-select-active');
        document.querySelectorAll('.msg-select-checkbox').forEach(cb => cb.style.display = 'none');
        document.querySelectorAll('.message-checkbox').forEach(cb => cb.checked = false);
        document.querySelectorAll('.chat-msg-item').forEach(item => item.classList.remove('msg-selected'));

        btnForward.style.display = 'none';
        btnMultiSelect.textContent = '多选';
        btnMultiSelect.classList.remove('btn-primary');
        btnMultiSelect.classList.add('btn-secondary');
    }
}

function openForwardModal() {
    const selectedCbs = document.querySelectorAll('.message-checkbox:checked');
    if (selectedCbs.length === 0) {
        toast('请先选择要转发的消息', 'warning');
        return;
    }

    let modal = document.getElementById('forwardModal');
    if (!modal) {
        const modalHTML = `
        <div id="forwardModal" class="modal" style="z-index: 100000; position: fixed;">
            <div class="modal-overlay" onclick="closeForwardModal()" style="z-index: 1;"></div>
            <div class="modal-content" style="max-width: 500px; z-index: 2; position: relative; pointer-events: auto;" onclick="event.stopPropagation()">
                <div class="modal-header">
                    <h2 class="modal-title">合并转发</h2>
                    <button class="close-btn" onclick="closeForwardModal()">×</button>
                </div>
                <div class="modal-body" style="padding: 80px 20px 20px; flex: 1; overflow-y: auto;">
                    <div class="form-group">
                        <label class="form-label">选择目标群聊</label>
                        <div style="margin-bottom: 12px; padding: 12px; background: #E7F8EE; border-radius: 6px; display:flex; gap: 20px; align-items: center;">
                            <label style="font-weight: 500; cursor:pointer; display:flex; align-items:center; gap:6px;"><input type="checkbox" id="forwardAllGroups" onchange="toggleAllForwardGroups(this)" style="width:16px; height:16px;"> 全部群聊</label>
                            <label style="font-weight: 500; cursor:pointer; display:flex; align-items:center; gap:6px;"><input type="checkbox" id="forwardCurrentGroup" checked style="width:16px; height:16px;"> 本群 (<span id="forwardCurrentGroupLabel"></span>)</label>
                        </div>
                        <div id="forwardGroupList" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; background: #F6F7F9; padding: 16px; border-radius: 6px; max-height: 250px; overflow-y: auto;">
                        </div>
                    </div>
                    <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
                        <button type="button" class="btn btn-secondary" onclick="closeForwardModal()">取消</button>
                        <button type="button" class="btn btn-primary" onclick="confirmForward()">确认转发</button>
                    </div>
                </div>
            </div>
        </div>`;
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        modal = document.getElementById('forwardModal');
    }

    document.getElementById('forwardCurrentGroupLabel').textContent = currentSelectedGroupName || '未知群';
    document.getElementById('forwardCurrentGroup').value = currentSelectedGroupId;

    const cachedGroups = apiCache.get('groups') || [];
    const container = document.getElementById('forwardGroupList');

    if (cachedGroups && cachedGroups.length > 0) {
        container.innerHTML = cachedGroups.map(g => {
            if (g.id == currentSelectedGroupId) return '';
            return `<label style="display:flex; align-items:center; gap:6px; cursor:pointer;"><input type="checkbox" class="forward-group-cb" value="${escapeAttr(g.id)}" style="width:14px; height:14px;"> <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-size:14px;" title="${escapeAttr(g.name)}">${escapeAttr(g.name)}</span></label>`;
        }).join('');
    } else {
        container.innerHTML = '<div style="color:#999; font-size:13px; grid-column:1/-1;">暂无可转发的其他群聊</div>';
    }

    modal.classList.add('active');
}

function closeForwardModal() {
    const modal = document.getElementById('forwardModal');
    if (modal) modal.classList.remove('active');
}

function toggleAllForwardGroups(checkbox) {
    document.querySelectorAll('.forward-group-cb').forEach(cb => cb.checked = checkbox.checked);
    document.getElementById('forwardCurrentGroup').checked = checkbox.checked;
}

async function confirmForward() {
    const selectedGroupIds = [];
    const currentCb = document.getElementById('forwardCurrentGroup');
    if (currentCb && currentCb.checked) selectedGroupIds.push(currentCb.value);

    document.querySelectorAll('.forward-group-cb:checked').forEach(cb => {
        if (!selectedGroupIds.includes(cb.value)) selectedGroupIds.push(cb.value);
    });

    if (selectedGroupIds.length === 0) {
        toast('请选择要转发的目标群聊', 'warning');
        return;
    }

    const selectedMessages = [];
    document.querySelectorAll('.message-checkbox:checked').forEach(cb => {
        try {
            selectedMessages.push(JSON.parse(decodeURIComponent(cb.value)));
        } catch (e) {}
    });

    if (selectedMessages.length === 0) {
        toast('请选择要转发的消息', 'warning');
        return;
    }

    selectedMessages.sort((a, b) => new Date(a.timestamp) - new Date(b.timestamp));

    const historyPayload = {
        title: "群聊的聊天记录",
        appName: "聊天记录",
        items: selectedMessages.map(msg => {
            let actualType = msg.type || 'text';
            let content = msg.content;
            let textPreview = '';

            // 核心修复2：自动深度侦测 JSON，以完美支持聊天记录的二次嵌套
            let parsedContent = null;
            if (typeof content === 'string') {
                try {
                    parsedContent = JSON.parse(content);
                } catch(e) {}
            } else if (typeof content === 'object') {
                parsedContent = content;
            }

            let historyObj = undefined;

            if (parsedContent && typeof parsedContent === 'object') {
                if (parsedContent.items && Array.isArray(parsedContent.items)) {
                    actualType = 'history';
                    historyObj = parsedContent;
                } else if (parsedContent.title && parsedContent.appName) {
                    actualType = 'card';
                }
            }

            // 获取各类型在卡片里的显示文案
            if (actualType === 'history') textPreview = '[聊天记录]';
            else if (actualType === 'image') textPreview = '[图片]';
            else if (actualType === 'video') textPreview = '[视频]';
            else if (actualType === 'voice') textPreview = '[语音]';
            else if (actualType === 'file') textPreview = '[文件]';
            else if (actualType === 'card') textPreview = '[卡片]';
            else textPreview = typeof content === 'string' ? content : '[未知消息]';

            return {
                from: msg.user_nickname || (msg.is_admin ? '管理员' : '匿名用户'),
                time: msg.timestamp,
                type: actualType,
                content: typeof content === 'object' ? JSON.stringify(content) : content,
                url: ['image', 'video', 'file', 'voice'].includes(actualType) ? content : '',
                text: textPreview,
                avatar: msg.user_avatar || '/user.jpg',
                history_payload: historyObj // 提供该属性，让下一次渲染时它不再变为[媒体]而是正确的可点击历史卡片
            };
        })
    };

    const adminId = typeof adminConfig !== 'undefined' && adminConfig.userId ? adminConfig.userId : 'admin';
    const adminName = typeof adminConfig !== 'undefined' && adminConfig.nickname ? adminConfig.nickname : '管理员';
    const adminAvatar = typeof adminConfig !== 'undefined' && adminConfig.avatar ? adminConfig.avatar : '/user.jpg';

    closeForwardModal();
    toast(`正在顺序转发到 ${selectedGroupIds.length} 个群聊，请稍候...`, 'info');

    let successCount = 0;
    let failCount = 0;

    for (const groupId of selectedGroupIds) {
        const messageData = {
            group_id: groupId,
            user_id: adminId,
            user_nickname: adminName,
            user_avatar: adminAvatar,
            type: 'history',
            content: JSON.stringify(historyPayload),
            is_admin: true
        };

        try {
            const res = await fetch('api/chat/send_message.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(messageData)
            });
            const data = await res.json();
            if (data.success) {
                successCount++;
            } else {
                failCount++;
            }
        } catch (e) {
            failCount++;
        }
    }

    if (successCount > 0) toast(`成功合并转发到 ${successCount} 个群聊`, 'success');
    if (failCount > 0) toast(`${failCount} 个群聊转发失败`, 'error');

    toggleMultiSelect();
    refreshMessages();
}
</script>

<!-- ================= 新增：资源发送模态框 ================= -->
<style>

/* ================= 卡片消息美化 (Card Message) ================= */
.admin-msg-card {
    background: #FFFFFF;
    border: 1px solid #EAEAEA;
    border-radius: 8px;
    padding: 12px 14px;
    min-width: 240px;
    max-width: 280px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    display: flex;
    flex-direction: column;
    text-align: left;
    cursor: pointer;
}
.admin-msg-card:hover {
    background: #FAFAFA;
}
.admin-msg-card-body {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 10px;
    border-bottom: 1px solid #F0F0F0;
    padding-bottom: 10px;
}
.admin-msg-card-info {
    flex: 1;
    min-width: 0;
}
.admin-msg-card-title {
    font-size: 15px;
    font-weight: 500;
    color: #1A1A1A;
    margin-bottom: 6px;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.admin-msg-card-desc {
    font-size: 12px;
    color: #8C8C8C;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.admin-msg-card-thumb {
    width: 48px;
    height: 48px;
    border-radius: 6px;
    object-fit: cover;
    flex-shrink: 0;
    border: 1px solid #F0F0F0;
}
.admin-msg-card-footer {
    font-size: 11px;
    color: #999;
    display: flex;
    align-items: center;
    gap: 4px;
}
.admin-msg-card-footer::before {
    content: "🔗";
    font-size: 10px;
}

.resource-card {
    border: 1px solid #EBEBEB;
    border-radius: 6px;
    padding: 12px;
    display: flex;
    gap: 12px;
    cursor: pointer;
    transition: all 0.2s;
    background: #FFF;
}
.resource-card:hover {
    border-color: #07C160;
}
.resource-card.selected {
    border-color: #07C160;
    background: #E7F8EE;
}
.resource-cover {
    width: 60px;
    height: 60px;
    border-radius: 4px;
    object-fit: cover;
    background: #F0F0F0;
    flex-shrink: 0;
}
.resource-info {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.resource-title {
    font-size: 14px;
    font-weight: 500;
    color: #1A1A1A;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.resource-desc {
    font-size: 12px;
    color: #888;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}
</style>
<script>
let selectedResource = null;
let resourceSearchTimer = null;

function openResourceModal() {
    if (!currentSelectedGroupId) {
        toast('请先选择群聊', 'warning');
        return;
    }

    let modal = document.getElementById('resourceModal');
    if (!modal) {
        const modalHTML = `
        <div id="resourceModal" class="modal" style="z-index: 100000; position: fixed;">
            <!-- 去掉 onclick="closeResourceModal()"，禁止点空白处关闭 -->
            <div class="modal-overlay" style="z-index: 1;"></div>
            <div class="modal-content" style="max-width: 600px; height: 80vh; z-index: 2; position: relative; pointer-events: auto;" onclick="event.stopPropagation()">
                <div class="modal-header">
                    <h2 class="modal-title">发送资源卡片</h2>
                    <!-- 只能点右上角 x 关闭 -->
                    <button class="close-btn" onclick="closeResourceModal()">×</button>
                </div>
                <div style="padding: 70px 20px 20px; display: flex; flex-direction: column; height: 100%; box-sizing: border-box;">
                    <div style="display: flex; gap: 8px; margin-bottom: 16px;">
                        <input type="text" id="resourceSearchInput" class="form-input" placeholder="输入标题搜索，按回车或点击搜索" style="flex: 1;" onkeypress="if(event.key === 'Enter') fetchResourcesAPI()">
                        <button class="btn btn-primary" onclick="fetchResourcesAPI()">搜索</button>
                    </div>
                    <div id="resourceListContainer" style="flex: 1; overflow-y: auto; display: grid; grid-template-columns: 1fr 1fr; gap: 12px; align-content: flex-start;">
                        <div style="grid-column: 1/-1; text-align:center; padding: 40px; color: #999;">正在加载资源...</div>
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 12px; padding-top: 16px; border-top: 1px solid #F0F0F0; margin-top: 12px;">
                        <!-- 去掉了取消按钮，只保留发送 -->
                        <button type="button" class="btn btn-primary" onclick="confirmSendResource()">发送</button>
                    </div>
                </div>
            </div>
        </div>`;
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        modal = document.getElementById('resourceModal');
    }

    modal.classList.add('active');
    selectedResource = null;
    document.getElementById('resourceSearchInput').value = '';
    fetchResourcesAPI(); // 默认无关键词加载第一页
}

function closeResourceModal() {
    const modal = document.getElementById('resourceModal');
    if (modal) modal.classList.remove('active');
}

function fetchResourcesAPI() {
    const keyword = document.getElementById('resourceSearchInput').value.trim();
    const container = document.getElementById('resourceListContainer');
    container.innerHTML = '<div style="grid-column: 1/-1; text-align:center; padding: 40px; color: #999;">请求数据中，请稍候...</div>';

    // 构造 FormData
    const formData = new FormData();
    formData.append('page', 1);
    formData.append('pagesSize', 100);
    if (keyword) formData.append('keyword', keyword);

    fetch('https://web.lv.klink.cc/public/search.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        let list = [];

        if (res && res.code === 200 && res.data && res.data.results) {
            const results = res.data.results;

            if (results.video && Array.isArray(results.video.list)) {
                list = list.concat(results.video.list.map(item => Object.assign({}, item, {_resType: '视频'})));
            }
            if (results.article && Array.isArray(results.article.list)) {
                list = list.concat(results.article.list.map(item => Object.assign({}, item, {_resType: '文章'})));
            }
            if (results.photo && Array.isArray(results.photo.list)) {
                list = list.concat(results.photo.list.map(item => Object.assign({}, item, {_resType: '图集'})));
            }
            if (results.app && Array.isArray(results.app.list)) {
                list = list.concat(results.app.list.map(item => Object.assign({}, item, {_resType: 'APP'})));
            }
        }

        renderResourceList(list);
    })
    .catch(err => {
        console.error(err);
        container.innerHTML = '<div style="grid-column: 1/-1; text-align:center; color:red;">加载失败，请检查网络或跨域设置</div>';
    });
}

function renderResourceList(list) {
    const container = document.getElementById('resourceListContainer');
    if (!list || list.length === 0) {
        container.innerHTML = '<div style="grid-column: 1/-1; text-align:center; color:#999; padding: 20px;">没有找到匹配的资源</div>';
        return;
    }

    container.innerHTML = list.map(item => {
        // 解析图片数组
        let cover = '/user.jpg';
        try {
            if (item.cover) {
                cover = item.cover;
            } else if (item.images) {
                const imgs = typeof item.images === 'string' ? JSON.parse(item.images) : item.images;
                if (Array.isArray(imgs) && imgs.length > 0) cover = imgs[0];
            }
        } catch(e){}

        // 编码以便在 HTML 属性中传递
        const encodedData = encodeURIComponent(JSON.stringify(item));

        
        // 兼容接口的文字描述字段可能是 desc 或者 content
        const descText = item.desc || item.content || '';

        // 分类标签渲染
        const typeBadge = item._resType ? `<span style="font-size:10px; background:#E7F8EE; color:#07C160; padding:2px 4px; border-radius:4px; margin-right:4px; vertical-align: middle; flex-shrink:0;">${item._resType}</span>` : '';

        return `
        <div class="resource-card" onclick="selectResource(this, '${encodedData}')">
            <img src="${escapeHtml(cover)}" class="resource-cover" onerror="this.src='/user.jpg'">
            <div class="resource-info">
                <div class="resource-title" style="display:flex; align-items:center;" title="${escapeHtml(item.title || '未知资源')}">
                    ${typeBadge}
                    <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${escapeHtml(item.title || '未知资源')}</span>
                </div>
                <div class="resource-desc">${escapeHtml(descText)}</div>
            </div>
        </div>`;
    }).join('');
}

function selectResource(element, encodedData) {
    document.querySelectorAll('.resource-card').forEach(el => el.classList.remove('selected'));
    element.classList.add('selected');
    try {
        selectedResource = JSON.parse(decodeURIComponent(encodedData));
    } catch(e) {
        selectedResource = null;
    }
}

function confirmSendResource() {
    if (!selectedResource) {
        toast('请先点击选择一个资源', 'warning');
        return;
    }

    // 构造小程序卡片 payload 格式
    let cover = '';
    try {
        if (selectedResource.cover) {
            cover = selectedResource.cover;
        } else if (selectedResource.images) {
            const imgs = typeof selectedResource.images === 'string' ? JSON.parse(selectedResource.images) : selectedResource.images;
            if (Array.isArray(imgs) && imgs.length > 0) cover = imgs[0];
        }
    } catch(e){}

    const cardPayload = {
        ...selectedResource,
        title: selectedResource.title || '精彩内容',
        desc: selectedResource.desc || selectedResource.content || '',
        thumbUrl: cover,
        appName: '内部资源',
        url: window.location.origin + '/share.php?id=' + selectedResource.id 
    };

    const messageData = {
        group_id: currentSelectedGroupId,
        user_id: adminConfig.userId || 'admin',
        user_nickname: adminConfig.nickname || '管理员',
        type: 'card',  // 使用卡片类型渲染
        content: JSON.stringify(cardPayload),
        is_admin: true
    };

    closeResourceModal();

    // 使用系统中原有的 sendToAPI 方法，这样不仅会自动调用 appendTempMessage 实现消息列表的实时预渲染，还能统一处理成功逻辑。
    sendToAPI(messageData);
}
</script>
</body>
</html>