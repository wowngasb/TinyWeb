<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/6 0006
 * Time: 15:30
 */

namespace app\common\Dispatch;

use ErrorException;
use GraphQL\GraphQL;

use app\Bootstrap;
use TinyWeb\Base\BaseModel;
use Exception;
use GraphQL\Type\Definition\Config;
use TinyWeb\Application;
use TinyWeb\BaseDispatch;
use TinyWeb\Exception\AppStartUpError;
use TinyWeb\ExecutableEmptyInterface;
use TinyWeb\Helper\ApiHelper;
use TinyWeb\Base\BaseGraphQLContext;
use TinyWeb\Request;
use TinyWeb\Response;

class GraphQLDispatch extends BaseModel implements BaseDispatch
{
    private static $instance = null;

    /**
     * 单实例实现
     * @return BaseDispatch
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
        return isset($data['variables']) ? $data['variables'] : null;
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
     * @return BaseGraphQLContext 可返回实现此接口的 其他对象 方便做类型限制
     * @throws AppStartUpError
     */
    public static function fixActionObject(array $routeInfo, $action)
    {
        $user_namespace = "\\" . Application::join("\\", [Application::instance()->getAppName(), 'api', 'GraphQL', "{$routeInfo[1]}", 'CurrentUser']);
        $context_namespace = "\\" . Application::join("\\", [Application::instance()->getAppName(), 'api', 'GraphQL', "{$routeInfo[1]}", 'AppContext']);
        $user = new $user_namespace();
        $context = new $context_namespace(Request::instance(), $user);
        if (!($context instanceof BaseGraphQLContext)) {
            throw new AppStartUpError("class:{$context_namespace} isn't instanceof BaseApiModel with routeInfo:" . json_encode($routeInfo));
        }
        if (!is_callable([$context, $action]) || ApiHelper::isIgnoreMethod($action)) {
            throw new AppStartUpError("action:{$context_namespace}->{$action} not allowed with routeInfo:" . json_encode($routeInfo));
        }
        return $context;
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
     * @param array $params
     * @return mixed
     * @throws AppStartUpError
     */
    public static function dispatch(array $routeInfo, array $params)
    {
        $debug = Request::_request('debug', '');
        if (!empty($debug)) {
            // Enable additional validation of type configs
            // (disabled by default because it is costly)
            Config::enableValidation();
            // Catch custom errors (to report them in query results if debugging is enabled)
            $phpErrors = [];
            set_error_handler(function ($severity, $message, $file, $line) use (&$phpErrors) {
                $phpErrors[] = new ErrorException($message, 0, $severity, $file, $line);
            });
        }
        $requestString = isset($params['query']) ? $params['query'] : '{hello}';
        $operationName = isset($params['operation']) ? $params['operation'] : null;

        $action = self::fixActionName($routeInfo[1]);
        $context = self::fixActionObject($routeInfo, $action);
        $variableValues = self::fixActionParams($context, $action, $params);

        $httpStatus = 200;
        try {
            // GraphQL schema to be passed to query executor:
            $result = GraphQL::execute(
                $context->schema(),
                $requestString,
                null,
                $context,
                $variableValues,
                $operationName
            );
            // Add reported PHP errors to result (if any)
            if (!empty($_GET['debug']) && !empty($phpErrors)) {
                $result['extensions']['phpErrors'] = array_map(
                    ['GraphQL\Error\FormattedError', 'createFromPHPError'],
                    $phpErrors
                );
            }
            Bootstrap::_D([
                'requestString' => $requestString,
                'operationName' => $operationName,
                'variableValues' => $variableValues,
                'result' => $result
            ], 'GraphQL');
        } catch (Exception $ex) {
            Bootstrap::_D((array)$ex, 'api Exception');
            $result = self::getTraceAsResult($ex);
            $httpStatus = 500;
        }

        $json_str = isset($params['callback']) && !empty($params['callback']) ? "{$params['callback']}(" . json_encode($result) . ');' : json_encode($result);
        $response = Response::instance();
        $response->setResponseCode($httpStatus)
            ->addHeader('Content-Type: application/json;charset=utf-8', true)
            ->appendBody($json_str);
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

}