<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/8 0008
 * Time: 14:04
 */

namespace TinyWeb\Base;


use TinyWeb\Traits\CacheTrait;
use TinyWeb\Traits\LogTrait;
use TinyWeb\Traits\RpcTrait;

abstract class BaseGraphQL extends  BaseApi
{

    use LogTrait, RpcTrait, CacheTrait;

    /**
     * @return mixed
     */
    abstract public function schema();

}