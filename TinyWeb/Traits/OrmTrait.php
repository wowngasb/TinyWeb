<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/14 0014
 * Time: 18:20
 */

namespace TinyWeb\Traits;


use TinyWeb\Application;
use TinyWeb\Base\BaseBootstrap;
use TinyWeb\Exception\OrmStartUpError;
use TinyWeb\Helper\DbHelper;

/**
 * Class BaseOrmModel
 * array $where  检索条件数组 格式为 dict 每个元素都表示一个检索条件  条件之间为 and 关系
 * ① [  `filed` => `value`, ]   例如 ['votes' => 100, ]
 *    key不为数值，value不是数组   表示 某个字段为某值的检索 对应 ->where('votes', 100)
 * ② [  `filed` => [``, ``], ]   例如 ['votes' => ['>', 100], ]
 *    key不为数值的元素 表示 某个字段为某值的检索 对应  ->where('votes', '>', 100)
 * ③ [ [``, ``], ]
 *    key为数值的元素 表示 使用某种检索
 * 例如 [   ['whereBetween', 'votes', [1, 100]],  ]   对应  ->whereBetween('votes', [1, 100])
 * 例如 [   ['whereIn', 'id', [1, 2, 3]],  ]   对应  ->whereIn('id', [1, 2, 3])
 * 例如 [   ['whereNull', 'updated_at'],  ]   对应  ->whereNull('updated_at')
 * @package TinyWeb\Traits
 */

trait OrmTrait
{

    use CacheTrait;

    private static $_db = null;
    private static $_cache_dict = [];

    ####################################
    ############ 获取配置 ##############
    ####################################

    /**
     * 使用这个特性的子类必须 实现这个方法 返回特定格式的数组 表示数据表的配置
     * @return array
     */
    protected static function getOrmConfig()
    {
        return [
            'table_name' => '',     //数据表名
            'primary_key' => '',   //数据表主键
            'max_select' => 5000,  //最多获取 5000 条记录 防止数据库拉取条目过多
            'db_name' => '',       //数据库名
            'cache_time' => 0,     //数据缓存时间
        ];
    }

    ####################################
    ############ 可重写方法 #############
    ####################################

    /**
     * 根据主键获取数据 自动使用缓存
     * @param $id
     * @param null $timeCache
     * @return array|null
     */
    public static function getDataById($id, $timeCache = null)
    {

        if (is_null($timeCache)) {
            $timeCache = static::getOrmConfig()['cache_time'];
        }
        $id = intval($id);
        if ($id <= 0) {
            return [];
        }
        if (isset(self::$_cache_dict[$id])) {
            return self::$_cache_dict[$id];
        }
        $db_name = static::getOrmConfig()['db_name'];
        $table_name = static::getOrmConfig()['table_name'];
        $data = static::_cacheDataByRedis("{$db_name}:{$table_name}", "id:{$id}", function () use ($id) {
            $tmp = static::getItem($id);
            return $tmp;
        }, function ($data) {
            return !empty($data);
        }, $timeCache, false, 'DbCache');

        if (!empty($data)) {
            self::$_cache_dict[$id] = $data;
        }
        return $data;
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
            static::getItem($id, $data);
        }
        return self::getDataById($id, 0);
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
            return self::getDataById($id, 0);
        } else {
            return [];
        }
    }

    /**
     * @param $val
     * @return mixed
     */
    protected static function _fixItem($val)
    {
        return $val;
    }

    ####################################
    ############ 辅助函数 ##############
    ####################################

    /**
     * @param $name
     * @param $arguments
     * @return string
     */
    public static function __callStatic($name, $arguments)
    {
        return static::getFiledById($name, $arguments[0], isset($arguments[1]) ? $arguments[1] : null);
    }

    /**
     * 根据主键获取某个字段的值
     * @param string $name
     * @param int $id
     * @param mixed $default
     * @return mixed
     */
    public static function getFiledById($name, $id, $default = null)
    {
        $tmp = self::getDataById($id);
        return isset($tmp[$name]) ? $tmp[$name] : $default;
    }

    /**
     * 获取一个数组的指定键值 未设置则使用 默认值
     * @param array $val
     * @param string $key
     * @param mixed $default 默认值 默认为 null
     * @return mixed
     */
    protected static function _v(array $val, $key, $default = null)
    {
        return isset($val[$key]) ? $val[$key] : $default;
    }

    /**
     * 根据魔术常量获取获取 类名 并转换为 小写字母加下划线格式 的 数据表名
     * @param string $str
     * @return string
     */
    protected static function _class2table($str)
    {
        $idx = strripos($str, '::');
        $str = $idx > 0 ? substr($str, 0, $idx) : $str;
        $idx = strripos($str, '\\');
        $str = $idx > 0 ? substr($str, $idx + 1) : $str;
        return strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', '_', $str));
    }

    /**
     * 根据魔术常量获取获取 函数名 并转换为 小写字母加下划线格式 的 字段名
     * @param string $str
     * @return string
     */
    protected static function _method2field($str)
    {
        $idx = strripos($str, '::');
        $str = $idx > 0 ? substr($str, $idx + 2) : $str;
        return strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', '_', $str));
    }

    /**
     * @return \Illuminate\Database\Connection
     * @throws OrmStartUpError
     */
    private static function _getDb()
    {
        if (!empty(self::$_db)) {
            return self::$_db;
        }
        $config = static::getOrmConfig();
        if (empty($config['table_name']) || empty($config['primary_key']) || empty($config['max_select']) || empty($config['db_name'])) {
            throw new OrmStartUpError('Orm:' . __CLASS__ . 'with error config');
        }
        self::$_db = DbHelper::initDb()->getConnection($config['db_name']);
        return self::$_db;
    }

    protected static function debugSql($time, $sql, $param, $tag = 'sql')
    {
        $tag = str_replace(__TRAIT__, 'SQL', $tag);
        BaseBootstrap::_D(['sql' => static::showQuery($sql, $param), 'use'=>round($time * 1000, 2) . 'ms'], $tag);
    }

    protected static function showQuery($query, $params)
    {
        $keys = [];
        $values = [];

        # build a regular expression for each parameter
        foreach ($params as $key => $value) {
            if (is_string($key)) {
                $keys[] = '/:' . $key . '/';
            } else {
                $keys[] = '/[?]/';
            }
            if (is_numeric($value)) {
                $values[] = intval($value);
            } else {
                $values[] = '"' . $value . '"';
            }
        }
        $query = preg_replace($keys, $values, $query, 1, $count);
        return $query;
    }

    ####################################
    ########### 条目操作函数 ############
    ####################################

    /**
     * @param array $where 检索条件数组 具体格式参见文档
     * @return \Illuminate\Database\Query\Builder
     */
    protected static function tableItem(array $where = [])
    {
        $table_name = static::getOrmConfig()['table_name'];
        $table = static::_getDb()->table($table_name);
        $query_list = [];
        foreach ($where as $key => $item) {
            if (is_integer($key) && is_array($item)) {
                $tag = $item[0];
                $query = array_slice($item, 1);
                $query_list[] = [$tag, $query];
            } else {
                $tag = 'where';
                if (is_array($item)) {
                    $query = [$key, self::_v($item, 0), self::_v($item, 1), self::_v($item, 2, 'and')];
                } else {
                    $query = [$key, '=', $item, 'and'];
                }
                $query_list[] = [$tag, $query];
            }
        }
        foreach ($query_list as $query_item) {
            list($tag, $query) = $query_item;
            if (Application::striCmp('where', $tag)) {
                list($column, $operator, $value, $boolean) = $query;
                $table->where($column, $operator, $value, $boolean);
            } else {
                call_user_func_array([$table, $tag], $query);
            }
        }
        return $table;
    }

    /**
     * 查询数据总量
     * @param array $where 检索条件数组 具体格式参见文档
     * @param array $columns 需要获取的列 格式为[`column_1`, ]  默认为所有
     * @return int  数据条目数
     */
    public static function countItem(array $where = [], array $columns = ['*'])
    {
        $start_time = microtime(true);
        $table = static::tableItem($where);
        $count = $table->count($columns);
        static::debugSql(microtime(true) - $start_time, $table->toSql(), $table->getBindings(), __METHOD__);
        return $count;
    }

    /**
     * 分页查询数据  不允许超过最大数量限制
     * @param int $start 起始位置 skip
     * @param int $limit 数量限制 take 上限为 $this->_max_select_item_counts
     * @param array $sort_option 排序依据 格式为 [`field` => `column`, `direction` => `asc|desc`]
     * @param array $where 检索条件数组 具体格式参见文档
     * @param array $columns 需要获取的列 格式为[`column_1`, ]  默认为所有
     * @return array 数据 list 格式为 [`item`, ]
     */
    public static function selectItem($start = 0, $limit = 0, array $sort_option = [], array $where = [], array $columns = ['*'])
    {
        $start_time = microtime(true);
        $max_select = static::getOrmConfig()['max_select'];
        $table = static::tableItem($where);
        $start = $start <= 0 ? 0 : $start;
        $limit = $limit > $max_select ? $max_select : $limit;
        if ($start > 0) {
            $table->skip($start);
        }
        if ($limit > 0) {
            $table->take($limit);
        } else {
            $table->take($max_select);
        }
        if (!empty($sort_option['field']) && !empty($sort_option['direction'])) {
            $table->orderBy($sort_option['field'], $sort_option['direction']);
        }
        $data = $table->get($columns);
        static::debugSql(microtime(true) - $start_time, $table->toSql(), $table->getBindings(), __METHOD__);

        $rst = [];
        foreach ($data as $key => $val) {
            $val = (array)$val;
            $rst[$key] = static::_fixItem($val);
        }
        return $rst;
    }

    /**
     * 获取以主键为key的dict   不允许超过最大数量限制
     * @param array $where 检索条件数组 具体格式参见文档
     * @param array $columns 需要获取的列 格式为[`column_1`, ]  默认为所有
     * @return array 数据 dict 格式为 [`item.primary_key` => `item`, ]
     */
    public static function dictItem(array $where = [], array $columns = ['*'])
    {
        $start_time = microtime(true);
        $max_select = static::getOrmConfig()['max_select'];
        $primary_key = static::getOrmConfig()['primary_key'];
        $table = static::tableItem($where);
        $table->take($max_select);
        $data = $table->get($columns);
        static::debugSql(microtime(true) - $start_time, $table->toSql(), $table->getBindings(), __METHOD__);

        $rst = [];
        foreach ($data as $key => $val) {
            $val = (array)$val;
            $id = $val[$primary_key];
            $rst[$id] = static::_fixItem($val);
        }
        return $rst;
    }

    /**
     * 根据某个字段的值 获取第一条记录
     * @param mixed $value 需匹配的字段的值
     * @param string $filed 字段名 默认为 null 表示使用主键
     * @param array $columns 需要获取的列 格式为[`column_1`, ]  默认为所有
     * @return array
     */
    public static function getItem($value, $filed = null, array $columns = ['*'])
    {
        $primary_key = static::getOrmConfig()['primary_key'];
        $filed = $filed ?: $primary_key;
        return static::firstItem([strtolower($filed) => $value], $columns);
    }

    /**
     * 根据查询条件 获取第一条记录
     * @param array $where 检索条件数组 具体格式参见文档
     * @param array $columns 需要获取的列 格式为[`column_1`, ]  默认为所有
     * @return array
     */
    public static function firstItem(array $where, array $columns = ['*'])
    {
        $start_time = microtime(true);
        $table = static::tableItem($where);
        $item = $table->first($columns);
        static::debugSql(microtime(true) - $start_time, $table->toSql(), $table->getBindings(), __METHOD__);
        return static::_fixItem((array)$item);
    }

    /**
     * 插入数据 返回插入的自增id
     * @param array $data 数据[`filed` => `value`, ]
     * @return int
     */
    public static function newItem(array $data)
    {
        $start_time = microtime(true);
        $primary_key = static::getOrmConfig()['primary_key'];
        unset($data[$primary_key]);
        $time_str = date('Y-m-d H:i:s');
        $default = [
            'create_time' => $time_str,
        ];
        $data = array_merge($default, $data);
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = json_encode($value);
            }
        }
        $table = static::tableItem();
        $id = $table->insertGetId($data, $primary_key);
        static::debugSql(microtime(true) - $start_time, $table->toSql(), $table->getBindings(), __METHOD__);
        return $id;
    }

    /**
     * 根据主键修改数据
     * @param int $id 主键值
     * @param array $data 更新的数据 格式为 [`filed` => `value`, ]
     * @return int 操作影响的行数
     */
    public static function setItem($id, array $data)
    {
        $start_time = microtime(true);
        $primary_key = static::getOrmConfig()['primary_key'];
        unset($data['create_time'], $data['uptime'], $data[$primary_key]);
        $table = static::tableItem()->where($primary_key, $id);
        $update = $table->update($data);
        static::debugSql(microtime(true) - $start_time, $table->toSql(), $table->getBindings(), __METHOD__);
        return $update;
    }

    /**
     * 根据主键删除数据
     * @param int $id 主键值
     * @return int 操作影响的行数
     */
    public static function delItem($id)
    {
        $start_time = microtime(true);
        $primary_key = static::getOrmConfig()['primary_key'];
        $table = static::tableItem()->where($primary_key, $id);
        $delete = $table->delete();
        static::debugSql(microtime(true) - $start_time, $table->toSql(), $table->getBindings(), __METHOD__);
        return $delete;
    }

    /**
     * 根据主键增加某字段的值
     * @param int $id 主键id
     * @param string $filed 需要增加的字段
     * @param int $value 需要改变的值 默认为 1
     * @return int  操作影响的行数
     */
    public function incItem($id, $filed, $value = 1)
    {
        $start_time = microtime(true);
        $primary_key = static::getOrmConfig()['primary_key'];
        $table = static::tableItem()->where($primary_key, $id);
        $increment = $table->increment($filed, $value);
        static::debugSql(microtime(true) - $start_time, $table->toSql(), $table->getBindings(), __METHOD__);
        return $increment;
    }

    /**
     * 根据主键减少某字段的值
     * @param int $id 主键id
     * @param string $filed 需要减少的字段
     * @param int $value 需要改变的值 默认为 1
     * @return int  操作影响的行数
     */
    public function decItem($id, $filed, $value = 1)
    {
        $start_time = microtime(true);
        $primary_key = static::getOrmConfig()['primary_key'];
        $table = static::tableItem()->where($primary_key, $id);
        $decrement = $table->decrement($filed, $value);
        static::debugSql(microtime(true) - $start_time, $table->toSql(), $table->getBindings(), __METHOD__);
        return $decrement;
    }

    /**
     * 更新或插入数据  优先根据条件查询数据 无法查询到数据时插入数据
     * @param array $where 检索条件数组 具体格式参见文档
     * @param array $data 需要插入的数据  格式为 [`filed` => `value`, ]
     * @return int 返回数据 主键 自增id
     */
    public static function upsertItem(array $where, array $data)
    {
        $primary_key = static::getOrmConfig()['primary_key'];
        $tmp = static::firstItem($where);
        if (empty($tmp)) {
            return static::newItem($data);
        } else {
            $id = $tmp[$primary_key];
            static::setItem($id, $data);
            return $id;
        }
    }
}