<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/12 0012
 * Time: 2:42
 */

namespace app\api\GraphQL;


use app\api\GraphQL\Type\BasicUser;
use app\Bootstrap;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use TinyWeb\Traits\LogTrait;

class QueryType extends ObjectType
{
    use LogTrait;

    public function __construct()
    {
        $config = [
            'fields' => [
                'user' => [
                    'type' => Types::BasicUser(),
                    'description' => 'Returns user by id',
                    'args' => [
                        'id' => Types::nonNull(Types::id())
                    ]
                ],
                'deprecatedField' => [
                    'type' => Types::string(),
                    'deprecationReason' => 'This field is deprecated!'
                ],
                'fieldWithException' => [
                    'type' => Types::string(),
                    'resolve' => function () {
                        throw new \Exception("Exception message thrown in field resolver");
                    }
                ],
                'hello' => Types::string()
            ],
            'resolveField' => function ($val, $args, $context, ResolveInfo $info) {
                return $this->{$info->fieldName}($val, $args, $context, $info);
            }
        ];
        parent::__construct($config);
    }

    public function user($rootValue, $args)
    {
        false && func_get_args();
        $user = BasicUser::getItem($args['id']);
        Bootstrap::_D($user, 'test');
        return $user;
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
