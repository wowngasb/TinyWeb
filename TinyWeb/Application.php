<?php
namespace TinyWeb;

use TinyWeb\Exception\RouteError;
use TinyWeb\Exception\AppStartUpError;
use TinyWeb\Helper\ApiHelper;
use TinyWeb\Plugin\EventTrait;

/**
 * Class Application
 * @package TinyWeb
 */
final class Application implements DispatchInterface
{
    use EventTrait;

    protected $_config = [];  // 全局配置
    protected $_appname = '';  // app 目录，用于 拼接命名空间 和 定位模板文件
    protected $_run = false;  // 布尔值, 指明当前的Application是否已经运行
    protected $_routes = [];  // 路由列表
    protected $_dispatches = [];  // 分发列表

    protected static $instance = null;  // Application通过特殊的方式实现了单利模式, 此属性保存当前实例
    private static $_microtime = null;

    /**
     * Application constructor.
     * @param array $config 关联数组的配置
     */
    public function __construct(array $config = [])
    {
        self::$_microtime = microtime(true);
        $this->_config = $config;
        self::$instance = $this;
    }

    public static function usedMilliSecond()
    {
        return round(microtime(true) - self::$_microtime, 3) * 1000;
    }

    /**
     * 获取 全局配置 数组
     * @param void
     * @return array
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * 获取 全局配置 指定key的值 不存在则返回 default
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getEnv($key, $default = '')
    {
        return isset($this->_config[$key]) ? $this->_config[$key] : $default;
    }

    /**
     * @param $appname
     * @return $this
     * @throws AppStartUpError
     */
    public function setAppName($appname)
    {
        if ($this->_run) {
            throw new AppStartUpError('cannot setAppName in running');
        }

        $this->_appname = $appname;
        return $this;
    }

    /**
     * @return string
     */
    public function getAppName()
    {
        return $this->_appname;
    }

    /**
     * 运行一个Application, 开始接受并处理请求. 这个方法只能成功调用一次.
     * @throws RouteError
     * @throws AppStartUpError
     */
    public function run()
    {
        if (empty($this->_routes)) {
            throw new AppStartUpError('empty routes');
        }
        $this->_run = true;
        $request = Request::instance();
        $response = Response::instance();
        $this->fire('routerStartup', [$this, $request, $response]);  // 在路由之前触发	这个是7个事件中, 最早的一个. 但是一些全局自定的工作, 还是应该放在Bootstrap中去完成

        list($route, list($routeInfo, $params)) = $this->chooseRoute(null, $request);  // 必定会 匹配到一条路由 RoutesSimple
        if (empty($routeInfo)) {
            throw new RouteError('cannot match with request:' . json_encode($request) . ', routes:' . json_encode(array_keys($this->_routes)));
        }
        $request->setUnRouted()
            ->setCurrentRoute($route)
            ->setParams($params)
            ->setRouteInfo($routeInfo)
            ->setRouted();

        $this->fire('routerShutdown', [$this, $request, $response]);  // 路由结束之后触发	此时路由一定正确完成, 否则这个事件不会触发
        $this->fire('dispatchLoopStartup', [$this, $request, $response]);  // 分发循环开始之前被触发
        $this->forward($routeInfo, $params, $route);
        $this->fire('dispatchLoopShutdown', [$this, $request, $response]);  // 分发循环结束之后触发	此时表示所有的业务逻辑都已经运行完成, 但是响应还没有发送

        $response->sendBody();
    }

    /**
     * @param array|null $routeInfo
     * @param array|null $params
     * @param null $route
     * @throws AppStartUpError
     */
    public function forward(array $routeInfo = null, array $params = null, $route = null)
    {
        $request = Request::instance();
        $response = Response::instance();

        // 对使用默认值 null 的参数 用当前值补全
        if (is_null($route)) {
            $route = $request->getCurrentRoute();
        }
        $this->getRoute($route);  // 检查对应 route 是否注册过
        if (is_null($routeInfo)) {
            $routeInfo = $request->getRouteInfo();
        }
        if (is_null($params)) {
            $params = $request->getParams();

        }

        $request->setUnRouted()
            ->setCurrentRoute($route)
            ->setRouteInfo($routeInfo)
            ->setParams($params)
            ->setRouted();  // 根据新的参数 再次设置 $request 的路由信息
        // 设置完成 锁定 $request

        $response->clearResponse();  // 清空已设置的 信息
        $this->fire('preDispatch', [$this, $request, $response]);  // 分发之前触发	如果在一个请求处理过程中, 发生了forward, 则这个事件会被触发多次

        $dispatcher = $this->getDispatch($route);
        $dispatcher::dispatch($routeInfo, $params);  //分发

        $this->fire('postDispatch', [$this, $request, $response]);  // 分发结束之后触发	此时动作已经执行结束, 视图也已经渲染完成. 和preDispatch类似, 此事件也可能触发多次
    }

    /**
     * 重定向请求到新的路径  HTTP 302 自带 exit 效果
     * @param string $url 要重定向到的URL
     * @return void
     */
    public static function redirect($url)
    {
        header("Location: {$url}");  // Redirect browser
        exit;  // Make sure that code below does not get executed when we redirect.
    }

    /**
     * 添加路由到 路由列表 接受请求后 根据添加的先后顺序依次进行匹配 直到成功
     * @param string $route
     * @param RouteInterface $routeObj
     * @param DispatchInterface $dispatch 处理分发接口
     * @return $this
     * @throws AppStartUpError
     */
    public function addRoute($route, RouteInterface $routeObj, DispatchInterface $dispatch = null)
    {
        if ($this->_run) {
            throw new AppStartUpError('cannot add route after run');
        }
        if (isset($this->_routes[$route])) {
            throw new AppStartUpError("route:{$route} has been added");
        }
        $this->_routes[$route] = $routeObj;
        if (!empty($dispatch)) {
            $this->_dispatches[$route] = $dispatch;
        }
        return $this;
    }

    /**
     * 根据 名字 获取 路由
     * @param string $route
     * @return RouteInterface
     * @throws AppStartUpError
     */
    public function getRoute($route)
    {
        if (!isset($this->_routes[$route])) {
            {
                throw new AppStartUpError("route:{$route}, routes:" . json_encode(array_keys($this->_routes)) . ' not found');
            }
        }
        return $this->_routes[$route];
    }

    /**
     * 根据 名字 获取 分发器  默认返回this
     * @param string $route
     * @return DispatchInterface
     * @throws AppStartUpError
     */
    public function getDispatch($route)
    {
        if (!isset($this->_routes[$route])) {
            return $this;
        }
        return $this->_dispatches[$route];
    }

    /**
     * 根据请求 $request 的 $_method $_request_uri $_language 得出 路由信息 及 参数
     * 匹配成功后 获取 [$routeInfo, $params]  失败 [null, null]
     * 一般参数应使用 php 原始 $_GET,$_POST 保存 保持一致性
     * @param Request $request 请求对象
     * @param string $route 指定路由名称  null 表示 依次尝试 路由列表
     * @return array 匹配成功 [$route, [$routeInfo, $params], ]  失败 ['', [null, null], ]
     */
    public function chooseRoute($route, Request $request)
    {
        if (!is_null($route)) {
            $tmp = $this->getRoute($route)->route($request);
            return [$route, $tmp,];
        }
        foreach ($this->_routes as $route => $val) {
            $tmp = $this->getRoute($route)->route($request);
            if (!empty($tmp[0])) {
                return [$route, $tmp,];
            }
        }
        return ['', [null, []],];
    }

    /**
     * 根据 路由信息 和 参数 按照路由规则生成 url
     * @param array $routerArr
     * @param array $params
     * @param string $route 指定路由名称
     * @return string
     */
    public function routeFord($route, array $routerArr, array $params)
    {
        return $this->getRoute($route)->ford($routerArr, $params);
    }

    ###############################################################
    ############ 实现 DispatchInterface 默认分发器 ################
    ###############################################################

    /**
     * 根据对象和方法名 获取 修复后的参数
     * @param ExecutableEmptyInterface $object
     * @param $action
     * @param array $params
     * @return array
     */
    public static function fixActionParams(ExecutableEmptyInterface $object, $action, array $params)
    {
        return ApiHelper::fixActionParams($object, $action, $params);
    }

    /**
     * @param string $action
     * @return string
     */
    public static function fixActionName($action)
    {
        return $action;
    }

    /**
     * @param array $routeInfo
     * @param string $action
     * @return ControllerAbstract
     * @throws AppStartUpError
     */
    public static function fixActionObject(array $routeInfo, $action)
    {
        $controller = (isset($routeInfo[0]) && !empty($routeInfo[0])) ? $routeInfo[0] : '';
        $module = isset($routeInfo[2]) ? $routeInfo[2] : '';
        list($controller, $module) = [strtolower($controller), strtolower($module),];
        if (empty($controller) ) {
            throw new AppStartUpError("empty controller with routeInfo:" . json_encode($routeInfo));
        }

        $namespace = "\\" . Application::join("\\", [Application::instance()->getAppName(), $module, 'controllers', $controller]);

        if (!class_exists($namespace)) {
            throw new AppStartUpError("class:{$namespace} not exists with routeInfo:" . json_encode($routeInfo));
        }
        $object = new $namespace();
        if (!($object instanceof ControllerAbstract)) {
            throw new AppStartUpError("class:{$namespace} isn't instanceof ControllerAbstract with routeInfo:" . json_encode($routeInfo));
        }
        if (!is_callable([$object, $action])) {
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
     * @param array $params
     * @return void
     */
    public static function dispatch(array $routeInfo, array $params)
    {
        $request = Request::instance();
        $response = Response::instance();
        $action = self::fixActionName($routeInfo[1]);
        $object = self::fixActionObject($routeInfo, $action);
        $params = self::fixActionParams($object, $action, $params);
        $request->setParams($params);

        $object->beforeAction();  //控制器 beforeAction 不允许显式输出
        $buffer = self::execute($object, $action, $params);

        if (!empty($buffer)) {
            $response->apendBody($buffer);
        }
    }

    /**
     * 根据对象和方法名 获取 执行结果
     * @param ExecutableEmptyInterface $object
     * @param string $action
     * @param array $params
     * @return mixed
     */
    public static function execute(ExecutableEmptyInterface $object, $action, array $params)
    {
        ob_start();
        call_user_func_array([$object, $action], $params);
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }

    /**
     * 获取当前的Application实例
     * @return Application
     */
    public static function instance()
    {
        return self::$instance;
    }

    ###############################################################
    ############## 重写 EventTrait::isAllowedEvent ################
    ###############################################################

    /**
     *  注册回调函数  回调参数为 callback(Request $request, Response $response)  两个参数都为单实例
     *  1、routerStartup    在路由之前触发    这个是7个事件中, 最早的一个. 但是一些全局自定的工作, 还是应该放在Bootstrap中去完成
     *  2、routerShutdown    路由结束之后触发    此时路由一定正确完成, 否则这个事件不会触发
     *  3、dispatchLoopStartup    分发循环开始之前被触发
     *  4、preDispatch    分发之前触发    如果在一个请求处理过程中, 发生了forward, 则这个事件会被触发多次
     *  5、postDispatch    分发结束之后触发    此时动作已经执行结束, 视图也已经渲染完成. 和preDispatch类似, 此事件也可能触发多次
     *  6、dispatchLoopShutdown    分发循环结束之后触发    此时表示所有的业务逻辑都已经运行完成, 但是响应还没有发送
     * @param string $event
     * @return bool
     */
    protected static function isAllowedEvent($event)
    {
        static $allow_event = ['routerStartup', 'routerShutdown', 'dispatchLoopStartup', 'preDispatch', 'postDispatch', 'dispatchLoopShutdown',];
        return in_array($event, $allow_event);
    }


    ###############################################################
    ############## 常用 辅助函数 放在这里方便使用 #################
    ###############################################################

    public static function safe_base64_encode($str)
    {
        $str = rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
        return $str;
    }

    public static function safe_base64_decode($str)
    {
        $str = base64_decode(str_pad(strtr($str, '-_', '+/'), strlen($str) % 4, '=', STR_PAD_RIGHT));
        return $str;
    }

    /**
     * 加密函数 使用 常量 CRYPT_KEY 作为 key
     * @param string $string 需要加密的字符串
     * @param int $expiry 加密生成的数据 的 有效期 为0表示永久有效， 单位 秒
     * @return string 加密结果 使用了 safe_base64_encode
     * @throws AppStartUpError
     */
    public static function encrypt($string, $expiry = 0)
    {
        if (empty($string)) {
            return '';
        }
        if (!defined('CRYPT_KEY') || empty(CRYPT_KEY)) {
            throw new AppStartUpError("cannot found const CRYPT_KEY");
        }
        return self::authString($string, 'ENCODE', CRYPT_KEY, $expiry);
    }

    /**
     * 解密函数 使用 常量 CRYPT_KEY 作为 key  成功返回原字符串  失败或过期 返回 空字符串
     * @param string $string 需解密的 字符串 safe_base64_encode 格式编码
     * @return string 解密结果
     * @throws AppStartUpError
     */
    public static function decrypt($string)
    {
        if (empty($string)) {
            return '';
        }
        if (!defined('CRYPT_KEY') || empty(CRYPT_KEY)) {
            throw new AppStartUpError("cannot found const CRYPT_KEY");
        }
        return self::authString($string, 'DECODE', CRYPT_KEY);
    }

    /**
     * 加密或解密操作
     * @param string $string 需要操作的字符串
     * @param string $operation 具体操作  'ENCODE' 加密   'DECODE' 解密
     * @param string $key 加密解密 key
     * @param int $expiry 加密生成的数据 的 有效期 为0表示永久有效， 单位 秒
     * @return string 结果
     */
    public static function authString($string, $operation, $key, $expiry = 0)
    {
        // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
        $ckey_length = 4;
        // 密匙
        $key = md5($key);
        // 密匙a会参与加解密
        $keya = md5(substr($key, 0, 16));
        // 密匙b会用来做数据完整性验证
        $keyb = md5(substr($key, 16, 16));
        // 密匙c用于变化生成的密文
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) :
            substr(md5(microtime()), -$ckey_length)) : '';
        // 参与运算的密匙
        $cryptkey = $keya . md5($keya . $keyc);
        $key_length = strlen($cryptkey);
        // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，
        //解密时会通过这个密匙验证数据完整性
        // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
        $string = $operation == 'DECODE' ? self::safe_base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);
        $rndkey = array();
        // 产生密匙簿
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
        // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        // 核心加解密部分
        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            // 从密匙簿得出密匙进行异或，再转成字符
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if ($operation == 'DECODE') {
            // 验证数据有效性，请看未加密明文的格式
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) &&
                substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)
            ) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
            // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
            return $keyc . str_replace('=', '', self::safe_base64_encode($result));
        }
    }

    public static function strCmp($str1, $str2)
    {
        if (!function_exists('hash_equals')) {
            if (strlen($str1) != strlen($str2)) {
                return false;
            } else {
                $res = $str1 ^ $str2;
                $ret = 0;
                for ($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);
                return !$ret;
            }
        } else {
            return hash_equals($str1, $str2);
        }
    }

    /**
     * Byte 数据 格式化 为 字符串
     * @param int $num 大小
     * @param string $in_tag 输入单位
     * @param string $out_tag 输出单位  为空表示自动尝试 最适合的单位
     * @param int $dot 小数位数 默认为2
     * @return string
     */
    public static function byte2size($num, $in_tag = '', $out_tag = '', $dot = 2)
    {
        $num = $num * 1.0;
        $out_tag = strtoupper($out_tag);
        $in_tag = strtoupper($in_tag);
        $dot = $dot > 0 ? intval($dot) : 0;
        $tag_map = ['K' => 1024, 'M' => 1024 * 1024, 'G' => 1024 * 1024 * 1024, 'T' => 1024 * 1024 * 1024 * 1024];
        if (!empty($in_tag) && isset($tag_map[$in_tag])) {
            $num = $num * $tag_map[$in_tag];  //正确转换输入数据 去掉单位
        }
        $zero_list = [];
        for ($i = 0; $i < $dot; $i++) {
            $zero_list[] = '0';
        }
        $zero_str = '.' . join($zero_list, '');  // 构建字符串 .00 用于替换 1.00G 为 1G
        if ($num < 1024) {
            return str_replace($zero_str, '', sprintf("%.{$dot}f", $num));
        } else if (!empty($out_tag) && isset($tag_map[$out_tag])) {
            $tmp = round($num / $tag_map[$out_tag], $dot);
            return str_replace($zero_str, '', sprintf("%.{$dot}f", $tmp)) . $out_tag;  //使用设置的单位输出
        } else {
            foreach ($tag_map as $key => $val) {  //尝试找到一个合适的单位
                $tmp = round($num / $val, $dot);
                if ($tmp >= 1 && $tmp < 1024) {
                    return str_replace($zero_str, '', sprintf("%.{$dot}f", $tmp)) . $key;
                }
            }
            //未找到合适的单位  使用最大 tag T 进行输出
            return self::byte2size($num, '', 'T', $dot);
        }
    }

    /**
     * 使用 seq 把 list 数组中的非空字符串连接起来  _join('_', [1,2,3]) = '1_2_3_'
     * @param string $seq
     * @param array $list
     * @return string
     */
    public static function join($seq, array $list)
    {
        $tmp_list = [];
        foreach ($list as $item) {
            $item = trim($item);
            if (!empty($item)) {
                $tmp_list[] = strval($item);
            }
        }
        return join($seq, $tmp_list);
    }

}