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
 * Class RbacPermission
 * @package app\api\GraphQL\Type
 */
class RbacPermission extends ObjectType
{
    use OrmTrait;

    public function __construct()
    {
        $config = [
            'description' => 'rbac_permission 数据表 RBAC权限表.',
            'fields' => []
        ];
        $config['fields']['id'] = [
            'type' => Types::nonNull(Types::id()),
            'description' => 'rbac_permission 数据表 id 字段 自增主键',
        ];
        $config['fields']['description'] = [
            'type' => Types::string(),
            'description' => 'rbac_permission 数据表 description 字段 权限描述',
        ];
        $config['fields']['title'] = [
            'type' => Types::string(),
            'description' => 'rbac_permission 数据表 title 字段 权限标题',
        ];
        $config['fields']['p_key'] = [
            'type' => Types::string(),
            'description' => 'rbac_permission 数据表 p_key 字段 权限标识 用于区分权限',
        ];
        $config['fields']['p_type'] = [
            'type' => Types::string(),
            'description' => 'rbac_permission 数据表 p_type 字段 权限类型 "MENU"表示菜单的访问权限、"OPERATION"表示功能模块的操作权限、"FILE"表示文件的权限、"ELEMENT"表示页面元素的可见性',
        ];
        $config['fields']['created_at'] = [
            'type' => Types::string(),
            'description' => 'rbac_permission 数据表 created_at 字段 记录创建时间',
        ];
        $config['fields']['updated_at'] = [
            'type' => Types::string(),
            'description' => 'rbac_permission 数据表 updated_at 字段 记录更新时间',
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