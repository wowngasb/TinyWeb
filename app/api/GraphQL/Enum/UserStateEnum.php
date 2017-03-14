<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/13 0013
 * Time: 15:45
 */

namespace app\api\GraphQL\Basic\Enum;


use Youshido\GraphQL\Type\Enum\AbstractEnumType;

class UserStateEnum extends AbstractEnumType
{

    /**
     * @return array
     */
    public function getValues()
    {
        return [
            ['value' => 1, 'name' => 'NORMAL'],
            ['value' => 2, 'name' => 'FROZEN'],
            ['value' => 9, 'name' => 'DELETE'],
        ];
    }

    public function getDescription()
    {
        return '用户账号状态';
    }

}