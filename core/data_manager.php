<?php

/**
 * 数据管理类 - 负责JSON数据的读写操作
 */
class DataManager {
    private $basePath;
    
    public function __construct() {
        $this->basePath = __DIR__ . '/data/';
        if (!file_exists($this->basePath)) {
            mkdir($this->basePath, 0777, true);
        }
    }
    
    /**
     * 保存数据到JSON文件
     * @param string $filename 文件名
     * @param mixed $data 要保存的数据
     */
    public function save($filename, $data) {
        $file = $this->basePath . $filename;
        file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
    
    /**
     * 从JSON文件加载数据
     * @param string $filename 文件名
     * @return mixed 加载的数据
     */
    public function load($filename) {
        $file = $this->basePath . $filename;
        if (file_exists($file)) {
            $content = file_get_contents($file);
            return json_decode($content, true) ?: [];
        }
        return [];
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
}

?>