<?php
/**
 * Created by table_graphQL.
 * User: Administrator
 * Date: 2017-04-12 02:46:22
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
 * Class VlssApp
 * @package app\api\GraphQL\Type
 */
class VlssApp extends ObjectType
{
    use OrmTrait;

    public function __construct()
    {
        $config = [
            'description' => 'vlss_app 数据表 演播厅基本信息表.',
            'fields' => []
        ];
        $config['fields']['id'] = [
            'type' => Types::nonNull(Types::id()),
            'description' => 'vlss_app 数据表 id 字段 自增主键',
        ];
        $config['fields']['active_template_id'] = [
            'type' => Types::nonNull(Types::string()),
            'description' => 'vlss_app 数据表 active_template_id 字段 激活的场景模版id',
        ];
        $config['fields']['user_id'] = [
            'type' => Types::string(),
            'description' => 'vlss_app 数据表 user_id 字段 用户id',
        ];
        $config['fields']['active_group_id'] = [
            'type' => Types::nonNull(Types::string()),
            'description' => 'vlss_app 数据表 active_group_id 字段 激活的场景组id',
        ];
        $config['fields']['lcps_host'] = [
            'type' => Types::string(),
            'description' => 'vlss_app 数据表 lcps_host 字段 导播台域名  不带http://前缀 和 结尾/',
        ];
        $config['fields']['title'] = [
            'type' => Types::string(),
            'description' => 'vlss_app 数据表 title 字段 演播厅标题',
        ];
        $config['fields']['state'] = [
            'type' => Types::int(),
            'description' => 'vlss_app 数据表 state 字段 1@NORMAL#正常;2@FROZEN#冻结;9@DELETED#删除',
        ];
        $config['fields']['created_at'] = [
            'type' => Types::string(),
            'description' => 'vlss_app 数据表 created_at 字段 记录创建时间',
        ];
        $config['fields']['updated_at'] = [
            'type' => Types::string(),
            'description' => 'vlss_app 数据表 updated_at 字段 记录更新时间',
        ];
        $config['resolveField'] = function($value, $args, $context, ResolveInfo $info) {
            if (method_exists($this, $info->fieldName)) {
                return $this->{$info->fieldName}($value, $args, $context, $info);
            } else {
                return $value->{$info->fieldName};
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