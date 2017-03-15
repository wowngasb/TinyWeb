<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/15 0015
 * Time: 17:36
 */

namespace app\api\GraphQL\Type\Hsms;


use app\api\GraphQL\Enum\VlssHsmsPositionEnum;
use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;

class VlssHsmsTrailerType extends AbstractObjectType
{

    /**
     * @param ObjectTypeConfig $config
     */
    public function build($config)
    {
        $config
            ->addField('position', [
                'type'              => new VlssHsmsPositionEnum(),
                'description'       => '位置',
            ])
            ->addField('interval', [
                'type'              => new IntType(),
                'description'       => '滚动间隔',
            ])
            ->addField('contents', [
                'type'              => new ListType(new VlssHsmsTrailerContentType()),
                'description'       => '预告内容',
            ]);
    }
}