<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/25 0025
 * Time: 9:04
 */

namespace TinyWeb\Route;

use TinyWeb\Request;
use TinyWeb\RouteInterface;

/**
 * Class DefaultRoute
 * RouteSimple是基于请求中的query string来做路由的, 在初始化一个RouteSimple路由协议的时候, 我们需要给出3个参数, 这3个参数分别代表在query string中Module, Controller, Action的变量名:
 *  $route = new RouteSimple("m", "c", "a");
 *  $app->addRoute("name", $route);
 *  对于如下请求: "http://domain.com/index.php?c=index&a=test 能得到如下路由结果
 *  $routeInfo = ['controller'=>'index', 'action'=> 'test', 'module'=>self::$default_module, ]
 * unset($params[$this->module_key], $params[$this->controller_key], $params[$this->action_key]);
 * $params = $_REQUEST;
 * @package TinyWeb
 */
class RouteSimple implements RouteInterface
{

    protected $module_key = 'm';
    protected $controller_key = 'c';
    protected $action_key = 'a';

    public function __construct($module_key = 'm', $controller_key = 'c', $action_key = 'a')
    {
        list($this->module_key, $this->controller_key, $this->action_key) = [$module_key, $controller_key, $action_key];
    }

    /**
     * 根据请求的 $_method $_request_uri $_language 得出 路由信息 及 参数
     * 匹配成功后 获得 路由信息 及 参数  总是可以成功
     * 一般参数应设置到 php 原始 $_GET, $_POST $_REQUEST 中， 保持一致性
     * @param Request $request 请求对象
     * @return array 匹配成功 [$routeInfo, $params]  失败 [null, null]
     */
    public function route(Request $request)
    {
        list($default_controller, $default_action, $default_module) = self::getDefaultRouteInfo();
        $controller = $request->_get($this->controller_key, $default_controller);
        $action = $request->_get($this->action_key, $default_action);
        $module = $request->_get($this->module_key, $default_module);
        $routeInfo = [strtolower($controller), strtolower($action), strtolower($module),];
        $params = $_REQUEST;
        unset($params[$this->module_key], $params[$this->controller_key], $params[$this->action_key]);
        return [$routeInfo, $params];
    }

    /**
     * 根据 路由信息 及 参数 生成反路由 得到 url
     * @param array $routeInfo 路由信息数组
     * @param array $params 参数数组
     * @return string
     */
    public function ford(array $routeInfo, array $params = [])
    {
        list($default_controller, $default_action, $default_module) = self::getDefaultRouteInfo();
        unset($params[$this->module_key], $params[$this->controller_key], $params[$this->action_key]);
        $controller = (isset($routeInfo[0]) && !empty($routeInfo[0])) ? $routeInfo[0] : $default_controller;
        $action = (isset($routeInfo[1]) && !empty($routeInfo[1])) ? $routeInfo[1] : $default_action;
        $module = isset($routeInfo[2]) ? $routeInfo[2] : $default_module;
        list($controller, $action, $module) = [strtolower($controller), strtolower($action), strtolower($module),];

        $url = SYSTEM_HOST . 'index.php';
        $args_list = [];
        if (!empty($module)) {
            $args_list[] = "{$this->module_key}={$module}";
        }
        $args_list[] = "{$this->controller_key}={$controller}";
        $args_list[] = "{$this->action_key}={$action}";
        foreach ($params as $key => $val) {
            $args_list[] = trim($key) . '=' . urlencode($val);
        }
        return !empty($args_list) ? $url . '?' . join($args_list, '&') : $url;
    }

    /**
     * 获取路由 默认参数 用于url参数不齐全时 补全
     * @return array  $routeInfo [$controller, $action, $module]
     */
    public static function getDefaultRouteInfo()
    {
        return ['index', 'index', 'index'];
    }
}