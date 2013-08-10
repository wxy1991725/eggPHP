<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (!defined('APP_ROOT')) {
    exit();
}
return array(
    'local' => array(
        'db_driver' => 'pdo',
        'db_host' => '127.0.0.1',
        'db_port'=>3306,
        'db_name' => 'demo',
        'db_usr' => 'root',
        'db_pwd' => 'root',
        'db_prefix'=>'yx_',
        'db_charset'=>'utf8'
    ),
    'server' => array(
        'db_driver' => 'pdo',
        'db_host' => '127.0.0.1',
        'db_port'=>3306,
        'db_name' => 'demo',
        'db_usr' => 'root',
        'db_pwd' => 'root',
        'db_prefix'=>'yx_'
    ),
);
?>