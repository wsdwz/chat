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
    <title>Chat 管理后台</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; outline: none; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'PingFang SC', 'Helvetica Neue', Arial, sans-serif;
            background-color: #F6F7F9;
            color: #1A1A1A;
            font-size: 14px;
            height: 100vh;
            overflow: hidden;
        }

        .app-container { display: flex; height: 100vh; }

        .sidebar {
            width: 200px;
            background-color: #FBFBFB;
            border-right: 1px solid #EBEBEB;
            display: flex;
            flex-direction: column;
            z-index: 100;
            flex-shrink: 0;
        }

        .sidebar-header {
            padding: 24px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-header::before {
            content: '';
            display: block;
            width: 24px; height: 24px;
            background-color: #07C160;
            border-radius: 50%;
            flex-shrink: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='white'%3E%3Cpath d='M12 2C6.48 2 2 6.03 2 11c0 2.84 1.48 5.37 3.82 7.03-.32 1.34-1.07 2.78-1.12 2.92-.06.16.03.34.19.4.05.02.1.02.15.02.11 0 .22-.06.28-.15 0 0 2.22-2.91 5.38-3.03.43.05.86.08 1.3.08 5.52 0 10-4.03 10-9S17.52 2 12 2z'/%3E%3C/svg%3E");
            background-size: 16px; background-position: center; background-repeat: no-repeat;
        }

        .sidebar-title { font-size: 16px; font-weight: 600; color: #1A1A1A; margin: 0; }

        .sidebar-menu { padding: 10px 12px; flex: 1; }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 10px 14px;
            margin-bottom: 2px;
            border-radius: 6px;
            color: #595959;
            text-decoration: none;
            font-size: 14px;
            border: none;
            background: transparent;
            cursor: pointer;
            width: 100%;
        }

        .menu-item:hover { background-color: #F2F2F2; }
        .menu-item.active { background-color: #E7F8EE; color: #07C160; font-weight: 500; }
        .menu-item.active .menu-item-icon,
        .menu-item.active .menu-item-text { color: #07C160; }
        .menu-item-icon { font-size: 16px; margin-right: 10px; }

        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid #EBEBEB;
            display: flex; flex-direction: column; gap: 16px;
        }

        .sidebar-footer-links { display: flex; gap: 24px; color: #595959; font-size: 13px; }
        .sidebar-footer-links span { display: flex; align-items: center; gap: 6px; cursor: pointer; }
        .sidebar-footer-user { display: flex; justify-content: space-between; align-items: center; }
        .sidebar-avatar {
            width: 24px; height: 24px; border-radius: 50%; background: #333;
            display: flex; align-items: center; justify-content: center; color: white; font-size: 12px;
        }

        .main-panel { flex: 1; display: flex; flex-direction: column; background-color: #F6F7F9; overflow: hidden; }

        .top-nav {
            background-color: #F6F7F9;
            padding: 20px 32px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .nav-left { display: flex; align-items: center; }
        .nav-left::before { content: '🤖'; font-size: 22px; margin-right: 10px; }
        .nav-title { font-size: 20px; font-weight: 600; color: #1A1A1A; margin: 0; }
        .nav-actions { font-size: 12px; color: #8C8C8C; }

        .content-wrapper { flex: 1; padding: 0 32px 32px; overflow-y: auto; }

        /* Cards */
        .card {
            background-color: #FFFFFF;
            border-radius: 6px;
            padding: 24px;
            margin-bottom: 16px;
            border: 1px solid #F0F0F0;
        }
        .card:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .card-header { padding-bottom: 16px; border-bottom: 1px solid #F0F0F0; display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .card-title { font-size: 16px; font-weight: 600; color: #1A1A1A; margin: 0; }

        .alert-bar {
            background-color: white; border-radius: 4px; padding: 14px 20px; margin-bottom: 20px;
            display: flex; justify-content: space-between; align-items: center; border: 1px solid #EBEBEB;
        }
        .alert-bar .text { font-size: 13px; color: #595959; display: flex; align-items: center; gap: 8px; }
        .alert-bar .text::before { content: '🔊'; font-size: 16px; }
        .text-brand { color: #07C160; cursor: pointer; font-size: 13px; font-weight: 500; }

        .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 20px; }

        .dashed-card {
            background: white; border: 1px dashed #E0E0E0; border-radius: 6px;
            padding: 24px 20px; display: flex; align-items: flex-start; gap: 16px;
            cursor: pointer; transition: border-color 0.2s, box-shadow 0.2s;
        }
        .dashed-card:hover { border-color: #07C160; box-shadow: 0 2px 8px rgba(7,193,96,0.08); }
        .dashed-icon {
            width: 48px; height: 48px; border-radius: 10px; background: #595959;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; color: white; flex-shrink: 0;
        }
        .dashed-content h4 { font-size: 15px; font-weight: 600; color: #1A1A1A; margin-bottom: 6px; }
        .dashed-content p { font-size: 13px; color: #8C8C8C; line-height: 1.6; }

        .app-panel { background: white; border-radius: 6px; padding: 20px; border: 1px solid #EBEBEB; }
        .app-title { font-size: 15px; font-weight: 500; color: #1A1A1A; margin-bottom: 20px; }
        .app-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 12px; }
        .app-item {
            border: 1px dashed #E0E0E0; border-radius: 6px; padding: 20px 12px;
            display: flex; flex-direction: column; align-items: center; gap: 10px;
            cursor: pointer; transition: 0.2s;
        }
        .app-item:hover { border-color: #07C160; background: #F0FFF6; }
        .app-name { font-size: 12px; color: #1A1A1A; text-align: center; }

        /* Chat Layout */
        .chat-layout {
            display: flex; gap: 0;
            height: calc(100vh - 110px);
            border: 1px solid #EBEBEB; border-radius: 6px; overflow: hidden;
        }
        .chat-sidebar {
            width: 260px; background: #FFFFFF; border-right: 1px solid #EBEBEB;
            display: flex; flex-direction: column; flex-shrink: 0;
        }
        .chat-sidebar-header { padding: 16px 20px; border-bottom: 1px solid #F0F0F0; }
        .chat-sidebar-title { font-size: 15px; font-weight: 600; color: #1A1A1A; }
        .chat-search { padding: 10px 14px; border-bottom: 1px solid #F0F0F0; }
        .chat-search input {
            width: 100%; padding: 7px 12px; background: #F6F7F9;
            border: 1px solid #EBEBEB; border-radius: 4px; font-size: 13px; color: #1A1A1A;
        }
        .chat-search input:focus { border-color: #07C160; background: white; }
        .chat-list { flex: 1; overflow-y: auto; }
        .chat-group-item {
            display: flex; align-items: center; gap: 10px; padding: 12px 14px;
            border-bottom: 1px solid #F5F5F5; cursor: pointer; transition: background 0.15s;
        }
        .chat-group-item:hover { background-color: #F6F7F9; }
        .chat-group-item.selected { background-color: #E7F8EE; }
        .chat-group-item img { width: 40px; height: 40px; border-radius: 6px; object-fit: cover; flex-shrink: 0; }
        .chat-group-name { font-size: 14px; font-weight: 500; color: #1A1A1A; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .chat-group-meta { font-size: 12px; color: #999; }
        .chat-badge { background: #FF4D4F; color: white; font-size: 11px; padding: 1px 5px; border-radius: 8px; margin-left: auto; flex-shrink: 0; }

        .chat-main { flex: 1; display: flex; flex-direction: column; background: #FFFFFF; min-width: 0; }
        .chat-header {
            padding: 14px 20px; border-bottom: 1px solid #F0F0F0;
            background: #FAFAFA; display: flex; justify-content: space-between; align-items: center; flex-shrink: 0;
        }
        .chat-header-title { font-size: 15px; font-weight: 600; color: #1A1A1A; }
        .chat-header-sub { font-size: 12px; color: #999; margin-top: 2px; }
        .chat-header-actions { display: flex; gap: 8px; }

        /* 消息区 */
        .chat-messages { flex: 1; overflow-y: auto; padding: 16px 20px; background: #EDEDED; }

        /* 消息气泡 - 微信风格 */
        .chat-msg-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
            gap: 10px;
            animation: fadeInUp 0.25s ease-out;
        }
        .chat-msg-item.is-admin { flex-direction: row-reverse; }

        .chat-msg-avatar {
            width: 38px; height: 38px; border-radius: 4px;
            object-fit: cover; flex-shrink: 0; background: #D9D9D9;
            display: flex; align-items: center; justify-content: center; font-size: 16px;
            border: 1px solid rgba(0,0,0,0.06);
        }

        .chat-msg-body { display: flex; flex-direction: column; max-width: 65%; min-width: 0; }
        .chat-msg-item.is-admin .chat-msg-body { align-items: flex-end; }
        .chat-msg-item.is-user .chat-msg-body { align-items: flex-start; }

        .chat-msg-info {
            display: flex; align-items: center; gap: 6px; margin-bottom: 4px;
            font-size: 11px; color: #999;
        }
        .chat-msg-item.is-admin .chat-msg-info { flex-direction: row-reverse; }
        .chat-msg-name { font-weight: 500; font-size: 12px; color: #666; }

        .chat-msg-bubble-wrap { display: flex; align-items: flex-end; gap: 8px; }
        .chat-msg-item.is-admin .chat-msg-bubble-wrap { flex-direction: row-reverse; }

        .chat-msg-bubble {
            padding: 9px 13px;
            border-radius: 4px 12px 12px 12px;
            font-size: 14px; line-height: 1.6;
            word-break: break-word;
            position: relative;
        }
        .chat-msg-item.is-admin .chat-msg-bubble {
            background: #95EC69;
            color: #111;
            border-radius: 12px 4px 12px 12px;
        }
        .chat-msg-item.is-user .chat-msg-bubble {
            background: #FFFFFF;
            color: #111;
            box-shadow: 0 1px 2px rgba(0,0,0,0.06);
        }

        /* 图片消息 */
        .chat-msg-bubble.is-image {
            padding: 0; background: transparent !important;
            border: none !important; box-shadow: none !important;
        }
        .chat-msg-bubble.is-image img {
            max-width: 220px; max-height: 300px;
            border-radius: 6px; display: block; cursor: zoom-in;
            border: 1px solid rgba(0,0,0,0.08);
        }

        /* 历史记录卡片消息 */
        .chat-msg-bubble.is-history {
            padding: 0; background: transparent !important;
            border: none !important; box-shadow: none !important;
        }
        .admin-history-card {
            background: #fff; border: 1px solid #EBEBEB; border-radius: 8px;
            padding: 12px 14px; cursor: pointer; max-width: 260px;
            transition: box-shadow 0.15s;
        }
        .admin-history-card:hover { box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
        .admin-history-card-title { font-size: 13px; font-weight: 600; color: #1A1A1A; margin-bottom: 6px; }
        .admin-history-card-desc { font-size: 12px; color: #8C8C8C; line-height: 1.5; }
        .admin-history-card-footer { margin-top: 8px; padding-top: 8px; border-top: 1px solid #F5F5F5; font-size: 11px; color: #07C160; }

        .chat-msg-actions { opacity: 0; transition: opacity 0.2s; display: flex; align-items: center; }
        .chat-msg-item:hover .chat-msg-actions { opacity: 1; }

        .btn-withdraw {
            background: #FF4D4F; color: white; border: none; border-radius: 4px;
            padding: 3px 8px; font-size: 11px; cursor: pointer; white-space: nowrap;
        }
        .btn-withdraw:hover { background: #FF7875; }

        /* 图片预览遮罩 */
        .img-preview-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.85); z-index: 9000;
            align-items: center; justify-content: center; cursor: zoom-out;
        }
        .img-preview-overlay.active { display: flex; }
        .img-preview-overlay img { max-width: 90vw; max-height: 90vh; border-radius: 4px; }

        .chat-empty {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            height: 100%; background-color: #EDEDED; gap: 12px;
        }
        .chat-empty-icon-wrapper {
            width: 80px; height: 80px; background: white; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        }
        .chat-empty-icon { font-size: 36px; opacity: 0.5; }
        .chat-empty-title { font-size: 15px; font-weight: 600; color: #1A1A1A; margin: 0; }
        .chat-empty-sub { font-size: 13px; color: #999; margin: 0; text-align: center; max-width: 240px; line-height: 1.6; }

        .chat-footer { padding: 12px 16px; border-top: 1px solid #F0F0F0; background: #FAFAFA; flex-shrink: 0; }
        .chat-tabs { display: flex; gap: 0; border-bottom: 1px solid #F0F0F0; margin-bottom: 10px; }
        .chat-tab { padding: 5px 14px; font-size: 13px; color: #999; cursor: pointer; border-bottom: 2px solid transparent; }
        .chat-tab.active { color: #07C160; border-bottom-color: #07C160; }
        .chat-input-area { display: flex; gap: 8px; align-items: flex-end; }
        .chat-input {
            flex: 1; padding: 9px 12px; border: 1px solid #EBEBEB; border-radius: 4px;
            font-size: 14px; resize: vertical; background: white; font-family: inherit; height: 80px;
        }
        .chat-input:focus { border-color: #07C160; }

        /* Stats */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 20px; }
        .stat-card { background: white; border-radius: 6px; padding: 20px; border: 1px solid #F0F0F0; }
        .stat-card-label { font-size: 13px; color: #999; margin-bottom: 6px; }
        .stat-card-value { font-size: 28px; font-weight: 600; color: #1A1A1A; }
        .stat-card-value.green { color: #07C160; }
        .stat-card-value.blue { color: #1677FF; }
        .stat-card-value.orange { color: #FAAD14; }

        .stats-table { background: white; border-radius: 6px; border: 1px solid #F0F0F0; overflow: hidden; }
        .stats-table table { width: 100%; border-collapse: collapse; }
        .stats-table th { padding: 12px 16px; text-align: left; font-size: 12px; font-weight: 500; color: #999; background: #FAFAFA; border-bottom: 1px solid #F0F0F0; }
        .stats-table td { padding: 14px 16px; font-size: 14px; color: #1A1A1A; border-bottom: 1px solid #F5F5F5; vertical-align: middle; }
        .stats-table tr:hover td { background: #FAFAFA; }

        /* Modals */
        .modal { display: none; position: fixed; inset: 0; z-index: 1000; }
        .modal.active { display: flex; align-items: center; justify-content: center; }
        .modal-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.45); }
        .modal-content {
            background: white; border-radius: 8px; width: 90%; max-width: 560px;
            max-height: 88vh; position: relative; z-index: 2;
            box-shadow: 0 8px 32px rgba(0,0,0,0.14);
            animation: slideUp 0.2s ease; display: flex; flex-direction: column;
        }
        /* 嵌套 modal（如历史记录弹窗）需要更高层级 */
        .modal.nested { z-index: 2000; }
        .modal.nested .modal-overlay { z-index: 1; }
        .modal.nested .modal-content { z-index: 2; }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 18px 20px; border-bottom: 1px solid #F0F0F0; flex-shrink: 0;
            border-radius: 8px 8px 0 0;
        }
        .modal-title { font-size: 16px; font-weight: 600; color: #1A1A1A; margin: 0; }
        .close-btn { background: none; border: none; font-size: 20px; cursor: pointer; color: #BFBFBF; line-height: 1; }
        .close-btn:hover { color: #FF4D4F; }
        .modal-body { padding: 20px; flex: 1; overflow-y: auto; }
        .modal-footer { display: flex; justify-content: flex-end; gap: 8px; padding: 14px 20px; border-top: 1px solid #F0F0F0; flex-shrink: 0; }

        /* Buttons */
        .btn {
            padding: 7px 16px; border-radius: 4px; font-size: 13px; font-weight: 500;
            cursor: pointer; border: 1px solid transparent;
            display: inline-flex; align-items: center; gap: 6px; font-family: inherit;
        }
        .btn-primary { background: #07C160; color: white; border-color: #07C160; }
        .btn-primary:hover { background: #06AD56; }
        .btn-secondary { background: white; color: #595959; border-color: #EBEBEB; }
        .btn-secondary:hover { border-color: #BFBFBF; }
        .btn-danger { background: #FF4D4F; color: white; border-color: #FF4D4F; }
        .btn-danger:hover { background: #FF7875; }
        .btn-sm { padding: 5px 12px; font-size: 12px; }
        .btn-xs { padding: 2px 8px; font-size: 11px; }

        .tag { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 3px; font-size: 12px; font-weight: 500; }
        .tag-success { background: #E7F8EE; color: #07C160; }
        .tag-warning { background: #FFFBE6; color: #FAAD14; }
        .tag-danger { background: #FFF2F0; color: #FF4D4F; }

        .form-group { margin-bottom: 16px; }
        .form-label { display: flex; align-items: center; gap: 6px; font-size: 14px; font-weight: 500; margin-bottom: 6px; color: #1A1A1A; }
        .form-label::before { content: ''; width: 2px; height: 12px; background: #07C160; border-radius: 1px; flex-shrink: 0; }
        .form-input { width: 100%; padding: 9px 12px; border: 1px solid #EBEBEB; border-radius: 4px; font-size: 14px; background: white; color: #1A1A1A; font-family: inherit; }
        .form-input:focus { border-color: #07C160; outline: none; }
        .form-textarea { resize: vertical; min-height: 90px; line-height: 1.6; }
        select.form-input { cursor: pointer; }

        .detail-section { margin-bottom: 20px; }
        .detail-section-title {
            font-size: 14px; font-weight: 600; color: #1A1A1A; margin-bottom: 10px;
            display: flex; align-items: center; gap: 8px;
        }
        .detail-section-title::before { content: ''; width: 3px; height: 14px; background: #07C160; border-radius: 2px; flex-shrink: 0; }

        .member-row { display: flex; align-items: center; gap: 10px; padding: 10px 0; border-bottom: 1px solid #F5F5F5; }
        .member-row:last-child { border-bottom: none; }
        .member-row img { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; }
        .member-name { font-size: 14px; font-weight: 500; color: #1A1A1A; }
        .member-id-text { font-size: 12px; color: #999; }

        /* Toggle */
        .toggle-switch { position: relative; display: inline-flex; align-items: center; cursor: pointer; }
        .toggle-switch input { opacity: 0; width: 0; height: 0; position: absolute; }
        .toggle-slider { position: relative; display: inline-block; width: 36px; height: 20px; background-color: #D9D9D9; border-radius: 10px; transition: background-color 0.2s; flex-shrink: 0; }
        .toggle-slider:before { content: ""; position: absolute; height: 16px; width: 16px; left: 2px; bottom: 2px; background-color: white; border-radius: 50%; transition: transform 0.2s; }
        .toggle-switch input:checked + .toggle-slider { background-color: #07C160; }
        .toggle-switch input:checked + .toggle-slider:before { transform: translateX(16px); }

        /* Toast */
        .toast-container { position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 8px; }
        .toast {
            padding: 10px 18px; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            color: #fff; font-size: 14px; font-weight: 500;
            animation: slideIn 0.3s ease-out forwards, fadeOut 0.3s ease-in forwards 2.7s;
            display: flex; align-items: center; gap: 8px; max-width: 320px;
        }
        .toast-success { background-color: #07C160; }
        .toast-error { background-color: #FA5151; }
        .toast-info { background-color: #1677FF; }
        .toast-warning { background-color: #FF9500; }

        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; transform: translateX(100%); } }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        @keyframes slideUpFade { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .loading { display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 60px 20px; color: #999; gap: 12px; font-size: 13px; }
        .loading::after { content: ''; width: 22px; height: 22px; border: 2px solid #EBEBEB; border-top-color: #07C160; border-radius: 50%; animation: spin 0.7s linear infinite; }

        .empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 60px 20px; text-align: center; color: #BFBFBF; }
        .empty-state-icon { font-size: 44px; margin-bottom: 12px; opacity: 0.3; }
        .empty-state-title { font-size: 15px; font-weight: 600; margin-bottom: 6px; color: #8C8C8C; }
        .empty-state-desc { font-size: 13px; line-height: 1.6; max-width: 340px; color: #BFBFBF; }

        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-thumb { background: #D9D9D9; border-radius: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }

        .group-info-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; }
        .group-info-card { background: #FFFFFF; border: 1px solid #EBEBEB; border-radius: 6px; padding: 18px; cursor: pointer; transition: box-shadow 0.15s; }
        .group-info-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.06); border-color: #D9D9D9; }
        .group-info-header { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
        .group-info-avatar { width: 52px; height: 52px; border-radius: 8px; object-fit: cover; border: 1px solid #F0F0F0; flex-shrink: 0; }
        .group-info-name { font-size: 15px; font-weight: 600; color: #1A1A1A; margin-bottom: 4px; }
        .group-info-desc { font-size: 12px; color: #999; text-overflow: ellipsis; white-space: nowrap; overflow: hidden; }
        .group-info-footer { display: flex; gap: 8px; justify-content: flex-end; border-top: 1px solid #F5F5F5; padding-top: 12px; margin-top: 12px; }
    </style>
</head>
<body>
    <div id="toastContainer" class="toast-container"></div>

    <!-- 图片预览遮罩 -->
    <div id="imgPreviewOverlay" class="img-preview-overlay" onclick="closeImgPreview()">
        <img id="imgPreviewImg" src="" alt="预览">
    </div>

    <div class="app-container">
        <!-- 左侧导航 -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2 class="sidebar-title">Chat</h2>
            </div>
            <nav class="sidebar-menu">
                <a href="#" class="menu-item" id="menu-loadGroups" onclick="loadGroups(); return false;">
                    <span class="menu-item-icon">💬</span>
                    <span class="menu-item-text">首页</span>
                </a>
                <a href="#" class="menu-item" id="menu-loadChatMessages" onclick="loadChatMessages(); return false;">
                    <span class="menu-item-icon">💭</span>
                    <span class="menu-item-text">群聊列表</span>
                </a>
                <a href="#" class="menu-item" id="menu-loadGroupInfo" onclick="loadGroupInfo(); return false;">
                    <span class="menu-item-icon">📋</span>
                    <span class="menu-item-text">运营数据</span>
                </a>
                <a href="#" class="menu-item" id="menu-loadAdminConfig" onclick="loadAdminConfig(); return false;">
                    <span class="menu-item-icon">⚙️</span>
                    <span class="menu-item-text">网站配置</span>
                </a>
            </nav>
            <div class="sidebar-footer">
                <div class="sidebar-footer-links">
                    <span>📄 文档</span>
                    <span>💬 社区</span>
                </div>
                <div class="sidebar-footer-user">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div class="sidebar-avatar">A</div>
                        <span style="font-size:12px;color:#595959;">Admin</span>
                    </div>
                    <span style="color:#8C8C8C;">📱</span>
                </div>
            </div>
        </aside>

        <!-- 右侧主内容 -->
        <main class="main-panel">
            <header class="top-nav">
                <div class="nav-left">
                    <h1 class="nav-title" id="navTitle">首页</h1>
                </div>
                <div class="nav-actions" id="navActions"></div>
            </header>
            <div class="content-wrapper" id="mainContent">
                <div class="loading">加载中...</div>
            </div>
        </main>
    </div>

    <!-- 创建群聊模态框 -->
    <div id="createGroupModal" class="modal">
        <div class="modal-overlay" onclick="closeCreateGroupModal()"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">创建群聊</h2>
                <button class="close-btn" onclick="closeCreateGroupModal()">×</button>
            </div>
            <div class="modal-body">
                <form id="createGroupForm" enctype="multipart/form-data" onsubmit="submitCreateGroup(event)">
                    <div class="form-group"><label class="form-label" for="groupName">群名称</label><input type="text" class="form-input" id="groupName" name="groupName" required></div>
                    <div class="form-group"><label class="form-label" for="groupDesc">群介绍</label><textarea class="form-input form-textarea" id="groupDesc" name="groupDesc"></textarea></div>
                    <div class="form-group"><label class="form-label" for="groupAnnouncement">群公告</label><textarea class="form-input form-textarea" id="groupAnnouncement" name="groupAnnouncement" placeholder="输入群公告内容"></textarea></div>
                    <div class="form-group"><label class="form-label" for="groupAvatar">群头像</label><input type="file" class="form-input" id="groupAvatar" name="groupAvatar" accept="image/*"></div>
                    <div class="form-group">
                        <label class="form-label" for="groupMemberLimit">群人数限制</label>
                        <select class="form-input" id="groupMemberLimit" name="groupMemberLimit">
                            <option value="10">10人</option><option value="100">100人</option>
                            <option value="500">500人</option><option value="1000">1000人</option>
                            <option value="2000">2000人</option><option value="5000">5000人</option>
                            <option value="0">无限制</option>
                        </select>
                    </div>
                    <div class="form-group"><label class="form-label" for="groupTag">群聊标签</label><input type="text" class="form-input" id="groupTag" name="groupTag" placeholder="输入群聊专属标签"></div>
                    <div style="display:flex;gap:10px;margin-top:8px;">
                        <button type="button" class="btn btn-secondary" onclick="closeCreateGroupModal()" style="flex:1;">取消</button>
                        <button type="submit" class="btn btn-primary" style="flex:1;">创建</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 群聊详情模态框 -->
    <div id="groupDetailModal" class="modal">
        <div class="modal-overlay" onclick="closeGroupDetailModal()"></div>
        <div class="modal-content" style="max-width:680px;">
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
        <div class="modal-overlay" onclick="closeEditGroupModal()"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">编辑群聊</h2>
                <button class="close-btn" onclick="closeEditGroupModal()">×</button>
            </div>
            <div class="modal-body">
                <form id="editGroupForm" enctype="multipart/form-data" onsubmit="submitEditGroup(event)">
                    <input type="hidden" id="editGroupId" name="groupId">
                    <div class="form-group"><label class="form-label" for="editGroupName">群名称</label><input type="text" class="form-input" id="editGroupName" name="groupName" required></div>
                    <div class="form-group"><label class="form-label" for="editGroupDesc">群介绍</label><textarea class="form-input form-textarea" id="editGroupDesc" name="groupDesc"></textarea></div>
                    <div class="form-group"><label class="form-label" for="editGroupAnnouncement">群公告</label><textarea class="form-input form-textarea" id="editGroupAnnouncement" name="groupAnnouncement"></textarea></div>
                    <div class="form-group"><label class="form-label" for="editGroupAvatar">群头像（不更换留空）</label><input type="file" class="form-input" id="editGroupAvatar" name="groupAvatar" accept="image/*"></div>
                    <div class="form-group"><label class="form-label" for="editCustomGroupId">自定义群ID（5-10位数字）</label><input type="text" class="form-input" id="editCustomGroupId" name="customGroupId" placeholder="留空则使用系统生成的ID"></div>
                    <div class="form-group">
                        <label class="form-label" for="editGroupMemberLimit">群人数限制</label>
                        <select class="form-input" id="editGroupMemberLimit" name="groupMemberLimit">
                            <option value="10">10人</option><option value="100">100人</option>
                            <option value="500">500人</option><option value="1000">1000人</option>
                            <option value="2000">2000人</option><option value="5000">5000人</option>
                            <option value="0">无限制</option>
                        </select>
                    </div>
                    <div class="form-group"><label class="form-label" for="editGroupTag">群聊标签</label><input type="text" class="form-input" id="editGroupTag" name="groupTag" placeholder="输入群聊专属标签"></div>
                    <div style="display:flex;gap:10px;margin-top:8px;">
                        <button type="button" class="btn btn-secondary" onclick="closeEditGroupModal()" style="flex:1;">取消</button>
                        <button type="submit" class="btn btn-primary" style="flex:1;">保存</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 违禁词管理模态框 -->
    <div id="bannedWordsModal" class="modal">
        <div class="modal-overlay" onclick="closeBannedWordsModal()"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">违禁词管理</h2>
                <button class="close-btn" onclick="closeBannedWordsModal()">×</button>
            </div>
            <div class="modal-body">
                <div id="groupSelectSection">
                    <div class="form-group">
                        <label class="form-label">选择群聊</label>
                        <div id="bannedGroupList" style="max-height:400px;overflow-y:auto;padding:12px;background:#F6F7F9;border-radius:6px;margin-top:8px;">
                            <div style="text-align:center;color:#999;">加载中...</div>
                        </div>
                    </div>
                </div>
                <div id="bannedWordsSection" style="display:none;">
                    <div class="form-group">
                        <label class="form-label">添加违禁词</label>
                        <div style="display:flex;gap:8px;margin-top:8px;">
                            <input type="text" id="newBannedWord" class="form-input" placeholder="输入违禁词">
                            <button type="button" class="btn btn-primary" onclick="addBannedWord()">添加</button>
                        </div>
                    </div>
                    <div class="form-group" style="margin-top:16px;">
                        <label class="form-label">当前违禁词列表</label>
                        <div id="bannedWordsList" style="max-height:280px;overflow-y:auto;padding:12px;background:#F6F7F9;border-radius:6px;margin-top:8px;">
                            <div style="text-align:center;color:#999;">暂无违禁词</div>
                        </div>
                    </div>
                    <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px;">
                        <button type="button" class="btn btn-secondary" onclick="backToGroupSelect()">← 返回</button>
                        <button type="button" class="btn btn-primary" onclick="saveBannedWords()">保存</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 历史记录嵌套 Modal（nested，z-index 更高） -->
    <div id="historyModal" class="modal nested">
        <div class="modal-overlay" onclick="closeHistoryModal()"></div>
        <div class="modal-content" style="max-width:640px;">
            <div class="modal-header">
                <h2 class="modal-title">聊天记录</h2>
                <button class="close-btn" onclick="closeHistoryModal()">×</button>
            </div>
            <div id="historyModalBody" class="modal-body" style="padding:16px;background:#EDEDED;min-height:300px;max-height:60vh;overflow-y:auto;">
                <div class="loading">加载中...</div>
            </div>
        </div>
    </div>

    <script>
    // ===================== 全局状态变量 =====================
    let globalMessagesCheckInterval = null;
    let lastMessageTimestamps = {};
    let unreadMessageCounts = {};
    let currentSelectedGroupId = null;
    let currentMessageRequest = null;
    let groupDetailRefreshInterval = null;
    let currentBannedWordsGroupId = null;
    let currentBannedWords = [];
    let messagesRefreshInterval = null;

    // ===================== 工具函数 =====================
    function toast(message, type = 'info') {
        const container = document.getElementById('toastContainer');
        if (!container) return;
        const el = document.createElement('div');
        el.className = `toast toast-${type}`;
        const icons = { success: '✅', error: '❌', warning: '⚠️', info: 'ℹ️' };
        el.innerHTML = `${icons[type] || 'ℹ️'} ${message}`;
        container.appendChild(el);
        setTimeout(() => el.remove(), 3100);
    }

    // 修复后的 escapeAttr：先处理反斜杠，再处理单引号，避免二次转义
    function escapeAttr(str) {
        if (str == null) return '';
        return String(str).replace(/\\/g, '\\\\').replace(/'/g, "\\'");
    }

    function escapeHtml(str) {
        if (str == null) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function formatTime(ts) {
        if (!ts) return '';
        // 兼容 Unix 秒级和毫秒级时间戳，以及日期字符串
        let d;
        if (typeof ts === 'number') {
            d = ts > 1e10 ? new Date(ts) : new Date(ts * 1000);
        } else {
            d = new Date(ts);
        }
        if (isNaN(d.getTime())) return ts;
        const now = new Date();
        const isToday = d.toDateString() === now.toDateString();
        const pad = n => String(n).padStart(2, '0');
        const time = `${pad(d.getHours())}:${pad(d.getMinutes())}`;
        if (isToday) return time;
        return `${d.getMonth()+1}/${d.getDate()} ${time}`;
    }

    function setActiveMenuItem(fnName) {
        document.querySelectorAll('.sidebar-menu .menu-item').forEach(item => item.classList.remove('active'));
        const el = document.getElementById('menu-' + fnName);
        if (el) el.classList.add('active');
    }

    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    // ===================== 缓存系统 =====================
    const apiCache = {
        data: {}, timestamp: {}, maxAge: 30000,
        get(key) {
            if (this.data[key] && (Date.now() - this.timestamp[key] < this.maxAge)) return this.data[key];
            return null;
        },
        set(key, value) { this.data[key] = value; this.timestamp[key] = Date.now(); },
        invalidate(key) { delete this.data[key]; delete this.timestamp[key]; },
        clear() { this.data = {}; this.timestamp = {}; }
    };

    // 注意：动态创建的 DOM 元素（messagesList, groupList）不能使用 domCache
    // 只缓存页面初始化时就存在的静态元素
    const domCache = {
        _cache: {},
        get(id) {
            // mainContent 等静态元素才缓存
            const staticIds = ['mainContent', 'toastContainer', 'navTitle'];
            if (staticIds.includes(id)) {
                if (!this._cache[id]) this._cache[id] = document.getElementById(id);
                return this._cache[id];
            }
            return document.getElementById(id);
        }
    };

    // ===================== 图片预览 =====================
    function previewImage(src) {
        const overlay = document.getElementById('imgPreviewOverlay');
        const img = document.getElementById('imgPreviewImg');
        img.src = src;
        overlay.classList.add('active');
    }
    function closeImgPreview() {
        document.getElementById('imgPreviewOverlay').classList.remove('active');
    }

    // ===================== 菜单导航 =====================
    function init() {
        loadGroups();
        startGlobalMessagesCheck();
    }

    // ===================== 全局消息检查 =====================
    function startGlobalMessagesCheck() {
        if (!globalMessagesCheckInterval) {
            globalMessagesCheckInterval = setInterval(checkGlobalMessages, 10000);
        }
    }

    function stopGlobalMessagesCheck() {
        if (globalMessagesCheckInterval) {
            clearInterval(globalMessagesCheckInterval);
            globalMessagesCheckInterval = null;
        }
    }

    function checkGlobalMessages() {
        const cachedGroups = apiCache.get('groups');
        const groupsPromise = cachedGroups ? Promise.resolve(cachedGroups) :
            fetch('api/admin/groups.php').then(r => r.json())
            .then(g => { if (Array.isArray(g)) apiCache.set('groups', g); return Array.isArray(g) ? g : []; })
            .catch(() => []);

        groupsPromise.then(groups => {
            const groupIds = groups.map(g => g.id);
            if (!groupIds.length) return;
            const lastTs = groupIds.map(id => lastMessageTimestamps[id] || 0).join(',');
            fetch(`api/chat/get_messages.php?group_ids=${groupIds.join(',')}&last_timestamps=${lastTs}`)
                .then(r => r.json())
                .then(allMessages => {
                    if (allMessages && typeof allMessages === 'object' && !Array.isArray(allMessages)) {
                        Object.entries(allMessages).forEach(([gid, msgs]) => processGroupMessages(gid, msgs));
                    }
                })
                .catch(() => fallbackToIndividualRequests(groups));
        });
    }

    function processGroupMessages(groupId, messages) {
        if (!Array.isArray(messages)) messages = [];
        if (!lastMessageTimestamps[groupId] && messages.length > 0) {
            lastMessageTimestamps[groupId] = messages[messages.length - 1].timestamp;
            return;
        }
        const newMessages = messages.filter(m => m.timestamp > (lastMessageTimestamps[groupId] || 0));
        if (newMessages.length > 0) {
            unreadMessageCounts[groupId] = (unreadMessageCounts[groupId] || 0) + newMessages.length;
            lastMessageTimestamps[groupId] = newMessages[newMessages.length - 1].timestamp;
            playAdminNewMessageSound();
            updateUIForNewMessages(groupId);
        } else if (!lastMessageTimestamps[groupId]) {
            lastMessageTimestamps[groupId] = Date.now() / 1000;
        }
    }

    function updateUIForNewMessages(groupId) {
        // 如果在群聊消息页面，更新左侧列表徽标
        const groupItem = document.querySelector(`.chat-group-item[data-group-id="${groupId}"]`);
        if (groupItem) {
            let badge = groupItem.querySelector('.chat-badge');
            const cnt = unreadMessageCounts[groupId] || 0;
            if (cnt > 0) {
                if (!badge) { badge = document.createElement('span'); badge.className = 'chat-badge'; groupItem.appendChild(badge); }
                badge.textContent = cnt;
            } else if (badge) { badge.remove(); }
        }
    }

    function fallbackToIndividualRequests(groups) {
        groups.forEach(group => {
            let url = `api/chat/get_messages.php?group_id=${group.id}`;
            if (lastMessageTimestamps[group.id]) url += `&last_timestamp=${lastMessageTimestamps[group.id]}`;
            fetch(url).then(r => r.json()).then(msgs => processGroupMessages(group.id, msgs)).catch(() => {});
        });
    }

    function playAdminNewMessageSound() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain); gain.connect(ctx.destination);
            osc.frequency.value = 880; gain.gain.value = 0.1;
            osc.start(); osc.stop(ctx.currentTime + 0.15);
        } catch(e) {}
    }

    function copyShareLink(url) {
        navigator.clipboard.writeText(url).then(() => toast('链接已复制', 'success')).catch(() => {
            const t = document.createElement('textarea');
            t.value = url; document.body.appendChild(t); t.select();
            document.execCommand('copy'); document.body.removeChild(t);
            toast('链接已复制', 'success');
        });
    }

    // ===================== 首页：群聊列表 =====================
    function loadGroups() {
        setActiveMenuItem('loadGroups');
        document.getElementById('navTitle').textContent = '首页';
        startGlobalMessagesCheck();
        const mc = domCache.get('mainContent');
        mc.innerHTML = '<div class="loading">加载中...</div>';

        const cached = apiCache.get('groups');
        if (cached) renderGroupList(cached);

        fetch('api/admin/groups.php').then(r => r.json()).then(groups => {
            if (Array.isArray(groups)) { apiCache.set('groups', groups); renderGroupList(groups); }
        }).catch(() => {
            if (!apiCache.get('groups')) mc.innerHTML = `<div class="empty-state"><div class="empty-state-icon">❌</div><h3 class="empty-state-title">加载失败</h3><p class="empty-state-desc">请检查网络连接或刷新页面重试</p></div>`;
        });
    }

    function renderGroupList(groups) {
        const mc = domCache.get('mainContent');
        if (!mc) return;
        let html = `
        <div class="alert-bar">
            <div class="text">群聊管理后台 - 共管理 ${groups.length} 个群聊</div>
            <span class="text-brand">v1.0</span>
        </div>
        <div class="grid-2">
            <div class="dashed-card" onclick="openCreateGroupModal()">
                <div class="dashed-icon">➕</div>
                <div class="dashed-content"><h4>创建新群聊</h4><p>点击此处立刻创建新的聊天室房间。</p></div>
            </div>
            <div class="dashed-card" onclick="openBannedWordsModal()">
                <div class="dashed-icon">🚫</div>
                <div class="dashed-content"><h4>违禁词设置</h4><p>点击此处管理群聊的敏感词拦截功能。</p></div>
            </div>
        </div>
        <div class="app-panel">
            <div class="app-title">管理的群聊列表（${groups.length} 个）</div>
            <div class="app-grid">
                ${groups.length === 0
                    ? '<div style="grid-column:1/-1;padding:40px;text-align:center;color:#999;">暂无群聊</div>'
                    : groups.map(g => `
                    <div class="app-item" onclick="openGroupDetail('${escapeAttr(g.id)}')" title="群ID: ${escapeAttr(g.id)}">
                        <img src="${g.avatar || 'user.jpg'}" alt="${escapeHtml(g.name)}" style="width:48px;height:48px;border-radius:8px;object-fit:cover;">
                        <div class="app-name" style="font-weight:500;">${escapeHtml(g.name)}</div>
                        <div style="font-size:11px;color:#8C8C8C;">👥 ${g.members?.length || 0}人</div>
                    </div>`).join('')}
            </div>
        </div>`;
        requestAnimationFrame(() => { mc.innerHTML = html; });
    }

    // ===================== 群聊详情 =====================
    function openGroupDetail(groupId) {
        if (groupDetailRefreshInterval) { clearInterval(groupDetailRefreshInterval); groupDetailRefreshInterval = null; }
        document.getElementById('modalGroupName').textContent = '加载中...';
        document.getElementById('groupDetailContent').innerHTML = '<div class="loading">加载中...</div>';
        document.getElementById('groupDetailModal').classList.add('active');

        function fetchDetail() {
            fetch(`api/admin/groups.php?group_id=${groupId}`).then(r => r.json()).then(g => {
                document.getElementById('modalGroupName').textContent = g.name;
                const days = Math.floor((Date.now() - new Date(g.created_at)) / 86400000);
                document.getElementById('groupDetailContent').innerHTML = `
                <div style="text-align:center;padding-bottom:20px;border-bottom:1px solid #F0F0F0;margin-bottom:20px;">
                    <img src="${g.avatar || 'user.jpg'}" style="width:72px;height:72px;border-radius:10px;object-fit:cover;margin-bottom:12px;" onerror="this.src='user.jpg'">
                    <h3 style="font-size:18px;font-weight:600;color:#1A1A1A;margin:0 0 6px;">${escapeHtml(g.name)}</h3>
                    <p style="font-size:13px;color:#8C8C8C;margin:0;">${g.members?.length || 0} 人群成员 · ${g.today_active_users || 0} 今日活跃</p>
                </div>
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:20px;">
                    <div style="background:#F6F7F9;padding:14px;border-radius:8px;text-align:center;"><div style="font-size:22px;font-weight:600;color:#07C160;">${g.today_active_users||0}</div><div style="font-size:12px;color:#8C8C8C;margin-top:2px;">今日活跃</div></div>
                    <div style="background:#F6F7F9;padding:14px;border-radius:8px;text-align:center;"><div style="font-size:22px;font-weight:600;color:#1677FF;">${g.total_active_users||0}</div><div style="font-size:12px;color:#8C8C8C;margin-top:2px;">总活跃</div></div>
                    <div style="background:#F6F7F9;padding:14px;border-radius:8px;text-align:center;"><div style="font-size:22px;font-weight:600;color:#FAAD14;">${days}</div><div style="font-size:12px;color:#8C8C8C;margin-top:2px;">建群天数</div></div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                    <div>
                        <div style="font-size:13px;font-weight:600;color:#1A1A1A;margin-bottom:8px;display:flex;align-items:center;gap:6px;"><span style="width:3px;height:13px;background:#07C160;border-radius:2px;display:inline-block;"></span>基本信息</div>
                        <div style="background:#fff;border:1px solid #EBEBEB;border-radius:6px;padding:12px;font-size:13px;">
                            <div style="display:flex;justify-content:space-between;padding-bottom:8px;border-bottom:1px solid #F5F5F5;margin-bottom:8px;">
                                <span style="color:#8C8C8C;">群ID</span>
                                <div style="display:flex;gap:6px;align-items:center;">
                                    <code style="background:#F6F7F9;padding:2px 6px;border-radius:3px;font-size:12px;">${escapeHtml(String(g.id))}</code>
                                    <span onclick="copyShareLink(location.href.split('?')[0].replace('admin.php','')+'?group_id=${escapeAttr(g.id)}')" style="cursor:pointer;color:#07C160;font-size:11px;background:#E7F8EE;padding:2px 6px;border-radius:3px;">🔗复制</span>
                                </div>
                            </div>
                            <div style="display:flex;justify-content:space-between;padding-bottom:8px;border-bottom:1px solid #F5F5F5;margin-bottom:8px;"><span style="color:#8C8C8C;">人数限制</span><span>${g.member_limit ? g.member_limit+'人' : '无限制'}</span></div>
                            <div style="display:flex;justify-content:space-between;padding-bottom:8px;border-bottom:1px solid #F5F5F5;margin-bottom:8px;"><span style="color:#8C8C8C;">标签</span><span>${escapeHtml(g.tag||'无')}</span></div>
                            <div style="display:flex;justify-content:space-between;"><span style="color:#8C8C8C;">创建时间</span><span style="font-size:12px;">${new Date(g.created_at).toLocaleString()}</span></div>
                        </div>
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:600;color:#1A1A1A;margin-bottom:8px;display:flex;align-items:center;gap:6px;"><span style="width:3px;height:13px;background:#07C160;border-radius:2px;display:inline-block;"></span>群管理</div>
                        <div style="background:#fff;border:1px solid #EBEBEB;border-radius:6px;padding:12px;">
                            <div style="display:flex;justify-content:space-between;align-items:center;padding-bottom:10px;border-bottom:1px solid #F5F5F5;margin-bottom:10px;">
                                <span style="font-size:13px;">全员禁言</span>
                                <label class="toggle-switch"><input type="checkbox" ${!g.allow_speak?'checked':''} onchange="toggleGroupSpeak('${escapeAttr(g.id)}',!this.checked)"><span class="toggle-slider"></span></label>
                            </div>
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <span style="font-size:13px;">允许上传图片</span>
                                <label class="toggle-switch"><input type="checkbox" ${g.allow_image_upload!==false?'checked':''} onchange="toggleGroupImageUpload('${escapeAttr(g.id)}',this.checked)"><span class="toggle-slider"></span></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="margin-bottom:20px;">
                    <div style="font-size:13px;font-weight:600;color:#1A1A1A;margin-bottom:8px;display:flex;align-items:center;gap:6px;"><span style="width:3px;height:13px;background:#07C160;border-radius:2px;display:inline-block;"></span>群公告与介绍</div>
                    <div style="background:#fff;border:1px solid #EBEBEB;border-radius:6px;padding:12px;font-size:13px;color:#595959;line-height:1.7;">
                        <div style="margin-bottom:6px;"><strong style="color:#1A1A1A;">公告：</strong>${escapeHtml(g.announcement||'暂无公告')}</div>
                        <div><strong style="color:#1A1A1A;">介绍：</strong>${escapeHtml(g.desc||'暂无介绍')}</div>
                    </div>
                </div>
                <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:16px;border-top:1px solid #F0F0F0;">
                    <button class="btn btn-secondary" onclick="editGroup('${escapeAttr(g.id)}')">✏️ 编辑信息</button>
                    <button class="btn btn-danger" onclick="deleteGroup('${escapeAttr(g.id)}')">🗑 解散群聊</button>
                </div>`;
            }).catch(() => {
                if (document.getElementById('groupDetailContent').innerHTML.includes('加载')) {
                    document.getElementById('groupDetailContent').innerHTML = '<div class="empty-state"><div class="empty-state-icon">❌</div><h3 class="empty-state-title">加载失败</h3></div>';
                }
            });
        }
        fetchDetail();
        groupDetailRefreshInterval = setInterval(fetchDetail, 15000);
    }

    function closeGroupDetailModal() {
        document.getElementById('groupDetailModal').classList.remove('active');
        if (groupDetailRefreshInterval) { clearInterval(groupDetailRefreshInterval); groupDetailRefreshInterval = null; }
    }

    // ===================== 群聊操作 =====================
    function toggleGroupSpeak(groupId, allowSpeak) {
        fetch('api/admin/toggle_group_speak.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({group_id:groupId, allow_speak:allowSpeak}) })
            .then(r => r.json()).then(d => { if(d.success) toast('发言权限已更新','success'); else toast('操作失败','error'); }).catch(() => toast('网络错误','error'));
    }

    function toggleGroupImageUpload(groupId, allow) {
        fetch('api/admin/toggle_group_image_upload.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({group_id:groupId, allow_image_upload:allow}) })
            .then(r => r.json()).then(d => { if(d.success) toast('图片权限已更新','success'); else toast('操作失败','error'); }).catch(() => toast('网络错误','error'));
    }

    function editGroup(groupId) {
        fetch(`api/admin/groups.php?group_id=${groupId}`).then(r => r.json()).then(g => {
            if (!g) return;
            document.getElementById('editGroupName').value = g.name || '';
            document.getElementById('editGroupDesc').value = g.desc || '';
            document.getElementById('editGroupAnnouncement').value = g.announcement || '';
            document.getElementById('editCustomGroupId').value = g.custom_group_id || '';
            document.getElementById('editGroupMemberLimit').value = g.member_limit || 0;
            document.getElementById('editGroupTag').value = g.tag || '';
            document.getElementById('editGroupId').value = groupId;
            document.getElementById('editGroupModal').classList.add('active');
        }).catch(() => toast('加载群聊信息失败','error'));
    }

    function deleteGroup(groupId) {
        if (!confirm('确定要解散该群聊吗？此操作不可恢复！')) return;
        fetch(`api/admin/group.php?group_id=${groupId}`, { method:'DELETE' })
            .then(r => r.json()).then(d => {
                if (d.success) { closeGroupDetailModal(); apiCache.invalidate('groups'); loadGroups(); toast('群聊已解散','success'); }
                else toast('删除失败: '+(d.message||'未知错误'),'error');
            }).catch(() => toast('删除失败','error'));
    }

    function kickMember(groupId, userId) {
        if (!confirm('确定要移除该成员吗？')) return;
        fetch('api/admin/remove_member.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({group_id:groupId, user_id:userId}) })
            .then(r => r.json()).then(d => {
                if (d.success) { openGroupDetail(groupId); apiCache.invalidate('groups'); toast('成员移除成功','success'); }
                else toast('移除失败: '+(d.message||''),'error');
            }).catch(() => toast('移除失败','error'));
    }

    // ===================== 创建/编辑群聊表单 =====================
    function openCreateGroupModal() { document.getElementById('createGroupModal').classList.add('active'); }
    function closeCreateGroupModal() { document.getElementById('createGroupModal').classList.remove('active'); }
    function closeEditGroupModal() { document.getElementById('editGroupModal').classList.remove('active'); }

    function submitCreateGroup(e) {
        e.preventDefault();
        const fd = new FormData(document.getElementById('createGroupForm'));
        fetch('api/admin/create_group.php', { method:'POST', body: fd })
            .then(r => r.json()).then(d => {
                if (d.success) { closeCreateGroupModal(); apiCache.invalidate('groups'); loadGroups(); toast('群聊创建成功','success'); }
                else toast('创建失败: '+(d.message||''),'error');
            }).catch(() => toast('创建失败','error'));
    }

    function submitEditGroup(e) {
        e.preventDefault();
        const fd = new FormData(document.getElementById('editGroupForm'));
        fetch('api/admin/edit_group.php', { method:'POST', body: fd })
            .then(r => r.json()).then(d => {
                if (d.success) { closeEditGroupModal(); apiCache.invalidate('groups'); openGroupDetail(fd.get('groupId')); toast('保存成功','success'); }
                else toast('保存失败: '+(d.message||''),'error');
            }).catch(() => toast('保存失败','error'));
    }

    // ===================== 群聊消息管理 =====================
    function loadChatMessages() {
        setActiveMenuItem('loadChatMessages');
        document.getElementById('navTitle').textContent = '群聊列表';
        startGlobalMessagesCheck();
        if (messagesRefreshInterval) { clearInterval(messagesRefreshInterval); messagesRefreshInterval = null; }

        document.getElementById('mainContent').innerHTML = `
        <div class="chat-layout">
            <div class="chat-sidebar">
                <div class="chat-sidebar-header"><span class="chat-sidebar-title">群聊列表</span></div>
                <div class="chat-search"><input type="text" id="groupSearch" placeholder="搜索群聊..."></div>
                <div id="groupList" class="chat-list"><div class="loading">加载中...</div></div>
            </div>
            <div class="chat-main">
                <div class="chat-header">
                    <div>
                        <div id="chatGroupName" class="chat-header-title">请选择群聊</div>
                        <div id="chatGroupInfo" class="chat-header-sub"></div>
                    </div>
                    <div class="chat-header-actions">
                        <button class="btn btn-sm btn-secondary" onclick="refreshMessages()">🔄 刷新</button>
                        <button class="btn btn-sm btn-danger" onclick="withdrawAllMessages()">撤回全部</button>
                    </div>
                </div>
                <div id="messagesList" class="chat-messages">
                    <div class="chat-empty">
                        <div class="chat-empty-icon-wrapper"><div class="chat-empty-icon">👈</div></div>
                        <h3 class="chat-empty-title">未选择群聊</h3>
                        <p class="chat-empty-sub">请在左侧选择一个群聊查看消息</p>
                    </div>
                </div>
                <div id="chatFooter" class="chat-footer" style="display:none;">
                    <div class="chat-tabs">
                        <div class="chat-tab active" onclick="switchMessageType('text',this)">文本</div>
                        <div class="chat-tab" onclick="switchMessageType('image',this)">图片</div>
                    </div>
                    <div class="chat-input-area">
                        <textarea id="messageInput" class="chat-input" placeholder="输入消息，Ctrl+Enter 发送"></textarea>
                        <button class="btn btn-primary" onclick="sendMessage()">发送</button>
                    </div>
                    <div id="mediaUploadContainer" style="display:none;margin-top:8px;">
                        <input type="file" id="mediaUpload" class="form-input" accept="image/*" style="font-size:13px;padding:6px;">
                    </div>
                </div>
            </div>
        </div>`;

        loadGroupsForChat();

        document.getElementById('messageInput').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.ctrlKey) { e.preventDefault(); sendMessage(); }
        });

        document.getElementById('groupSearch').addEventListener('input', debounce(function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('.chat-group-item').forEach(item => {
                item.style.display = item.querySelector('.chat-group-name').textContent.toLowerCase().includes(q) ? 'flex' : 'none';
            });
        }, 200));
    }

    let currentMessageType = 'text';
    function switchMessageType(type, el) {
        currentMessageType = type;
        document.querySelectorAll('.chat-tab').forEach(t => t.classList.remove('active'));
        if (el) el.classList.add('active');
        const mc = document.getElementById('mediaUploadContainer');
        const mi = document.getElementById('messageInput');
        if (type === 'text') { mc.style.display = 'none'; mi.style.display = 'block'; mi.placeholder = '输入消息，Ctrl+Enter 发送'; }
        else { mc.style.display = 'block'; mi.style.display = 'none'; }
    }

    function loadGroupsForChat() {
        const cached = apiCache.get('groups');
        if (cached) renderGroupsForChat(cached);

        fetch('api/admin/groups.php').then(r => r.json()).then(groups => {
            if (Array.isArray(groups)) { apiCache.set('groups', groups); renderGroupsForChat(groups); }
        }).catch(() => {
            const gl = document.getElementById('groupList');
            if (gl && !cached) gl.innerHTML = '<div class="chat-empty" style="padding:20px;color:#999;">加载失败</div>';
        });
    }

    function renderGroupsForChat(groups) {
        const gl = document.getElementById('groupList');
        if (!gl) return;
        if (!groups.length) { gl.innerHTML = '<div style="padding:20px;text-align:center;color:#999;">暂无群聊</div>'; return; }
        gl.innerHTML = groups.map(g => {
            const unread = unreadMessageCounts[g.id] || 0;
            return `<div class="chat-group-item${currentSelectedGroupId === g.id ? ' selected' : ''}" onclick="selectGroupForChat('${escapeAttr(g.id)}','${escapeAttr(g.name)}',${g.members?.length||0})" data-group-id="${g.id}">
                <img src="${g.avatar || 'user.jpg'}" onerror="this.src='user.jpg'">
                <div style="flex:1;min-width:0;">
                    <div class="chat-group-name">${escapeHtml(g.name)}</div>
                    <div class="chat-group-meta">👥 ${g.members?.length||0}人</div>
                </div>
                ${unread > 0 ? `<span class="chat-badge">${unread}</span>` : ''}
            </div>`;
        }).join('');
    }

    function selectGroupForChat(groupId, groupName, memberCount) {
        document.querySelectorAll('.chat-group-item').forEach(c => c.classList.remove('selected'));
        const card = document.querySelector(`.chat-group-item[data-group-id="${groupId}"]`);
        if (card) { card.classList.add('selected'); const b = card.querySelector('.chat-badge'); if(b) b.remove(); }
        unreadMessageCounts[groupId] = 0;
        currentSelectedGroupId = groupId;
        document.getElementById('chatGroupName').textContent = groupName;
        document.getElementById('chatGroupInfo').textContent = `${memberCount} 名成员 | ID: ${groupId}`;
        document.getElementById('chatFooter').style.display = 'block';

        if (messagesRefreshInterval) { clearInterval(messagesRefreshInterval); messagesRefreshInterval = null; }
        loadGroupMessages(groupId, true);
        messagesRefreshInterval = setInterval(() => { if (currentSelectedGroupId === groupId) loadGroupMessages(groupId, false); }, 5000);
    }

    function loadGroupMessages(groupId, isInitialLoad) {
        const ml = document.getElementById('messagesList');
        if (!ml) return;
        if (currentMessageRequest) { try { currentMessageRequest.abort(); } catch(e) {} }

        if (isInitialLoad) ml.innerHTML = '<div style="text-align:center;color:#999;padding:30px;">加载消息中...</div>';

        const ctrl = new AbortController();
        currentMessageRequest = ctrl;

        fetch(`api/chat/get_messages.php?group_id=${groupId}`, { signal: ctrl.signal })
            .then(r => r.json()).then(messages => {
                currentMessageRequest = null;
                if (Array.isArray(messages)) {
                    apiCache.set(`messages_${groupId}`, messages);
                    renderMessages(messages, groupId, isInitialLoad);
                }
            }).catch(err => {
                currentMessageRequest = null;
                if (err.name === 'AbortError') return;
                if (isInitialLoad) ml.innerHTML = '<div style="text-align:center;color:#FF4D4F;padding:30px;">加载消息失败，请重试</div>';
            });
    }

    function renderMessages(messages, groupId, isInitialLoad) {
        const ml = document.getElementById('messagesList');
        if (!ml) return;

        const wasAtBottom = ml.scrollHeight - ml.scrollTop - ml.clientHeight < 60;

        if (!messages.length) {
            if (isInitialLoad) ml.innerHTML = `<div class="chat-empty"><div class="chat-empty-icon-wrapper"><div class="chat-empty-icon">📭</div></div><h3 class="chat-empty-title">暂无消息</h3><p class="chat-empty-sub">快来发送第一条消息吧！</p></div>`;
            return;
        }

        const html = messages.map(msg => renderMessageItem(msg)).join('');

        requestAnimationFrame(() => {
            ml.innerHTML = html;
            if (messages.length > 0) lastMessageTimestamps[groupId] = messages[messages.length - 1].timestamp;
            if (isInitialLoad || wasAtBottom) ml.scrollTop = ml.scrollHeight;
        });
    }

    function renderMessageItem(msg) {
        const isAdmin = msg.is_admin;
        const avatarSrc = msg.user_avatar || (isAdmin ? 'user.jpg' : 'user.jpg');
        const name = escapeHtml(msg.user_nickname || (isAdmin ? '管理员' : '用户'));
        const timeStr = formatTime(msg.timestamp);
        const bubbleContent = renderMessageContent(msg);
        const bubbleClass = msg.type === 'image' ? 'chat-msg-bubble is-image' : (msg.type === 'history' ? 'chat-msg-bubble is-history' : 'chat-msg-bubble');

        return `<div class="chat-msg-item ${isAdmin ? 'is-admin' : 'is-user'}" data-message-id="${msg.id||''}">
            <img class="chat-msg-avatar" src="${avatarSrc}" onerror="this.src='user.jpg'" alt="${name}">
            <div class="chat-msg-body">
                <div class="chat-msg-info">
                    <span class="chat-msg-name">${name}</span>
                    <span>${timeStr}</span>
                </div>
                <div class="chat-msg-bubble-wrap">
                    <div class="${bubbleClass}">${bubbleContent}</div>
                    <div class="chat-msg-actions">
                        <button class="btn-withdraw" onclick="withdrawMessage('${msg.id||''}','${msg.group_id||''}')">撤回</button>
                    </div>
                </div>
            </div>
        </div>`;
    }

    function renderMessageContent(msg) {
        const type = msg.type || 'text';
        if (type === 'image') {
            // 修复：确保图片路径正确，支持相对路径和绝对路径
            let src = msg.content || msg.url || '';
            if (src && !src.startsWith('http') && !src.startsWith('/') && !src.startsWith('data:')) {
                src = src; // 保持相对路径，由服务器根目录解析
            }
            return `<img src="${escapeHtml(src)}" alt="图片" loading="lazy"
                onclick="previewImage('${escapeAttr(src)}')"
                onerror="this.parentNode.innerHTML='<span style=color:#999;font-size:12px;>图片加载失败</span>'"
                style="max-width:220px;max-height:300px;border-radius:6px;display:block;cursor:zoom-in;">`;
        }
        if (type === 'video') {
            return `<video src="${escapeHtml(msg.content||msg.url||'')}" controls style="max-width:220px;border-radius:6px;"></video>`;
        }
        if (type === 'history' || type === 'chat_history') {
            const historyData = msg.history_data || {};
            const preview = historyData.preview || '聊天记录';
            const count = historyData.count || 0;
            return `<div class="admin-history-card" onclick="openHistoryModal('${escapeAttr(JSON.stringify(historyData))}')">
                <div class="admin-history-card-title">📋 聊天记录</div>
                <div class="admin-history-card-desc">${escapeHtml(preview)}</div>
                <div class="admin-history-card-footer">共 ${count} 条消息 · 点击查看</div>
            </div>`;
        }
        // 文本消息：换行转 <br>，防 XSS
        return escapeHtml(msg.content || '').replace(/\n/g, '<br>');
    }

    // ===================== 嵌套历史记录弹窗 =====================
    function openHistoryModal(historyDataStr) {
        try {
            const data = typeof historyDataStr === 'string' ? JSON.parse(historyDataStr) : historyDataStr;
            document.getElementById('historyModal').classList.add('active');
            const body = document.getElementById('historyModalBody');
            const msgs = data.messages || data.records || [];
            if (!msgs.length) { body.innerHTML = '<div style="text-align:center;color:#999;padding:40px;">暂无记录</div>'; return; }
            body.innerHTML = msgs.map(msg => renderMessageItem(msg)).join('');
        } catch(e) {
            document.getElementById('historyModalBody').innerHTML = '<div style="text-align:center;color:#FF4D4F;padding:40px;">记录解析失败</div>';
        }
    }

    function closeHistoryModal() {
        document.getElementById('historyModal').classList.remove('active');
    }

    // ===================== 消息操作 =====================
    function withdrawMessage(msgId, groupId) {
        if (!msgId || msgId === 'unknown') { toast('无效的消息ID','error'); return; }
        if (!confirm('确定要撤回此消息吗？')) return;
        fetch('api/admin/withdraw_message.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({message_id:msgId, group_id:groupId}) })
            .then(r => r.json()).then(d => {
                if (d.success) {
                    const el = document.querySelector(`[data-message-id="${msgId}"]`);
                    if (el) el.remove();
                    toast('消息已撤回','success');
                } else toast('撤回失败: '+(d.message||''),'error');
            }).catch(() => toast('撤回失败','error'));
    }

    function withdrawAllMessages() {
        if (!currentSelectedGroupId) { toast('请先选择群聊','warning'); return; }
        if (!confirm('确定要撤回该群聊所有消息吗？此操作不可恢复！')) return;
        fetch('api/admin/withdraw_all_messages.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({group_id:currentSelectedGroupId}) })
            .then(r => r.json()).then(d => {
                if (d.success) { loadGroupMessages(currentSelectedGroupId, true); toast('所有消息已撤回','success'); }
                else toast('操作失败','error');
            }).catch(() => toast('操作失败','error'));
    }

    function refreshMessages() {
        if (!currentSelectedGroupId) { toast('请先选择群聊','warning'); return; }
        loadGroupMessages(currentSelectedGroupId, true);
    }

    function sendMessage() {
        if (!currentSelectedGroupId) { toast('请先选择群聊','warning'); return; }
        if (currentMessageType === 'image') {
            const file = document.getElementById('mediaUpload')?.files[0];
            if (!file) { toast('请选择图片文件','warning'); return; }
            const fd = new FormData();
            fd.append('group_id', currentSelectedGroupId);
            fd.append('image', file);
            fd.append('type', 'image');
            fetch('api/admin/send_message.php', { method:'POST', body: fd })
                .then(r => r.json()).then(d => {
                    if (d.success) { document.getElementById('mediaUpload').value = ''; loadGroupMessages(currentSelectedGroupId, false); toast('发送成功','success'); }
                    else toast('发送失败: '+(d.message||''),'error');
                }).catch(() => toast('发送失败','error'));
            return;
        }
        const input = document.getElementById('messageInput');
        const content = input.value.trim();
        if (!content) { toast('请输入消息内容','warning'); return; }
        fetch('api/admin/send_message.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({group_id:currentSelectedGroupId, content, type:'text'}) })
            .then(r => r.json()).then(d => {
                if (d.success) { input.value = ''; loadGroupMessages(currentSelectedGroupId, false); }
                else toast('发送失败: '+(d.message||''),'error');
            }).catch(() => toast('发送失败','error'));
    }

    // ===================== 运营数据 =====================
    function loadGroupInfo() {
        setActiveMenuItem('loadGroupInfo');
        document.getElementById('navTitle').textContent = '运营数据';
        const mc = domCache.get('mainContent');
        mc.innerHTML = '<div class="loading">加载中...</div>';

        fetch('api/admin/groups.php').then(r => r.json()).then(groups => {
            if (!Array.isArray(groups)) throw new Error('数据格式错误');
            const totalMembers = groups.reduce((s,g) => s+(g.members?.length||0), 0);
            const totalToday = groups.reduce((s,g) => s+(g.today_active_users||0), 0);
            const totalActive = groups.reduce((s,g) => s+(g.total_active_users||0), 0);

            mc.innerHTML = `
            <div class="stats-grid">
                <div class="stat-card"><div class="stat-card-label">群聊总数</div><div class="stat-card-value green">${groups.length}</div></div>
                <div class="stat-card"><div class="stat-card-label">总成员数</div><div class="stat-card-value blue">${totalMembers}</div></div>
                <div class="stat-card"><div class="stat-card-label">今日活跃</div><div class="stat-card-value orange">${totalToday}</div></div>
                <div class="stat-card"><div class="stat-card-label">累计活跃</div><div class="stat-card-value">${totalActive}</div></div>
            </div>
            <div class="stats-table">
                <table>
                    <thead><tr><th>群聊名称</th><th>群ID</th><th>成员数</th><th>今日活跃</th><th>总活跃</th><th>创建时间</th><th>操作</th></tr></thead>
                    <tbody>
                    ${groups.map(g => `
                        <tr>
                            <td><div style="display:flex;align-items:center;gap:8px;"><img src="${g.avatar||'user.jpg'}" style="width:28px;height:28px;border-radius:4px;object-fit:cover;" onerror="this.src='user.jpg'">${escapeHtml(g.name)}</div></td>
                            <td><code style="background:#F6F7F9;padding:2px 6px;border-radius:3px;font-size:12px;">${escapeHtml(String(g.id))}</code></td>
                            <td>${g.members?.length||0}</td>
                            <td><span style="color:#07C160;font-weight:500;">${g.today_active_users||0}</span></td>
                            <td>${g.total_active_users||0}</td>
                            <td style="font-size:12px;color:#999;">${new Date(g.created_at).toLocaleDateString()}</td>
                            <td><button class="btn btn-sm btn-secondary" onclick="openGroupDetail('${escapeAttr(g.id)}')">详情</button></td>
                        </tr>`).join('')}
                    </tbody>
                </table>
            </div>`;
        }).catch(() => {
            mc.innerHTML = '<div class="empty-state"><div class="empty-state-icon">❌</div><h3 class="empty-state-title">加载失败</h3><p class="empty-state-desc">请刷新重试</p></div>';
        });
    }

    // ===================== 网站配置 =====================
    function loadAdminConfig() {
        setActiveMenuItem('loadAdminConfig');
        document.getElementById('navTitle').textContent = '网站配置';
        const mc = domCache.get('mainContent');
        mc.innerHTML = '<div class="loading">加载中...</div>';

        fetch('api/admin/config.php').then(r => r.json()).then(config => {
            mc.innerHTML = `
            <div class="card">
                <div class="card-header"><h3 class="card-title">⚙️ 网站基础配置</h3></div>
                <div style="margin-top:16px;">
                    <form onsubmit="saveAdminConfig(event)">
                        <div class="form-group"><label class="form-label">网站名称</label><input type="text" class="form-input" name="site_name" value="${escapeHtml(config.site_name||'')}"></div>
                        <div class="form-group"><label class="form-label">网站描述</label><textarea class="form-input form-textarea" name="site_desc" style="min-height:70px;">${escapeHtml(config.site_desc||'')}</textarea></div>
                        <div class="form-group"><label class="form-label">默认欢迎语</label><input type="text" class="form-input" name="welcome_msg" value="${escapeHtml(config.welcome_msg||'')}"></div>
                        <div style="text-align:right;margin-top:16px;"><button type="submit" class="btn btn-primary">💾 保存配置</button></div>
                    </form>
                </div>
            </div>`;
        }).catch(() => {
            mc.innerHTML = `<div class="card"><div class="card-header"><h3 class="card-title">⚙️ 网站配置</h3></div><p style="margin-top:16px;color:#999;">配置加载失败，请检查 api/admin/config.php 是否存在。</p></div>`;
        });
    }

    function loadAdminConfigData() {
        // 静默加载配置数据，不改变页面
        fetch('api/admin/config.php').then(r => r.json()).then(config => {
            apiCache.set('adminConfig', config);
        }).catch(() => {});
    }

    function saveAdminConfig(e) {
        e.preventDefault();
        const fd = new FormData(e.target);
        const data = Object.fromEntries(fd.entries());
        fetch('api/admin/config.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(data) })
            .then(r => r.json()).then(d => {
                if (d.success) toast('配置保存成功','success');
                else toast('保存失败','error');
            }).catch(() => toast('保存失败','error'));
    }

    // ===================== 违禁词管理 =====================
    function openBannedWordsModal() {
        document.getElementById('bannedWordsModal').classList.add('active');
        document.getElementById('groupSelectSection').style.display = 'block';
        document.getElementById('bannedWordsSection').style.display = 'none';

        const gl = document.getElementById('bannedGroupList');
        gl.innerHTML = '<div style="text-align:center;color:#999;padding:20px;">加载中...</div>';

        const groups = apiCache.get('groups');
        const render = (gs) => {
            gl.innerHTML = gs.map(g => `
                <div onclick="openBannedWordsForGroup('${escapeAttr(g.id)}','${escapeAttr(g.name)}')"
                    style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:4px;cursor:pointer;background:white;margin-bottom:6px;border:1px solid #EBEBEB;"
                    onmouseover="this.style.borderColor='#07C160'" onmouseout="this.style.borderColor='#EBEBEB'">
                    <img src="${g.avatar||'user.jpg'}" style="width:32px;height:32px;border-radius:4px;object-fit:cover;" onerror="this.src='user.jpg'">
                    <span style="font-size:14px;font-weight:500;">${escapeHtml(g.name)}</span>
                    <span style="margin-left:auto;font-size:12px;color:#999;">👥${g.members?.length||0}</span>
                </div>`).join('') || '<div style="text-align:center;color:#999;padding:20px;">暂无群聊</div>';
        };

        if (groups) render(groups);
        fetch('api/admin/groups.php').then(r => r.json()).then(gs => { if(Array.isArray(gs)) render(gs); }).catch(() => {});
    }

    function closeBannedWordsModal() { document.getElementById('bannedWordsModal').classList.remove('active'); }

    function openBannedWordsForGroup(groupId, groupName) {
        currentBannedWordsGroupId = groupId;
        document.getElementById('groupSelectSection').style.display = 'none';
        document.getElementById('bannedWordsSection').style.display = 'block';

        fetch(`api/admin/banned_words.php?group_id=${groupId}`).then(r => r.json()).then(data => {
            currentBannedWords = Array.isArray(data.words) ? data.words : [];
            renderBannedWords();
        }).catch(() => { currentBannedWords = []; renderBannedWords(); });
    }

    function renderBannedWords() {
        const el = document.getElementById('bannedWordsList');
        if (!currentBannedWords.length) { el.innerHTML = '<div style="text-align:center;color:#999;padding:16px;">暂无违禁词</div>'; return; }
        el.innerHTML = currentBannedWords.map((w,i) => `
            <div style="display:flex;align-items:center;justify-content:space-between;padding:6px 10px;background:white;border-radius:4px;margin-bottom:4px;border:1px solid #EBEBEB;">
                <span style="font-size:13px;">${escapeHtml(w)}</span>
                <button onclick="removeBannedWord(${i})" style="background:none;border:none;color:#FF4D4F;cursor:pointer;font-size:16px;line-height:1;">×</button>
            </div>`).join('');
    }

    function addBannedWord() {
        const input = document.getElementById('newBannedWord');
        const word = input.value.trim();
        if (!word) { toast('请输入违禁词','warning'); return; }
        if (currentBannedWords.includes(word)) { toast('该词已存在','warning'); return; }
        currentBannedWords.push(word);
        input.value = '';
        renderBannedWords();
    }

    function removeBannedWord(index) {
        currentBannedWords.splice(index, 1);
        renderBannedWords();
    }

    function saveBannedWords() {
        if (!currentBannedWordsGroupId) return;
        fetch('api/admin/banned_words.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({group_id:currentBannedWordsGroupId, words:currentBannedWords}) })
            .then(r => r.json()).then(d => {
                if (d.success) toast('违禁词已保存','success');
                else toast('保存失败','error');
            }).catch(() => toast('保存失败','error'));
    }

    function backToGroupSelect() {
        document.getElementById('groupSelectSection').style.display = 'block';
        document.getElementById('bannedWordsSection').style.display = 'none';
    }

    // ===================== 初始化 =====================
    document.addEventListener('DOMContentLoaded', init);
    </script>
</body>
</html>
