<?php

/**
 * 公用方法类
 */
final class Tools {

    /**
     * 用于替代require_once方法
     * @staticvar array $_file  保持加载函数的类别
     * @param type $path    要加加载文件的完整路径
     * @param type $is_return 是否直接返回包含的内容
     */
    static public function import($path, $is_return = false) {
        static $_file = array();
        if (isset($_file[$path])) {
            $error_msg = Tools::txt("{0} Is Already Exists", $path);
            trigger_error($error_msg);
        } else {
            if (file_exists($path)) {
                if ($is_return) {
                    $content = require $path;
                    return $content;
                }
                require $path;
                return $_file[$path] = true;
            } else {
                throw new Exception(Tools::txt("{0} Is Not Exists", $path));
            }
        }
    }

    /**
     * 批量包含文件
     * @param type $files   文件名=>文件路径 的数组
     * @return type 返回成功加载的数组
     */
    static public function imports($files) {
        $result = array();
        foreach ($files as $class => $file) {
            $result[$class] = Tools::import($file);
        }
        return $result;
    }

    /**
     *   自动解析相应规则的类路径
     * @param string $classname 以 . 为分割符的类路径 如 vendor.cache.file 会被解析为 vendor/cache/file.php 路径
     * @param boolean $autoload true 自动加载 false 仅仅输出 类 => 路径的 数组
     * @return array|boolean  输出解析后的数组 基本用于autoloader  
     */
    static public function vendor($classname, $autoload = true) {
        $exarray = explode('.', $classname);
        $count = sizeof($exarray);
        $name = $exarray[$count - 1];
        $path = implode(DS, $exarray) . ".php";
        if ($autoload) {
            Tools::import(EXT_DIR . $path);
            return true;
        }
        return array($name => EXT_DIR . $path);
    }

    /**
     * 格式化内容 func("{demo} output",array('demo'=>'this demo'),true) // output: this demo output
     * @param type $message 原始内容
     * @param type $params 
     * @return type
     */
    static public function txt($message, $params = array(), $return = true) {
        if ($params === array()) {
            return $message;
        } elseif (is_string($params)) {
            $params = (array) $params;
        }
        foreach ($params as $key => $value) {
            $message = str_replace('{' . $key . '}', $value, $message);
        }
        if ($return) {
            return $message;
        }
        echo $message;
        return;
    }
    
    static public function halt($string){
        
    } 

}

?>