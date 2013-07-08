<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 过滤超全局变量
 *
 * @author WXY
 */
class filterEvent extends Event {

    //put your code here
    static function run() {
        $filter = 'htmlspecialchars';
        array_walk_recursive($_POST, $filter);
        array_walk_recursive($_GET, $filter);
        var_dump($_GET);
    }

}

?>
