<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/13 0013
 * Time: 13:33
 */

namespace app\api\GraphQL\Vlss;


use app\api\GraphQL\Basic\BasicUser;
use app\api\GraphQL\Vlss\Enum\AppStateEnum;
use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\DateTimeType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class VlssApp extends AbstractObjectType
{

    protected static $_orm = null;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        if(empty(static::$_orm)){
            static::$_orm = new \app\api\OrmDao\Vlss\App();
        }
    }

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
                'type'              => new NonNullType(new BasicUser()),
                'description'       => '用户',
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
            ->addField('active_template_id', [
                'type'              => new IdType(),
                'description'       => '激活的场景模版id',
            ])
            ->addField('state', [
                'type'              => new AppStateEnum(),
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

}