<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/9 0009
 * Time: 18:22
 */

namespace TinyWeb\Dispatch;


use TinyWeb\Base\BaseContext;
use TinyWeb\DispatchInterface;
use TinyWeb\Request;
use TinyWeb\Response;

class YarDispatch   implements DispatchInterface
{
    /**
     * 根据对象和方法名 获取 修复后的参数
     * @param BaseContext $context
     * @param string $action
     * @param array $params
     * @return array
     */
    public static function fixMethodParams(BaseContext $context, $action, array $params)
    {
        // TODO: Implement fixMethodParams() method.
    }

    /**
     * 修复并返回 真实需要调用对象的方法名称
     * @param array $routeInfo
     * @internal param string $action
     */
    public static function fixMethodName(array $routeInfo)
    {
        // TODO: Implement fixMethodName() method.
    }

    /**
     * 创建需要调用的对象 并检查对象和方法的合法性
     * @param Request $request
     * @param Response $response
     * @param array $routeInfo
     * @param string $action
     * @return BaseContext 可返回实现此接口的 其他对象 方便做类型限制
     */
    public static function fixMethodContext(Request $request, Response $response, array $routeInfo, $action)
    {
        // TODO: Implement fixMethodContext() method.
    }

    /**
     * 调用分发 渲染输出执行结果  请在方法开头加上 固定流程 调用自身接口  无任何返回值
     * @param BaseContext $context
     * @param string $action
     * @param array $params
     */
    public static function dispatch(BaseContext $context, $action, array $params)
    {
        // TODO: Implement dispatch() method.
    }
}