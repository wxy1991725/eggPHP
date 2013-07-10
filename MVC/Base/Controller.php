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

    /**
     * 控制器初始化函数
     * @param string $class_name 控制器的类名
     * @return Controller 返回继承了控制器基类的子控制器
     */
    static public function newClass($class_name) {
        $class = new $class_name;
        if (method_exists($class, '_init')) {
            $class->_init();
        }
        return $class;
    }

}

?>
