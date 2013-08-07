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
    protected $_db_root; //数据库用户名
    protected $_db_database; //数据库名
    protected $_db_prefix; //前缀
    protected $_db_charset; //数据库编码
    protected $_db_type; //数据库类型

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

    function factory($config) {
        $db_config = $this->parseConfig($config);
        $this->dbType = ucwords(strtolower($db_config['db_driver']));
    }

    function parseConfig($config = "") {
        if (empty($config)) {
            $config = Model::getConfig();
        }
        if (is_string($config)) {
            $db_config = $this->parseDSN($config);
        }
        return $db_config;
    }

    public function parseDSN($dsnStr) {
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
