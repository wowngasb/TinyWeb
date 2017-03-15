<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/15 0015
 * Time: 17:48
 */

namespace app\api\GraphQL\Type\Hsms;


use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;

class VlssHsmsSubtitleType extends AbstractObjectType
{

    /**
     * @param ObjectTypeConfig $config
     */
    public function build($config)
    {
        $config
            ->addField('backgound', [
                'type'              => new StringType(),
                'description'       => 'backgound',
            ])
            ->addField('fixedText', [
                'type'              => new StringType(),
                'description'       => '上部文字',
            ])
            ->addField('scrollText', [
                'type'              => new StringType(),
                'description'       => '滚动文字',
            ]);
    }
}