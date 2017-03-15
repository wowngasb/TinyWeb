<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/15 0015
 * Time: 17:47
 */

namespace app\api\GraphQL\Type\Hsms;


use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;

class VlssHsmsStyleType extends AbstractObjectType
{

    /**
     * @param ObjectTypeConfig $config
     */
    public function build($config)
    {
        $config
            ->addField('left', [
                'type'              => new StringType(),
                'description'       => 'left',
            ])
            ->addField('top', [
                'type'              => new StringType(),
                'description'       => 'top',
            ])
            ->addField('width', [
                'type'              => new StringType(),
                'description'       => 'width',
            ])
            ->addField('height', [
                'type'              => new StringType(),
                'description'       => 'height',
            ])
            ->addField('opacity', [
                'type'              => new StringType(),
                'description'       => 'opacity',
            ]);
    }
}