<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/8 0008
 * Time: 16:31
 */

namespace app\api\GraphQL\Vlss\Type\Enum;


use GraphQL\Type\Definition\EnumType;

class SceneGroupStateEnum extends EnumType
{
    public function __construct()
    {
        $config = [
            // Note: 'name' option is not needed in this form - it will be inferred from className
            'values' => [
                '未定义' => 0,
                '正常' => 1,
                '删除' => 9
            ]
        ];

        parent::__construct($config);
    }
}