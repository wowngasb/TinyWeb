<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/15 0015
 * Time: 17:38
 */

namespace app\api\GraphQL\Enum;


use Youshido\GraphQL\Type\Enum\AbstractEnumType;

class VlssHsmsPositionEnum extends AbstractEnumType
{
    /**
     * @return array
     */
    public function getValues()
    {
        return [
            ['value' => 1, 'name' => 'LEFT_DOWN'],
            ['value' => 2, 'name' => 'RIGHT_DOWN'],
            ['value' => 3, 'name' => 'LEFT_UP'],
            ['value' => 4, 'name' => 'RIGHT_UP'],
        ];
    }

    public function getDescription()
    {
        return '元素位置选项，左下、右下、左上、右上';
    }
}