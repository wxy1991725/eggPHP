<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 模型基类
 *
 * @author WXY
 */
class Model {

    /**
     *  AR
     * @var type 
     */
    private $_model_active = array();
    //put your code here
    private $_sql_conn;
    private $_conn_active = false;

    static public function openSql($tablename = null) {
        $this->_sql_conn = Db::build($tablename);
        if ($this->_sql_conn) {
            $this->_conn_active = true;
        } else {
            $this->_conn_active = false;
        }
    }
    /**
     * 添加
     * @return type
     */
    function insert() {
        if ($this->_conn_active) {
            return $this->_sql_conn->insert($this->_model_active);
        }
    }
    /**
     * 更新
     * @return type
     */
    function update() {
        if ($this->_conn_active) {
            return $this->_sql_conn->update($this->_model_active);
        }
    }

    public function __construct() {
        ;
    }

    public function __call($name, $arguments) {
        return call_user_func_array(array("Db", $name), array($this->_model_active, $arguments));
    }

}

?>