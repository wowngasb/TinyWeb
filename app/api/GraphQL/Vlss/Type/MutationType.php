<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/8 0008
 * Time: 17:05
 */

namespace app\api\GraphQL\Vlss\Type;


use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class MutationType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Mutation',
            'fields' => [
            ],
            'resolveField' => function($val, $args, $context, ResolveInfo $info) {
                return $this->{$info->fieldName}($val, $args, $context, $info);
            }
        ];
        parent::__construct($config);
    }


}