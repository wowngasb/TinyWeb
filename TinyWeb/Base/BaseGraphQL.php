<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/8 0008
 * Time: 14:04
 */

namespace TinyWeb\Base;


abstract class BaseGraphQL extends  BaseApi
{

    use BaseModelTrait;

    /**
     * @return mixed
     */
    abstract public function schema();

}