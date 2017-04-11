<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/13 0013
 * Time: 14:00
 */

namespace app\api\GraphQL\Enum;


use Youshido\GraphQL\Type\Enum\AbstractEnumType;

class VlssSceneTypeEnum extends AbstractEnumType
{

    /**
     * @return array
     */
    public function getValues()
    {
        return [
            ['value' => 'hsms-trailer', 'name' => 'HSMS_TRAILER'],
            ['value' => 'hsms-logo', 'name' => 'HSMS_LOGO'],
            ['value' => 'hsms-subtitle', 'name' => 'HSMS_SUBTITLE'],
            ['value' => 'hsms-tvlogo', 'name' => 'HSMS_TVLOGO'],
            ['value' => 'hsms-scoreboard', 'name' => 'HSMS_SCOREBOARD'],
        ];
    }

    public function getDescription()
    {
        return '虚拟演播厅场景元素类型';
    }
}