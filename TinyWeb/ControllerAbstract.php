<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/24 0024
 * Time: 14:59
 */

namespace TinyWeb;
use TinyWeb\Plugin\EventTrait;

/**
 * Class Controller
 * @package TinyWeb
 */
abstract class ControllerAbstract implements ExecutableEmptyInterface
{
    use EventTrait;

    protected $request = null;
    protected $response = null;
    protected $_view = null;

    protected $routeInfo = [];  // 在路由完成后, 请求被分配到的路由信息 [$_controller, $_action, $_module]
    protected $appname = '';

    final public function __construct()
    {
        $request = Request::instance();
        $response = Response::instance();

        if (!$request->isRouted()) {
            $request->setRouted();
        }
        $this->request = $request;
        $this->response = $response;

        $this->routeInfo = $request->getRouteInfo();
        $this->appname = Application::instance()->getAppName();
    }

    /**
     * Controller 构造完成之后 具体action 之前调佣 通常用于初始化 需显示调用父类 beforeAction
     */
    abstract public function beforeAction();

    /**
     * 为 Controller 绑定模板引擎
     * @param ViewInterface $view 实现视图接口的模板引擎
     * @return $this
     */
    final protected function setView(ViewInterface $view)
    {
        $this->_view = $view;
        return $this;
    }

    /**
     * @return ViewInterface
     */
    final protected function getView()
    {
        return $this->_view;
    }

    /**
     * 添加 模板变量
     * @param mixed $name 字符串或者关联数组, 如果为字符串, 则$value不能为空, 此字符串代表要分配的变量名. 如果为数组, 则$value须为空, 此参数为变量名和值的关联数组.
     * @param mixed $value 分配的模板变量值
     * @return $this
     */
    final protected function assign($name, $value = null)
    {
        $this->getView()->assign($name, $value);
        return $this;
    }

    /**
     * @param string $tpl_path
     */
    public function display($tpl_path = '')
    {
        if (empty($tpl_path)) {
            $tpl_path = ROOT_PATH . Application::join(DIRECTORY_SEPARATOR, [$this->appname, $this->routeInfo[2], 'views', $this->routeInfo[0], $this->routeInfo[1] . '.php']);
        } else {
            $tpl_path = ROOT_PATH . Application::join(DIRECTORY_SEPARATOR, [$this->appname, $this->routeInfo[2], 'views', $tpl_path]);
        }
        $view = $this->getView();
        $params = $view->getAssign();
        self::fire('preDisplay', [$this, $tpl_path, $params]);
        $view->display($tpl_path, $params);
    }

    public function widget($tpl_path, array $params)
    {
        $tpl_path = strtolower(trim($tpl_path));
        if (empty($tpl_path)) {
            return '';
        }

        $routeInfo = $this->routeInfo;
        $appname = $this->appname;
        $params['routeInfo'] = $routeInfo;
        $params['appname'] = $appname;
        $tpl_path = ROOT_PATH . Application::join(DIRECTORY_SEPARATOR, [$appname, 'widget', $tpl_path]);

        self::fire('preWidget', [$this, $tpl_path, $params]);
        $buffer = $this->getView()->widget($tpl_path, $params);
        return $buffer;
    }


    /**
     *  注册回调函数  回调参数为 callback($this, $tpl_path, $params)
     *  1、preDisplay	在模板渲染之前触发
     *  2、preWidget	在组件渲染之前触发
     * @param string $event
     * @return bool
     */
    protected static function isAllowedEvent($event)
    {
        static $allow_event = ['preDisplay', 'preWidget',];
        return in_array($event, $allow_event);
    }

}