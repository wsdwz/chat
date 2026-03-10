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
/* 使用系统默认字体，确保在国内正常显示 */
/* 如需使用PingFang SC，可考虑使用国内CDN或本地字体 */
* { margin: 0; padding: 0; box-sizing: border-box; outline: none; }
body {
    font-family: -apple-system, BlinkMacSystemFont, 'PingFang SC', 'Helvetica Neue', Arial, sans-serif;
    background-color: #F6F7F9; color: #1A1A1A; font-size: 14px; height: 100vh; overflow: hidden;
}
/* App Layout */
.app-container { display: flex; height: 100vh; }
.sidebar { width: 200px !important; background-color: #FBFBFB !important; border-right: 1px solid #EBEBEB !important; display: flex; flex-direction: column; z-index: 100; }
.sidebar-header { padding: 24px 20px !important; border-bottom: none !important; display: flex; align-items: center; gap: 12px; }
.sidebar-header::before {
    content: ''; display: block; width: 24px; height: 24px; background-color: #FA5151; border-radius: 50%; flex-shrink: 0;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='white'%3E%3Cpath d='M12 2C6.48 2 2 6.03 2 11c0 2.84 1.48 5.37 3.82 7.03-.32 1.34-1.07 2.78-1.12 2.92-.06.16.03.34.19.4.05.02.1.02.15.02.11 0 .22-.06.28-.15 0 0 2.22-2.91 5.38-3.03.43.05.86.08 1.3.08 5.52 0 10-4.03 10-9S17.52 2 12 2z'/%3E%3C/svg%3E");
    background-size: 16px; background-position: center; background-repeat: no-repeat;
}
.sidebar-title, .sidebar-header h2 { font-size: 16px !important; font-weight: 600 !important; color: #1A1A1A !important; margin: 0 !important; }
.sidebar-menu { padding: 10px 12px !important; flex: 1; }
.menu-item { display: flex !important; align-items: center !important; padding: 12px 14px !important; margin-bottom: 2px !important; border-radius: 6px !important; color: #595959 !important; text-decoration: none !important; font-size: 14px !important; border: none !important; background: transparent !important; transform: none !important; position: relative; }
.menu-item::before, .menu-item::after { display: none !important; }
.menu-item:hover { background-color: #F2F2F2 !important; }
.menu-item.active { background-color: #E7F8EE !important; color: #07C160 !important; font-weight: 500 !important; }
.menu-item.active::after { display: none !important; }
.menu-item.active .menu-item-icon, .menu-item.active .menu-item-text { color: #07C160 !important; }
.menu-item-icon { font-size: 16px !important; margin-right: 10px !important; }
.sidebar-footer { padding: 16px 20px; border-top: 1px solid #EBEBEB; display: flex; flex-direction: column; gap: 16px; }
.sidebar-footer-links { display: flex; gap: 24px; color: #595959; font-size: 13px; }
.sidebar-footer-links span { display: flex; align-items: center; gap: 6px; cursor: pointer; }
.sidebar-footer-user { display: flex; justify-content: space-between; align-items: center; }
.sidebar-avatar { width: 24px; height: 24px; border-radius: 50%; background: #333; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; }

/* Main Content */
.main-panel { flex: 1; display: flex; flex-direction: column; background-color: #F6F7F9 !important; overflow: hidden; }
.top-nav { background-color: #F6F7F9 !important; padding: 24px 32px 16px !important; height: auto !important; border-bottom: none !important; display: flex !important; align-items: center !important; justify-content: flex-start !important; }
.nav-left { display: flex; align-items: center; }
.nav-left::before { content: '🤖'; font-size: 24px; margin-right: 12px; }
.nav-title { font-size: 20px !important; font-weight: 600 !important; color: #1A1A1A !important; margin: 0 !important; }
.nav-subtitle, .nav-actions { display: none !important; }
.content-wrapper { flex: 1; padding: 0 32px 32px !important; overflow-y: auto; }

/* Basic Card overrides */
.card { background-color: #FFFFFF !important; border-radius: 4px !important; padding: 24px !important; margin-bottom: 16px !important; box-shadow: none !important; transform: none !important; position: static !important; }
.card::before, .card::after { display: none !important; }
.card:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.04) !important; }
.card-header { padding-bottom: 16px !important; border-bottom: 1px solid #F0F0F0 !important; display:flex; justify-content:space-between; align-items:center; }
.card-title { font-size: 16px !important; font-weight: 600 !important; color: #1A1A1A !important; margin:0!important;}

/* Homepage Elements */
.alert-bar { background-color: white; border-radius: 4px; padding: 16px 24px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #EBEBEB; }
.alert-bar .text { font-size: 13px; color: #595959; display: flex; align-items: center; gap: 8px; }
.alert-bar .text::before { content: '🔊'; font-size: 16px; }
.text-brand { color: #07C160; cursor: pointer; font-size: 13px; font-weight: 500; }
.flex-between { display: flex; align-items: center; justify-content: space-between; }
.grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 24px; }
.grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px; }
.dashed-card { background: white; border: 1px dashed #E0E0E0; border-radius: 4px; padding: 32px 24px; display: flex; align-items: flex-start; gap: 20px; cursor: pointer; transition: 0.2s; }
.dashed-card:hover { border-color: #07C160; }
.dashed-icon { width: 56px; height: 56px; border-radius: 12px; background: #595959; display: flex; align-items: center; justify-content: center; font-size: 24px; color: white; flex-shrink: 0; }
.dashed-content h4 { font-size: 16px; font-weight: 600; color: #1A1A1A; margin-bottom: 8px; }
.dashed-content p { font-size: 13px; color: #8C8C8C; line-height: 1.6; }
.solid-card { background: white; border-radius: 4px; padding: 24px; border: 1px solid #EBEBEB; display: flex; flex-direction: column; }
.solid-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.solid-title { font-size: 16px; font-weight: 500; color: #1A1A1A; }
.solid-body { flex: 1; font-size: 13px; color: #595959; line-height: 1.6; }
.solid-footer { margin-top: 24px; }
.status-pill { display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border: 1px solid #EBEBEB; border-radius: 20px; font-size: 12px; color: #8C8C8C; background: #FAFAFA; }
.status-pill::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: #D9D9D9; }
.status-pill.active::before { background: #07C160; }
.app-panel { background: white; border-radius: 4px; padding: 24px; border: 1px solid #EBEBEB; }
.app-title { font-size: 16px; font-weight: 500; color: #1A1A1A; margin-bottom: 24px; }
.app-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 16px; }
.app-item { border: 1px dashed #E0E0E0; border-radius: 4px; padding: 32px 16px; display: flex; flex-direction: column; align-items: center; gap: 16px; cursor: pointer; transition: 0.2s; }
.app-item:hover { border-color: #07C160; }
.app-name { font-size: 13px; color: #1A1A1A; }
.floating-tools { position: fixed; right: 24px; bottom: 48px; display: flex; flex-direction: column; gap: 12px; z-index: 1000; }
.float-btn { width: 48px; height: 48px; border-radius: 24px; background: white; box-shadow: 0 2px 12px rgba(0,0,0,0.08); display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; border: 1px solid #F0F0F0; }
.float-btn.green { color: #07C160; }
.float-btn.blue { color: #1677FF; }
.float-icon { font-size: 18px; margin-bottom: 2px; }
.float-text { font-size: 10px; }

/* Group Info Page / Operations */
.group-info-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; }
.group-info-card { background: #FFFFFF; border: 1px solid #EBEBEB; border-radius: 4px; padding: 20px; cursor: pointer; transition: box-shadow 0.15s, border-color 0.15s; }
.group-info-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.06); border-color: #D9D9D9; }
.group-info-header { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
.group-info-avatar { width: 56px; height: 56px; border-radius: 8px; object-fit: cover; border: 1px solid #F0F0F0; flex-shrink: 0;}
.group-info-name { font-size: 15px; font-weight: 600; color: #1A1A1A; margin-bottom: 4px; }
.group-info-desc { font-size: 12px; color: #999; text-overflow: ellipsis; white-space: nowrap; overflow: hidden; }
.group-info-stats { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr 1fr; gap: 8px; margin-bottom: 16px; }
.stat-item { text-align: center; }
.stat-value { font-size: 18px; font-weight: 600; color: #1A1A1A; }
.stat-label { font-size: 11px; color: #999; margin-top: 2px; }
.group-info-footer { display: flex; gap: 8px; justify-content: flex-end; border-top: 1px solid #F5F5F5; padding-top: 12px; }

/* Stats Page */
.stats-header { margin-bottom: 24px; }
.stats-title { font-size: 20px; font-weight: 600; color: #1A1A1A; margin-bottom: 4px; }
.stats-sub { font-size: 13px; color: #999; }
.stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
.stat-card { background: white; border-radius: 4px; padding: 24px; border: 1px solid #F0F0F0; }
.stat-card-label { font-size: 13px; color: #999; margin-bottom: 8px; }
.stat-card-value { font-size: 32px; font-weight: 600; color: #1A1A1A; }
.stat-card-value.green { color: #07C160; }
.stat-card-value.blue { color: #1677FF; }
.stat-card-value.orange { color: #FAAD14; }
.stat-card-value.cyan { color: #13C2C2; }
.stats-table { background: white; border-radius: 4px; border: 1px solid #F0F0F0; overflow: hidden; }
.stats-table table { width: 100%; border-collapse: collapse; }
.stats-table th { padding: 14px 20px; text-align: left; font-size: 12px; font-weight: 500; color: #999; background: #FAFAFA; border-bottom: 1px solid #F0F0F0; }
.stats-table td { padding: 16px 20px; font-size: 14px; color: #1A1A1A; border-bottom: 1px solid #F5F5F5; vertical-align: middle; }
.stats-table tr:hover td { background: #FAFAFA; }

/* Chat Configuration Page */
.chat-layout { display: flex; gap: 0; height: calc(100vh - 128px); border: 1px solid #EBEBEB; border-radius: 4px; overflow: hidden; }
.chat-sidebar { width: 260px; background: #FFFFFF; border-right: 1px solid #EBEBEB; display: flex; flex-direction: column; flex-shrink: 0; }
.chat-sidebar-header { padding: 16px 20px; border-bottom: 1px solid #F0F0F0; }
.chat-sidebar-title { font-size: 15px; font-weight: 600; color: #1A1A1A; }
.chat-search { padding: 12px 16px; border-bottom: 1px solid #F0F0F0; }
.chat-search input { width: 100%; padding: 7px 12px; background: #F6F7F9; border: 1px solid #EBEBEB; border-radius: 4px; font-size: 13px; color: #1A1A1A; outline: none; }
.chat-search input:focus { border-color: #07C160; background: white; }
.chat-list { flex: 1; overflow-y: auto; }
.chat-group-item { display: flex; align-items: center; gap: 12px; padding: 14px 16px; border-bottom: 1px solid #F5F5F5; cursor: pointer; transition: background 0.15s; }
.chat-group-item:hover { background-color: #F6F7F9; }
.chat-group-item.selected { background-color: #E7F8EE; }
.chat-group-item img { width: 44px; height: 44px; border-radius: 6px; object-fit: cover; flex-shrink: 0; }
.chat-group-name { font-size: 14px; font-weight: 500; color: #1A1A1A; margin-bottom: 3px; }
.chat-group-meta { font-size: 12px; color: #999; }
.chat-badge { background: #FF4D4F; color: white; font-size: 11px; padding: 1px 5px; border-radius: 8px; margin-left: auto; flex-shrink: 0; }
.chat-main { flex: 1; display: flex; flex-direction: column; background: #FFFFFF; }
.chat-header { padding: 16px 24px; border-bottom: 1px solid #F0F0F0; background: #FAFAFA; display: flex; justify-content: space-between; align-items: center; }
.chat-header-title { font-size: 15px; font-weight: 600; color: #1A1A1A; }
.chat-header-sub { font-size: 12px; color: #999; margin-top: 2px; }
.chat-header-actions { display: flex; gap: 8px; }
.chat-messages { flex: 1; overflow-y: auto; padding: 20px 24px; background: #F6F7F9; }

        .chat-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; background-color: #F6F7F9; animation: slideUpFade 0.4s ease-out; }
        .chat-empty-icon-wrapper { position: relative; width: 88px; height: 88px; background: linear-gradient(145deg, #ffffff 0%, #f5f5f5 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(0,0,0,0.06), inset 0 2px 4px rgba(255,255,255,0.9); margin-bottom: 20px; z-index: 1; }
        .chat-empty-icon-wrapper::after { content: ''; position: absolute; inset: -4px; border-radius: 50%; border: 2px solid rgba(7, 193, 96, 0.15); animation: ripplePulse 2.5s infinite; z-index: -1; }
        
        .chat-empty-title { font-size: 16px; font-weight: 600; color: #1A1A1A; margin: 0 0 8px 0; letter-spacing: 0.5px; }
        .chat-empty-sub { font-size: 13px; color: #999999; margin: 0; max-width: 260px; text-align: center; line-height: 1.6; }
        @keyframes slideUpFade { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes ripplePulse { 0% { transform: scale(1); opacity: 0.8; } 100% { transform: scale(1.3); opacity: 0; } }
.chat-empty-icon { font-size: 48px; opacity: 0.3; }
.chat-footer { padding: 16px 24px; border-top: 1px solid #F0F0F0; background: #FAFAFA; }
.chat-tabs { display: flex; gap: 0; border-bottom: 1px solid #F0F0F0; margin-bottom: 12px; }
.chat-tab { padding: 6px 16px; font-size: 13px; color: #999; cursor: pointer; border-bottom: 2px solid transparent; }
.chat-tab.active { color: #07C160; border-bottom-color: #07C160; }
.chat-input-area { display: flex; gap: 10px; align-items: flex-end; }
.chat-input { flex: 1; padding: 10px 14px; border: 1px solid #EBEBEB; border-radius: 4px; font-size: 14px; resize: vertical; background: white; font-family: inherit; height: 120px; transition: border-color 0.2s; box-shadow: inset 0 1px 3px rgba(0,0,0,0.02); }
.chat-input:focus { border-color: #07C160; outline: none; }


/* 高级聊天气泡样式 */
.chat-msg-item { display: flex; flex-direction: column; margin-bottom: 24px; position: relative; width: 100%; }
.chat-msg-item.is-admin { align-items: flex-end; }
.chat-msg-item.is-user { align-items: flex-start; }

.chat-msg-info { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; font-size: 12px; color: #999; }
.chat-msg-item.is-admin .chat-msg-info { flex-direction: row-reverse; }

.chat-msg-name { font-weight: 500; font-size: 13px; }
.chat-msg-item.is-admin .chat-msg-name { color: #595959; }
.chat-msg-item.is-user .chat-msg-name { color: #595959; }

.chat-msg-bubble-wrap { display: flex; align-items: flex-end; max-width: 35%; gap: 12px; }
.chat-msg-item.is-admin .chat-msg-bubble-wrap { flex-direction: row-reverse; }

.chat-msg-bubble { padding: 10px 14px; border-radius: 8px 12px 12px 12px; font-size: 15px; line-height: 1.5; word-break: break-word; box-shadow: 0 1px 2px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.03); }
.chat-msg-item.is-admin .chat-msg-bubble { background: #95EC69; color: #111; border-radius: 12px 8px 12px 12px; border-color: rgba(0,0,0,0.05); box-shadow: none; }
.chat-msg-item.is-user .chat-msg-bubble { background: #FFFFFF; color: #111; }

.chat-msg-actions { opacity: 0; transition: opacity 0.2s; display: flex; align-items: center; }
.chat-msg-item:hover .chat-msg-actions { opacity: 1; }

.btn-withdraw { width:50px; background: #FF4D4F; color: white; border: none; border-radius: 4px; padding: 2px 8px; font-size: 11px; cursor: pointer; transition: 0.2s; box-shadow: 0 2px 4px rgba(255,77,79,0.2); }
.btn-withdraw:hover { background: #FF7875; transform: scale(1.05); }

/* 图片气泡特殊处理 */
.chat-msg-bubble:has(img) { padding: 0; background: transparent !important; border: none !important; box-shadow: none !important; }
.chat-msg-bubble img { display: block; border-radius: 8px; max-width: 100%; max-height: 250px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border: 1px solid rgba(0,0,0,0.05); cursor: zoom-in; object-fit: contain; background: #fff; }

/* Msg Bubbles */
.msg-item { display: flex; margin-bottom: 20px; }
.msg-item.is-admin { flex-direction: row-reverse; }
.msg-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; flex-shrink: 0; background: #EBEBEB; display: flex; align-items: center; justify-content: center; font-size: 16px; }
.msg-body { max-width: 60%; margin: 0 10px; }
.msg-meta { font-size: 11px; color: #BFBFBF; margin-bottom: 4px; }
.msg-item.is-admin .msg-meta { text-align: right; }
.msg-bubble { padding: 10px 14px; border-radius: 8px; font-size: 14px; line-height: 1.6; background: #FFFFFF; color: #1A1A1A; border: 1px solid #EBEBEB; display: inline-block; }
.msg-item.is-admin .msg-bubble { background: #95EC69; border: none; color: #1A1A1A; }
.msg-actions { margin-top: 4px; font-size: 10px; }
.msg-item.is-admin .msg-actions { text-align: right; }

/* Modals */
.modal { display:none; position:fixed; inset:0; z-index:1000; overflow:hidden; }
.modal-overlay { position:absolute; inset:0; background:rgba(0,0,0,0.4); z-index:1; padding:20px; overflow:hidden; }
.modal.active { display:flex; align-items:center; justify-content:center; overflow:hidden; }
.modal-content { background:white; border-radius:6px; padding:0; width:90%; max-width:560px; max-height:88vh; position:relative; z-index:2; box-shadow: 0 8px 32px rgba(0,0,0,0.12); animation: slideUp 0.2s ease; display:flex; flex-direction:column; }
.modal-content > form, .modal-content > div.modal-body { padding:80px 20px 20px; flex:1; overflow-y:auto; }
.modal-content > form { min-height:0; }
@keyframes slideUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
.modal-header { display:flex; justify-content:space-between; align-items:center; padding: 20px; position: absolute; top: 0; left: 0; right: 0; background: white; z-index: 10; border-radius: 6px 6px 0 0;  flex-shrink:0; }
.modal-body { padding: 24px; }
.modal-title { font-size:16px; font-weight:600; color:#1A1A1A; margin:0; }
.close-btn { background:none; border:none; font-size:20px; cursor:pointer; color:#BFBFBF; line-height:1; }
.close-btn:hover { color: #FF4D4F; }
.detail-section { margin-bottom: 24px; }
.detail-section-title { font-size: 14px; font-weight: 600; color: #1A1A1A; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
.detail-section-title::before { content: ''; width: 3px; height: 14px; background: #07C160; border-radius: 2px; flex-shrink: 0; }
.detail-grid { display: grid; grid-template-columns: 100px 1fr; gap: 12px; }
.detail-label { font-size: 13px; color: #999; }
.detail-value { font-size: 13px; color: #1A1A1A; }
.member-row { display: flex; align-items: center; gap: 12px; padding: 12px 0; border-bottom: 1px solid #F5F5F5; }
.member-row:last-child { border-bottom: none; }
.member-row img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 1px solid #F0F0F0; }
.member-name { font-size: 14px; font-weight: 500; color: #1A1A1A; }
.member-id-text { font-size: 12px; color: #999; }
.modal-footer { display:flex; justify-content:flex-end; gap:8px; padding: 16px 24px; border-top:1px solid #F0F0F0; }

/* Buttons & Utils */
.btn { padding: 7px 16px; border-radius: 4px; font-size: 13px; font-weight: 500; cursor: pointer; border: 1px solid transparent; display: inline-flex; align-items: center; gap: 6px; font-family: inherit; }
.btn::before, .btn::after { display: none !important; }
.btn:hover { transform: none !important; box-shadow: none !important; }
.btn-primary { background:#07C160; color:white; border-color:#07C160; }
.btn-primary:hover { background:#06AD56; }
.btn-secondary { background:white; color:#595959; border-color:#EBEBEB; }
.btn-secondary:hover { border-color:#BFBFBF; }
.btn-danger { background:#FF4D4F; color:white; border-color:#FF4D4F; }
.btn-sm { padding: 5px 12px; font-size: 12px; }
.btn-xs { padding: 2px 8px; font-size: 11px; }
.tag { display:inline-flex; align-items:center; padding:2px 8px; border-radius:3px; font-size:12px; font-weight:500; }
.tag-success { background:#E7F8EE; color:#07C160; }
.tag-warning { background:#FFFBE6; color:#FAAD14; }
.tag-danger { background:#FFF2F0; color:#FF4D4F; }
.form-group { margin-bottom:16px; }
.form-label { display:flex; align-items:center; gap:6px; font-size:14px; font-weight:500; margin-bottom:6px; color:#1A1A1A; }
.form-label::before { content:''; width:2px; height:12px; background:#07C160; border-radius:1px; flex-shrink:0; }
.form-input { width:100%; padding:9px 12px; border:1px solid #EBEBEB; border-radius:4px; font-size:14px; background:white; color:#1A1A1A; outline:none; font-family:inherit; }
.form-input:focus { border-color:#07C160; }
.form-textarea { resize:vertical; min-height:100px; line-height:1.6; }
::-webkit-scrollbar { width: 4px; }
::-webkit-scrollbar-thumb { background: #E0E0E0; border-radius: 4px; }
::-webkit-scrollbar-track { background: transparent; }
.loading { display:flex; flex-direction:column; justify-content:center; align-items:center; padding:60px 20px; color:#999; gap:12px; font-size:13px; }
.loading::after { content:''; width:22px; height:22px; border:2px solid #EBEBEB; border-top-color:#07C160; border-radius:50%; animation:spin 0.7s linear infinite; }
.empty-state { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:64px 20px; text-align:center; color:#BFBFBF; }
.empty-state-icon { font-size:44px; margin-bottom:12px; opacity:0.3; }
.empty-state-title { font-size:15px; font-weight:600; margin-bottom:6px; color:#8C8C8C; }
.empty-state-desc { font-size:13px; line-height:1.6; max-width:340px; color:#BFBFBF; }

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
        .toggle-switch input:checked + .toggle-slider {
            background-color: #07C160;
        }
        .toggle-switch input:checked + .toggle-slider:before {
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
            animation: slideIn 0.3s ease-out forwards, fadeOut 0.3s ease-in 2.7s forwards;
        }
        
        .toast.success { background-color: #07C160; }
        .toast.error { background-color: #FF4D4F; }
        .toast.warning { background-color: #FAAD14; }
        
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }

        /* 卡片消息样式（admin端）*/
        .admin-msg-card {
            background: #FFFFFF;
            border-radius: 10px;
            width: 220px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
            overflow: hidden;
            border: 0.5px solid rgba(0,0,0,0.06);
            text-decoration: none;
            display: flex;
            flex-direction: column;
        }
        .admin-msg-card-body {
            padding: 10px 12px;
            display: flex;
            gap: 8px;
            align-items: flex-start;
        }
        .admin-msg-card-info { flex: 1; min-width: 0; }
        .admin-msg-card-title {
            font-size: 14px;
            font-weight: 500;
            color: #1A1A1A;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        .admin-msg-card-desc {
            font-size: 12px;
            color: #999;
            margin-top: 3px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        .admin-msg-card-thumb {
            width: 44px;
            height: 44px;
            border-radius: 6px;
            object-fit: cover;
            flex-shrink: 0;
            background: #F3F4F6;
        }
        .admin-msg-card-footer {
            padding: 5px 12px;
            font-size: 11px;
            color: #999;
            border-top: 0.5px solid #F0F0F0;
            background: #FAFAFA;
        }
        /* 合并转发卡片（admin端）*/
        .admin-msg-history {
            background: #FFFFFF;
            border-radius: 10px;
            width: 230px;
            padding: 10px 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
            border: 0.5px solid rgba(0,0,0,0.06);
            cursor: pointer;
        }
        .admin-msg-history-title {
            font-size: 13px;
            font-weight: 500;
            color: #1A1A1A;
            margin-bottom: 6px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .admin-msg-history-item {
            font-size: 12px;
            color: #878B99;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.4;
            margin-bottom: 2px;
        }
        .admin-msg-history-footer {
            font-size: 11px;
            color: #B0B3BF;
            padding-top: 6px;
            border-top: 0.5px solid #F0F0F0;
            margin-top: 4px;
        }
        /* 文件消息（admin端）*/
        .admin-msg-file {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: #FFFFFF;
            border-radius: 8px;
            text-decoration: none;
            color: #1A1A1A;
            border: 0.5px solid rgba(0,0,0,0.06);
            max-width: 200px;
        }
        /* 系统消息（admin端）*/
        .admin-msg-system {
            text-align: center;
            color: #999;
            font-size: 12px;
            padding: 4px 10px;
            background: rgba(0,0,0,0.04);
            border-radius: 4px;
            display: inline-block;
        }
    </style>
</head>
<body>
<div class="toast-container" id="toastContainer"></div>
<div class="app-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <span class="sidebar-title">管理后台</span>
        </div>
        <nav class="sidebar-menu">
            <a href="#" class="menu-item" onclick="showPage('dashboard')">
                <span class="menu-item-icon">🏠</span>
                <span class="menu-item-text">控制台</span>
            </a>
            <a href="#" class="menu-item" onclick="showPage('groups')">
                <span class="menu-item-icon">👥</span>
                <span class="menu-item-text">群聊管理</span>
            </a>
            <a href="#" class="menu-item" onclick="showPage('chat')">
                <span class="menu-item-icon">💬</span>
                <span class="menu-item-text">聊天记录</span>
            </a>
            <a href="#" class="menu-item" onclick="showPage('users')">
                <span class="menu-item-icon">👤</span>
                <span class="menu-item-text">用户管理</span>
            </a>
            <a href="#" class="menu-item" onclick="showPage('stats')">
                <span class="menu-item-icon">📊</span>
                <span class="menu-item-text">数据统计</span>
            </a>
            <a href="#" class="menu-item" onclick="showPage('settings')">
                <span class="menu-item-icon">⚙️</span>
                <span class="menu-item-text">系统设置</span>
            </a>
        </nav>
        <div class="sidebar-footer">
            <div class="sidebar-footer-user">
                <div style="display:flex;align-items:center;gap:8px;">
                    <div class="sidebar-avatar">A</div>
                    <span style="font-size:13px;color:#1A1A1A;">管理员</span>
                </div>
                <a href="admin_logout.php" style="font-size:12px;color:#999;text-decoration:none;">退出</a>
            </div>
        </div>
    </div>

    <!-- Main Panel -->
    <div class="main-panel">
        <div class="top-nav">
            <div class="nav-left">
                <span class="nav-title" id="pageTitle">控制台</span>
            </div>
        </div>
        <div class="content-wrapper" id="contentWrapper">
            <!-- 内容由JS动态渲染 -->
        </div>
    </div>
</div>

<script>
// ==================== 工具函数 ====================
function showToast(msg, type = 'success') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = msg;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

function escapeHtml(str) {
    if (typeof str !== 'string') return '';
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ==================== 消息内容渲染（对齐 index.php 逻辑）====================
function renderMessageContent(message) {
    const type = message.type || 'text';
    let content = message.content || '';

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
        const safeUrl = escapeHtml(content);
        return `<img src="${safeUrl}" style="max-width:180px;max-height:220px;border-radius:8px;display:block;cursor:zoom-in;object-fit:cover;border:0.5px solid rgba(0,0,0,0.06);" onclick="window.open('${safeUrl}','_blank')">`;
    }

    // ---------- 视频 ----------
    if (type === 'video') {
        const safeUrl = escapeHtml(content);
        return `<video src="${safeUrl}" controls preload="metadata" style="max-width:180px;max-height:220px;border-radius:8px;display:block;background:#000;"></video>`;
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
                const thumbHtml = payload.thumbUrl
                    ? `<img src="${escapeHtml(payload.thumbUrl)}" class="admin-msg-card-thumb">`
                    : '';
                return `<div class="admin-msg-card">
                    <div class="admin-msg-card-body">
                        <div class="admin-msg-card-info">
                            <div class="admin-msg-card-title">${escapeHtml(payload.title || '应用推荐')}</div>
                            <div class="admin-msg-card-desc">${escapeHtml(payload.desc || '')}</div>
                        </div>
                        ${thumbHtml}
                    </div>
                    <div class="admin-msg-card-footer">${escapeHtml(payload.appName || '来自外部应用')}</div>
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
                    return `<div class="admin-msg-history-item">${escapeHtml(it.from || '')}: ${escapeHtml(preview)}</div>`;
                }).join('');
                const encodedPayload = encodeURIComponent(JSON.stringify(payload));
                return `<div class="admin-msg-history" data-payload="${encodedPayload}" onclick="adminOpenHistoryModal(this.getAttribute('data-payload'))">
                    <div class="admin-msg-history-title">${escapeHtml(payload.title || '群聊的聊天记录')}</div>
                    <div>${itemsHtml}</div>
                    <div class="admin-msg-history-footer">查看 ${items.length} 条转发消息</div>
                </div>`;
            }
        } catch(e) {
            // JSON 解析失败，回退显示原文
            return `<span style="color:#FF4D4F;font-size:12px;">[解析失败] </span>${escapeHtml(content)}`;
        }
    }

    // ---------- 文本（默认，含 @全体成员 高亮）----------
    let html = escapeHtml(content);
    if (type === 'text' && content.includes('@全体成员')) {
        html = html.replace(/@全体成员/g, '<span style="background:rgba(255,140,0,0.1);color:#FF8C00;padding:0 4px;border-radius:4px;">@全体成员</span>');
    }
    return html;
}

// ==================== 合并转发查看弹窗（admin端）====================
window.adminOpenHistoryModal = function(payloadStr) {
    const payload = JSON.parse(decodeURIComponent(payloadStr));
    const items = payload.items || [];

    const itemsHtml = items.map(it => {
        let contentHtml = '';
        if (it.history_payload) {
            const nested = encodeURIComponent(JSON.stringify(it.history_payload));
            contentHtml = `<div class="admin-msg-history" data-payload="${nested}" onclick="adminOpenHistoryModal(this.getAttribute('data-payload'))" style="margin-top:4px;">
                <div class="admin-msg-history-title">${escapeHtml(it.history_payload.title || '群聊的聊天记录')}</div>
                <div class="admin-msg-history-footer">查看 ${(it.history_payload.items||[]).length} 条转发消息</div>
            </div>`;
        } else if (it.type === 'image' && it.url) {
            contentHtml = `<img src="${escapeHtml(it.url)}" style="max-width:120px;max-height:160px;border-radius:8px;display:block;margin-top:4px;cursor:zoom-in;" onclick="window.open('${escapeHtml(it.url)}','_blank')">`;
        } else if (it.type === 'video' && it.url) {
            contentHtml = `<video src="${escapeHtml(it.url)}" controls preload="metadata" style="max-width:120px;max-height:160px;border-radius:8px;display:block;margin-top:4px;"></video>`;
        } else if (it.type === 'voice' && it.url) {
            contentHtml = `<audio src="${escapeHtml(it.url)}" controls style="max-width:200px;height:36px;margin-top:4px;"></audio>`;
        } else if (it.type === 'file' && it.url) {
            const fn = it.url.split('/').pop() || '文件';
            contentHtml = `<a href="${escapeHtml(it.url)}" target="_blank" style="display:flex;align-items:center;gap:6px;padding:6px 10px;background:#F5F5F5;border-radius:6px;text-decoration:none;color:#000;margin-top:4px;"><span>📄</span><span style="word-break:break-all;font-size:12px;">${escapeHtml(fn)}</span></a>`;
        } else {
            contentHtml = `<span style="font-size:14px;color:#1A1A1A;">${escapeHtml(it.text || '')}</span>`;
        }
        return `<div style="display:flex;padding:12px 16px;border-bottom:0.5px solid rgba(0,0,0,0.04);gap:10px;align-items:flex-start;">
            <img src="${escapeHtml(it.avatar || '/user.jpg')}" style="width:36px;height:36px;border-radius:50%;object-fit:cover;flex-shrink:0;">
            <div style="flex:1;min-width:0;">
                <div style="font-size:12px;color:#878B99;margin-bottom:4px;">${escapeHtml(it.from || '')}</div>
                <div>${contentHtml}</div>
            </div>
        </div>`;
    }).join('');

    // 创建或复用弹窗
    let modal = document.getElementById('adminHistoryModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'adminHistoryModal';
        modal.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:9000;display:flex;align-items:center;justify-content:center;';
        modal.innerHTML = `<div style="background:#fff;border-radius:8px;width:90%;max-width:480px;max-height:80vh;display:flex;flex-direction:column;overflow:hidden;">
            <div style="display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:1px solid #F0F0F0;flex-shrink:0;">
                <span id="adminHistoryModalTitle" style="font-size:15px;font-weight:600;"></span>
                <button onclick="document.getElementById('adminHistoryModal').style.display='none'" style="background:none;border:none;font-size:20px;cursor:pointer;color:#999;">×</button>
            </div>
            <div id="adminHistoryModalBody" style="flex:1;overflow-y:auto;"></div>
        </div>`;
        document.body.appendChild(modal);
    }
    document.getElementById('adminHistoryModalTitle').textContent = payload.title || '聊天记录';
    document.getElementById('adminHistoryModalBody').innerHTML = itemsHtml;
    modal.style.display = 'flex';
    modal.onclick = function(e) { if (e.target === modal) modal.style.display = 'none'; };
};

// ==================== 页面路由 ====================
const pages = {};
let currentPage = '';

function showPage(name) {
    currentPage = name;
    const titles = {
        dashboard: '控制台',
        groups: '群聊管理',
        chat: '聊天记录',
        users: '用户管理',
        stats: '数据统计',
        settings: '系统设置'
    };
    document.getElementById('pageTitle').textContent = titles[name] || name;
    document.querySelectorAll('.menu-item').forEach((el, i) => {
        el.classList.remove('active');
    });
    const menuItems = document.querySelectorAll('.menu-item');
    const pageOrder = ['dashboard','groups','chat','users','stats','settings'];
    const idx = pageOrder.indexOf(name);
    if (idx >= 0 && menuItems[idx]) menuItems[idx].classList.add('active');

    const wrapper = document.getElementById('contentWrapper');
    switch(name) {
        case 'dashboard': renderDashboard(wrapper); break;
        case 'groups': renderGroupsPage(wrapper); break;
        case 'chat': renderChatPage(wrapper); break;
        case 'users': renderUsersPage(wrapper); break;
        case 'stats': renderStatsPage(wrapper); break;
        case 'settings': renderSettingsPage(wrapper); break;
        default: wrapper.innerHTML = '<div class="loading">页面不存在</div>';
    }
}

// ==================== 控制台 ====================
function renderDashboard(wrapper) {
    wrapper.innerHTML = '<div class="loading"></div>';
    fetch('api/admin/stats.php').then(r=>r.json()).then(data=>{
        wrapper.innerHTML = `
        <div class="stats-grid">
            <div class="stat-card"><div class="stat-card-label">群聊总数</div><div class="stat-card-value blue">${data.total_groups||0}</div></div>
            <div class="stat-card"><div class="stat-card-label">用户总数</div><div class="stat-card-value green">${data.total_users||0}</div></div>
            <div class="stat-card"><div class="stat-card-label">消息总数</div><div class="stat-card-value orange">${data.total_messages||0}</div></div>
            <div class="stat-card"><div class="stat-card-label">今日消息</div><div class="stat-card-value cyan">${data.today_messages||0}</div></div>
        </div>
        `;
    }).catch(()=>{ wrapper.innerHTML = '<div class="empty-state"><div class="empty-state-icon">⚠️</div><div class="empty-state-title">加载失败</div></div>'; });
}

// ==================== 群聊管理 ====================
function renderGroupsPage(wrapper) {
    wrapper.innerHTML = '<div class="loading"></div>';
    fetch('api/admin/groups.php').then(r=>r.json()).then(groups=>{
        if (!Array.isArray(groups) || groups.length === 0) {
            wrapper.innerHTML = '<div class="empty-state"><div class="empty-state-icon">👥</div><div class="empty-state-title">暂无群聊</div></div>';
            return;
        }
        const cards = groups.map(g => `
        <div class="group-info-card" onclick="showGroupDetail('${g.id}')">
            <div class="group-info-header">
                <img class="group-info-avatar" src="${escapeHtml(g.avatar||'/group_avatar.png')}" onerror="this.src='/group_avatar.png'">
                <div>
                    <div class="group-info-name">${escapeHtml(g.name||'未命名群聊')}</div>
                    <div class="group-info-desc">ID: ${escapeHtml(String(g.id))}</div>
                </div>
            </div>
            <div class="group-info-stats">
                <div class="stat-item"><div class="stat-value">${g.member_count||0}</div><div class="stat-label">成员</div></div>
                <div class="stat-item"><div class="stat-value">${g.message_count||0}</div><div class="stat-label">消息</div></div>
            </div>
            <div class="group-info-footer">
                <button class="btn btn-sm btn-secondary" onclick="event.stopPropagation();loadGroupChat('${g.id}','${escapeHtml(g.name||'群聊')}');">查看聊天</button>
            </div>
        </div>
        `).join('');
        wrapper.innerHTML = `<div class="group-info-grid">${cards}</div>`;
    }).catch(()=>{ wrapper.innerHTML = '<div class="empty-state"><div class="empty-state-icon">⚠️</div><div class="empty-state-title">加载失败</div></div>'; });
}

// ==================== 聊天记录页面 ====================
function renderChatPage(wrapper) {
    wrapper.innerHTML = `
    <div class="chat-layout">
        <div class="chat-sidebar">
            <div class="chat-sidebar-header"><div class="chat-sidebar-title">群聊列表</div></div>
            <div class="chat-search"><input type="text" placeholder="搜索群聊..." oninput="filterChatGroups(this.value)"></div>
            <div class="chat-list" id="chatGroupList"><div class="loading"></div></div>
        </div>
        <div class="chat-main" id="chatMain">
            <div class="chat-empty">
                <div class="chat-empty-icon-wrapper"><span style="font-size:40px;">💬</span></div>
                <div class="chat-empty-title">选择一个群聊</div>
                <div class="chat-empty-sub">从左侧选择群聊查看聊天记录</div>
            </div>
        </div>
    </div>
    `;
    loadChatGroupList();
}

let allChatGroups = [];
function loadChatGroupList() {
    fetch('api/admin/groups.php').then(r=>r.json()).then(groups=>{
        allChatGroups = Array.isArray(groups) ? groups : [];
        renderChatGroupList(allChatGroups);
    }).catch(()=>{
        document.getElementById('chatGroupList').innerHTML = '<div class="empty-state"><div class="empty-state-icon">⚠️</div><div class="empty-state-title">加载失败</div></div>';
    });
}

function renderChatGroupList(groups) {
    const list = document.getElementById('chatGroupList');
    if (!list) return;
    if (!groups.length) {
        list.innerHTML = '<div class="empty-state"><div class="empty-state-icon">👥</div><div class="empty-state-title">暂无群聊</div></div>';
        return;
    }
    list.innerHTML = groups.map(g => `
    <div class="chat-group-item" id="cgitem-${g.id}" onclick="loadGroupChat('${g.id}','${escapeHtml(g.name||'群聊')}')">
        <img src="${escapeHtml(g.avatar||'/group_avatar.png')}" onerror="this.src='/group_avatar.png'" style="width:44px;height:44px;border-radius:6px;object-fit:cover;flex-shrink:0;">
        <div style="flex:1;min-width:0;">
            <div class="chat-group-name">${escapeHtml(g.name||'未命名群聊')}</div>
            <div class="chat-group-meta">${g.member_count||0} 人</div>
        </div>
    </div>
    `).join('');
}

function filterChatGroups(q) {
    const filtered = allChatGroups.filter(g => (g.name||'').includes(q) || String(g.id).includes(q));
    renderChatGroupList(filtered);
}

let currentGroupId = null;
let chatPollTimer = null;

function loadGroupChat(groupId, groupName) {
    currentGroupId = groupId;
    if (chatPollTimer) clearInterval(chatPollTimer);

    // 高亮选中项
    document.querySelectorAll('.chat-group-item').forEach(el => el.classList.remove('selected'));
    const selItem = document.getElementById('cgitem-' + groupId);
    if (selItem) selItem.classList.add('selected');

    const main = document.getElementById('chatMain');
    if (!main) return;
    main.innerHTML = `
    <div class="chat-header">
        <div>
            <div class="chat-header-title">${escapeHtml(groupName)}</div>
            <div class="chat-header-sub" id="chatHeaderSub">加载中...</div>
        </div>
        <div class="chat-header-actions">
            <button class="btn btn-sm btn-secondary" onclick="loadGroupMessages('${groupId}', true)">🔄 刷新</button>
        </div>
    </div>
    <div class="chat-messages" id="chatMessages"><div class="loading"></div></div>
    <div class="chat-footer">
        <div class="chat-tabs"><div class="chat-tab active">发送消息</div></div>
        <div class="chat-input-area">
            <textarea class="chat-input" id="adminChatInput" placeholder="以管理员身份发送消息..."></textarea>
            <button class="btn btn-primary" onclick="adminSendMessage('${groupId}')">发送</button>
        </div>
    </div>
    `;
    loadGroupMessages(groupId, true);
    chatPollTimer = setInterval(() => loadGroupMessages(groupId, false), 5000);
}

let lastMsgCount = 0;
function loadGroupMessages(groupId, forceScroll) {
    fetch(`api/chat/get_messages.php?group_id=${groupId}`)
        .then(r=>r.json())
        .then(messages=>{
            const area = document.getElementById('chatMessages');
            if (!area || currentGroupId != groupId) return;

            const sub = document.getElementById('chatHeaderSub');
            if (sub) sub.textContent = `共 ${messages.length} 条消息`;

            if (messages.length === lastMsgCount && !forceScroll) return;
            lastMsgCount = messages.length;

            if (messages.length === 0) {
                area.innerHTML = '<div class="empty-state"><div class="empty-state-icon">💬</div><div class="empty-state-title">暂无消息</div></div>';
                return;
            }

            area.innerHTML = messages.map(msg => {
                const isAdmin = (msg.user_id === 'admin' || msg.is_admin);
                const avatarUrl = escapeHtml(msg.user_avatar || '/user.jpg');
                const senderName = escapeHtml(msg.user_nickname || '用户');
                const timestamp = msg.timestamp || msg.created_at || '';
                const date = timestamp ? new Date(timestamp) : new Date();
                const timeStr = date.getHours().toString().padStart(2,'0') + ':' + date.getMinutes().toString().padStart(2,'0');

                // ---------- 系统消息单独处理 ----------
                if (msg.type === 'system') {
                    return `<div style="text-align:center;margin:12px 0;">${renderMessageContent(msg)}</div>`;
                }

                const contentHtml = renderMessageContent(msg);

                return `
                <div class="chat-msg-item ${isAdmin ? 'is-admin' : 'is-user'}">
                    <div class="chat-msg-info">
                        <img src="${avatarUrl}" style="width:28px;height:28px;border-radius:50%;object-fit:cover;" onerror="this.src='/user.jpg'">
                        <span class="chat-msg-name">${senderName}</span>
                        <span style="font-size:11px;color:#BFBFBF;">${timeStr}</span>
                    </div>
                    <div class="chat-msg-bubble-wrap">
                        <div class="chat-msg-actions">
                            <button class="btn-withdraw" onclick="withdrawMessage('${escapeHtml(String(msg.id))}','${groupId}')">撤回</button>
                        </div>
                        <div class="chat-msg-bubble">${contentHtml}</div>
                    </div>
                </div>`;
            }).join('');

            if (forceScroll) {
                setTimeout(() => { area.scrollTop = area.scrollHeight; }, 80);
            }
        })
        .catch(e => console.error('加载消息失败', e));
}

function adminSendMessage(groupId) {
    const input = document.getElementById('adminChatInput');
    if (!input) return;
    const content = input.value.trim();
    if (!content) return;
    fetch('api/chat/send_message.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            group_id: groupId,
            user_id: 'admin',
            user_nickname: '管理员',
            user_avatar: '/user.jpg',
            type: 'text',
            content: content,
            is_admin: true
        })
    }).then(r=>r.json()).then(res=>{
        if (res.success) {
            input.value = '';
            loadGroupMessages(groupId, true);
        } else {
            showToast(res.message || '发送失败', 'error');
        }
    }).catch(()=>showToast('发送失败','error'));
}

function withdrawMessage(msgId, groupId) {
    if (!confirm('确认撤回此消息？')) return;
    fetch('api/admin/withdraw_message.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message_id: msgId, group_id: groupId })
    }).then(r=>r.json()).then(res=>{
        if (res.success) {
            showToast('撤回成功');
            loadGroupMessages(groupId, false);
        } else {
            showToast(res.message || '撤回失败', 'error');
        }
    }).catch(()=>showToast('撤回失败','error'));
}

// ==================== 用户管理 ====================
function renderUsersPage(wrapper) {
    wrapper.innerHTML = '<div class="loading"></div>';
    fetch('api/admin/users.php').then(r=>r.json()).then(users=>{
        if (!Array.isArray(users) || users.length === 0) {
            wrapper.innerHTML = '<div class="empty-state"><div class="empty-state-icon">👤</div><div class="empty-state-title">暂无用户</div></div>';
            return;
        }
        wrapper.innerHTML = `
        <div class="stats-table">
            <table>
                <thead><tr>
                    <th>用户</th><th>ID</th><th>注册时间</th><th>操作</th>
                </tr></thead>
                <tbody>
                ${users.map(u=>`
                <tr>
                    <td style="display:flex;align-items:center;gap:10px;">
                        <img src="${escapeHtml(u.avatar||'/user.jpg')}" onerror="this.src='/user.jpg'" style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                        <span>${escapeHtml(u.nickname||u.username||u.id)}</span>
                    </td>
                    <td style="color:#999;font-size:12px;">${escapeHtml(String(u.id))}</td>
                    <td style="color:#999;font-size:12px;">${escapeHtml(u.created_at||'-')}</td>
                    <td><button class="btn btn-sm btn-danger" onclick="banUser('${escapeHtml(String(u.id))}')">封禁</button></td>
                </tr>
                `).join('')}
                </tbody>
            </table>
        </div>
        `;
    }).catch(()=>{ wrapper.innerHTML = '<div class="empty-state"><div class="empty-state-icon">⚠️</div><div class="empty-state-title">加载失败</div></div>'; });
}

function banUser(userId) {
    if (!confirm(`确认封禁用户 ${userId}？`)) return;
    showToast('功能待实现', 'warning');
}

// ==================== 数据统计 ====================
function renderStatsPage(wrapper) {
    renderDashboard(wrapper);
}

// ==================== 系统设置 ====================
function renderSettingsPage(wrapper) {
    wrapper.innerHTML = `
    <div class="card">
        <div class="card-header"><span class="card-title">系统设置</span></div>
        <div style="padding-top:20px;">
            <div class="form-group">
                <label class="form-label">管理员密码</label>
                <input type="password" class="form-input" id="newPassword" placeholder="输入新密码">
            </div>
            <button class="btn btn-primary" onclick="changePassword()">保存修改</button>
        </div>
    </div>
    `;
}

function changePassword() {
    const pwd = document.getElementById('newPassword').value.trim();
    if (!pwd) { showToast('请输入新密码', 'warning'); return; }
    showToast('功能待实现', 'warning');
}

// ==================== 群聊详情 ====================
function showGroupDetail(groupId) {
    showPage('chat');
    setTimeout(() => {
        const g = allChatGroups.find(x => String(x.id) === String(groupId));
        if (g) loadGroupChat(g.id, g.name || '群聊');
    }, 300);
}

// ==================== 初始化 ====================
showPage('dashboard');
</script>
</body>
</html>
