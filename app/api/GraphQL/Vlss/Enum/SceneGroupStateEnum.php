<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/13 0013
 * Time: 13:40
 */

namespace app\api\GraphQL\Vlss\Enum;


use Youshido\GraphQL\Type\Enum\AbstractEnumType;

class SceneGroupStateEnum extends AbstractEnumType
{

    /**
     * @return array
     */
    public function getValues()
    {
        return [
            ['value' => 1, 'name' => 'NORMAL'],
            ['value' => 9, 'name' => 'DELETE'],
        ];
    }

    public function getDescription()
    {
        return '虚拟演播厅场景组状态';
    }
}