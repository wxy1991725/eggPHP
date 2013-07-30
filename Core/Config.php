<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 配置类,用于项目初始化
 * 单例模式，全项目可获取
 * @author WXY
 */
class Config implements ArrayAccess {

    /**
     *  用户配置存储数组
     * @var type 
     */
    private $_user_config = array();

    /**
     * 取得配置类的实例，无则创建一个
     * @param type $config 载入
     * @return Config  配置对象
     */
    static public function instance() {
        static $_self = null;
        $parms = func_get_args();
        if (empty($parms)) {
            return $_self['config'];
        }
        if (!empty($parms) && is_array($parms[0])) {
            $type = isset($parms[1]) ? $parms[1] : 'config';
            if (!isset($_self[$type])) {
                $_self[$type] = new self($parms[0], $type);
                return $_self[$type]->_user_config;
            } else {
                return $_self[$type];
            }
        } elseif (is_string($parms[0])) {
            return $_self[$parms[0]];
        }
    }

    static function loadConfig($type = 'config') {
        $configfile = RUN_DIR . 'config' . DS . Debug::get_env() . DS . $type . '.php';
        if (file_exists($configfile)) {
            $config = Tools::import($configfile, true);
            return static::instance($config, $type);
        } else {
            throw new Exception($configfile . '文件不存在!');
        }
    }

    /**
     * 获得单个配置项
     * @param string $name 要获得的属性
     * @return object 内容
     */
    static public function getConfig($name, $config = 'config') {
        return self::instance($config)->$name;
    }

    /**
     * 显示配置列表
     * @return array 配置数组 
     */
    function show() {
        return $this->_user_config;
    }

    /**
     * 添加 可以连贯添加，不可覆盖原配置
     * @param string|int $name 添加的键名
     * @param object $value  要添加的值名
     */
    function add($name, $value) {
        if (!isset($this->_user_config[$name]))
            $this->_user_config[$name] = $value;

        return $this;
    }

    /**
     * 预先加载好配置文件
     * @param type $config 配置数组 
     */
    public function __construct($config) {
        foreach ($config as $key => $value) {
            $this->_user_config[$key] = $value;
        }
    }

    /**
     * 彻底清空配置
     */
    public function reset() {
        $this->_user_config = null;
    }

    /**
     * 设置配置参数
     * @param type $name
     * @param type $value
     */
    public function __set($name, $value) {
        $this->_user_config[strtolower($name)] = $value;
    }

    /**
     * 注销配置参数
     * @param type $name
     */
    public function __unset($name) {
        $this->_user_config[strtolower($name)] = null;
    }

    /**
     * 获得配置参数
     * @param type $name
     * @param type $value
     */
    public function __get($name) {
        return $this->_user_config[strtolower($name)];
    }

    public function storge() {
        return serialize($this->_user_config);
    }

    public function offsetExists($offset) {
        if (isset($this->_user_config[$offset])) {
            return true;
        }
    }

    public function offsetGet($offset) {
        if (isset($this->_user_config[$offset])) {
            return $this->_user_config[$offset];
        } else {
            return null;
        }
    }

    public function offsetSet($offset, $value) {
        $this->_user_config[$offset] = $value;
    }

    public function offsetUnset($offset) {
        unset($this->_user_config[$offset]);
    }

}

?>