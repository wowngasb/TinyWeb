<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/15 0015
 * Time: 17:45
 */

namespace app\api\GraphQL\Type\Hsms;


use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;

class VlssHsmsLogoType extends AbstractObjectType
{

    /**
     * @param ObjectTypeConfig $config
     */
    public function build($config)
    {
        $config
            ->addField('src', [
                'type'              => new StringType(),
                'description'       => '图片',
            ])
            ->addField('style', [
                'type'              => new VlssHsmsStyleType(),
                'description'       => 'style',
            ]);
    }
}