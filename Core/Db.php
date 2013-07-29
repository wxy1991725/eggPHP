<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Db {

    protected static $_db_config_array = array();
    private $_db_tablename = null;
    protected $_db_driver;
    protected $_db_host;
    protected $_db_user;
    protected $_db_root;//数据库用户名
    protected $_db_database;//数据库名
    protected $_db_prefix;//前缀
    protected $_db_charset;//数据库编码
    protected $_db_type;//数据库类型

    /**
     *  数据库pdo实例
     * @var pdo 
     */
    private $_db_obj;

    /**
     * 建立数据库连接
     * @staticvar null $_model_db 记录连接的资源 用于单例使用
     * @param type $table 表名
     * @param type $rest 是否重置连接
     * @param type $dbconfig 数据库配置
     * @return Db 返回连接的数据库实例
     */
    static public function build($table, $rest = false, $dbconfig = 'local') {
        static $_model_db = null;
        if ($rest === true) {
            $_model_db = null;
        }
        if (!isset($_model_db)) {
            $db_config = Db::getConfig($dbconfig);
            $_model_db = new self($table, $db_config);
        }
        return $_model_db;
    }

    private function __construct($table, $db_config) {
        $this->_db_prefix = isset($db_config['db_prefix']) ? $db_config['db_prefix'] : '';
        $this->_db_host = isset($db_config['db_host']) ? $db_config['db_host'] : '127.0.0.1';
        $this->_db_user = isset($db_config['db_user']) ? $db_config['db_user'] : 'root';
        $this->_db_root = isset($db_config['db_root']) ? $db_config['db_root'] : 'root';
        $this->_db_database = isset($db_config['db_name']) ? $db_config['db_name'] : '';
        $this->_db_charset = isset($db_config['db_charset']) ? $db_config['db_charset'] : 'utf-8';
        $this->_db_driver = isset($db_config['db_driver']) ? $db_config['db_driver'] : 'pdo';
        $this->_db_type = isset($db_config['db_type']) ? $db_config['db_type'] : 'mysql';
        $this->_db_tablename = $this->_db_prefix . $table;
        try {
            switch (strtolower($this->_db_driver)) {
                case 'pdo':
                default :
                    $this->_db_obj = new PDO(
                            $this->_db_type . ':host=' . $$this->_db_host . ';dbname=' . $this->_db_database, $$this->_db_user, $this->_db_charset
                    );
                    $this->_db_obj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    break;
            }
        } catch (PDOException $e) {
            trigger_error('Sql Error!!');
            
            return false;
        }
    }

    private function getTableInfo() {
        
    }

    /**
     * 获得数据库配置
     * @param string $db
     * @return type
     */
    static public function getConfig($db) {
        if (!isset(static::$_db_config_array))
            static::$_db_config_array = require RUN_DIR . 'config' . DS . Debug::get_env() . DS . 'db.php';
        if (isset(static::$_db_config_array[$db])) {
            return static::$_db_config_array[$db];
        }
    }

    /**
     * 自定义设置数据库参数
     * @param type $db
     * @param type $config
     */
    static public function setConfig($db, $config) {
        static::$_db_config_array[$db] = $config;
    }

}

?>
