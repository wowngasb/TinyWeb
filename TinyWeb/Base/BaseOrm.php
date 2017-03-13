<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/17 0017
 * Time: 16:14
 */

namespace TinyWeb\Base;

use TinyWeb\Application;
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
 * @package app\common\Base
 */
abstract class BaseOrm
{
    use BaseModelTrait;

    protected static $_table_name = '';
    protected static $_primary_key = 'id';
    protected static $_max_select_item_counts = 10000;  //最多获取1w条记录 防止数据库拉取条目过多

    /* @var \Illuminate\Database\Connection */
    private $db = null;
    private static $m_instance = [];

    /**
     * 实现的可继承的单实例模式
     * @return BaseOrm
     */
    public static function instance()
    {
        $name = get_called_class();
        if (!isset(self::$m_instance[$name])) {
            self::$m_instance[$name] = new static();
        }
        return self::$m_instance[$name];
    }

    public function __construct()
    {
        if (empty(static::$_table_name) || empty(static::$_primary_key)) {
            throw new OrmStartUpError('Dao:' . class_basename($this) . ' init with empty tablename or primary_key');
        }
        $name = get_class($this);
        if (is_null($this->db)) {
            if (isset(self::$m_instance[$name])) {
                $this->db = self::$m_instance[$name]->db;
            } else {
                $this->db = DbHelper::initDb()->getConnection(Application::instance()->getEnv('ENV_MYSQL_DB'));
            }
        }
        self::$m_instance[$name] = $this;
    }

    protected static function debugSql($sql, $param, $tag = 'sql')
    {
        $tag = str_replace(__CLASS__, 'SQL', $tag);
        BaseBootstrap::_D(['sql' => static::showQuery($sql, $param)], $tag);
    }

    final protected static function showQuery($query, $params)
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

    /**
     * @param $val
     * @return mixed
     */
    protected static function _fixItem($val)
    {
        return $val;
    }

    private static function _v($val, $key, $default = null)
    {
        return isset($val[$key]) ? $val[$key] : $default;
    }

    /**
     * @param array $where 检索条件数组 具体格式参见文档
     * @return \Illuminate\Database\Query\Builder
     */
    protected static function tableItem(array $where = [])
    {
        $table = static::instance()->db->table(static::$_table_name);
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
        $table = static::tableItem($where);
        $count = $table->count($columns);
        static::debugSql($table->toSql(), $table->getBindings(), __METHOD__);
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
        $table = static::tableItem($where);
        $start = $start <= 0 ? 0 : $start;
        $limit = $limit > static::$_max_select_item_counts ? static::$_max_select_item_counts : $limit;
        if ($start > 0) {
            $table->skip($start);
        }
        if ($limit > 0) {
            $table->take($limit);
        } else {
            $table->take(static::$_max_select_item_counts);
        }
        if (!empty($sort_option['field']) && !empty($sort_option['direction'])) {
            $table->orderBy($sort_option['field'], $sort_option['direction']);
        }
        $data = $table->get($columns);
        static::debugSql($table->toSql(), $table->getBindings(), __METHOD__);

        $rst = [];
        foreach ($data as $key => $val) {
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
        $table = static::tableItem($where);
        $table->take(static::$_max_select_item_counts);
        $data = $table->get($columns);
        static::debugSql($table->toSql(), $table->getBindings(), __METHOD__);

        $rst = [];
        foreach ($data as $key => $val) {
            $id = $val[static::$_primary_key];
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
        $filed = $filed ?: static::$_primary_key;
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
        $table = static::tableItem($where);
        $item = $table->first($columns);
        static::debugSql($table->toSql(), $table->getBindings(), __METHOD__);
        return static::_fixItem($item);
    }

    /**
     * 插入数据 返回插入的自增id
     * @param array $data 数据[`filed` => `value`, ]
     * @return int
     */
    public static function newItem(array $data)
    {
        unset($data[static::$_primary_key]);
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
        $id = $table->insertGetId($data, static::$_primary_key);
        static::debugSql($table->toSql(), $table->getBindings(), __METHOD__);
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
        unset($data['create_time'], $data['uptime'], $data[static::$_primary_key]);
        $table = static::tableItem()->where(static::$_primary_key, $id);
        $update = $table->update($data);
        static::debugSql($table->toSql(), $table->getBindings(), __METHOD__);
        return $update;
    }

    /**
     * 根据主键删除数据
     * @param int $id 主键值
     * @return int 操作影响的行数
     */
    public static function delItem($id)
    {
        $table = static::tableItem()->where(static::$_primary_key, $id);
        $delete = $table->delete();
        static::debugSql($table->toSql(), $table->getBindings(), __METHOD__);
        return $delete;
    }

    /**
     * 更新或插入数据  优先根据条件查询数据 无法查询到数据时插入数据
     * @param array $where 检索条件数组 具体格式参见文档
     * @param array $data 需要插入的数据  格式为 [`filed` => `value`, ]
     * @return int 返回数据 主键 自增id
     */
    public static function upsertItem(array $where, array $data)
    {
        $tmp = static::firstItem($where);
        if (empty($tmp)) {
            return static::newItem($data);
        } else {
            $id = $tmp[static::$_primary_key];
            static::setItem($id, $data);
            return $id;
        }
    }

    /**
     * 获取分页获取数据最大条目数
     * @return int
     */
    public static function getMaxSelectItemCounts()
    {
        return static::$_max_select_item_counts;
    }
}