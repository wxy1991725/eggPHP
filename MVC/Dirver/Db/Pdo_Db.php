<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Pdo_Db
 *
 * @author WXY
 */
class Pdo_Db extends Db {

    public $connected = false;
    protected $PDOStatement = null;
    private $table = '';

    //put your code here
    public function __construct($config) {
        if (!class_exists('PDO')) {
            throw_exception('PDO扩展不存在');
        }
        if (!empty($config)) {
            $this->_db_config_array = $config;
            if (empty($this->_db_config_array['params'])) {
                $this->_db_config_array['params'] = array();
            }
        }
    }

    /**
     * 释放查询结果
     * @access public
     */
    public function free() {
        $this->PDOStatement = null;
    }

    /**
     * 执行查询 返回数据集
     * @access public
     * @param string $str  sql指令
     * @param array $bind 参数绑定
     * @return mixed
     */
    public function query($str, $bind = array()) {
        $this->initConnect();
        if (!$this->_linkID)
            return false;
        $this->queryStr = $str;
        if (!empty($bind)) {
            $this->queryStr .= '[ ' . print_r($bind, true) . ' ]';
        }
        //释放前次的查询结果
        if (!empty($this->PDOStatement))
            $this->free();
        Debug::time('db_query:' . $str);
        // 记录开始执行时间
        Debug::memory('db_query:' . $str);
        $this->PDOStatement = $this->_linkID->prepare($str);
        if (false === $this->PDOStatement)
            throw_exception($this->error());
        $result = $this->PDOStatement->execute($bind);
        $this->debug();
        if (false === $result) {
            $this->error();
            return false;
        } else {
            return $this->getAll();
        }
    }

    public function initConnect() {
        if (!$this->connected)
            $this->_linkID = $this->connect();
    }

    public function getFields($tableName) {
        $this->initConnect();
        switch ($this->dbType) {
            case 'MSSQL':
            case 'SQLSRV':
                $sql = "SELECT   column_name as 'Name',   data_type as 'Type',   column_default as 'Default',   is_nullable as 'Null'
        FROM    information_schema.tables AS t
        JOIN    information_schema.columns AS c
        ON  t.table_catalog = c.table_catalog
        AND t.table_schema  = c.table_schema
        AND t.table_name    = c.table_name
        WHERE   t.table_name = '$tableName'";
                break;
            case 'SQLITE':
                $sql = 'PRAGMA table_info (' . $tableName . ') ';
                break;
            case 'ORACLE':
            case 'OCI':
                $sql = "SELECT a.column_name \"Name\",data_type \"Type\",decode(nullable,'Y',0,1) notnull,data_default \"Default\",decode(a.column_name,b.column_name,1,0) \"pk\" "
                        . "FROM user_tab_columns a,(SELECT column_name FROM user_constraints c,user_cons_columns col "
                        . "WHERE c.constraint_name=col.constraint_name AND c.constraint_type='P' and c.table_name='" . strtoupper($tableName)
                        . "') b where table_name='" . strtoupper($tableName) . "' and a.column_name=b.column_name(+)";
                break;
            case 'PGSQL':
                $sql = 'select fields_name as "Name",fields_type as "Type",fields_not_null as "Null",fields_key_name as "Key",fields_default as "Default",fields_default as "Extra" from table_msg(' . $tableName . ');';
                break;
            case 'IBASE':
                break;
            case 'MYSQL':
            default:
                $sql = 'DESCRIBE ' . $tableName; //备注: 驱动类不只针对mysql，不能加``
        }

        $result = $this->query($sql);
        $info = array();
        if ($result) {
            foreach ($result as $key => $val) {
                $val = array_change_key_case($val);
                $val['name'] = isset($val['name']) ? $val['name'] : "";
                $val['type'] = isset($val['type']) ? $val['type'] : "";
                $name = isset($val['field']) ? $val['field'] : $val['name'];
                $info[$name] = array(
                    'name' => $name,
                    'type' => $val['type'],
                    'notnull' => (bool) (((isset($val['null'])) && ($val['null'] === '')) || ((isset($val['notnull'])) && ($val['notnull'] === ''))), // not null is empty, null is yes
                    'default' => isset($val['default']) ? $val['default'] : (isset($val['dflt_value']) ? $val['dflt_value'] : ""),
                    'primary' => isset($val['dey']) ? strtolower($val['dey']) == 'pri' : (isset($val['pk']) ? $val['pk'] : false),
                    'autoinc' => isset($val['extra']) ? strtolower($val['extra']) == 'auto_increment' : (isset($val['key']) ? $val['key'] : false),
                );
            }
        }
        return $info;
    }

}

?>
