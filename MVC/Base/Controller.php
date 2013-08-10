<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 控制器基类
 * @author WXY
 */
class Controller {

    public $config = array();

    /**
     * 控制器初始化函数
     * @param string $class_name 控制器的类名
     * @return Controller 返回继承了控制器基类的子控制器
     */
    static public function newClass($class_name) {
        $class = new $class_name;
        $class->config = Config::instance();
        if (method_exists($class, '_init')) {
            $class->_init();
        }
        return $class;
    }

    /**
     * 获得对应的模型类
     * @param type $modelname
     * @return Model
     */
    public function getModel($modelname = null, $prefix = '', $config = array()) {
        static $_model = array();
        if ($modelname == null) {
            $modelname = $this->config->router_flag['class'];
        }
        $id = $modelname . "_" . md5(serialize($config));
        if (!isset($_model[$id])) {
            if (class_exists($modelname . "_model")) {
                $model = $modelname . "_model";
                $_model[$id] = new $model($modelname, $prefix, $config);
            } else {
                $_model[$id] = new Model($modelname, $prefix, $config);
            }
        }
        return $_model[$id];
    }

}

?>