<?php
/**
 * 开始运行时间
 */
define('BEGINTIME', microtime(1));
define('BEGINMEM', memory_get_usage());
/**
 * 常量定义一系列配置
 */
define('DS', DIRECTORY_SEPARATOR);
define('APP_ROOT', realpath(dirname(__FILE__) . DS . ".." . DS) . DS);
define('CORE_DIR', APP_ROOT . 'Core' . DS);
define('COMMON_DIR', APP_ROOT . 'Common' . DS);
define('SYS_EXT_DIR', APP_ROOT . 'Extend' . DS);
define('RUN_DIR', APP_ROOT . 'Run' . DS);

/**
 * 定义运行环境 开发:dev 生产:pro 测试:test
 */
define('APP_MODE', 'dev');

/**
 * 基础工具类
 */
require CORE_DIR . 'Tools.php';
/**
 * 加载配置数组
 */
$config = Tools::import(RUN_DIR . 'Config' . DS . APP_MODE . DS . 'config.php', true);

/*
 * 核心类导入
 */
Tools::import(CORE_DIR . 'App.php');

/**
 * 核心类初始化
 */
App::init($config);
/**
 * 项目运行
 */
App::run();
?>