<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/28 0028
 * Time: 10:12
 */

namespace TinyWeb;


use TinyWeb\Exception\AppStartUpError;

class BaseAbstract
{
    protected static $_event_map = [];  // 注册事件列表
    public static $allow_event = [];  // 允许的事件列表

    /*
     *  注册回调函数  回调参数为 callback(Request $request, Response $response)  两个参数都为单实例
     *  1、routerStartup	在路由之前触发	这个是7个事件中, 最早的一个. 但是一些全局自定的工作, 还是应该放在Bootstrap中去完成
     *  2、routerShutdown	路由结束之后触发	此时路由一定正确完成, 否则这个事件不会触发
     *  3、dispatchLoopStartup	分发循环开始之前被触发
     *  4、preDispatch	分发之前触发	如果在一个请求处理过程中, 发生了forward, 则这个事件会被触发多次
     *  5、postDispatch	分发结束之后触发	此时动作已经执行结束, 视图也已经渲染完成. 和preDispatch类似, 此事件也可能触发多次
     *  6、dispatchLoopShutdown	分发循环结束之后触发	此时表示所有的业务逻辑都已经运行完成, 但是响应还没有发送
     * @param string $event
     * @param callable $callback
     */
    public static function on($event, callable $callback)
    {
        if (!in_array($event, static::$allow_event)) {
            throw new AppStartUpError("event:{$event} not support");
        }
        if (!isset(self::$_event_map[$event])) {
            self::$_event_map[$event] = [];
        }
        self::$_event_map[$event][] = $callback;
    }

    /**
     * 触发事件  依次调用注册的回调
     * @param  string $event 事件名称
     * @param array $args 调用触发回调的参数
     * @throws AppStartUpError
     */
    protected static function fire($event, array $args)
    {
        if (!in_array($event, static::$allow_event)) {
            throw new AppStartUpError("event:{$event} not support");
        }
        $callback_list = isset(self::$_event_map[$event]) ? self::$_event_map[$event] : [];
        foreach ($callback_list as $idx => $val) {
            call_user_func_array($val, $args);
        }

    }

    /**
     * 使用 seq 把 list 数组中的非空字符串连接起来  _join('_', [1,2,3]) = '1_2_3_'
     * @param string $seq
     * @param array $list
     * @return string
     */
    public static function join($seq, array $list)
    {
        $tmp_list = [];
        foreach ($list as $item) {
            $item = trim($item);
            if (!empty($item)) {
                $tmp_list[] = strval($item);
            }
        }
        return join($seq, $tmp_list);
    }

}