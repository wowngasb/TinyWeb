<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/13 0013
 * Time: 13:50
 */

namespace app\api\GraphQL;


use app\api\GraphQL\Enum\VlssSceneTemplateStateEnum;
use app\api\GraphQL\Type\VlssTemplateSwitchType;
use TinyWeb\Application;
use TinyWeb\Traits\OrmTrait;
use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\DateTimeType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\StringType;

class VlssSceneTemplate  extends AbstractObjectType
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
            ->addField('vlss_id', [
                'type'              => new NonNullType(new IdType()),
                'description'       => '虚拟演播厅id',
            ])
            ->addField('template_name', [
                'type'              => new NonNullType(new StringType()),
                'description'       => '模板名称',
            ])
            ->addField('switch_config', [
                'type'              => new ListType(new VlssTemplateSwitchType()),
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
                'type'              => new VlssSceneTemplateStateEnum(),
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
            'db_name' => Application::getInstance()->getEnv('ENV_MYSQL_DB'),       //数据库名
            'cache_time' => 300,     //数据缓存时间
        ];
    }

    protected static function _fixItem($val)
    {
        if(!empty($val)) {
            $val['switch_config'] = !empty($val['switch_config']) ? json_decode($val['switch_config'], true) : [];
        }
        return $val;
    }
}