<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Debug
 *
 * @author WXY
 */
final class Debug {

    static private $_trace_value = array();
    static private $_trace_array = array();

    /**
     * 获得当前运行模式
     * @return string 当前运行环境的简称
     * @throws Exception 没有定义运行环境则报错
     */
    static public function get_env() {
        if (defined('APP_MODE')) {
            return APP_MODE;
        } else {
            throw new Exception('APP_MODE must be defined!');
        }
    }

    /**
     * 调试方法
     * @param type $key
     * @param string $value
     */
    static public function trace($key, string $value) {
        self::$_trace_value[$key] = $value;
    }

    /**
     * 调试用计时器
     * @param string $name 计时器的标识
     * @param type $info 备注
     * @return int 如果初次使用返回当前时间，否则返回消耗的时间
     */
    static public function time($name, $info = null) {
        $time = microtime(true);
        if ($info !== null) {
            self::$_trace_array[$name]['info'] = $info;
        }
        if (!isset(self::$_trace_array[$name]["_start"])) {
            self::$_trace_array[$name]["_start"] = $time;
        } else {
            self::$_trace_array[$name]["_end"] = $time;
            self::$_trace_array[$name]["_cost"] = $time - self::$_trace_array[$name]["_start"];
        }
        return empty(self::$_trace_array[$name]["_cost"]) ? self::$_trace_array[$name]["_start"] : self::$_trace_array[$name]["_cost"];
    }

    /**
     * 
     * @param type $name
     * @param type $info
     */
    static public function show($name, $info) {
        if (self::get_env() == 'dev') {
            self::time($name, $info);
            self::memory($name, $info);
            return self::$_trace_array[$name];
        }
    }

    /**
     * 调试用内存
     * @param string $name 键值
     * @param type $info 备注
     * @return int 如果初次使用返回当前剩余内存，否则返回消耗的内存
     */
    static public function memory($name, $info = null) {
        $memory = memory_get_usage();
        if ($info !== null) {
            self::$_trace_array[$name]['info'] = $info;
        }
        if (!isset(self::$_trace_array[$name]["_before"])) {
            self::$_trace_array[$name]["_before"] = $memory;
        } else {
            self::$_trace_array[$name]["_after"] = $memory;
            self::$_trace_array[$name]["_use"] = $memory - self::$_trace_array[$name]["_before"];
        }
        return empty(self::$_trace_array[$name]["_use"]) ? self::$_trace_array[$name]["_before"] : self::$_trace_array[$name]["_use"];
    }

    static public function displayError($log) {
        if (self::get_env() != 'dev') {
            $log = Config::getConfig('error_message');
        }
        include TPL_DIR . '404.html';
    }

    static public function halt($errno, $errstr, $errfile, $errline) {
        if ($errno) {
            // disable error capturing to avoid recursive errors

            $log = "$errstr ($errfile:$errline)\nStack trace:\n";
            $trace = debug_backtrace();
            // skip the first 3 stacks as they do not tell the error position
            if (count($trace) > 3)
                $trace = array_slice($trace, 3);

            foreach ($trace as $i => $t) {
                if (!isset($t['file']))
                    $t['file'] = 'unknown';
                if (!isset($t['line']))
                    $t['line'] = 0;
                if (!isset($t['function']))
                    $t['function'] = 'unknown';
                $log.="#$i {$t['file']}({$t['line']}): ";
                if (isset($t['object']) && is_object($t['object']))
                    $log.=get_class($t['object']) . '->';
                $log.="{$t['function']}()\n";
            }
            if (isset($_SERVER['REQUEST_URI']))
                $log.='REQUEST_URI=' . $_SERVER['REQUEST_URI'];
            Log::add(Log::$_log_error, $log);
            if (self::get_env() != 'dev') {
                $log = '';
            }
            return $log;
        }
    }

}

?>
