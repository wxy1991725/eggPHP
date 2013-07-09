<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
return array(
    'timezone' => 'PRC',
    'error_level' => E_ALL,
    'include_path' => '',
    'url_setting' => array(
        'default_class' => 'home',
        'default_action' => 'index',
    ),
    'url_type' => 2,
    'url_type_setting' => array(
        0 => array('class_parm' => 'c', 'action_parm' => 'a'),
    ),
    'event_list'=>array(
        'app_init'=>'filter',
    )
);
?>
