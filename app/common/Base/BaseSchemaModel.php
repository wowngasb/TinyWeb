<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/6 0006
 * Time: 15:49
 */

namespace app\common\Base;


use GraphQL\Schema;
use TinyWeb\ExecutableEmptyInterface;
use TinyWeb\Plugin\LogTrait;
use TinyWeb\Plugin\RpcTrait;

class BaseSchemaModel extends BaseModel implements ExecutableEmptyInterface
{

    use LogTrait, RpcTrait;
    protected static $detail_log = false;

    /**
     * @return Schema
     */
    public function buildSchema(){
        return new Schema();
    }

}