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

    static public function openSql($tablename = null) {
        $this->_sql_conn=  Db::
    }

}

?>
