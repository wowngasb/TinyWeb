<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/13 0013
 * Time: 13:50
 */

namespace app\api\GraphQL\Vlss;


use app\api\GraphQL\Vlss\Enum\SceneItemStateEnum;
use app\api\GraphQL\Vlss\Enum\SceneTypeEnum;
use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\DateTimeType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class SceneItem  extends AbstractObjectType
{

    /**
     * @param ObjectTypeConfig $config
     */
    public function build($config)
    {
        $config
            ->addField('id', [
                'type'              => new NonNullType(new IdType()),
                'description'       => '自增id',
            ])
            ->addField('group_id', [
                'type'              => new NonNullType(new IdType()),
                'description'       => '所属场景组id',
            ])
            ->addField('scene_name', [
                'type'              => new NonNullType(new StringType()),
                'description'       => '场景名称',
            ])
            ->addField('scene_config', [
                'type'              => new NonNullType(new StringType()),
                'description'       => '场景配置 格式为 json 字符串',
            ])
            ->addField('scene_type', [
                'type'              => new SceneTypeEnum(),
                'description'       => '场景类型',
            ])
            ->addField('scene_sort', [
                'type'              => new NonNullType(new IntType()),
                'description'       => '场景叠加排序',
            ])
            ->addField('state', [
                'type'              => new SceneItemStateEnum(),
                'description'       => '状态',
            ])
            ->addField('create_time', [
                'type'              => new DateTimeType(),
                'description'       => '记录创建时间',
            ])
            ->addField('uptime', [
                'type'              => new DateTimeType(),
                'description'       => '更新时间',
            ]);
    }
}