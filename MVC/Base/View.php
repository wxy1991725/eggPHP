<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 视图类
 *
 * @author WXY
 */
class View {

    const SMARTY = 0;
    const Twig = 1;
    const PHP = 2;

    //const SELF=3;
    //put your code here
    private $_template_type = null;
    private $_template_option = array();

    static public function setTemplateType($type, $option) {
        switch ($type) {
            case self::SMARTY:
                break;
            case self::Twig;
                break;
            case self::PHP;
                break;
            default;
                break;
        }
    }

    static public function show($content) {
        echo $content;
    }

    static public function showjson($value) {
        echo json_encode($value);
    }

    static public function buildHtml($file, $content) {
        
    }

}

?>
