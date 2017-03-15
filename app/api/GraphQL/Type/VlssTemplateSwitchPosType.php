<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/15 0015
 * Time: 17:22
 */

namespace app\api\GraphQL\Type;


use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\IntType;

class VlssTemplateSwitchPosType extends AbstractObjectType
{

    /**
     * @param ObjectTypeConfig $config
     */
    public function build($config)
    {
        $config
            ->addField('w', [
                'type'              => new IntType(),
                'description'       => '宽',
            ])
            ->addField('h', [
                'type'              => new IntType(),
                'description'       => '高',
            ])
            ->addField('x', [
                'type'              => new IntType(),
                'description'       => 'x坐标',
            ])
            ->addField('y', [
                'type'              => new IntType(),
                'description'       => 'y坐标',
            ])
            ->addField('checked', [
                'type'              => new BooleanType(),
                'description'       => '是否选择',
            ])
            ->addField('v', [
                'type'              => new IntType(),
                'description'       => '音量',
            ])
            ->addField('z', [
                'type'              => new IntType(),
                'description'       => '叠加层次',
            ]);
    }
}