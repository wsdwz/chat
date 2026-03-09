<?php

/**
 * SQLite数据管理类 - 负责SQLite数据库的读写操作
 */
class SQLiteManager {
    private $db;
    private $dbPath;
    private $cache = [];
    private $cacheEnabled = true;
    private $cacheTTL = 30; // 缓存过期时间（秒）
    
    public function __construct() {
        // 将数据库文件放在项目根目录，避免路径和权限问题
        $this->dbPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'chat.db';
        $this->initDatabase();
    }
    
    /**
     * 初始化SQLite数据库
     */
    private function initDatabase() {
        try {
            // 连接数据库
            $this->db = new PDO('sqlite:' . $this->dbPath);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // 创建表结构
            $this->createTables();
        } catch (Exception $e) {
            throw new Exception('SQLite数据库初始化失败: ' . $e->getMessage() . ' (路径: ' . $this->dbPath . ')');
        }
    }
    
    /**
     * 创建数据库表结构
     */
    private function createTables() {
        // 创建chat_groups表
        $this->db->exec("CREATE TABLE IF NOT EXISTS chat_groups (
            id TEXT PRIMARY KEY,
            custom_group_id TEXT,
            name TEXT,
            desc TEXT,
            avatar TEXT,
            banned_words TEXT,
            announcement TEXT,
            allow_speak INTEGER DEFAULT 1,
            allow_image_upload INTEGER DEFAULT 1,
            quick_actions TEXT,
            members TEXT,
            member_titles TEXT,
            member_joined_times TEXT,
            online_users TEXT,
            member_limit INTEGER DEFAULT 0,
            tag TEXT,
            created_at TEXT
        )");
        
        // 为已存在的表添加member_limit字段
        try {
            $this->db->exec("ALTER TABLE chat_groups ADD COLUMN member_limit INTEGER DEFAULT 0");
        } catch (Exception $e) {
            // 忽略错误，因为如果字段已存在，ALTER TABLE会失败
        }
        
        // 为已存在的表添加tag字段
        try {
            $this->db->exec("ALTER TABLE chat_groups ADD COLUMN tag TEXT");
        } catch (Exception $e) {
            // 忽略错误，因为如果字段已存在，ALTER TABLE会失败
        }
        
        // 为已存在的表添加member_joined_times字段
        try {
            $this->db->exec("ALTER TABLE chat_groups ADD COLUMN member_joined_times TEXT");
        } catch (Exception $e) {
            // 忽略错误，因为如果字段已存在，ALTER TABLE会失败
        }
        
        // 创建chat_users表
        $this->db->exec("CREATE TABLE IF NOT EXISTS chat_users (
            id TEXT PRIMARY KEY,
            nickname TEXT,
            avatar TEXT,
            allow_speak INTEGER DEFAULT 1,
            joined_groups TEXT,
            created_at TEXT
        )");
        
        // 创建chat_messages表
        $this->db->exec("CREATE TABLE IF NOT EXISTS chat_messages (
            id TEXT PRIMARY KEY,
            group_id TEXT,
            user_id TEXT,
            user_nickname TEXT,
            user_avatar TEXT,
            type TEXT,
            content TEXT,
            timestamp TEXT,
            is_admin INTEGER DEFAULT 0,
            duration INTEGER
        )");
    }
    
    /**
     * 保存数据到数据库
     * @param string $table 表名
     * @param array $data 数据
     * @param string $id 主键ID（用于更新）
     * @return string 操作结果
     */
    public function save($table, $data, $id = null) {
        // 处理JSON字段
        foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $data[$key] = json_encode($value, JSON_UNESCAPED_UNICODE);
                }
                // 确保布尔字段被正确转换为整数
                if (in_array($key, ['allow_speak', 'allow_image_upload', 'is_admin'])) {
                    $data[$key] = $value ? 1 : 0;
                }
                // 确保tag字段被正确处理
                if ($key === 'tag' && $value === null) {
                    $data[$key] = '';
                }
            }
        
        if ($id) {
            // 更新操作
            $fields = [];
            $values = [];
            
            foreach ($data as $key => $value) {
                // 跳过 id 字段，因为它是主键，不应该被更新
                if ($key !== 'id') {
                    $fields[] = "$key = ?";
                    $values[] = $value;
                }
            }
            $values[] = $id;
            
            $sql = "UPDATE $table SET " . implode(', ', $fields) . " WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($values);
            
            return $id;
        } else {
            // 插入操作
            $keys = array_keys($data);
            $placeholders = array_fill(0, count($keys), '?');
            $values = array_values($data);
            
            $sql = "INSERT INTO $table (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $placeholders) . ")";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($values);
            
            return $data['id'];
        }
    }
    
    /**
     * 从数据库加载数据
     * @param string $table 表名
     * @param array $conditions 查询条件
     * @return array 加载的数据
     */
    public function load($table, $conditions = []) {
        $sql = "SELECT * FROM $table";
        $params = [];
        
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $key => $value) {
                $whereClause[] = "$key = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // 处理JSON字段
            foreach ($row as $key => $value) {
                if (in_array($key, ['banned_words', 'quick_actions', 'members', 'member_titles', 'member_joined_times', 'online_users', 'joined_groups'])) {
                    $row[$key] = json_decode($value, true) ?: [];
                }
                // 确保数字字段被正确转换为整数
                if (in_array($key, ['allow_speak', 'allow_image_upload', 'is_admin', 'duration', 'member_limit'])) {
                    $row[$key] = (int)$value;
                }
                // 确保tag字段被正确加载，避免null值
                if ($key === 'tag' && ($value === null || $value === '')) {
                    $row[$key] = '';
                }
            }
            $result[] = $row;
        }
        
        return $result;
    }
    
    /**
     * 从数据库加载单个数据
     * @param string $table 表名
     * @param string $id 主键ID
     * @return array|null 加载的数据
     */
    public function loadOne($table, $id) {
        $data = $this->load($table, ['id' => $id]);
        return !empty($data) ? $data[0] : null;
    }
    
    /**
     * 删除数据
     * @param string $table 表名
     * @param string $id 主键ID
     * @return bool 是否成功
     */
    public function delete($table, $id) {
        $sql = "DELETE FROM $table WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([$id]);
        return $result;
    }
    
    /**
     * 执行自定义SQL查询
     * @param string $sql SQL语句
     * @param array $params 参数
     * @return array 查询结果
     */
    public function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // 处理JSON字段
            foreach ($row as $key => $value) {
                if (in_array($key, ['banned_words', 'quick_actions', 'members', 'member_titles', 'member_joined_times', 'online_users', 'joined_groups'])) {
                    $row[$key] = json_decode($value, true) ?: [];
                }
                // 确保数字字段被正确转换为整数
                if (in_array($key, ['allow_speak', 'allow_image_upload', 'is_admin', 'duration', 'member_limit'])) {
                    $row[$key] = (int)$value;
                }
                // 确保tag字段被正确加载，避免null值
                if ($key === 'tag' && ($value === null || $value === '')) {
                    $row[$key] = '';
                }
            }
            $result[] = $row;
        }
        
        return $result;
    }
    
    /**
     * 执行SQL语句（无返回结果）
     * @param string $sql SQL语句
     * @param array $params 参数
     * @return bool 是否成功
     */
    public function execute($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute($params);
        return $result;
    }
    
    /**
     * 生成随机ID
     * @return string 随机ID
     */
    public function generateId() {
        $min = 10000; // 5位数最小值
        $max = 9999999999; // 10位数最大值
        return (string)rand($min, $max);
    }
    
    /**
     * 迁移JSON数据到SQLite
     */
    public function migrateFromJson() {
        // 检查是否已有数据
        $groups = $this->load('chat_groups');
        if (!empty($groups)) {
            return; // 已有数据，跳过迁移
        }
        
        try {
            // 迁移groups数据
            $jsonManager = new DataManager();
            $jsonGroups = $jsonManager->load('groups.json');
            foreach ($jsonGroups as $group) {
                $this->save('chat_groups', $group, $group['id']);
            }
            
            // 迁移users数据
            $jsonUsers = $jsonManager->load('users.json');
            foreach ($jsonUsers as $user) {
                $this->save('chat_users', $user, $user['id']);
            }
            
            // 迁移messages数据
            $jsonMessages = $jsonManager->load('messages.json');
            foreach ($jsonMessages as $message) {
                $this->save('chat_messages', $message, $message['id']);
            }
        } catch (Exception $e) {
            // 迁移失败时不抛出异常，允许继续运行
            // 这样即使没有JSON数据，系统也能正常启动
            error_log('数据迁移失败: ' . $e->getMessage());
        }
    }
}

?>