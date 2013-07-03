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

    static function addError($message) {
        self::$_log_array[] = "\r\n" . date("[ c ]") . "\n" . $message . "\n";
    }

    static function addDebug($message) {
        
    }

    static function clearError() {
        self::$_log_array[] = null;
    }

    static function save() {
        $filename = LOG_DIR . date('Ymd') . '.log';
        foreach (self::$_log_array as $value) {
            Tools::fileappend($filename, $value);
        }
        Tools::fileappend($filename, "\n###############" . date("[ c ]") . "###############\n");
    }

}

?>
