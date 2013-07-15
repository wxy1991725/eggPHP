<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Db {

    protected $_db_config_array = array();
    private $_db_name = null;

    static public function buildPDO($table, $rest = false) {
        static $pdo_conn;
        if ($rest === true) {
            $this->_db_name = $table;
        }
        if (empty($pdo_conn)) {
            $this->_db_name = $table;
            $pdo_conn = new PDO($dsn, $username, $passwd, $options);
        }
    }

    /**
     * 获得数据库配置
     * @param string $db
     * @return type
     */
    public function getConfig($db) {
        if (!isset($this->_db_config_array))
            $this->_db_config_array = require RUN_DIR . 'config' . DS . Debug::get_env() . DS . 'db.php';
        if (isset($this->_db_config_array[$db])) {
            return $this->_db_config_array[$db];
        }
    }

    public function setConfig($db, $config) {
        $this->_db_config_array[$db] = $config;
    }

}

?>
