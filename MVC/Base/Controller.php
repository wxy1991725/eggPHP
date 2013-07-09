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

    //put your code here
    static public function newClass($class_name) {
        $class = new $class_name();
        if (method_exists($class, '_init')) {
            $class->_init();
        }
        return $class;
    }

}

?>
