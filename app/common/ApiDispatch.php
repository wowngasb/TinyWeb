<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/2 0002
 * Time: 18:11
 */

namespace app\common;


use app\Bootstrap;
use app\common\Base\BaseApiModel;
use app\common\Base\BaseModel;
use Exception;
use TinyWeb\Application;
use TinyWeb\DispatchInterface;
use TinyWeb\Exception\ApiParamsError;
use TinyWeb\Exception\AppStartUpError;
use TinyWeb\ExecutableEmptyInterface;
use TinyWeb\Helper\ApiHelper;
use TinyWeb\Request;
use TinyWeb\Response;

class ApiDispatch extends BaseModel implements DispatchInterface
{

    private static $instance = null;

    /**
     * 单实例实现
     * @return DispatchInterface
     */
    public static function instance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * 根据对象和方法名 获取 修复后的参数
     * @param ExecutableEmptyInterface $object
     * @param string $action
     * @param array $params
     * @return array
     */
    public static function fixActionParams(ExecutableEmptyInterface $object, $action, array $params)
    {
        return ApiHelper::fixActionParams($object, $action, $params);
    }

    /**
     * 修复并返回 真实需要调用对象的方法名称
     * @param string $action
     * @return string
     */
    public static function fixActionName($action)
    {
        return $action;
    }

    /**
     * 创建需要调用的对象 并检查对象和方法的合法性
     * @param array $routeInfo
     * @param string $action
     * @return BaseApiModel 可返回实现此接口的 其他对象 方便做类型限制
     * @throws AppStartUpError
     */
    public static function fixActionObject(array $routeInfo, $action)
    {
        $namespace = "\\" . Application::join("\\", [Application::instance()->getAppName(), 'api', $routeInfo[0]]);
        $object = new $namespace();
        if (!($object instanceof BaseApiModel)) {
            throw new AppStartUpError("class:{$namespace} isn't instanceof BaseApiModel with routeInfo:" . json_encode($routeInfo));
        }
        if (!is_callable([$object, $action]) || ApiHelper::isIgnoreMethod($action)) {
            throw new AppStartUpError("action:{$namespace}->{$action} not allowed with routeInfo:" . json_encode($routeInfo));
        }
        return $object;
    }

    /**
     * 调用分发 请在方法开头加上 固定流程 调用自身接口
     *        $request = Request::getInstance();
     *        $response = Response::getInstance();
     *        $actionFunc = self::fixActionName($routeInfo[1]);
     *        $controller = self::fixActionObject($routeInfo, $actionFunc);
     *        $params = self::fixActionParams($controller, $actionFunc, $params);
     *        $request->setParams($params);
     * @param array $routeInfo
     * @param array $_params
     * @return mixed
     * @throws AppStartUpError
     */
    public static function dispatch(array $routeInfo, array $_params)
    {
        if (isset($_SERVER['CONTENT_TYPE']) && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== false && $_SERVER['REQUEST_METHOD'] == "POST") {
            $json_str = isset($GLOBALS["HTTP_RAW_POST_DATA"]) ? $GLOBALS["HTTP_RAW_POST_DATA"] : (file_get_contents('php://input') ?: '');
            $json = !empty($json_str) ? json_decode($json_str, true) : [];
            $params = array_merge($_params, $json);  //补充上$_REQUEST 中的信息
        } else {
            $params = $_params;
        }

        if (Application::striCmp($routeInfo[0], 'GraphQL')) {
            GraphQLDispatch::dispatch($routeInfo, $params);
        } else {
            self::_dispatch($routeInfo, $params);
        }
    }


    private static function _dispatch(array $routeInfo, array $params)
    {
        $request = Request::instance();
        $action = self::fixActionName($routeInfo[1]);
        $object = self::fixActionObject($routeInfo, $action);
        $fixed_params = self::fixActionParams($object, $action, $params);

        try {
            self::checkCsrfToken();
            $fixed_params = $object->hookAccessAndFilterRequest($fixed_params, $params);  //所有API类继承于BaseApi，默认行为直接原样返回参数不作处理
            $request->setParams($fixed_params);
            $result = call_user_func_array([$object, $action], $fixed_params);
            (DEV_MODEL == 'DEBUG') && Bootstrap::_D([
                'class' => get_class($object),
                'method' => $action,
                'params' => $fixed_params,
                'result' => $result
            ], 'api');
        } catch (Exception $ex) {
            Bootstrap::_D((array)$ex, 'api Exception');
            $result = self::getTraceAsResult($ex);
        }

        $json_str = isset($params['callback']) && !empty($params['callback']) ? "{$params['callback']}(" . json_encode($result) . ');' : json_encode($result);
        $response = Response::instance();
        $response->addHeader('Content-Type: application/json;charset=utf-8', false)->appendBody($json_str);
    }

    private static function getTraceAsResult(Exception $ex)
    {
        $code = $ex->getCode();  // errno为0 或 无error字段 表示没有错误  errno设置为0 会忽略error字段
        $error = (DEV_MODEL == 'DEBUG') ? [
            'Exception' => get_class($ex),
            'code' => $ex->getCode(),
            'message' => $ex->getMessage(),
            'file' => $ex->getFile() . ' [' . $ex->getLine() . ']',
        ] : [
            'code' => $code,
            'message' => $ex->getMessage(),
        ];
        $result = ['errno' => $code == 0 ? -1 : $code, 'error' => $error];

        while (!empty($ex) && $ex->getPrevious()) {
            $result['error']['errors'] = isset($result['error']['errors']) ? $result['error']['errors'] : [];
            $ex = $ex->getPrevious();
            $result['error']['errors'][] = (DEV_MODEL == 'DEBUG') ? ['Exception' => get_class($ex), 'code' => $ex->getCode(), 'message' => $ex->getMessage(), 'file' => $ex->getFile() . ' [' . $ex->getLine() . ']'] : ['code' => $ex->getCode(), 'message' => $ex->getMessage()];
        }
        return $result;
    }

    private static function checkCsrfToken()
    {
        $request = Request::instance();
        $request_method = $request->getMethod();
        $session_id = $request->getSessionId();
        if ($request_method == 'HEAD' || $request_method == 'GET' || $request_method == 'OPTIONS' || empty($session_id)) {
            $log_msg = "CSRF ignore [{$request_method}] session_id:{$session_id}, this_url:" . $request->getThisUrl();
            self::debug($log_msg, __METHOD__, __CLASS__, __LINE__);
        } else {
            $csrf = $request->getCsrfToken();
            if (!self::validCsrfToken($csrf)) {
                $log_msg = "CSRF error [{$request_method}] token:" . $csrf . ", this_url:" . $request->getThisUrl() . ", referer_url:" . $request->getHttpReferer();
                self::error($log_msg, __METHOD__, __CLASS__, __LINE__);
                throw new ApiParamsError($log_msg);
            } else {
                $log_msg = "CSRF pass [{$request_method}] token:" . $csrf . ", this_url:" . $request->getThisUrl() . ", referer_url:" . $request->getHttpReferer();
                self::debug($log_msg, __METHOD__, __CLASS__, __LINE__);
            }
        }
        Bootstrap::_D($log_msg, 'csrf');
    }

    private static function validCsrfToken($csrf)
    {
        false && func_get_args();
        return true;
    }

}