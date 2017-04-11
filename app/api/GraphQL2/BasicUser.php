<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/13 0013
 * Time: 13:36
 */

namespace app\api\GraphQL;


use app\api\GraphQL\Enum\BasicUserStateEnum;
use TinyWeb\Application;
use TinyWeb\Exception\OrmParamsError;
use TinyWeb\Func;
use TinyWeb\Traits\OrmTrait;
use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\DateTimeType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class BasicUser extends AbstractObjectType
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
            ->addField('login_name', [
                'type'              => new NonNullType(new StringType()),
                'description'       => '用户管理后台登录名',
            ])
            ->addField('email', [
                'type'              => new NonNullType(new StringType()),
                'description'       => '用户邮箱',
            ])
            ->addField('telephone', [
                'type'              => new NonNullType(new StringType()),
                'description'       => '用户手机号',
            ])
            ->addField('access_id', [
                'type'              => new NonNullType(new StringType()),
                'description'       => '奥点云access_id',
            ])
            ->addField('access_key', [
                'type'              => new NonNullType(new StringType()),
                'description'       => '奥点云access_key',
            ])
            ->addField('aodian_uin', [
                'type'              => new NonNullType(new IdType()),
                'description'       => '奥点云 uin',
            ])
            ->addField('dms_sub_key', [
                'type'              => new NonNullType(new StringType()),
                'description'       => 'DMS sub_key',
            ])
            ->addField('dms_pub_key', [
                'type'              => new NonNullType(new StringType()),
                'description'       => 'DMS pub_key',
            ])
            ->addField('dms_s_key', [
                'type'              => new NonNullType(new StringType()),
                'description'       => 'DMS s_key',
            ])
            ->addField('state', [
                'type'              => new BasicUserStateEnum(),
                'description'       => '状态',
            ])
            ->addField('dms_s_key', [
                'type'              => new NonNullType(new StringType()),
                'description'       => 'DMS s_key',
            ])
            ->addField('last_login_ip', [
                'type'              => new NonNullType(new StringType()),
                'description'       => '用户上次登录ip',
            ])
            ->addField('login_count', [
                'type'              => new NonNullType(new IntType()),
                'description'       => '用户管理后台登录次数 登陆一次+1',
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

    private static function _hashPassWord($password)
    {    //使用自定义的 hash 算法 防止彩虹表破解
        $secret_key = Application::getInstance()->getEnv('CRYPT_KEY', '');
        $tmp = strtolower(md5($password));
        $tmp = strtolower(md5($secret_key . $tmp));
        return strtolower(md5($tmp . $secret_key));
    }

    /**
     * @param int $id
     * @param string $password
     * @return bool
     */
    public static function testPassWord($id, $password)
    {
        $id = intval($id);
        if (empty($password) || $id <= 0) {
            return false;
        }
        $user = self::getItem($id);
        $tmp = self::_hashPassWord($password);
        return Func::str_cmp($tmp, $user['password']);
    }

    protected static function _fixItem($val)
    {
        if (!empty($val)) {
            unset($val['password']);
        }
        return $val;
    }

    /**
     * 根据主键更新数据 自动更新缓存
     * @param $id
     * @param array $data
     * @return array 返回更新后的数据
     */
    public static function setDataById($id, array $data)
    {
        $id = intval($id);
        if ($id <= 0) {
            return [];
        }
        if (!empty($data['password'])) {
            $data['password'] = self::_hashPassWord($data['password']);
        } else {
            unset($data['password']);
        }

        if (!empty($data)) {
            static::getItem($id, $data);
        }
        return self::getDataById($id, 0);
    }

    /**
     * 添加新数据 自动更新缓存
     * @param array $data
     * @return array
     * @throws OrmParamsError
     */
    public static function newDataItem(array $data)
    {
        if (!empty($data)) {
            if (empty($data['password'])) {
                throw new OrmParamsError('cannot create user without password');
            }
            $data['password'] = self::_hashPassWord($data['password']);
            $id = static::newItem($data);
            return self::getDataById($id, 0);
        } else {
            return [];
        }
    }

    ################################
    ########### 常用字段 ###########
    ################################

    public static function login_name($id)
    {
        return static::getFiledById(__FUNCTION__, $id);
    }

    public static function email($id)
    {
        return static::getFiledById(__FUNCTION__, $id);
    }

    public static function telephone($id)
    {
        return static::getFiledById(__FUNCTION__, $id);
    }

    public static function access_id($id)
    {
        return static::getFiledById(__FUNCTION__, $id);
    }

    public static function access_key($id)
    {
        return static::getFiledById(__FUNCTION__, $id);
    }

    public static function aodian_uin($id)
    {
        return static::getFiledById(__FUNCTION__, $id);
    }

    public static function dms_sub_key($id)
    {
        return static::getFiledById(__FUNCTION__, $id);
    }

    public static function dms_pub_key($id)
    {
        return self::getFiledById(__FUNCTION__, $id);
    }

    public static function dms_s_key($id)
    {
        return self::getFiledById(__FUNCTION__, $id);
    }

    public static function state($id)
    {
        return self::getFiledById(__FUNCTION__, $id);
    }
}