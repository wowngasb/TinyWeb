<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/8 0008
 * Time: 14:04
 */

namespace app\common\Base;


use GraphQL\Schema;
use TinyWeb\CurrentUserInterface;
use TinyWeb\ExecutableEmptyInterface;
use TinyWeb\Request;

abstract class BaseSchemaAppContext extends BaseModel implements ExecutableEmptyInterface
{

    public $request;
    public $user;

    /**
     * @param Request $request
     * @param CurrentUserInterface $user
     */
    public function __construct(Request $request, CurrentUserInterface $user){
        $this->request = $request;
        $this->user = $user;
    }

    /**
     * @return Schema
     */
    abstract public function schema();

}