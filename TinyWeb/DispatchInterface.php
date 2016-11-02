<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/31 0031
 * Time: 17:15
 */

namespace TinyWeb;


interface DispatchInterface
{

    /**
     * 单实例实现
     * @return DispatchInterface
     */
    public static function instance();

    /**
     * 根据对象和方法名 获取 修复后的参数
     * @param ExecutableEmptyInterface $object
     * @param string $action
     * @param array $params
     * @return array
     */
    public static function fixActionParams(ExecutableEmptyInterface $object, $action, array $params);

    /**
     * 修复并返回 真实需要调用对象的方法名称
     * @param string $action
     * @return string
     */
    public static function fixActionName($action);

    /**
     * 创建需要调用的对象 并检查对象和方法的合法性
     * @param array $routeInfo
     * @param string $action
     * @return ExecutableEmptyInterface  可返回实现此接口的 其他对象 方便做类型限制
     */
    public static function fixActionObject(array $routeInfo, $action);

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
     * @return void
     */
    public static function dispatch(array $routeInfo, array $params);

    /**
     * 根据对象和方法名 获取 执行结果
     * @param ExecutableEmptyInterface $object
     * @param string $action
     * @param array $params
     * @return mixed
     */
    public static function execute(ExecutableEmptyInterface $object, $action, array $params);

}