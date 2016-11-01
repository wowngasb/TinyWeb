<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/24 0024
 * Time: 16:35
 */

namespace app;

use app\common\ApiHub;
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

    private static function _debugConsole($data, $tags = null, $ignoreTraceCalls = 0)
    {
        if( !empty($tags) ){
            $tags = strval($tags) . ':' . Application::usedMilliSecond() . 'ms';
        }
        (DEV_MODEL == 'DEBUG') && Connector::getInstance()->getDebugDispatcher()->dispatchDebug($data, $tags, $ignoreTraceCalls);
    }

    /**
     * 调试使用 开发模式下有效
     * @param array $data
     * @param string $tags
     * @param int $ignoreTraceCalls
     */
    public static function _D($data, $tags = null, $ignoreTraceCalls = 0){
        self::_debugConsole($data, $tags, $ignoreTraceCalls);
    }



    /** 在app run 之前, 设置app 命名空间 并 注册路由
     * @param Application $app
     * @return Application
     */
    public static function bootstrap(Application $app)
    {
        $app->setAppName('app')
            ->addRoute('api', new RouteApi('api'), function (array $routeInfo, array $params) {
                ApiHub::apiHub($routeInfo, $params);
            })
            ->addRoute('simple', new RouteSimple('m', 'c', 'a'));  // 添加默认路由
        //->addRoute('supervar', new RouteSupervar());   // 添加 r 路由

        Application::on('routerStartup', function (Application $obj, Request $request, Response $response) {
            false && func_get_args();
            $request->getCurrentRoute() != 'rpc' && $request->setSessionStart(true);  // 不是 rpc 调用的时候 开启 session
        });

        if (DEV_MODEL != 'DEBUG') {  // 非调试模式下  直接返回
            return $app;
        }

        //开启 辅助调试模式 注册对应事件
        Connector::getInstance()->setPassword(DEVELOP_KEY, true);

        Application::on('routerStartup', function (Application $obj, Request $request, Response $response) {
            false && func_get_args();
            self::_debugConsole(['request' => $request, 'session' => $_SESSION,], 'routerStartup', 1);
        });
        Application::on('routerShutdown', function (Application $obj, Request $request, Response $response) {
            false && func_get_args();
            $data = ['route' => $request->getCurrentRoute(), 'routeInfo' => $request->getRouteInfo(), 'params' => $request->getParams(), 'body' => $response->getBody()];
            self::_debugConsole($data, 'routerShutdown', 1);
        });
        Application::on('preDispatch', function (Application $obj, Request $request, Response $response) {
            false && func_get_args();
            $data = ['route' => $request->getCurrentRoute(), 'routeInfo' => $request->getRouteInfo(), 'params' => $request->getParams(), 'body' => $response->getBody()];
            self::_debugConsole($data, 'preDispatch', 1);
        });
        Application::on('dispatchLoopShutdown', function (Application $obj, Request $request, Response $response) {
            false && func_get_args();
            $data = ['route' => $request->getCurrentRoute(), 'routeInfo' => $request->getRouteInfo(), 'params' => $request->getParams(), 'body' => $response->getBody()];
            self::_debugConsole($data, 'dispatchLoopShutdown', 1);
        });

        ControllerAbstract::on('preDisplay', function (ControllerAbstract $obj, $tpl_path, array $params) {
            false && func_get_args();
            $data = ['params' => $params, 'tpl_path' => $tpl_path];
            self::_debugConsole($data, 'preDisplay', 1);
        });  // 注册 模版渲染 打印模版变量  用于调试
        ControllerAbstract::on('preWidget', function (ControllerAbstract $obj, $tpl_path, array $params) {
            false && func_get_args();
            $data = ['params' => $params, 'tpl_path' => $tpl_path];
            self::_debugConsole($data, 'preWidget', 1);
        });  // 注册 组件渲染 打印组件变量  用于调试
        return $app;
    }

}