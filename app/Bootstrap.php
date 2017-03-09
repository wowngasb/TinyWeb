<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/24 0024
 * Time: 16:35
 */

namespace app;

use app\common\Dispatch\ApiDispatch;
use TinyWeb\Base\BaseBootstrap;
use TinyWeb\Application;
use TinyWeb\Request;
use TinyWeb\Response;
use TinyWeb\Route\RouteApi;
use TinyWeb\Route\RouteSimple;

class Bootstrap extends BaseBootstrap
{

    /** 在app run 之前, 设置app 命名空间 并 注册路由
     *  #param Application $app
     *  #return Application
     * @param string $appname
     * @param Application $app
     * @return Application
     */
    public static function bootstrap($appname, Application $app)
    {
        $app->setAppName($appname)
            ->addRoute('api', new RouteApi('api'), ApiDispatch::instance())
            ->addRoute('yar', new RouteApi('yar'), ApiDispatch::instance())
            ->addRoute('simple', new RouteSimple('m', 'c', 'a'));  // 添加默认简单路由

        /* 注册回调 在路由结束后 根据路由情况 决定是否开启 session 保证 session 在 dispatchLoopStartup 之前可用 */
        Application::on('routerShutdown', function (Application $obj, Request $request, Response $response) {
            false && func_get_args();
            $request->getCurrentRoute() != 'api' && $request->setSessionStart(true);
        });

        self::debugStrap();
        return $app;
    }

}