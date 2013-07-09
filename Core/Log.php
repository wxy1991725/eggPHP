<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Log
 *
 * @author WXY
 */
final class Log {

    static $_log_error = 1;
    static $_log_debug = 2;

    /**
     * 日志记录数组
     * @var array 
     */
    public static $_log_array = array();
    public static $_log_level = 1;

    static function add($type, $message) {
        switch ($type) {
            case self::$_log_error:
                self::addError($message);
            case self::$_log_debug:
                self::addDebug($message);
            default :
                return false;
        }
        return;
    }

    /**
     * 提交错误参数
     * @param type $message
     * @return type
     */
    static function addError($message) {
        return self::$_log_array[] = "\r\n" . date("[ c ]") . "\n" . $message . "\n";
    }

    /**
     * 提交调试参数
     * @param type $message
     * @return type
     */
    static function addDebug($message) {
        return self::$_log_array[] = "\r\n" . date("[ c ]") . "\n" . $message . "\n";
    }
    /**
     * 清楚错误记录
     */
    static function clearError() {
        self::$_log_array[] = null;
    }
    /**
     * 保存log文件
     * @return void  _log_array为空则退出
     */
    static function save() {
        if (empty(self::$_log_array)) {
            return;
        }
        $filename = LOG_DIR . date('Ymd') . '.log';
        foreach (self::$_log_array as $value) {
            Tools::fileappend($filename, $value);
        }
        Tools::fileappend($filename, "\n###############" . date("[ c ]") . "###############\n");
    }

}

?>
