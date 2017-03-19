<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/2 0002
 * Time: 18:11
 */

namespace TinyWeb\Dispatch;


use app\Bootstrap;
use TinyWeb\Base\BaseApi;
use Exception;
use TinyWeb\Application;
use TinyWeb\Base\BaseContext;
use TinyWeb\DispatchInterface;
use TinyWeb\Exception\AppStartUpError;
use TinyWeb\Helper\ApiHelper;
use TinyWeb\Traits\CacheTrait;
use TinyWeb\Traits\LogTrait;
use TinyWeb\Traits\RpcTrait;
use TinyWeb\Request;
use TinyWeb\Response;

class ApiDispatch implements DispatchInterface
{
    use LogTrait, RpcTrait, CacheTrait;

    /**
     * 根据对象和方法名 获取 修复后的参数
     * @param BaseContext $object
     * @param string $action
     * @param array $params
     * @return array
     */
    public static function initMethodParams(BaseContext $object, $action, array $params)
    {
        if (isset($_SERVER['CONTENT_TYPE']) && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== false && $_SERVER['REQUEST_METHOD'] == "POST") {
            $json_str = file_get_contents('php://input') ?: '';
            $json = !empty($json_str) ? json_decode($json_str, true) : [];
            $params = array_merge($params, $json);  //补充上$_REQUEST 中的信息
        }
        $params = ApiHelper::fixActionParams($object, $action, $params);
        $object->getRequest()->setParams($params);
        return $params;
    }

    /**
     * 修复并返回 真实需要调用对象的方法名称
     * @param array $routeInfo
     * @return string
     */
    public static function initMethodName(array $routeInfo)
    {
        return strtolower($routeInfo[0]) == 'GraphQL' ? 'exec' : $routeInfo[1];
    }

    /**
     * 创建需要调用的对象 并检查对象和方法的合法性
     * @param Request $request
     * @param Response $response
     * @param array $routeInfo
     * @param string $action
     * @return BaseApi 可返回实现此接口的 其他对象 方便做类型限制
     * @throws AppStartUpError
     */
    public static function initMethodContext(Request $request, Response $response, array $routeInfo, $action)
    {
        $namespace = "\\" . Application::join("\\", [Application::getInstance()->getAppName(), 'api', $routeInfo[0]]);
        $context = new $namespace($request, $response);
        if (!($context instanceof BaseApi)) {
            throw new AppStartUpError("class:{$namespace} isn't instanceof BaseApiModel with routeInfo:" . json_encode($routeInfo));
        }
        if (!is_callable([$context, $action]) || ApiHelper::isIgnoreMethod($action)) {
            throw new AppStartUpError("action:{$namespace}->{$action} not allowed with routeInfo:" . json_encode($routeInfo));
        }
        $context->beforeApi();
        return $context;
    }

    public static function dispatch(BaseContext $context, $action, array $params)
    {
        try {
            $result = call_user_func_array([$context, $action], $params);
            Bootstrap::_D([
                'class' => get_class($context),
                'method' => $action,
                'params' => $params,
                'result' => $result
            ], 'api');
            $json_str = isset($params['callback']) && !empty($params['callback']) ? "{$params['callback']}(" . json_encode($result) . ');' : json_encode($result);
            $context->getResponse()->addHeader('Content-Type: application/json;charset=utf-8', false)->appendBody($json_str);
        } catch (Exception $ex) {
            Bootstrap::_D((array)$ex, 'api Exception');
            self::traceException($context->getRequest(), $context->getResponse(), $ex);
        }
    }

    /**
     * 处理异常接口 用于捕获分发过程中的异常
     * @param Request $request
     * @param Response $response
     * @param Exception $ex
     */
    public static function traceException(Request $request, Response $response, Exception $ex)
    {
        $params = $request->getParams();
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
        $json_str = isset($params['callback']) && !empty($params['callback']) ? "{$params['callback']}(" . json_encode($result) . ');' : json_encode($result);
        $response->addHeader('Content-Type: application/json;charset=utf-8', false)->appendBody($json_str);
    }
}