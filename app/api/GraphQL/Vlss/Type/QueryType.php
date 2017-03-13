<?php
namespace app\api\GraphQL\Vlss\Type;

use app\api\GraphQL\Vlss\Data\App;
use app\api\GraphQL\Vlss\Types;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

class QueryType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Query',
            'fields' => [
                'app' => [
                    'type' => Types::app(),
                    'description' => '根据演播厅id获取虚拟演播厅实例信息，需要该实例所有者权限',
                    'args' => [
                        'vlss_id' => Types::nonNull(Types::int())
                    ]
                ],
                'apps' => [
                    'type' => Types::listOf(Types::app()),
                    'description' => '分页检索所有虚拟演播厅实例信息，需要管理员权限',
                    'args' => [
                        'page' => [
                            'type' => Types::int(),
                            'description' => '页数从1开始，默认为1',
                            'defaultValue' => 1,
                        ],
                        'limit' => [
                            'type' => Types::int(),
                            'description' => '每页数量，不可超过:' . App::getMaxSelectItemCounts() . ',默认为10',
                            'defaultValue' => 10,
                        ],
                        'state' => [
                            'type' => Types::appStateEnum(),
                            'description' => '实例状态，检索条件，可选',
                            'defaultValue' => null,
                        ],
                        'login_name' => [
                            'type' => Types::string(),
                            'description' => '用户登录名，检索条件，可选',
                            'defaultValue' => null,
                        ],
                    ]
                ],
                'deprecatedField' => [
                    'type' => Types::string(),
                    'deprecationReason' => 'This field is deprecated!'
                ],
                'fieldWithException' => [
                    'type' => Types::string(),
                    'resolve' => function() {
                        throw new \Exception("Exception message thrown in field resolver");
                    }
                ],
                'hello' => Type::string()
            ],
            'resolveField' => function($val, $args, $context, ResolveInfo $info) {
                return $this->{$info->fieldName}($val, $args, $context, $info);
            }
        ];
        parent::__construct($config);
    }


    public function hello()
    {
        return 'Your graphql-php endpoint is ready! Use GraphiQL to browse API';
    }

    public function deprecatedField()
    {
        return 'You can request deprecated field, but it is not displayed in auto-generated documentation by default.';
    }
}
