<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Event
 *
 * @author WXY
 */
abstract class Event {

    /**
     *  事件列表
     * @var array
     */
    static public $_event_list = array();
    
    /**
     * 触发事件
     * @param string $EventName 事件名称
     */
    static public function happen($EventName, &$content = null) {
        if (empty(self::$_event_list))
            self::$_event_list = Config::getConfig('event_list');
        if (isset(self::$_event_list[$EventName])) {
            $EventNameList = Tools::xcode(self::$_event_list[$EventName]);
            if (empty($EventNameList)) {
                return;
            }
        } else {
            return;
        }
        foreach ($EventNameList as $value) {
            if (class_exists($value . 'Event')) {
                call_user_func(array($value . 'Event', 'run'), $content);
            } else {
                call_user_func($value, $content);
            }
        }
    }

    /**
     * 添加事件
     * @param string $EventName 事件名
     * @param callable $callback 方法或者对象,方法会被直接运行 对象则运行其中的Run方法
     */
    static public function add(string $EventName,$callback) {
        $EventNameList = Tools::xcode(self::$_event_list[$EventName]);
        $EventNameList[] = $callback;
        self::$_event_list[$EventName] = $EventNameList;
    }

    /**
     * 清除事件安排
     * @param string $EventName 事件名
     * @param array $callback 必须是事件之中有的方法,可以是数组(一批事件)
     */
    static public function clear(string $EventName = null, array $function = null) {
        if ($EventName === null && $function === null) {
            self::$_event_list = array();
            return;
        } elseif ($function === null) {
            self::$_event_list[$EventName] = null;
            return;
        }
        if (isset(self::$_event_list[$EventName])) {
            $EventNameList = Tools::xcode(self::$_event_list[$EventName]);
            $diffarray = array_diff($EventNameList, (array) $function);
            return self::$_event_list[$EventName] = $diffarray;
        } else {
            trigger_error($EventName . '事件不存在!');
        }
    }

}

?>
