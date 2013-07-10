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
        $config = Config::instance();
        $url_type_setting = $config->url_type_setting;
        switch ($config->url_type) {
            case self::TRADITION:
                break;
            case self::YII:
                $router = $url_type_setting[self::YII];
                $router_url = rtrim($_GET[$router['router_parm']], '/');
                return self::parseRouter($router_url, $config);
                break;
            case self::PATHINFO:
                $pathinfo=$_SERVER['PATH_INFO'];
                $router_url = rtrim($pathinfo, '/');
                return self::parseRouter($router_url, $config);
                break;
        }
    }

    static private function parseRouter($router_url, $config) {
        $default_array = array(
            'class' => $config->class_default,
            'action' => $config->action_default,
            'parms' => array()
        );
        if (empty($router_url)) {
            return $default_array;
        }
        $router_url = strip_tags($router_url);
        $router_url_array = explode('/', $router_url);
        $length = count($router_url_array);
        $default_array['class'] = empty($router_url_array[0]) ? $default_array['class'] : strtolower($router_url_array[0]);
        $default_array['action'] = empty($router_url_array[1]) ? $default_array['action'] : strtolower($router_url_array[1]);
        if ($length == 3) {
            $default_array['parms'] = array($router_url_array[2]);
        } elseif ($length > 3 && $length % 2 == 0) {
            for ($i = 2; $i < $length; $i += 2) {
                $arr_temp_hash = array(strtolower($router_url_array[$i]) => $router_url_array[$i + 1]);
                $default_array['parms'] = array_merge($default_array['parms'], $arr_temp_hash);
            }
        } else {
            return $default_array;
        }
        return $default_array;
    }
}
?>
