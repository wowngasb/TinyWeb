<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/24 0024
 * Time: 16:35
 */

namespace app;

use app\common\ApiDispatch;
use app\common\Base\BaseModel;
use PhpConsole\Connector;
use TinyWeb\Application;
use TinyWeb\ControllerAbstract;
use TinyWeb\Request;
use TinyWeb\Response;
use TinyWeb\Route\RouteApi;
use TinyWeb\Route\RouteSimple;


class Bootstrap extends BaseModel
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
            ->addRoute('simple', new RouteSimple('m', 'c', 'a'));  // 添加默认简单路由

        self::debugStrap();
        return $app;
    }

    public static function debugConsole($data, $tags = null, $ignoreTraceCalls = 0)
    {
        if (!empty($tags)) {
            $tags = strval($tags) . ':' . Application::instance()->usedMilliSecond() . 'ms';
        }
        Connector::getInstance()->getDebugDispatcher()->dispatchDebug($data, $tags, $ignoreTraceCalls);
    }

    /**
     * 调试使用 开发模式下有效
     * @param array $data
     * @param string|null $tags
     * @param int $ignoreTraceCalls
     */
    public static function _D($data, $tags = null, $ignoreTraceCalls = 0)
    {
        if (DEV_MODEL == 'DEBUG') {
            self::debugConsole($data, $tags, $ignoreTraceCalls);
        }
    }

    public static function debugStrap()
    {
        if (DEV_MODEL != 'DEBUG') {  // 非调试模式下  直接返回
            return;
        }

        //开启 辅助调试模式 注册对应事件
        Connector::getInstance()->setPassword(Application::instance()->getEnv('DEVELOP_KEY'), true);

        Application::on('routerStartup', function (Application $obj, Request $request, Response $response) {
            false && func_get_args();
            self::debugConsole(['request' => $request,], get_class($obj) . ' #routerStartup', 1);
        });
        Application::on('routerShutdown', function (Application $obj, Request $request, Response $response) {
            false && func_get_args();
            $data = ['route' => $request->getCurrentRoute(), 'routeInfo' => $request->strRouteInfo(), 'params' => $request->getParams(), 'body' => $response->getBody()];
            self::debugConsole($data, get_class($obj) . ' #routerShutdown', 1);
        });
        Application::on('dispatchLoopStartup', function (Application $obj, Request $request, Response $response) {
            false && func_get_args();
            $data = ['route' => $request->getCurrentRoute(), 'routeInfo' => $request->strRouteInfo(), 'params' => $request->getParams(), 'body' => $response->getBody()];
            self::debugConsole($data, get_class($obj) . ' #dispatchLoopStartup', 1);
        });
        Application::on('dispatchLoopShutdown', function (Application $obj, Request $request, Response $response) {
            false && func_get_args();
            $data = ['route' => $request->getCurrentRoute(), 'routeInfo' => $request->strRouteInfo(), 'body' => $response->getBody()];
            self::debugConsole($data, get_class($obj) . ' #dispatchLoopShutdown', 1);
        });
        Application::on('preDispatch', function (Application $obj, Request $request, Response $response) {
            false && func_get_args();
            $data = ['route' => $request->getCurrentRoute(), 'routeInfo' => $request->strRouteInfo(), 'params' => $request->getParams(), 'body' => $response->getBody()];
            self::debugConsole($data, get_class($obj) . ' #preDispatch', 1);
        });
        Application::on('postDispatch', function (Application $obj, Request $request, Response $response) {
            false && func_get_args();
            $data = ['route' => $request->getCurrentRoute(), 'routeInfo' => $request->strRouteInfo(), 'body' => $response->getBody()];
            self::debugConsole($data, get_class($obj) . ' #postDispatch', 1);
        });

        ControllerAbstract::on('preDisplay', function (ControllerAbstract $obj, $tpl_path, array $params) {
            false && func_get_args();
            $data = ['params' => $params, 'tpl_path' => $tpl_path];
            self::debugConsole($data, get_class($obj) . ' #preDisplay', 1);
        });  // 注册 模版渲染 打印模版变量  用于调试
        ControllerAbstract::on('preWidget', function (ControllerAbstract $obj, $tpl_path, array $params) {
            false && func_get_args();
            $data = ['params' => $params, 'tpl_path' => $tpl_path];
            self::debugConsole($data, get_class($obj) . ' #preWidget', 1);
        });  // 注册 组件渲染 打印组件变量  用于调试
    }

}