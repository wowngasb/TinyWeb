<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/1 0001
 * Time: 19:57
 */

namespace TinyWeb\Base;
use TinyWeb\Request;
use TinyWeb\Response;


/**
 * Interface ExecutableEmptyInterface
 * 一个空的接口  实现此接口的类 才可以被分发器执行
 * @package TinyWeb
 */
abstract class BaseContext
{
    protected $request = null;

    protected $response = null;

    /**
     * BaseContext constructor.
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @return null|Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return null|Response
     */
    public function getResponse()
    {
        return $this->response;
    }

}