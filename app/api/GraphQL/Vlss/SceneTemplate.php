<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/13 0013
 * Time: 13:50
 */

namespace app\api\GraphQL\Vlss;


use app\api\GraphQL\Vlss\Enum\SceneTemplateStateEnum;
use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\DateTimeType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class SceneTemplate  extends AbstractObjectType
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
            ->addField('vlss_id', [
                'type'              => new NonNullType(new IdType()),
                'description'       => '虚拟演播厅id',
            ])
            ->addField('template_name', [
                'type'              => new NonNullType(new StringType()),
                'description'       => '模板名称',
            ])
            ->addField('switch_config', [
                'type'              => new NonNullType(new StringType()),
                'description'       => '模版配置 格式为 json 字符串',
            ])
            ->addField('front_pic', [
                'type'              => new NonNullType(new StringType()),
                'description'       => '前景图片',
            ])
            ->addField('back_pic', [
                'type'              => new NonNullType(new StringType()),
                'description'       => '背景图片',
            ])
            ->addField('state', [
                'type'              => new SceneTemplateStateEnum(),
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