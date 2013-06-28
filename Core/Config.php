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
final class Config {

    /**
     *  用户配置存储数组
     * @var type 
     */
    private $_user_config = array();

    /**
     * 生产出配置类
     * @param type $config 载入
     * @return Config  配置对象
     */
    static public function instance($config = null) {
        static $_self = null;
        if ($_self === null) {
            return $_self = new self($config);
        } else {
            return $_self;
        }
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

}

?>
