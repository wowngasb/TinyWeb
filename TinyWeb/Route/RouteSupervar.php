<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/25 0025
 * Time: 18:34
 */

namespace TinyWeb\Route;
use TinyWeb\Request;
use TinyWeb\RouteInterface;


/**
 * Class RouteSupervar
 * RouteSupervar和RouteSimple相似, 都是在query string中获取路由信息, 不同的是, 它获取的是一个类似包含整个路由信息的request_uri
 * $route = new RouteSupervar("r");
 * $app->addRoute("name", $route);
 * 对于如下请求: "http://domain.com/index.php?r=a/b/c   能得到如下路由结果
 *  $routeInfo = ['controller'=>'b', 'action'=> 'c', 'module'=>'a', ]
 *  unset($params[$this->route_key]);
 *  $params = $_REQUEST;
 * @package TinyWeb
 */
class RouteSupervar implements RouteInterface
{

    protected $route_key = 'r';

    public function __construct($route_key = 'r')
    {
        $this->route_key = $route_key;
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
        $route_value = $request->_get($this->route_key, '');
        if(empty($route_value)){
            return [null, null];
        }
        while (strpos($route_value, '//') !== false) {
            $route_value = str_replace('//', '/', $route_value);
        }
        $route_value = substr($route_value, 0, 1) == '/' ? substr($route_value, 1) : $route_value;
        $route_array = explode('/', $route_value);
        if (count($route_array) >= 3) {
            $module = (isset($route_array[0]) && !empty($route_array[0])) ? $route_array[0] : $default_module;
            $controller = (isset($route_array[1]) && !empty($route_array[1])) ? $route_array[1] : $default_controller;
            $action = (isset($route_array[2]) && !empty($route_array[2])) ? $route_array[2] : $default_action;
        } else {
            $controller = (isset($route_array[0]) && !empty($route_array[0])) ? $route_array[0] : $default_controller;
            $action = (isset($route_array[1]) && !empty($route_array[1])) ? $route_array[1] : $default_action;
            $module = $default_module;
        }


        $routeInfo = [strtolower($controller), strtolower($action), strtolower($module),];
        $params = $request->_request();
        unset($params[$this->route_key]);
        return [$routeInfo, $params];
    }

    /**
     * 根据 路由信息 及 参数 生成反路由 得到 url
     * @param array $routeInfo 路由信息数组
     * @param array $params 参数数组
     * @return string
     */
    public function url(array $routeInfo, array $params = [])
    {
        list($default_controller, $default_action, $default_module) = self::getDefaultRouteInfo();
        unset($params[$this->route_key]);
        $controller = (isset($routeInfo[0]) && !empty($routeInfo[0])) ? $routeInfo[0] : $default_controller;
        $action = (isset($routeInfo[1]) && !empty($routeInfo[1])) ? $routeInfo[1] : $default_action;
        $module = isset($routeInfo[2]) ? $routeInfo[2] : $default_module;
        list($controller, $action, $module) = [strtolower($controller), strtolower($action), strtolower($module),];

        $url = SYSTEM_HOST . 'index.php';
        $args_list = [];
        $route_value = !empty($module) ? "{$module}/{$controller}/{$action}" : "{$controller}/{$action}";

        $args_list[] = "{$this->route_key}={$route_value}";
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