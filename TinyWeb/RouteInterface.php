<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/24 0024
 * Time: 15:08
 */

namespace TinyWeb;


/**
 * 路由接口类  实现此接口的可用于 路由分发
 * Interface RouteInterface
 * @package TinyWeb
 */
interface RouteInterface
{

    /**
     * 获取路由 默认参数 用于url参数不齐全时 补全
     * @return array  $routeInfo [$controller, $action, $module]
     */
    public static function getDefaultRouteInfo();

    /**
     * 根据请求的 $_method $_request_uri $_language 得出 路由信息 及 参数
     * 匹配成功后 获得 路由信息 及 参数  总是可以成功
     * 一般参数应设置到 php 原始 $_GET, $_POST $_REQUEST 中， 保持一致性
     * @param Request $request 请求对象
     * @return array 匹配成功 [ [$controller, $action, $module], $params ]  失败 [null, null]
     */
    public function route(Request $request);

    /**
     * 根据 路由信息 及 参数 生成反路由 得到 url  [$controller, $action, $module]
     * @param array $routeInfo 路由信息数组  [$controller, $action, $module]
     * @param array $params 参数数组
     * @return string
     */
    public function ford(array $routeInfo, array $params = []);

}