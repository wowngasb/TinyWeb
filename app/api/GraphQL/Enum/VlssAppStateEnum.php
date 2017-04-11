<?php
/**
 * Created by table_graphQL.
 * User: Administrator
 * Date: 2017-04-12 02:46:22
 */
namespace app\api\GraphQL\Enum;

use GraphQL\Type\Definition\EnumType;

/**
 * Class VlssAppStateEnum
 * @package app\api\GraphQL\Enum
 */
class VlssAppStateEnum extends EnumType
{

    public function __construct()
    {
        $config = [
            'description' => 'vlss_app 数据表 state 字段 表示状态.',
            'values' => []
        ];
        $config['values']['NORMAL'] = ['value' => 1, 'description' => '正常'];
        $config['values']['FROZEN'] = ['value' => 2, 'description' => '冻结'];
        $config['values']['DELETED'] = ['value' => 9, 'description' => '删除'];
        parent::__construct($config);
    }

}