<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 路由器类,用于解析路径
 * @author WXY
 */
class Router {
    /**
     * url模式 0 为传统 1为PATHINFO 2为 仿yii模式 3为 正则自定义路径
     */

    const TRADITION = 0;
    const PATHINFO = 1;
    const YII = 2;
    const REGX = 3;

    static public function fetchUrl() {
        $app_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', __FILE__);
        $url = str_replace($app_path, '', $_SERVER['REQUEST_URI']);
        $url = strip_tags($url);
        return $url;
    }

    static public function getParms() {
        $url_type = Config::getConfig('url_type');
        switch ($url_type) {
            case self::TRADITION:
                
                break;
        }
    }

}

?>
