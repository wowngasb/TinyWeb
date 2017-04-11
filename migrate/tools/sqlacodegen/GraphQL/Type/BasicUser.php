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
 * Class BasicUser
 * @package app\api\GraphQL\Type
 */
class BasicUser extends ObjectType
{
    use OrmTrait;

    public function __construct()
    {
        $config = [
            'description' => 'basic_user 数据表 用户基本信息表.',
            'fields' => []
        ];
        $config['fields']['id'] = [
            'type' => Types::nonNull(Types::id()),
            'description' => 'basic_user 数据表 id 字段 虚拟演播厅自增id',
        ];
        $config['fields']['access_key'] = [
            'type' => Types::string(),
            'description' => 'basic_user 数据表 access_key 字段 奥点云 access_key',
        ];
        $config['fields']['aodian_uin'] = [
            'type' => Types::string(),
            'description' => 'basic_user 数据表 aodian_uin 字段 奥点云 uin',
        ];
        $config['fields']['telephone'] = [
            'type' => Types::string(),
            'description' => 'basic_user 数据表 telephone 字段 用户手机号',
        ];
        $config['fields']['dms_id'] = [
            'type' => Types::string(),
            'description' => 'basic_user 数据表 dms_id 字段 DMS id',
        ];
        $config['fields']['dms_pub_key'] = [
            'type' => Types::string(),
            'description' => 'basic_user 数据表 dms_pub_key 字段 DMS pub_key',
        ];
        $config['fields']['dms_s_key'] = [
            'type' => Types::string(),
            'description' => 'basic_user 数据表 dms_s_key 字段 DMS s_key',
        ];
        $config['fields']['dms_sub_key'] = [
            'type' => Types::string(),
            'description' => 'basic_user 数据表 dms_sub_key 字段 DMS sub_key',
        ];
        $config['fields']['email'] = [
            'type' => Types::string(),
            'description' => 'basic_user 数据表 email 字段 用户邮箱',
        ];
        $config['fields']['access_id'] = [
            'type' => Types::string(),
            'description' => 'basic_user 数据表 access_id 字段 奥点云 access_id',
        ];
        $config['fields']['login_name'] = [
            'type' => Types::string(),
            'description' => 'basic_user 数据表 login_name 字段 用户管理后台登录名',
        ];
        $config['fields']['lss_app'] = [
            'type' => Types::string(),
            'description' => 'basic_user 数据表 lss_app 字段 LSS app',
        ];
        $config['fields']['password'] = [
            'type' => Types::string(),
            'description' => 'basic_user 数据表 password 字段 用户管理后台登录密码',
        ];
        $config['fields']['state'] = [
            'type' => Types::int(),
            'description' => 'basic_user 数据表 state 字段 1@NORMAL#正常;2@FROZEN#冻结;9@DELETED#删除',
        ];
        $config['fields']['created_at'] = [
            'type' => Types::string(),
            'description' => 'basic_user 数据表 created_at 字段 记录创建时间',
        ];
        $config['fields']['updated_at'] = [
            'type' => Types::string(),
            'description' => 'basic_user 数据表 updated_at 字段 记录更新时间',
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