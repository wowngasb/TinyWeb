<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/24 0024
 * Time: 14:28
 */

namespace TinyWeb;

use TinyWeb\Exception\AppStartUpError;

/**
 * Class Request
 * @package TinyWeb
 */
final class Request
{
    protected $_request_uri = '/';  // 当前请求的Request URI
    protected $_method = 'GET';  // 当前请求的Method, 对于命令行来说, Method为"CLI"
    protected $_language = ''; // 当前请求的希望接受的语言, 对于Http请求来说, 这个值来自分析请求头Accept-Language. 对于不能鉴别的情况, 这个值为空.
    protected $_routed = false; // 表示当前请求是否已经完成路由 完成后 不可修改路由和参数信息
    protected $_http_referer = '';
    protected $_this_url = '';
    protected $_csrf_token = '';

    protected $_current_route = '';  // 当前使用的 路由名称 在注册路由时给出的
    protected $_route_info = [];  // 当前 路由信息 [$controller, $action, $module]
    protected $_params = null;  // 匹配到的参数 用于调用 action

    private  $_session_started = false;
    private  $_session_start = false;

    private $_request_timestamp = null;

    private static $_instance = null;

    /**
     * @param $uri
     * @return Request
     */
    public function cloneAndHookUri($uri){
        $tmp = clone $this;
        $tmp->_request_uri = $uri;
        return $tmp;
    }

    /**
     * @return Request
     */
    public static function instance()
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new static();
        }
        self::$_instance->checkSessionStart();
        return self::$_instance;
    }

    /**
     * @return bool
     */
    public  function isSessionStarted()
    {
        return $this->_session_started;
    }

    /**
     * @return bool
     */
    public  function isSessionStart()
    {
        return $this->_session_start;
    }

    /**
     * @param bool $session_start
     * @return $this
     */
    public function setSessionStart($session_start)
    {
        $this->_session_start = $session_start;
        $this->checkSessionStart();
        return $this;
    }

    /**
     *
     */
    private function checkSessionStart(){
        if( $this->isSessionStart() && !$this->isSessionStarted() ){
            session_start();
            $this->_session_started = true;
        }
    }

    private function __construct()
    {
        $this->_request_timestamp = microtime(true);
        $this->_request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
        $this->_method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : '';
        $this->_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
        $this->_http_referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        $this->_this_url = SYSTEM_HOST . substr($this->_request_uri, 1);
        $this->_csrf_token = self::_request('CSRF', '');
    }

    public function get_request_timestamp(){
        return $this->_request_timestamp;
    }

    public function getCsrfToken()
    {
        return $this->_csrf_token;
    }

    public function getThisUrl()
    {
        return $this->_this_url;
    }

    public function getHttpReferer()
    {
        return $this->_http_referer;
    }

    public function getSessionId()
    {
        return session_id();
    }

    /**
     * @return array
     */
    public function getRouteInfo()
    {
        return $this->_route_info;
    }

    /**
     * @param array $routeInfo
     * @return $this
     * @throws AppStartUpError
     */
    public function setRouteInfo(array $routeInfo)
    {
        if ($this->_routed) {
            throw new AppStartUpError('request has been routed');
        }
        if( count($routeInfo)!==3 ){
            throw new AppStartUpError('routeInfo:' . json_encode($routeInfo) . ' error length');
        }
        $routeInfo = [strtolower($routeInfo[0]), strtolower($routeInfo[1]), strtolower($routeInfo[2]),];
        $this->_route_info = $routeInfo;
        return $this;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * 设置本次请求入口方法的参数 只有第一次设置有效
     * @param array $params
     * @return $this
     * @throws AppStartUpError
     */
    public function setParams(array $params)
    {
        if( is_null($this->_params) ){
            $this->_params = $params;
        }
        return $this;
    }

    /**
     * @param string $current_route
     * @return $this
     * @throws AppStartUpError
     */
    public function setCurrentRoute($current_route)
    {
        if ($this->_routed) {
            throw new AppStartUpError('request has been routed');
        }
        $this->_current_route = $current_route;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentRoute()
    {
        return $this->_current_route;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->_language;
    }

    /**
     * @return bool
     */
    public function isRouted()
    {
        return $this->_routed;
    }

    /**
     * @return $this
     */
    public function setRouted()
    {
        $this->_routed = true;
        return $this;
    }

    /**
     * @return $this
     * @throws AppStartUpError
     */
    public function setUnRouted()
    {
        $this->_routed = false;
        $this->_current_route = '';
        $this->_params = [];
        $this->_route_info = [];
        return $this;
    }

    /**
     * @return string
     */
    public function getRequestUri()
    {
        return $this->_request_uri;
    }

    /**
     * 根据 路由信息 和 参数 按照路由规则生成 url
     * @param array $routerArr
     * @param array $params
     * @return string
     */
    public static function urlTo(array $routerArr, array $params=[])
    {
        $route = self::instance() ->getCurrentRoute();
        return Application::instance()->getRoute($route)->ford($routerArr, $params);
    }

    /**
     * @param string $name
     * @param string $default
     * @return string
     */
    public static function _get($name, $default = '')
    {
        return isset($_GET[$name]) ? $_GET[$name] : $default;
    }

    /**
     * @param $name
     * @param string $default
     * @return string
     */
    public static function _post($name, $default = '')
    {
        return isset($_POST[$name]) ? $_POST[$name] : $default;
    }

    /**
     * @param $name
     * @param string $default
     * @return string
     */
    public static function _env($name, $default = '')
    {
        return isset($_ENV[$name]) ? $_ENV[$name] : $default;
    }

    /**
     * @param $name
     * @param string $default
     * @return string
     */
    public static function _server($name, $default = '')
    {
        return isset($_SERVER[$name]) ? $_SERVER[$name] : $default;
    }

    /**
     * @param $name
     * @param string $default
     * @return string
     */
    public static function _cookie($name, $default = '')
    {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default;
    }

    /**
     * @param $name
     * @param string $default
     * @return string
     */
    public static function _files($name, $default = '')
    {
        return isset($_FILES[$name]) ? $_FILES[$name] : $default;
    }

    /**
     * @param $name
     * @param string $default
     * @return string
     */
    public static function _request($name, $default = '')
    {
        return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $default;
    }

    /**
     * @param $name
     * @param string $default
     * @return string
     */
    public static function _session($name, $default = '')
    {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : $default;
    }


}