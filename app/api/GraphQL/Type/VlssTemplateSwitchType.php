<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/15 0015
 * Time: 16:24
 */

namespace app\api\GraphQL\Type;


use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\StringType;

class VlssTemplateSwitchType extends AbstractObjectType
{

    /**
     * @param ObjectTypeConfig $config
     */
    public function build($config)
    {
        $config
            ->addField('name', [
                'type'              => new StringType(),
                'description'       => '切换按钮名称',
            ])
            ->addField('enable', [
                'type'              => new BooleanType(),
                'description'       => '当前按钮是否启用',
            ])
            ->addField('param', [
                'type'              => new ListType(new VlssTemplateSwitchPosType()),
                'description'       => '具体切换参数',
            ]);
    }
}