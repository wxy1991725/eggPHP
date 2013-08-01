<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 模型基类
 * 实现了ORM和ActiveRecords模式
 * @author WXY
 */
class Model {

    /**
     * ActiveRecords 参数存储
     * @var type 
     */
    private $_model_active = array();

    /**
     * 返回的结果集
     * @var type 
     */
    private $_model_result = array();

    /**
     * 当前连接的数据库实例
     * @var type 
     */
    private $_sql_conn;

    /**
     * 数据库连接开关 
     * @var  boolean
     */
    private $_conn_active = false;

    /**
     * 主配置参数
     * @var Config 
     */
    private $_config;

    /**
     * 数据库配置数组
     * @var type 
     */
    private $_db_config_array;

    /**
     * 数据库表名
     * @var type 
     */
    protected $_table_name;

    /**
     * 数据库表名
     * @var type 
     */
    protected $_table_name;

    /**
     * 数据库名
     * @var type 
     */
    protected $_database;
    protected $_db_config_prefix = '';

//    public function openSql($tablename = null) {
//        $this->_sql_conn = Db::build($tablename);
//        if ($this->_sql_conn) {
//            $this->_conn_active = true;
//        } else {
//            $this->_conn_active = false;
//        }
//    }

    /**
     * 添加
     * @return type
     */
    function insert() {
        if ($this->_conn_active) {
            return $this->_sql_conn->insert($this->_model_active);
        } else {
            throw new Exception('数据库拒绝访问!');
        }
    }

    /**
     * 更新
     * @return type
     */
    function update() {
        if ($this->_conn_active) {
            return $this->_sql_conn->update($this->_model_active);
        } else {
            throw new Exception('数据库拒绝访问!');
        }
    }

    public function __set($name, $value) {
        $this->_model_active[$name] = $value;
    }

    public function __get($name) {
        return isset($this->_model_result[$name]) ? $this->_model_result[$name] : null;
    }

    public function __construct(string $tablename = '', $prefix = '', $conncetion = array()) {
        $this->_init();
        if (is_string($conncetion)) {
            $conncetion = self::getConfig($conncetion);
        }
        if (empty($conncetion)) {
            $conncetion = self::getConfig('local');
        }
        if (empty($tablename)) {
            $this->_config = Config::instance();
            $this->_database = $conncetion['db_name'];
            $this->_table_name = $this->config->router_flag['class'];
        } else {
            if (strpos('.', $tablename) > 0) {
                list($this->_database, $this->_table_name) = explode('.', $tablename);
            } else {
                $this->_database = $conncetion['db_name'];
                $this->_table_name = $tablename;
            }
        }
        if ("" != $prefix) {
            $this->_db_config_prefix = $prefix;
        } else {
            $this->_db_config_prefix = isset($conncetion['db_prefix']) ? $conncetion['db_prefix'] : "";
        }
        $this->db(0, $conncetion);
    }

    function db($linkNum = '', $config = "") {
        static $_db = array();
        static $_linkNum = array();
        if ($linkNum === '' && $config === "") {
            return $_linkNum;
        }
        if (!isset($_db[$linkNum]) || (isset($_db[$linkNum]) && $config && $_linkNum[$linkNum] != $config)) {
            if (!empty($config) && is_string($config)) {
                $config = self::getConfig($config);
            }
            $_db[$linkNum]=  Db::build($config);
        }
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

    function _init() {
        
    }

    /**
     * 自定义设置数据库参数
     * @param type $db
     * @param type $config
     */
    static public function setConfig(string $db, array $config) {
        static::$_db_config_array[$db] = $config;
    }

}

?>