<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/13 0013
 * Time: 13:33
 */

namespace app\api\GraphQL;


use app\api\GraphQL\Enum\VlssAppStateEnum;
use app\api\GraphQL\Enum\VlssSceneGroupStateEnum;
use app\api\GraphQL\Enum\VlssSceneTemplateStateEnum;
use TinyWeb\Application;
use TinyWeb\Traits\OrmTrait;
use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\DateTimeType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class VlssApp extends AbstractObjectType
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
            ->addField('user_id', [
                'type'              => new NonNullType(new IntType()),
                'description'       => '用户id',
            ])
            ->addField('user', [
                'type'              => new BasicUser(),
                'description'       => '用户',
                'resolve' => function ($value, array $args, ResolveInfo $info) {
                    return BasicUser::getDataById($value['user_id']);
                }
            ])
            ->addField('lcps_host', [
                'type'              => new NonNullType(new StringType()),
                'description'       => '导播台域名  不带http://前缀 和 结尾/',
            ])
            ->addField('vlss_name', [
                'type'              => new NonNullType(new StringType()),
                'description'       => '演播厅名字',
            ])
            ->addField('active_group_id', [
                'type'              => new IdType(),
                'description'       => '激活的场景组id',
            ])
            ->addField('groups', [
                'type'              => new ListType(new VlssSceneGroup()),
                'description'       => '场景组列表',
                'args'    => [
                    'state'   => new ListType(new VlssSceneGroupStateEnum()),
                ],
                'resolve' => function ($value, array $args, ResolveInfo $info) {
                    return array_values(VlssSceneGroup::dictItem(['app_id'=>$value['id'], ['whereIn', 'state', $args['state']], ]));
                }
            ])
            ->addField('active_template_id', [
                'type'              => new IdType(),
                'description'       => '激活的场景模版id',
            ])
            ->addField('templates', [
                'type'              => new ListType(new VlssSceneTemplate()),
                'description'       => '场景模版列表',
                'args'    => [
                    'state'   => new ListType(new VlssSceneTemplateStateEnum()),
                ],
                'resolve' => function ($value, array $args, ResolveInfo $info) {
                    return array_values(VlssSceneTemplate::dictItem(['app_id'=>$value['id'], ['whereIn', 'state', $args['state']], ]));
                }
            ])
            ->addField('state', [
                'type'              => new VlssAppStateEnum(),
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

}