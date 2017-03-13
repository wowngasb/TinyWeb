<?php
namespace GraphQL\Examples\Blog\Type\Enum;

use GraphQL\Type\Definition\EnumType;

class SceneItemStateEnum extends EnumType
{
    public function __construct()
    {
        $config = [
            // Note: 'name' option is not needed in this form - it will be inferred from className
            'values' => [
                '未定义' => 0,
                '显示' => 1,
                '隐藏' => 2,
                '删除' => 9
            ]
        ];

        parent::__construct($config);
    }
}
