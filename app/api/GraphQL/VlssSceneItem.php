<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/13 0013
 * Time: 13:50
 */

namespace app\api\GraphQL;

use app\api\GraphQL\Enum\VlssSceneItemStateEnum;
use app\api\GraphQL\Enum\VlssSceneTypeEnum;
use app\api\GraphQL\Union\Hsms\VlssHsmsSceneConfigUnion;
use TinyWeb\Application;
use TinyWeb\Traits\OrmTrait;
use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\DateTimeType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class VlssSceneItem  extends AbstractObjectType
{
    use OrmTrait;
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
                'type'              => new VlssHsmsSceneConfigUnion(),
                'description'       => '场景配置 格式为 json 字符串',
            ])
            ->addField('scene_type', [
                'type'              => new VlssSceneTypeEnum(),
                'description'       => '场景类型',
            ])
            ->addField('scene_sort', [
                'type'              => new NonNullType(new IntType()),
                'description'       => '场景叠加排序',
            ])
            ->addField('state', [
                'type'              => new VlssSceneItemStateEnum(),
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

    protected static function getOrmConfig()
    {
        return [
            'table_name' => static::_class2table(__METHOD__),     //数据表名
            'primary_key' => 'id',   //数据表主键
            'max_select' => 5000,  //最多获取 5000 条记录 防止数据库拉取条目过多
            'db_name' => Application::instance()->getEnv('ENV_MYSQL_DB'),       //数据库名
            'cache_time' => 300,     //数据缓存时间
        ];
    }

    protected static function _fixItem($val)
    {
        if(!empty($val)){
            $val['scene_config'] = !empty($val['scene_config']) ? json_decode($val['scene_config'], true) : [];
            $val['scene_config']['scene_type'] = $val['scene_type'];
        }
        return $val;
    }
}