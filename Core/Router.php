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
    const YIIREGX = 3;

    static public function fetchUrl() {
        $app_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', __FILE__);
        $url = str_replace($app_path, '', $_SERVER['REQUEST_URI']);
        $url = strip_tags($url);
        return $url;
    }

    /**
     * 路由分配
     * @return type
     */
    static public function getParms() {
        $config = Config::instance();
        $url_type_setting = $config->url_type_setting;
        switch ($config->url_type) {
            case self::TRADITION:
                break;
            case self::YIIREGX:
                $router = $url_type_setting[self::YIIREGX];
                $router_url = rtrim($_GET[$router['router_parm']], '/');
                return self::parseRegxRouter($router_url, $config, $router);
                break;
            case self::YII:
                $router = $url_type_setting[self::YII];
                $router_url = rtrim($_GET[$router['router_parm']], '/');
                return self::parseRouter($router_url, $config);
                break;
            case self::PATHINFO:
                $pathinfo = $_SERVER['PATH_INFO'];
                $router_url = rtrim($pathinfo, '/');
                return self::parseRouter($router_url, $config);
                break;
        }
    }

    static private function getRouter() {
        static $_router = null;
        if (empty($_router))
            $_router = Config::loadConfig('router');
        return $_router;
    }

    static private function parseRegxRouter($url, $config, $setting) {
        $default_array = array(
            'class' => $config->class_default,
            'action' => $config->action_default,
            'parms' => array()
        );
        $routers = (array) self::getRouter();
        //主目录直接返回默认配置
        if (empty($url)) {
            return isset($routers['*']) ? $routers[0] : $default_array;
        }
        if ($url != htmlentities($url)) {
            return isset($routers['*']) ? $routers[0] : $default_array;
        }
        unset($routers['*']);
        $rule = array();
        $keys = array_keys($routers);
        $vlaues = array_values($routers);
        foreach ($keys as $k => $router) {
            if (strpos('/', $router) === 0 && strrpos('/', $router) != strlen($router)) {
                //是否是正则路由
                preg_match_all($router, $url, $matches);
            } else {
                //是否是规则路由
                $reg1 = explode($setting['router_del'], $router);
                $reg2 = explode($setting['router_del'], $url);
                $match = true;
                if (count($reg2) >= count($reg1)) {
                    foreach ($reg1 as $key => $vlaue) {
                        if (':' == substr($vlaue, 0, 1)) {
                            $is_named = false;
                            if (strpos($vlaue, "\\")) {
                                $type = substr($vlaue, -1);

                                switch ($type) {
                                    case 'd':
                                        if (!is_numeric($reg2[$key])) {
                                            $match = false;
                                            break;
                                        }
                                        $rule[substr($vlaue, 0, -2)] = $reg2[$key];
                                        $is_named = true;
                                        break;
                                    case 'D':
                                        if (is_numeric($reg2[$key])) {
                                            $match = false;
                                            break;
                                        }
                                        $rule[substr($vlaue, 0, -2)] = $reg2[$key];
                                        $is_named = true;
                                }
                            } elseif (strpos($vlaue, '^')) {
                                $array = explode("|", substr(strstr('^', $vlaue), 1));
                                if (in_array($reg2[$key], $array)) {
                                    $match = false;
                                    break;
                                }
                                $rule[substr($vlaue, 0, (strstr('^', $vlaue) - 1))] = $reg2[$key];
                                $is_named = true;
                            }
                            if (!$is_named) {
                                $rule[$vlaue] = $reg2[$key];
                            }
                        } elseif (0 !== strcasecmp($vlaue, $reg2[$key])) {
                            $match = false;
                            break;
                        }
                    }
                    if ($match) {
                        return self::parseRegxUrl($vlaues[$k], $rule);
                    }
                } else {
                    continue;
                }
            }
        }
        return $default_array;
    }

    // array( 'news','read',array('id'=>':id') )   array(':id'=>2,':user'=>'name')
    static private function parseRegxUrl($regx, $rule = null) {
        $default_router = array();
        if (empty($rule)) {
            return $regx;
        }
        foreach ($rule as $key => $val) {
            if ($key == $regx[0]) {
                $regx[0] = $val;
            }
            if ($key == $regx[1]) {
                $regx[1] = $val;
            }
            if (array_key_exists($key, $regx[2])) {
                $regx[2][$key] = $val;
            }
            if ($k = array_search($key, $regx[2])) {
                $regx[2][$k] = $val;
            }
            unset($rule[$key]);
        }
        $default_router['class'] = $regx[0];
        $default_router['action'] = $regx[1];
        $default_router['parms'] = array_merge($regx[2], $rule);
        return $default_router;
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
        if ($router_url != htmlentities($router_url)) {
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