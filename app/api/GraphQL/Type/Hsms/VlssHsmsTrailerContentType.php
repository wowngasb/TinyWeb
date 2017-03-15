<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/15 0015
 * Time: 17:43
 */

namespace app\api\GraphQL\Type\Hsms;


use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;

class VlssHsmsTrailerContentType extends AbstractObjectType
{

    /**
     * @param ObjectTypeConfig $config
     */
    public function build($config)
    {
        $config
            ->addField('image', [
                'type'              => new StringType(),
                'description'       => '图片',
            ])
            ->addField('title1', [
                'type'              => new StringType(),
                'description'       => '上部文字',
            ])
            ->addField('title2', [
                'type'              => new StringType(),
                'description'       => '下部文字',
            ]);
    }
}