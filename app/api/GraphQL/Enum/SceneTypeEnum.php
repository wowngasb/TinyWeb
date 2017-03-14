<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/13 0013
 * Time: 14:00
 */

namespace app\api\GraphQL\Vlss\Enum;


use Youshido\GraphQL\Type\Enum\AbstractEnumType;

class SceneTypeEnum extends AbstractEnumType
{

    /**
     * @return array
     */
    public function getValues()
    {
        return [
            ['value' => 'hsms-trailer', 'name' => 'hsms-trailer'],
            ['value' => 'hsms-logo', 'name' => 'hsms-logo'],
            ['value' => 'hsms-subtitle', 'name' => 'hsms-subtitle'],
            ['value' => 'hsms-tvlogo', 'name' => 'hsms-tvlogo'],
            ['value' => 'hsms-scoreboard', 'name' => 'hsms-scoreboard'],
        ];
    }

    public function getDescription()
    {
        return '虚拟演播厅场景元素类型';
    }
}