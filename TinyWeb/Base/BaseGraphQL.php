<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/8 0008
 * Time: 14:04
 */

namespace TinyWeb\Base;

use GraphQL\Schema;
use TinyWeb\DispatchAbleInterface;
use TinyWeb\Request;

abstract class BaseGraphQL extends BaseModel implements DispatchAbleInterface
{

    public $request;
    public $user;

    /**
     * @param Request $request
     * @param BaseCurrentUser $user
     */
    public function __construct(Request $request, BaseCurrentUser $user){
        $this->request = $request;
        $this->user = $user;
    }

    /**
     * @return Schema
     */
    abstract public function schema();

}