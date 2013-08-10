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
     * 数据库连接实例
     * @var type 
     */
    private $db;

    /**
     * 返回的结果集
     * @var type 
     */
    private $_model_result = array();

    /**
     * 当前实例名
     * @var Model 
     */
    private $name;
    /**
     * 数据信息
     * 
     */
    protected $data             =   array();
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
     *  带数据库+前缀+模型的字符串
     * @var string 
     */
    private $trueTableName;

    /**
     * 主配置参数
     * @var Config 
     */
    private $_config;

    /**
     * 数据库配置数组
     * @var type 
     */
    static private $_db_config_array;

    /**
     * 数据库表名
     * @var type 
     */
    protected $_table_name;

    /**
     * 字段缓存
     * @var array 
     */
    protected $_fields;

    /**
     * 数据库名
     * @var type 
     */
    protected $_database;
    protected $_db_config_prefix = '';


    /**
     * 设置数据对象的值
     * @access public
     * @param string $name 名称
     * @param mixed $value 值
     * @return void
     */
    public function __set($name,$value) {
        // 设置数据对象属性
        $this->data[$name]  =   $value;
    }

    /**
     * 获取数据对象的值
     * @access public
     * @param string $name 名称
     * @return mixed
     */
    public function __get($name) {
        return isset($this->data[$name])?$this->data[$name]:null;
    }

    /**
     * 检测数据对象的值
     * @access public
     * @param string $name 名称
     * @return boolean
     */
    public function __isset($name) {
        return isset($this->data[$name]);
    }

    /**
     * 销毁数据对象的值
     * @access public
     * @param string $name 名称
     * @return void
     */
    public function __unset($name) {
        unset($this->data[$name]);
    }


    public function __construct($tablename = '', $prefix = '', $conncetion = array()) {
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
            if (!empty($config) && is_string($config) && strpos($config, '/') === false) {
                $config = self::getConfig($config);
            }
            $_db[$linkNum] = Db::getIntance($config);
        } elseif ($config === null) {
            $_db[$linkNum]->close();
            unset($_db[$linkNum]);
            return;
        }
        // 记录连接信息
        $_linkNum[$linkNum] = $config;
        // 切换数据库连接
        $this->db = $_db[$linkNum];
        $this->_after_db();
        // 字段检测
        if (!empty($this->_table_name))
            $this->_checkTableInfo();
        return $this;
    }

    private function _checkTableInfo() {
        $db = MODELCACHE_DIR . $this->_database . "." . $this->_db_config_prefix . $this->_table_name . ".php";
        if (empty($this->_fields)) {
            if (file_exists($db)) {
                $modelCache = Tools::import($db, true);
                if ($modelCache) {
                    $this->_fields = $modelCache;
                    return;
                }
            }
        }
        $this->flush($db);
    }

    private function flush($db) {
        $this->db->setModel($this->name);
        $fields = $this->db->getFields($this->getTableName());
        if (!$fields) { // 无法获取字段信息
            return false;
        }
        $this->_fields = array_keys($fields);
        $this->_fields['_autoinc'] = false;
        foreach ($fields as $key => $val) {
            // 记录字段类型
            $type[$key] = $val['type'];
            if ($val['primary']) {
                $this->_fields['_pk'] = $key;
                if ($val['autoinc'])
                    $this->fields['_autoinc'] = true;
            }
        }
        $this->_fields['_type'] = $type;
        Tools::fileappend($db, "<?php\treturn " . var_export($this->_fields, true) . ";?>", 'w');
    }

    /**
     * 得到完整的数据表名
     * @access public
     * @return string
     */
    public function getTableName() {
        if (empty($this->trueTableName)) {
            $tableName = !empty($this->_db_config_prefix) ? $this->_db_config_prefix : '';
            $tableName .= $this->_table_name;
            $this->trueTableName = strtolower($tableName);
        }
        return (!empty($this->_database) ? $this->_database . '.' : '') . $this->trueTableName;
    }

    /**
     * 得到当前的数据对象名称
     * @access public
     * @return string
     */
    public function getModelName() {
        if (empty($this->name))
            $this->name = substr(get_class($this), 0, -5);
        return $this->name;
    }

    /**
     * 数据库连接
     * @param type $config
     */
    static private function build($config) {
        
    }

    public function _after_db() {
        
    }

    /**
     * 获得数据库配置
     * @param string $db
     * @return type
     */
    static public function getConfig($db = 'local') {
        if (!isset(static::$_db_config_array))
            static::$_db_config_array = Tools::import(CONF_DIR . 'db.php', true);
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
        self::$_db_config_array[$db] = $config;
    }

}

?>