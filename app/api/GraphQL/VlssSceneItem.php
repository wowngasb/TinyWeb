<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/13 0013
 * Time: 13:50
 */

namespace app\api\GraphQL;

use app\api\GraphQL\Enum\VlssSceneItemStateEnum;
use app\api\GraphQL\Enum\VlssSceneTypeEnum;

use TinyWeb\Application;
use TinyWeb\Traits\OrmTrait;
use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\DateTimeType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class VlssSceneItem extends AbstractObjectType
{
    use OrmTrait;

    /**
     * @param ObjectTypeConfig $config
     */
    public function build($config)
    {
        $config
            ->addField('id', [
                'type' => new NonNullType(new IdType()),
                'description' => '自增id',
            ])
            ->addField('group_id', [
                'type' => new NonNullType(new IdType()),
                'description' => '所属场景组id',
            ])
            ->addField('scene_name', [
                'type' => new NonNullType(new StringType()),
                'description' => '场景名称',
            ])
            ->addField('scene_config', [
                'type' => new StringType(),
                'description' => '场景配置 格式为 json 字符串',
                'resolve' => function ($value, array $args, ResolveInfo $info) {
                    false && func_get_args();
                    return json_encode($value['scene_config']);
                }
            ])
            ->addField('scene_type', [
                'type' => new VlssSceneTypeEnum(),
                'description' => '场景类型',
            ])
            ->addField('scene_sort', [
                'type' => new NonNullType(new IntType()),
                'description' => '场景叠加排序',
            ])
            ->addField('state', [
                'type' => new VlssSceneItemStateEnum(),
                'description' => '状态',
            ])
            ->addField('create_time', [
                'type' => new DateTimeType(),
                'description' => '记录创建时间',
            ])
            ->addField('uptime', [
                'type' => new DateTimeType(),
                'description' => '更新时间',
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
        if (!empty($val)) {
            $val['scene_config'] = !empty($val['scene_config']) ? json_decode($val['scene_config'], true) : [];
            $val['scene_config']['scene_type'] = $val['scene_type'];
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
        if (!empty($data)) {
            static::setItem($id, $data);
        }
        $tmp = static::getDataById($id, 0);
        static::dictDataWithGroupIdState($tmp['group_id'], [], -1);  //清除与本次数据更改相关的缓存
        return $tmp;
    }

    /**
     * 添加新数据 自动更新缓存
     * @param array $data
     * @return array
     */
    public static function newDataItem(array $data)
    {
        if (!empty($data)) {
            $id = static::newItem($data);
            $tmp = static::getDataById($id, 0);
            static::dictDataWithGroupIdState($tmp['group_id'], [], -1);  //清除与本次数据更改相关的缓存
            return $tmp;
        } else {
            return [];
        }
    }

    /**
     * 使用了缓存 需要自己重写 数据的更改 及 插入代码  插入更新相关缓存的代码
     * 此接口查询 指定 group_id 下 某些特定 state 的条目
     * 缓存key 为 "group_id[xxx]:state["1,2,3]"
     * 匹配的缓存清除 key 为 "group_id[xxx]:*"
     * @param $group_id
     * @param array $state
     * @param int $timeCache
     * @return array|null
     */
    public static function dictDataWithGroupIdState($group_id, array $state = [], $timeCache = null)
    {
        $cfg = static::getOrmConfig();
        $query = [
            'timeCache' => is_null($timeCache) ? $cfg['cache_time'] : $timeCache,
            'tag' => "group_id[{$group_id}]:state[" . join(',', $state) . "]",
            'free' => "group_id[{$group_id}]:*",
            'func' => function () use ($group_id, $state) {
                return VlssSceneItem::dictItem(['group_id' => $group_id, ['whereIn', 'state', $state],]);
            },
            'filter' => function ($data) {
                return isset($data);  //空的数组也会缓存
            }
        ];
        $dict = $query['timeCache'] < 0 ? static::freeQuery($query) : static::runQuery($query);

        $log_msg = "group_id:{$group_id},timeCache:{$query['timeCache']},query:" . json_encode($query) . ',rst:' . json_encode($dict);
        self::debug($log_msg, __METHOD__, __CLASS__, __LINE__);
        return $dict;
    }

}