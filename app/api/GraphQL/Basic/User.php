<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/13 0013
 * Time: 13:36
 */

namespace app\api\GraphQL\Basic;


use app\api\GraphQL\Basic\Enum\UserStateEnum;
use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\DateTimeType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class User extends AbstractObjectType
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
            ->addField('login_name', [
                'type'              => new NonNullType(new StringType()),
                'description'       => '用户管理后台登录名',
            ])
            ->addField('password', [
                'type'              => new NonNullType(new StringType()),
                'description'       => '用户管理后台登录密码',
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
                'type'              => new UserStateEnum(),
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
}