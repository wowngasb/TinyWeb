<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/13 0013
 * Time: 13:19
 */

namespace app\api\GraphQL\Schema;


use app\api\GraphQL\Basic\BasicUser as BasicUser;
use app\api\GraphQL\Vlss\VlssApp as VlssApp;
use app\api\OrmDao\Vlss\VlssApp as VlssAppDao;
use Youshido\GraphQL\Config\Schema\SchemaConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\StringType;

class Schema extends AbstractSchema
{

    public function build(SchemaConfig $config)
    {
        $config->getQuery()->addFields([
            'hello' => [
                'type' => new StringType(),
                'description' => 'demo of hello, world!',
                'args' => [
                    'name' => [
                        'type' => new StringType(),
                        'description' => 'enter your name take the place of world',
                    ],
                ],
                'resolve' => function ($value, array $args, ResolveInfo $info) {
                    $args['name'] = isset($args['name']) ? $args['name'] : 'world';
                    return "hello,{$args['name']}!";
                }
            ],
            'basicUser'           => [
                'type'    => new BasicUser(),
                'description'       => '用户基本信息',
                'args'    => [
                    'id'   => new IdType(),
                ],
                'resolve' => function ($value, array $args, ResolveInfo $info) {
                    return UserConf::getDataById($args['id']);
                }
            ],
            'vlssApp'           => [
                'type'    => new VlssApp(),
                'description'       => '虚拟演播厅信息',
                'resolve' => function ($value, array $args, ResolveInfo $info) {
                    return VlssAppDao::getItem($args['id']);
                }
            ],
        ]);
        $config->getMutation()->addFields([
            'addVlssApp' => [
                'type'    => new VlssApp(),
                'args'    => [
                    'vlss_name'   => new StringType(),
                    'lcps_host' => new StringType(),
                ],
                'resolve' => function ($value, array $args, ResolveInfo $info) {
                    $post = [];
                    return $post;
                }
            ]
        ]);
    }
}