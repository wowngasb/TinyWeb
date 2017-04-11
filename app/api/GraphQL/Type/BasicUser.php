<?php
namespace app\api\GraphQL\Type;

use app\api\GraphQL\Types;
use GraphQL\Type\Definition\ObjectType;

use TinyWeb\Application;
use TinyWeb\Func;
use TinyWeb\OrmQuery\OrmConfig;
use TinyWeb\Traits\OrmTrait;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/12 0012
 * Time: 1:20
 */
class BasicUser extends ObjectType
{
    use OrmTrait;

    public function __construct()
    {
        $config = [
            'description' => '',
            'fields' => []
        ];
        $config['fields']['id'] = [
            'type' => Types::nonNull(Types::id()),
            'description' => '',
        ];
        $config['fields']['name'] = [
            'type' => Types::string(),
            'description' => '',
        ];
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