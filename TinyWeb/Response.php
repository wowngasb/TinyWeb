<?php
namespace TinyWeb;

use TinyWeb\Exception\AppStartUpError;

/**
 * Class Response
 * @package TinyWeb
 */
final class Response
{

    protected $_header = [];  // 响应给请求的Header
    protected $_started = false;  // 响应Header 是否已经发送
    protected $_code = 200;  // 响应给请求端的HTTP状态码
    protected $_body = [];  // 响应给请求的body

    private static $instance = null;

    /**
     * @return Response
     */
    public static function getInstance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    private function __construct()
    {
    }

    /**
     * 添加响应header
     * @param string $string
     * @param bool $replace
     * @return $this
     * @throws \Exception HeaderError
     */
    public function addHeader($string, $replace = true)
    {
        if ($this->_started) {
            throw new AppStartUpError('header has been send');
        }
        $this->_header[] = [$string, $replace];
        return $this;
    }

    public function clearResponse()
    {
        if ($this->_started) {
            throw new AppStartUpError('header has been send');
        }
        $this->_body = [];
        $this->_header = [];
        $this->_code = 200;
        return $this;
    }

    public function setResponseCode($code)
    {
        if ($this->_started) {
            throw new AppStartUpError('header has been send');
        }
        $this->_code = intval($code);
        return $this;
    }

    /**
     * 发送响应header给请求端
     * @return $this
     * @throws AppStartUpError
     */
    public function sendHeader()
    {
        if ($this->_started) {
            throw new AppStartUpError('header has been send');
        }
        foreach ($this->_header as $idx => $val) {
            header($val[0], $val[1]);
        }
        http_response_code($this->_code);
        $this->_started = true;
        return $this;
    }

    /**
     * 向请求回应 添加消息体
     * @param string $body 要发送的字符串
     * @param string $name 此次发送消息体的 名称 可用于debug
     * @return $this
     */
    public function apendBody($body, $name = '')
    {
        if (!isset($this->_body[$name])) {
            $this->_body[$name] = [];
        }
        $this->_body[$name][] = $body;
        return $this;
    }

    /**
     * @return $this
     */
    public function sendBody()
    {
        if (!$this->_started) {
            $this->sendHeader();
        }
        foreach($this->_body as $name => $body){
            foreach($body as $idx => $msg){
                echo $msg;
            }
        }
        return $this;
    }

    public function getBody($name = null)
    {
        if( is_null($name) ){
            return $this->_body;
        }
        return isset($this->_body[$name]) ? $this->_body[$name] : [];
    }


}