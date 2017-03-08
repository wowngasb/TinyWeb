<?php
namespace app\api\GraphQL\Vlss\Type;


use GraphQL\Type\Definition\ObjectType;


class SceneItemType extends ObjectType
{

    public function __construct()
    {
        $config = [
        ];
        parent::__construct($config);
    }
}
