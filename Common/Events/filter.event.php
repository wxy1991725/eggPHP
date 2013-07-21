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

    static function run() {
        foreach (array('_GET', '_POST', '_COOKIE') as $_request) {
            if (!empty($$_request)) {
                foreach ($$_request as $_k => $_v) {
                    ${$_k} = self::RunMagicQuotes($_v);
                }
            }
        }
    }
    
    /**
     * 批量转义过滤函数 取自dedecms
     * @param type $str
     * @return type
     */
    static function RunMagicQuotes(&$str) {
        if (!get_magic_quotes_gpc()) {
            if (is_array($str))
                foreach ($str as $key => $val)
                    $str[$key] = RunMagicQuotes($val);
            else
                $str = addslashes($str);
        }
        return $str;
    }

}

?>