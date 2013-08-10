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

    /**
     * 数据结果集
     * $type PDOStatement
     */
    protected $PDOStatement = null;
    private $table = '';

    //put your code here
    public function __construct($config) {
        if (!class_exists('PDO')) {
            throw new Exception('PDO扩展不存在');
        }
        if (!empty($config)) {
            $this->config = $config;
            if (empty($this->config['params'])) {
                $this->config['params'] = array();
            }
        }
    }
    
    

    /**
     * 事务回滚
     * @access public
     * @return boolen
     */
    public function rollback() {
        if ($this->transTimes > 0) {
            $result = $this->_linkID->rollback();
            $this->transTimes = 0;
            if(!$result){
                $this->error();
                return false;
            }
        }
        return true;
    }

    /**
     * 字段和表名处理
     * @access protected
     * @param string $key
     * @return string
     */
    protected function parseKey(&$key) {
        if ($this->dbType == 'MYSQL') {
            $key = trim($key);
            if (!preg_match('/[,\'\"\*\(\)`.\s]/', $key)) {
                $key = '`' . $key . '`';
            }
            return $key;
        } else {
            return parent::parseKey($key);
        }
    }

    /**
     * SQL指令安全简单过滤
     * @access public
     * @param string $str  SQL指令
     * @return string
     */
    public function escapeString($str) {
        switch ($this->dbType) {
            case 'PGSQL':
            case 'MSSQL':
            case 'SQLSRV':
            case 'MYSQL':
                return addslashes($str);
            case 'IBASE':
            case 'SQLITE':
            case 'ORACLE':
            case 'OCI':
                return str_ireplace("'", "''", $str);
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
     * @example
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
            throw new Exception($this->error());
        $result = $this->PDOStatement->execute($bind);
        $this->debug('db_query:' . $str);
        if (false === $result) {
            $this->error();
            return false;
        } else {
            return $this->getAll();
        }
    }

    /**
     * 连接数据库方法
     * @access public
     */
    public function connect($config = '', $linkNum = 0) {
        if (!isset($this->linkID[$linkNum])) {
            if (empty($config))
                $config = $this->config;
            if ($this->pconnect) {
                $config['params'][PDO::ATTR_PERSISTENT] = true;
            }
            try {
                if (!isset($config['dsn'])) {
                    $config['dsn'] = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset={$config['db_charset']}";
                }
                $this->linkID[$linkNum] = new PDO($config['dsn'], $config['db_usr'], $config['db_pwd'], $config['params']);
            } catch (PDOException $e) {
                throw new Exception($e->getMessage());
            }

            if (in_array($this->dbType, array('MSSQL', 'ORACLE', 'IBASE', 'OCI'))) {
                // 由于PDO对于以上的数据库支持不够完美，所以屏蔽了 如果仍然希望使用PDO 可以注释下面一行代码
                throw new Exception('由于目前PDO暂时不能完美支持' . $this->dbType . ' 请使用官方的' . $this->dbType . '驱动');
            }
            $this->linkID[$linkNum]->exec('SET NAMES ' . $config['db_charset']);
            // 标记连接成功
            $this->connected = true;
        }
        return $this->linkID[$linkNum];
    }

    /**
     * 获得所有的查询数据
     * @access private
     * @return array
     */
    private function getAll() {
        //返回数据集
        $result = $this->PDOStatement->fetchAll(PDO::FETCH_ASSOC);
        $this->numRows = count($result);
        return $result;
    }

    /**
     * 数据库错误信息
     * 并显示当前的SQL语句
     * @access public
     * @return string
     */
    public function error() {
        if ($this->PDOStatement) {
            $error = $this->PDOStatement->errorInfo();
            $this->error = $error[1] . ':' . $error[2];
        } else {
            $this->error = '';
        }
        if ('' != $this->queryStr) {
            $this->error .= "\n [ SQL语句 ] : " . $this->queryStr;
        }
        Log::addError($this->error);
        return $this->error;
    }

    /**
     *
     * 判断数据库是否连接
     */
    public function initConnect() {
        if (!$this->connected)
            $this->_linkID = $this->connect();
    }

    /**
     * 关闭数据库
     * @access public
     */
    public function close() {
        $this->_linkID = null;
    }

    /**
     * value分析
     * @access protected
     * @param mixed $value
     * @return string
     */
    protected function parseValue($value) {
        if (is_string($value)) {
            $value = strpos($value, ':') === 0 ? $this->escapeString($value) : '\'' . $this->escapeString($value) . '\'';
        } elseif (isset($value[0]) && is_string($value[0]) && strtolower($value[0]) == 'exp') {
            $value = $this->escapeString($value[1]);
        } elseif (is_array($value)) {
            $value = array_map(array($this, 'parseValue'), $value);
        } elseif (is_bool($value)) {
            $value = $value ? '1' : '0';
        } elseif (is_null($value)) {
            $value = 'null';
        }
        return $value;
    }

    /**
     *
     * 获得缓存字段
     * @param string $tableName 表名
     * @return array 获得数据库字段表
     */
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

    /**
     * 获取最后插入id
     * @access public
     * @return integer
     */
    public function getLastInsertId() {
        switch ($this->dbType) {
            case 'PGSQL':
            case 'SQLITE':
            case 'MSSQL':
            case 'SQLSRV':
            case 'IBASE':
            case 'MYSQL':
                return $this->_linkID->lastInsertId();
            case 'ORACLE':
            case 'OCI':
                $sequenceName = $this->table;
                $vo = $this->query("SELECT {$sequenceName}.currval currval FROM dual");
                return $vo ? $vo[0]["currval"] : 0;
        }
    }

}

?>
