<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (!defined('CORE_DIR')) {
    exit('Access');
}
return array(
    'charset'=>'utf-8',
    'timezone' => 'PRC',
    'error_level' => E_ALL,
    'include_path' => '',
    'url_setting' => array(
        'default_class' => 'home',
        'default_action' => 'index',
    ),
    'url_type' => 3,
    'class_default' => 'home',
    'action_default' => 'index',
    'class_error' => 'error',
    'url_type_setting' => array(
        2 => array('router_parm' => 'r',),
        3 => array('router_parm' => 'r','router_del'=>'/'),
    ),
    'event_list' => array(
        'app_init' => 'filter',
    )
);
?>
