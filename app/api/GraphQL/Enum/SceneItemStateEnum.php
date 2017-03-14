<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/13 0013
 * Time: 14:08
 */

namespace app\api\GraphQL\Vlss\Enum;


use Youshido\GraphQL\Type\Enum\AbstractEnumType;

class SceneItemStateEnum extends AbstractEnumType
{

    /**
     * @return array
     */
    public function getValues()
    {
        return [
            ['value' => 1, 'name' => 'SHOW'],
            ['value' => 2, 'name' => 'HIDE'],
            ['value' => 9, 'name' => 'DELETE'],
        ];
    }

    public function getDescription()
    {
        return '虚拟演播厅单个场景状态';
    }
}