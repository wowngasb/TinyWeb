<?php
/**
 * Created by table_graphQL.
 * User: Administrator
 * Date: 2017-04-12 01:16:27
 */
namespace app\api\GraphQL\Enum;

use GraphQL\Type\Definition\EnumType;

/**
 * Class VlssSceneGroupStateEnum
 * @package app\api\GraphQL\Enum
 */
class VlssSceneGroupStateEnum extends EnumType
{

    public function __construct()
    {
        $config = [
            'description' => 'VlssSceneGroup 数据表 state 字段 表示状态.',
            'values' => []
        ];
        $config['values']['NORMAL'] = ['value' => 1, 'description' => '正常'];
        $config['values']['DELETED'] = ['value' => 9, 'description' => '删除'];
        parent::__construct($config);
    }

}