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
class Event {

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
        } else {
            throw new Exception($EventName . '触发失败');
        }
        foreach ($EventNameList as $value) {
            if (class_exists($value . 'Event')) {
                call_user_func(array($value . 'Event', 'run'), $content);
            } elseif (is_callable($value)) {
                $value($content);
            }  else {
                call_user_func($value, $content);
            }
        }
    }

    /**
     * 
     * @param string $EventName
     * @param callable $callback
     */
    static public function add(string $EventName, callable $callback) {
        self::$_event_list[$EventName][] = $callback;
    }

}

?>
