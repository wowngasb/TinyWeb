<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/15 0015
 * Time: 13:50
 */

namespace TinyWeb\Base;


use Youshido\GraphQL\Schema\AbstractSchema;

abstract class BaseGraphQLSchema extends AbstractSchema
{

    private $context = null;

    public function __construct(BaseContext $context, array $config = [])
    {
        parent::__construct($config);
        $this->context = $context;
    }
}