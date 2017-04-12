<?php
/**
 * Created by table_graphQL.
 * User: Administrator
 * Date: 2017-04-12 03:50:22
 */
namespace app\api\GraphQL\Type;

use app\api\GraphQL\Types;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

use TinyWeb\Application;
use TinyWeb\Func;
use TinyWeb\OrmQuery\OrmConfig;
use TinyWeb\Traits\OrmTrait;


/**
 * Class VlssSceneGroup
 * @package app\api\GraphQL\Type
 */
class VlssSceneGroup extends ObjectType
{
    use OrmTrait;

    public function __construct()
    {
        $config = [
            'description' => 'vlss_scene_group 数据表 演播厅场景组表.',
            'fields' => []
        ];
        $config['fields']['id'] = [
            'type' => Types::nonNull(Types::id()),
            'description' => 'vlss_scene_group 数据表 id 字段 自增主键',
        ];
        $config['fields']['title'] = [
            'type' => Types::string(),
            'description' => 'vlss_scene_group 数据表 title 字段 场景组标题',
        ];
        $config['fields']['app_id'] = [
            'type' => Types::string(),
            'description' => 'vlss_scene_group 数据表 app_id 字段 演播厅id',
        ];
        $config['fields']['state'] = [
            'type' => Types::int(),
            'description' => 'vlss_scene_group 数据表 state 字段 1@NORMAL#正常;9@DELETED#删除',
        ];
        $config['fields']['created_at'] = [
            'type' => Types::string(),
            'description' => 'vlss_scene_group 数据表 created_at 字段 记录创建时间',
        ];
        $config['fields']['updated_at'] = [
            'type' => Types::string(),
            'description' => 'vlss_scene_group 数据表 updated_at 字段 记录更新时间',
        ];
        $config['resolveField'] = function($value, $args, $context, ResolveInfo $info) {
            if (method_exists($this, $info->fieldName)) {
                return $this->{$info->fieldName}($value, $args, $context, $info);
            } else {
                return is_array($value) ? $value[$info->fieldName] : $value->{$info->fieldName};
            }
        };
        parent::__construct($config);
    }

    /**
     * 使用这个特性的子类必须 实现这个方法 返回特定格式的数组 表示数据表的配置
     * @return OrmConfig
     */
    protected static function getOrmConfig()
    {
        if (is_null(static::$_orm_config)) {
            static::$_orm_config = new OrmConfig(Application::getInstance()->getEnv('ENV_MYSQL_DB'), Func::class2table(__METHOD__), 'id', 300, 5000);
        }
        return static::$_orm_config;
    }
}