<?php

/**
 * 常量定义一系列配置
 */
define('DS', DIRECTORY_SEPARATOR);
define('APP_ROOT', dirname(__FILE__) . DS);
define('CORE_DIR', APP_ROOT . 'Core' . DS);
define('COMMON_DIR', APP_ROOT . 'Common' . DS);
define('EXT_DIR', APP_ROOT . 'Extend' . DS);
define('RUN_DIR', APP_ROOT . 'Run' . DS);

/**
 * 定义运行环境 开发:dev 生产:pro 测试:test
 */
define('APP_MODE', 'dev');

/**
 * 基础工具类
 */
require COMMON_DIR . 'Tools.php';
/**
 * 加载配置数组
 */
$config = Tools::import(RUN_DIR . APP_MODE . DS . 'config.php', true);

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