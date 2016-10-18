<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/24 0024
 * Time: 16:35
 */

namespace app;

use app\common\Base\BaseModel;
use Exception;
use PhpConsole\Connector;
use TinyWeb\Application;
use TinyWeb\ControllerAbstract;
use TinyWeb\Helper\ApiHelper;
use TinyWeb\Request;
use TinyWeb\Response;
use TinyWeb\Route\RouteApi;
use TinyWeb\Route\RouteSimple;


class Bootstrap extends BaseModel
{

    public static function _debugConsole($data, $tags = null, $ignoreTraceCalls = 0)
    {
        if( !empty($tags) ){
            $tags = strval($tags) . ' @' . Application::useTimes();
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

    public static function apiHub(array $routeInfo, array $params)
    {
        $class_name = "\\" . Application::join("\\", [Application::app()->getAppName(), 'api', $routeInfo[0]]);
        $request = Request::getInstance();
        $request_method = $request->getMethod();
        $session_id = $request->getSessionId();
        if ($request_method == 'HEAD' || $request_method == 'GET' || $request_method == 'OPTIONS' || empty($session_id)) {
            $log_msg = "CSRF ignore [{$request_method}] this_url:" . $request->getThisUrl();
            self::debug($log_msg, __METHOD__, __CLASS__, __LINE__);
        } else {
            if ( !Application::strCmp($session_id, Application::decrypt($request->getCsrfToken())) ) {
                $log_msg = "CSRF error [{$request_method}] token:" . $request->getCsrfToken() . ", this_url:" . $request->getThisUrl() . ", referer_url:" . $request->getHttpReferer();
                self::error($log_msg, __METHOD__, __CLASS__, __LINE__);
            }
        }

        try {
            $json = ApiHelper::api($class_name, $routeInfo[1], $params);
        } catch (Exception $ex) {
            $code = $ex->getCode();  // errno为0 或 无error字段 表示没有错误  errno设置为0 会忽略error字段
            $error = (DEV_MODEL == 'DEBUG') ? ['Exception'=>get_class($ex), 'code' => $ex->getCode(), 'message' => $ex->getMessage(), 'file' => $ex->getFile().' ['.$ex->getLine().']'] : ['code' => $code, 'message' => $ex->getMessage()];
            $json = ['errno' => $code == 0 ? -1 : $code, 'error' =>$error];
            while (!empty($ex) && $ex->getPrevious()) {
                $json['error']['errors'] = isset($json['error']['errors']) ? $json['error']['errors'] : [];
                $ex = $ex->getPrevious();
                $json['error']['errors'][] = (DEV_MODEL == 'DEBUG') ? ['Exception'=>get_class($ex), 'code' => $ex->getCode(), 'message' => $ex->getMessage(), 'file' => $ex->getFile().' ['.$ex->getLine().']'] : ['code' => $ex->getCode(), 'message' => $ex->getMessage()];
            }
        }
        $json['apiVersion'] = '1.0';
        $json_str = isset($params['callback']) && !empty($params['callback']) ? "{$params['callback']}(" . json_encode($json) . ');' : json_encode($json);
        Response::getInstance()->addHeader('Content-Type: application/json;charset=utf-8', false)->apendBody($json_str, $class_name);
    }

    public static function ormHub(array $routeInfo, array $params){

    }

    /** 在app run 之前, 设置app 命名空间 并 注册路由
     * @param Application $app
     * @return Application
     */
    public static function bootstrap(Application $app)
    {
        $app->setAppName('app')
            ->addRoute('api', new RouteApi('api'), function (array $routeInfo, array $params) {
                call_user_func_array([__CLASS__, 'apiHub'], [$routeInfo, $params]);
            })
            ->addRoute('orm', new RouteApi('orm'), function (array $routeInfo, array $params) {
                call_user_func_array([__CLASS__, 'ormHub'], [$routeInfo, $params]);
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