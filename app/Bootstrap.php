<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/24 0024
 * Time: 16:35
 */

namespace app;

use TinyWeb\Dispatch\ApiDispatch;
use TinyWeb\Base\BaseBootstrap;
use TinyWeb\Application;
use TinyWeb\Dispatch\YarDispatch;
use TinyWeb\Route\RouteApi;
use TinyWeb\Route\RouteSimple;

final class Bootstrap extends BaseBootstrap
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
        $app->addRoute('api', new RouteApi('api'), new ApiDispatch())
            ->addRoute('yar', new RouteApi('yar'), new YarDispatch())
            ->addRoute('simple', new RouteSimple('m', 'c', 'a'));  // 添加默认简单路由

        return parent::bootstrap($appname, $app);
    }

}