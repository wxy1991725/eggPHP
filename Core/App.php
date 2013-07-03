<?php

/**
 * 中心类，主要驱动,包揽自动加载、错误异常处理等系列功能
 * @author WXY
 * @version 1.0.0
 */
final class App {
    /**
     * 保存配置数值
     * @var type 

      static public $_config = array();
     */

    /**
     * 用于保存已经加载的类与路径
     * @var array  类名 =>路径 方式保存
     */
    static public $_autoclass = array();

    /**
     * 保存提前加载的类列表
     * @var type 
     */
    static private $_proload = array(
        'Config' => 'Core/Config.php',
        'Router' => 'Core/Router.php',
        'Uri' => 'Core/Uri.php',
        'Debug' => 'Core/Debug.php',
        'Log' => 'Core/Log.php'
    );

    /**
     *  保存的类列表
     * @var type 
     */
    static public $_class = array();

    /**
     * 自定义的自动加载
     * @param type $classname 自动加载的类的名字
     */
    static public function autoload($classname) {
        if (isset(self::$_proload[$classname])) {
            Tools::import(APP_ROOT . self::$_proload[$classname]);
            return true;
        }
    }

    /**
     * 运行项目
     */
    static public function run() {
        ob_end_clean();
    }

    static public function appError($errno, $errstr, $errfile, $errline) {
        switch ($errno) {
            case E_NOTICE://警告仅仅记录
//                Debug::halt($errno, $errstr, $errfile, $errline);
//                Log::save();
//                Log::clearError();
                break;
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                ob_end_clean();
                $log = Debug::halt($errno, $errstr, $errfile, $errline);
                Log::save();
                Debug::displayError($log);
                break;
            case E_STRICT:
            case E_USER_WARNING:
            case E_USER_NOTICE:
            default:
                Debug::halt($errno, $errstr, $errfile, $errline);
                break;
        }
    }

    /**
     * 对于异常关闭的处理
     */
    static public function fatalError() {
        if ($e = error_get_last()) {
            ob_end_clean();
            self::appError($e['type'], $e['message'], $e['file'], $e['line']);
        }
    }

    /**
     * 初始化项目
     * 1. 注册自动加载函数
     * 2. 配置文件加载
     * 3. 时区设定
     * @param type $config 配置
     */
    static public function init($config) {
        // 页面压缩输出支持
        if (extension_loaded('zlib')) {
            ini_set('zlib.output_compression', 'On');
            ini_set('zlib.output_compression_level', '3');
        }
        ob_start();
        /**
         * 自定义 错误与载入机制
         */
        register_shutdown_function(array(__CLASS__, 'fatalError'));
        set_error_handler(array(__CLASS__, 'appError'));
        spl_autoload_register(array(__CLASS__, 'autoload'));

        /**
         * 扩展的结构文件夹
         */
        define('LOG_DIR', RUN_DIR . 'log' . DS);

        /**
         * 配置参数的各种配置
         */
        $c_obj = Config::instance($config);
        if ($c_obj->path)
            set_include_path(get_include_path() . PATH_SEPARATOR . $c_obj->path); //加载配置的包含路径
        error_reporting($c_obj->error_level);
        date_default_timezone_set($c_obj->timezone);
    }

}

?>
