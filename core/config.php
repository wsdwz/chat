<?php

/**
 * 项目配置文件
 */
return [
    // 项目名称
    'app_name' => 'Modern Chat Room',
    
    // 文件上传配置
    'upload' => [
        'max_size' => 50 * 1024 * 1024, // 50MB
        'allowed_types' => [
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'video' => ['mp4', 'avi', 'mov', 'wmv'],
            'file' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'rar', 'txt']
        ],
        'paths' => [
            'image' => 'uploads/images/',
            'video' => 'uploads/videos/',
            'file' => 'uploads/files/'
        ]
    ],
    
    // 消息配置
    'message' => [
        'max_length' => 1000,
        'polling_interval' => 1000 // 1秒
    ],
    
    // 安全配置
    'security' => [
        'allowed_ips' => ['*'], // 允许所有IP访问
        'csrf_protection' => false
    ],
    
    // 管理员配置
    'admin' => [
        'username' => 'admin',
        'password' => 'admin123'
    ]
];

?>