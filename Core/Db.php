<?php

/**
 *  仿TP的数据库中间类
 */
class Db {

    /**
     * 配置参数
     * @var array 
     */
    protected static $_db_config_array = array();
    private $_db_tablename = null;

    /**
     * 当前操作所属的模型名
     * @var string 
     */
    protected $model = '';
    protected $_db_driver;
    protected $_db_host;
    protected $_db_user;
    protected $_db_root; //数据库用户名
    protected $_db_database; //数据库名
    protected $_db_prefix; //前缀
    protected $_db_charset; //数据库编码
    protected $_db_type; //数据库类型

    /**
     * 单例模式 存储与读取数据库实例
     * @staticvar array $db_fool 数据库实例集
     * @param type $config 配置参数
     * @return Db 数据库实例 
     * @throws Exception
     */

    public static function getIntance($config) {
        static $db_fool = array();
        if (empty($config)) {
            throw new Exception('数据库配置参数为空!');
            return;
        }
        $id = md5(serialize($config));
        if (!isset($db_fool[$id])) {
            $db_obj = new self();
            $db_fool[$id] = $db_obj->factory($config);
        }
        return $db_fool[$id];
    }

    /**
     * 工厂生产实例
     * @param type $config 数据库配置
     * @return Db 数据库实例
     * @throws Exception 如果选择的连接类型不存在 则抛出异常 
     */
    function factory($config) {
        $db_config = $this->parseConfig($config);
        $this->dbType = ucwords(strtolower($db_config['db_driver']));
        $class = $this->dbType . '_Db';
        if (class_exists($class)) {
            $db = $class($db_config);
            if ('pdo' != strtolower($db_config['db_driver']))
                $db->dbType = strtoupper($this->dbType);
        }else {
            throw new Exception("配置中的数据库驱动不存在");
        }
        return $db;
    }

    /**
     * 数据库调试 记录当前SQL
     * @access protected
     */
    protected function debug() {
        $this->modelSql[$this->model] = $this->queryStr;
        $this->model = '_think_';
        // 记录操作结束时间
        if (C('DB_SQL_LOG')) {
            G('queryEndTime');
            trace($this->queryStr . ' [ RunTime:' . G('queryStartTime', 'queryEndTime', 6) . 's ]', '', 'SQL');
        }
    }

    /**
     * 转化配置，使之能被解读
     * @param mixed $config  配置参数
     * @return array 转化后的配置数组
     */
    function parseConfig($config = "") {
        if (empty($config)) {
            $config = Model::getConfig();
        }
        if (is_string($config)) {
            $db_config = $this->parseDSN($config);
        }
        return $db_config;
    }

    /**
     * 设置当前操作模型
     * @access public
     * @param string $model  模型名
     * @return void
     */
    public function setModel($model) {
        $this->model = $model;
    }

    /**
     * 
     * @param string $dsnStr DSN 表达式
     * @return boolean|string
     */
    public function parseDSN(string $dsnStr) {
        if (empty($dsnStr)) {
            return false;
        }
        $info = parse_url($dsnStr);
        if ($info['scheme']) {
            $dsn = array(
                'db_driver' => $info['scheme'],
                'db_usr' => isset($info['user']) ? $info['user'] : '',
                'db_pwd' => isset($info['pass']) ? $info['pass'] : '',
                'db_host' => isset($info['host']) ? $info['host'] : '',
                'db_port' => isset($info['port']) ? $info['port'] : '',
                'db_name' => isset($info['path']) ? substr($info['path'], 1) : ''
            );
        } else {
            preg_match('/^(.*?)\:\/\/(.*?)\:(.*?)\@(.*?)\:([0-9]{1, 6})\/(.*?)$/', trim($dsnStr), $matches);
            $dsn = array(
                'db_driver' => $matches[1],
                'db_usr' => $matches[2],
                'db_pwd' => $matches[3],
                'db_host' => $matches[4],
                'db_port' => $matches[5],
                'db_name' => $matches[6]
            );
        }
        $dsn['dsn'] = ''; // 兼容配置信息数组
        return $dsn;
    }

}

?>
