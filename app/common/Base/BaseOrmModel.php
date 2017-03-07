<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/17 0017
 * Time: 16:14
 */

namespace app\common\Base;


use app\Bootstrap;
use TinyWeb\Application;
use TinyWeb\Exception\AppStartUpError;
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
abstract class BaseOrmModel extends BaseModel
{

    protected $_tablename = '';
    protected $_primary_key = '';
    protected $_max_select_item_counts = 10000;  //最多获取1w条记录 防止数据库拉取条目过多

    private static $instance = null;

    /* @var \Illuminate\Database\Connection */
    private $db;

    public static function instance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    private function __construct()
    {
        if (empty($this->_tablename) || empty($this->_primary_key)) {
            throw new AppStartUpError('Dao:' . class_basename($this) . ' init with empty tablename or primary_key');
        }
        if (!$this->db) {
            $this->db = DbHelper::initDb()->getConnection(Application::instance()->getEnv('ENV_MYSQL_DB'));
        }
    }

    protected static function debugSql($sql, $param, $tag = 'sql')
    {
        $tag = str_replace(__CLASS__, 'SQL', $tag);
        Bootstrap::_D(['sql' => self::showQuery($sql, $param)], $tag);
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

    /**
     * @param $val
     * @return mixed
     */
    protected static function _fixItem($val)
    {
        return $val;
    }

    protected static function v($val, $key, $default = null)
    {
        return isset($val[$key]) ? $val[$key] : $default;
    }

    /**
     * @param array $where 检索条件数组 具体格式参见文档
     * @return \Illuminate\Database\Query\Builder
     */
    protected function _tableItem(array $where = [])
    {
        $table = $this->db->table($this->_tablename);
        $query_list = [];
        foreach ($where as $key => $item) {
            if (is_integer($key) && is_array($item)) {
                $tag = $item[0];
                $query = array_slice($item, 1);
                $query_list[] = [$tag, $query];
            } else {
                $tag = 'where';
                if (is_array($item)) {
                    $query = [$key, self::v($item, 0, null), self::v($item, 1, null), self::v($item, 2, 'and')];
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
    public function countItem(array $where = [], array $columns = ['*'])
    {
        $table = $this->_tableItem($where);
        $count = $table->count($columns);
        self::debugSql($table->toSql(), $table->getBindings(), __METHOD__);
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
    public function selectItem($start = 0, $limit = 0, array $sort_option = [], array $where = [], array $columns = ['*'])
    {
        $table = $this->_tableItem($where);
        $start = $start <= 0 ? 0 : $start;
        $limit = $limit > $this->_max_select_item_counts ? $this->_max_select_item_counts : $limit;
        if ($start > 0) {
            $table->skip($start);
        }
        if ($limit > 0) {
            $table->take($limit);
        } else {
            $table->take($this->_max_select_item_counts);
        }
        if (!empty($sort_option['field']) && !empty($sort_option['direction'])) {
            $table->orderBy($sort_option['field'], $sort_option['direction']);
        }
        $data = $table->get($columns);
        self::debugSql($table->toSql(), $table->getBindings(), __METHOD__);

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
    public function dictItem(array $where = [], array $columns = ['*'])
    {
        $table = $this->_tableItem($where);
        $table->take($this->_max_select_item_counts);
        $data = $table->get($columns);
        self::debugSql($table->toSql(), $table->getBindings(), __METHOD__);

        $rst = [];
        foreach ($data as $key => $val) {
            $id = $val[$this->_primary_key];
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
    public function getItem($value, $filed = null, array $columns = ['*'])
    {
        $filed = $filed ?: $this->_primary_key;
        return $this->firstItem([strtolower($filed) => $value], $columns);
    }

    /**
     * 根据查询条件 获取第一条记录
     * @param array $where 检索条件数组 具体格式参见文档
     * @param array $columns 需要获取的列 格式为[`column_1`, ]  默认为所有
     * @return array
     */
    public function firstItem(array $where, array $columns = ['*'])
    {
        $table = $this->_tableItem($where);
        $item = $table->first($columns);
        self::debugSql($table->toSql(), $table->getBindings(), __METHOD__);
        return static::_fixItem($item);
    }

    /**
     * 插入数据 返回插入的自增id
     * @param array $data 数据[`filed` => `value`, ]
     * @return int
     */
    public function newItem(array $data)
    {
        unset($data[$this->_primary_key]);
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
        $table = $this->db->table($this->_tablename);
        $id = $table->insertGetId($data, $this->_primary_key);
        self::debugSql($table->toSql(), $table->getBindings(), __METHOD__);
        return $id;
    }

    /**
     * 根据主键修改数据
     * @param int $id 主键值
     * @param array $data 更新的数据 格式为 [`filed` => `value`, ]
     * @return int 操作影响的行数
     */
    public function setItem($id, array $data)
    {
        unset($data['create_time'], $data['uptime'], $data[$this->_primary_key]);
        $table = $this->db->table($this->_tablename)->where($this->_primary_key, $id);
        $update = $table->update($data);
        self::debugSql($table->toSql(), $table->getBindings(), __METHOD__);
        return $update;
    }

    /**
     * 根据主键删除数据
     * @param int $id 主键值
     * @return int 操作影响的行数
     */
    public function delItem($id)
    {
        $table = $this->db->table($this->_tablename)->where($this->_primary_key, $id);
        $delete = $table->delete();
        self::debugSql($table->toSql(), $table->getBindings(), __METHOD__);
        return $delete;
    }

    /**
     * 更新或插入数据  优先根据条件查询数据 无法查询到数据时插入数据
     * @param array $where 检索条件数组 具体格式参见文档
     * @param array $data 需要插入的数据  格式为 [`filed` => `value`, ]
     * @return int 返回数据 主键 自增id
     */
    public function upsertItem(array $where, array $data)
    {
        $tmp = $this->firstItem($where);
        if (empty($tmp)) {
            return $this->newItem($data);
        } else {
            $id = $tmp[$this->_primary_key];
            $this->setItem($id, $data);
            return $id;
        }
    }
}