<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/15 0015
 * Time: 17:54
 */

namespace app\api\GraphQL\Type\Hsms;


use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;

class VlssHsmsSubtitleScrollTextType extends AbstractObjectType
{

    /**
     * @param ObjectTypeConfig $config
     */
    public function build($config)
    {
        $config
            ->addField('text', [
                'type'              => new StringType(),
                'description'       => 'text',
            ])
            ->addField('color', [
                'type'              => new StringType(),
                'description'       => 'color',
            ])
            ->addField('shadow', [
                'type'              => new StringType(),
                'description'       => 'shadow',
            ])
            ->addField('align', [
                'type'              => new StringType(),
                'description'       => 'align',
            ])
            ->addField('speed', [
                'type'              => new StringType(),
                'description'       => 'speed',
            ]);
    }
}