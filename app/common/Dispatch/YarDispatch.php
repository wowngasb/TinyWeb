<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/9 0009
 * Time: 18:22
 */

namespace app\common\Dispatch;


use TinyWeb\BaseDispatch;
use TinyWeb\ExecutableEmptyInterface;
use TinyWeb\Base\BaseModel;

class YarDispatch  extends BaseModel implements BaseDispatch
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
        // TODO: Implement fixActionParams() method.
    }

    /**
     * 修复并返回 真实需要调用对象的方法名称
     * @param string $action
     * @return string
     */
    public static function fixActionName($action)
    {
        // TODO: Implement fixActionName() method.
    }

    /**
     * 创建需要调用的对象 并检查对象和方法的合法性
     * @param array $routeInfo
     * @param string $action
     * @return ExecutableEmptyInterface  可返回实现此接口的 其他对象 方便做类型限制
     */
    public static function fixActionObject(array $routeInfo, $action)
    {
        // TODO: Implement fixActionObject() method.
    }

    /**
     * 调用分发 渲染输出执行结果  请在方法开头加上 固定流程 调用自身接口  无任何返回值
     *        $request = Request::getInstance();
     *        $actionFunc = self::fixActionName($routeInfo[1]);
     *        $controller = self::fixActionObject($routeInfo, $actionFunc);
     *        $params = self::fixActionParams($controller, $actionFunc, $params);
     *        $request->setParams($params);
     * @param array $routeInfo
     * @param array $params
     * @return void
     */
    public static function dispatch(array $routeInfo, array $params)
    {
        // TODO: Implement dispatch() method.
    }
}