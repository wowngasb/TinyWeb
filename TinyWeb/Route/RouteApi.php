<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/28 0028
 * Time: 17:40
 */

namespace TinyWeb\Route;


use TinyWeb\Exception\AppStartUpError;
use TinyWeb\Request;
use TinyWeb\RouteInterface;

class RouteApi implements RouteInterface
{
    private $module_name = 'api';

    public function __construct($module_name='api')
    {
        $this->module_name =$module_name;
        if(empty($this->module_name)){
            throw new AppStartUpError('RouteApi module_name empty');
        }
    }

    public function getModuleName()
    {
        return $this->module_name;
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
        $uri = $request->getRequestUri();
        $reg_str = "^\/{$this->module_name}\/([A-Za-z0-9_.]+)\/([A-Za-z0-9_]+)";
        $matches = [];
        preg_match("/{$reg_str}/i", $uri, $matches);

        if ( isset($matches[1]) && isset($matches[2]) ) {
            $routeInfo = [$matches[1], $matches[2], $this->module_name];
            return [$routeInfo, $_REQUEST];
        } else {
            return [null, null];
        }
    }

    /**
     * 根据 路由信息 及 参数 生成反路由 得到 url
     * @param array $routeInfo 路由信息数组
     * @param array $params 参数数组
     * @return string
     */
    public function url(array $routeInfo, array $params = [])
    {
        return '';
    }

    /**
     * 获取路由 默认参数 用于url参数不齐全时 补全
     * @return array  $routeInfo [$controller, $action, $module]
     */
    public static function getDefaultRouteInfo()
    {
        return ['', '', ''];  // 无默认 $routeInfo
    }
}