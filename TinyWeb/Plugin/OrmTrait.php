<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/18 0018
 * Time: 11:23
 */

namespace TinyWeb\Plugin;

use app\Bootstrap;
use app\common\Models\CurrentUser;
use TinyWeb\Application;
use TinyWeb\CurrentUserInterface;
use TinyWeb\Exception\ApiParamsError;
use TinyWeb\Exception\OrmQueryBuilderError;
use TinyWeb\Exception\OrmStartUpError;
use TinyWeb\Helper\BuilderHelper;
use TinyWeb\Helper\DbHelper;
use TinyWeb\ObserversInterface;

trait OrmTrait
{
    private $_current_table = null;
    private $_current_db = null;
    private $_current_user = null;
    private $_model_map = [];

    private static $_table_map = [];

    /**
     * 必须是一个 静态函数 函数体内不可使用$this
     * @return void
     */
    protected static function initOrm()
    {
    }

    public static function  autoHelp()
    {
        static::initOrm();
        return self::$_table_map;
    }

    protected static function hasTableMap()
    {
        return !empty(self::$_table_map);
    }

    protected static function initTableMap(array $map)
    {
        if (empty($map)) {
            throw new OrmStartUpError("table map empty");
        }
        if (!empty(self::$_table_map)) {
            return false;
        }
        foreach ($map as $table_name => &$config) {
            $config['primary_key'] = !isset($config['primary_key']) ? 'id' : $config['primary_key'];  // 主键默认为 id
            if (empty($config['primary_key'])) {
                throw new OrmStartUpError("table:{$table_name} has empty primary_key");
            }

            $config['unique_keys'] = isset($config['unique_keys']) ? $config['unique_keys'] : [];
            $config['unique_keys'][] = $config['primary_key'];
            $config['unique_keys'] = array_unique($config['unique_keys']);

            $config['Model'] = isset($config['Model']) ? $config['Model'] : '';
            if (empty($config['Model'])) {
                throw new OrmStartUpError("{$table_name} has empty Model");
            }

            $config['default_sort_column'] = isset($config['default_sort_column']) ? $config['default_sort_column'] : $config['primary_key'];
            $config['default_sort_direction'] = isset($config['default_sort_direction']) ? $config['default_sort_direction'] : 'asc';
            $config['default_sort_direction'] = $config['default_sort_direction'] == 'desc' ? 'desc' : 'asc';

            $config['attach'] = isset($config['attach']) ? $config['attach'] : [];
            foreach ($config['attach'] as $key => &$item) {
                if (empty($item['uri'])) {
                    throw new OrmStartUpError("table:{$table_name} attach:{$key} empty uri");
                }
                $item['params'] = isset($item['params']) ? $item['params'] : [];
                $item['dependent'] = self::parseAttachDependent($item['params'], []);
            }
        }
        self::$_table_map = $map;
        return true;
    }

    /**
     * @param string $db_table
     * @param string $attach
     * @param array $item
     * @param array $dependent
     * @param array $params
     * @param string $stag
     * @param string $etag
     * @return array
     * @throws OrmQueryBuilderError
     */
    private static function replaceAttachDependent($db_table, $attach, array $item, array $dependent, array $params, $stag = '%', $etag = '%')
    {
        if (empty($item) || empty($params)) {
            return [];
        }
        if (empty($dependent)) {
            return $params;
        }
        $search = [];
        $replace = [];
        foreach ($dependent as $key) {
            if( !isset($item[$key]) ){
                throw new OrmQueryBuilderError("table:{$db_table} attach:{$attach} dependent:{$key} not found in item:" . json_encode($item));
            }
            $search[] = "{$stag}{$key}{$etag}";
            $replace[] = $item[$key];
        }

        return self::replaceDependentArr($search, $replace, $params);
    }

    public function aTest(){
        $db_table = 'tiny.blog_posts';
        $attach = 'category';
        static::initOrm();

        Bootstrap::_D(self::$_table_map[$db_table]);
        $params = self::$_table_map[$db_table]['attach'][$attach]['params'];
        $dependent = self::$_table_map[$db_table]['attach'][$attach]['dependent'];
        $item = [
            'category_id' => 123,
        ];
        return self::replaceAttachDependent($db_table, $attach, $item, $dependent, $params);
    }

    private static function replaceDependentArr($search, $replace, array $params)
    {
        $result = [];
        foreach ($params as $key => $val) {
            if (is_string($key)) {
                $key = str_replace($search, $replace, $key);
            }
            if (is_string($val)) {
                $val = str_replace($search, $replace, $val);
            } else if (is_array($val)) {
                $val = self::replaceDependentArr($search, $replace, $val);
            }
            $result[$key] = $val;
        }
        return $result;
    }

    /**
     * @param string $str 源字符串
     * @param int $idx
     * @param string $stag
     * @param string $etag
     * @return string
     */
    private static function parseDependentStr($str, $idx, $stag = '%', $etag = '%')
    {
        $s_len = strlen($stag);
        $e_len = strlen($etag);
        $s_idx = strpos($str, $stag, $idx);
        $e_idx = strpos($str, $etag, $s_idx + $s_len);
        if ($s_idx !== false && $e_idx !== false && $s_idx < $e_idx) {
            $dep = substr($str, $s_idx + $s_len, $e_idx - $s_idx - $e_len);
            return [$dep, $e_idx + 1];
        }
        return ['', 0];
    }

    /**
     * 从 attach 的 $params 中解析出依赖 只会尝试解析字符串中的 %c% 格式的 为依赖
     * @param array $params
     * @param array $dependent
     * @return array
     */
    private static function parseAttachDependent(array $params, array $dependent)
    {
        if (empty($params)) {
            return [];
        }
        foreach ($params as $key => $val) {
            if (is_string($key)) {
                $idx = 0;
                do {
                    list($dep, $idx) = self::parseDependentStr($key, intval($idx));
                    if (!empty($dep) && !in_array($dep, $dependent)) {
                        $dependent[] = $dep;
                    }
                } while ($idx > 0);
            }
            if (is_string($val)) {
                $idx = 0;
                do {
                    list($dep, $idx) = self::parseDependentStr($val, intval($idx));
                    if (!empty($dep) && !in_array($dep, $dependent)) {
                        $dependent[] = $dep;
                    }
                } while ($idx > 0);
            } else if (is_array($val)) {
                $dependent = self::parseAttachDependent($val, $dependent);
            }
        }
        return $dependent;
    }

    /**
     * @param string $current_db
     * @return $this
     * @throws OrmStartUpError
     */
    public function hookCurrentDb($current_db)
    {
        self::initOrm();
        if (empty($current_db)) {
            throw new OrmStartUpError("db:{$current_db} empty name");
        }
        $this->_current_db = $current_db;
        return $this;
    }

    /**
     * @param string $current_table
     * @return $this
     * @throws OrmStartUpError
     */
    public function hookCurrentTable($current_table)
    {
        self::initOrm();
        if (empty($current_table)) {
            throw new OrmStartUpError("table:{$current_table} empty name");
        }
        $this->_current_table = $current_table;
        return $this;
    }

    /**
     * @param CurrentUserInterface $user
     * @return $this
     */
    public function hookCurrentUser(CurrentUserInterface $user)
    {
        self::initOrm();
        $this->_current_user = $user;
        /** @var ObserversInterface $model */
        foreach ($this->_model_map as $table_name => $model) {
            !empty($model) && $model->hookCurrentUser($user);
        }
        return $this;
    }

    /**
     * @param string $table_name
     * @param CurrentUserInterface $user
     * @param string $db_name
     * @return $this
     */
    public function hookCurrent($table_name, CurrentUserInterface $user=null, $db_name = null)
    {
        self::initOrm();
        $db_name = is_null($db_name) ? Application::instance()->getEnv('ENV_MYSQL_DB') : $db_name;
        $table_name = strtolower($table_name);
        $db_name = strtolower($db_name);
        $user = is_null($user) ? new CurrentUser() : $user;

        $this->hookCurrentDb($db_name)->hookCurrentTable($table_name)->hookCurrentUser($user);
        return $this;
    }

    public static function _getTableMapConfig($db_table, $key, $default = null)
    {
        $table_map = self::_getTableMap($db_table);
        return isset($table_map[$key]) ? $table_map[$key] : $default;
    }

    public static function _getTableMap($db_table)
    {
        static::initOrm();
        $db_table = strtolower($db_table);
        if (empty(self::$_table_map[$db_table]) || empty($db_table)) {
            throw new ApiParamsError("table:{$db_table} not allowed");
        }
        return self::$_table_map[$db_table];
    }

    /**
     * @return ObserversInterface
     * @throws OrmStartUpError
     * @throws ApiParamsError
     */
    private function getModel()
    {
        return $this->getTableModel("{$this->_current_db}.{$this->_current_table}");
    }

    /**
     * @param string $db_table
     * @return ObserversInterface
     * @throws OrmStartUpError
     */
    private function getTableModel($db_table)
    {
        /** @var ObserversInterface $tmp */
        $tmp = isset($this->_model_map[$db_table]) ? $this->_model_map[$db_table] : null;

        if (!empty($tmp)) {
            $tmp->hookCurrentUser($this->_current_user);
            return $tmp;
        }
        $model_str = self::_getTableMapConfig($db_table, 'Model');
        $tmp = new $model_str();
        $tmp->hookCurrentUser($this->_current_user);
        $this->setTableModel($db_table, $tmp);
        return $tmp;
    }

    /**
     * @param string $table_name
     * @param ObserversInterface $model
     * @return ObserversInterface
     */
    private function setTableModel($table_name, ObserversInterface $model)
    {
        $this->_model_map[$table_name] = $model;
    }

    private function getConfig($key, $default = null)
    {
        return self::_getTableMapConfig("{$this->_current_db}.{$this->_current_table}", $key, $default);
    }

    /**
     * @param string $db_name
     * @param string $table_name
     * @return BuilderHelper
     * @throws ApiParamsError
     */
    public static function table($table_name, $db_name = null)
    {
        static::initOrm();
        $db_name = is_null($db_name) ? Application::instance()->getEnv('ENV_MYSQL_DB') : $db_name;
        $table_name = strtolower($table_name);
        $db_name = strtolower($db_name);

        $table_config = self::_getTableMap("{$db_name}.{$table_name}");
        $table = DbHelper::_table($table_name, $db_name, $table_config);

        return $table;
    }

    private function builder(array $queries = [])
    {
        $table = self::table($this->_current_table, $this->_current_db);
        $table = $this->getModel()->beforeBuilderQueries($table, $queries);

        $table = self::_builderQuery($table, $queries);

        $table = $this->getModel()->afterBuilderQueries($table, $queries);
        return $table;
    }

    private static function _allowQueryFunc($func)
    {
        static $allow_func = ['where', 'orWhere', 'whereBetween', 'orWhereBetween', 'whereNotBetween', 'orWhereNotBetween', 'whereIn', 'orWhereIn', 'whereNotIn', 'orWhereNotIn', 'whereNull', 'orWhereNull', 'whereDate', 'whereDay', 'whereMonth', 'whereYear', 'groupBy', 'having', 'orHaving'];

        if (empty($func)) {
            return false;
        }
        if (in_array($func, $allow_func)) {
            return true;
        }
        return false;
    }

    private static function _builderQuery(BuilderHelper $table, array $queries)
    {
        if (empty($queries)) {
            return $table;
        }

        foreach ($queries as $idx => $val) {
            if (!is_array($val) || count($val) < 2) {
                throw new OrmQueryBuilderError("error idx:{$idx} query:" . json_encode($val));
            }
            if (self::_allowQueryFunc($val[0])) {
                $func = array_shift($val);
                call_user_func_array([$table, $func], $val);
            } else {
                call_user_func_array([$table, 'where'], $val);
            }
        }

        return $table;
    }

    public static function _allowSortField($db_table, $order_field)
    {
        static::initOrm();
        if (empty($order_field)) {
            return false;
        }
        $order_field = strtolower($order_field);
        $sort = self::_getTableMapConfig($db_table, 'sort', []);
        if (is_string($sort) && trim($sort) == '*') {
            return true;
        } else if (is_array($sort)) {
            return in_array($order_field, $sort);
        }
        return false;
    }

    private static function fixParamsOrderBy($db_table, array $orderBy)
    {
        $primary_key = strtolower(self::_getTableMapConfig($db_table, 'primary_key', ''));
        $default_sort_column = strtolower(self::_getTableMapConfig($db_table, 'default_sort_column', $primary_key));
        $default_sort_direction = strtolower(self::_getTableMapConfig($db_table, 'default_sort_direction', 'asc'));
        $orderBy = empty($orderBy) ? [$default_sort_column, $default_sort_direction] : $orderBy;
        $orderBy[0] = isset($orderBy[0]) && self::_allowSortField($db_table, $orderBy[0]) ? strtolower($orderBy[0]) : $default_sort_column;
        $orderBy[1] = isset($orderBy[1]) ? strtolower($orderBy[1]) : '';
        $orderBy[1] = ($orderBy[1] == 'asc' || $orderBy[1] == 'desc') ? $orderBy[1] : $default_sort_direction;
        return [$orderBy[0], $orderBy[1]];
    }

    /**
     * Pluck a single column's value from the first result of a query.
     * @param string $column
     * @param array $queries
     * @return mixed
     */
    public function pluck($column, array $queries = [])
    {
        $item = $this->first([$column,], $queries);
        $rst = isset($item[$column]) ? $item[$column] : null;
        return $rst;
    }

    /**
     * Get an array with the values of a given column.
     *
     * @param  string $column
     * @param array $queries
     * @return array
     */
    public function lists($column, array $queries = [])
    {
        $table = $this->builder($queries);
        $rst = $table->lists($column);

        return $rst;
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param int $skip
     * @param int $take
     * @param array $orderBy
     * @param array $columns
     * @param array $queries
     * @return array|static[]
     */
    public function get($skip = 0, $take = 20, array $orderBy = [], array $columns = ['*'], array $queries = [])
    {
        $id_list = $this->lists($this->getConfig('primary_key'), $queries);
        $this->getModel()->beforeGetMany($id_list);

        $table = $this->builder($queries);
        list($skip, $take) = [intval($skip), intval($take)];
        if ($take > 0 && $skip >= 0) {
            $table->skip($skip)->take($take);
        }
        $orderBy = self::fixParamsOrderBy("{$this->_current_db}.{$this->_current_table}", $orderBy);
        if (!empty($orderBy[0])) {
            $table->orderBy($orderBy[0], $orderBy[1]);
        }
        $table->select($columns);
        $rst = $table->get();

        $rst = $this->getModel()->afterGetMany($id_list, $rst);
        return $rst;
    }

    /**
     * Get a record from the database by id.
     *
     * @param int $id
     * @return array
     */
    public function getItem($id)
    {
        $id = $this->getModel()->beforeGetItem($id);
        $queries = [
            'where' =>
                [$this->getConfig('primary_key'), $id],
        ];
        $rst = $this->first(['*'], $queries);

        $rst = !empty($rst) ? $this->getModel()->afterGetItem($rst) : $rst;
        return $rst;
    }

    /**
     * Update a record in the database.
     *
     * @param array $values
     * @param array $queries
     * @return int
     * @throws ApiParamsError
     */
    public function update(array $values, array $queries = [])
    {
        if (empty($values)) {
            throw new ApiParamsError("table:{$this->_current_db}.{$this->_current_table} update with empty values");
        }

        $id_list = $this->lists($this->getConfig('primary_key'), $queries);
        $values = $this->getModel()->beforeUpdateMany($id_list, $values);

        $table = $this->builder($queries);
        $primary_key = strtolower($this->getConfig('primary_key', 'id'));
        unset($values[$primary_key]);

        $rst = $table->update($values);

        $this->getModel()->afterUpdateMany($id_list, $values);
        return $rst;
    }

    /**
     * update a record from the database by id.
     *
     * @param $id
     * @param array $values
     * @return int
     * @throws OrmStartUpError
     * @throws ApiParamsError
     */
    public function updateItem($id, array $values)
    {
        if (empty($values)) {
            throw new ApiParamsError("table:{$this->_current_db}.{$this->_current_table} updateItem with empty values");
        }
        $values = $this->getModel()->beforeUpdateItem($id, $values);

        $queries = [
            'where' =>
                [$this->getConfig('primary_key'), $id],
        ];
        $table = $this->builder($queries);
        $primary_key = strtolower($this->getConfig('primary_key', 'id'));
        unset($values[$primary_key]);

        $rst = $table->update($values);

        $this->getModel()->afterUpdateItem($id, $values);

        return $rst;
    }

    /**
     * Increment a column's value by a given amount.
     *
     * @param  string $column
     * @param  int $amount
     * @param  array $extra
     * @param array $queries
     * @return int
     */
    public function increment($column, $amount = 1, array $extra = [], array $queries = [])
    {
        $table = $this->builder($queries);
        $rst = $table->increment($column, $amount, $extra);

        return $rst;
    }

    /**
     * Decrement a column's value by a given amount.
     *
     * @param  string $column
     * @param  int $amount
     * @param  array $extra
     * @param array $queries
     * @return int
     */
    public function decrement($column, $amount = 1, array $extra = [], array $queries = [])
    {
        $table = $this->builder($queries);
        $rst = $table->decrement($column, $amount, $extra);

        return $rst;
    }

    /**
     * Delete a record from the database.
     *
     * @param array $queries
     * @return array
     */
    public function delete(array $queries = [])
    {
        $id_list = $this->lists($this->getConfig('primary_key'), $queries);
        foreach ($id_list as $item_id) {
            $this->getModel()->beforeDeleteItem($item_id);
        }

        $table = $this->builder($queries);
        $rst = $table->delete();

        foreach ($id_list as $item_id) {
            $this->getModel()->afterDeleteItem($item_id);
        }

        return $rst;
    }

    /**
     * Delete a record from the database by id.
     *
     * @param $id
     * @return int
     */
    public function deleteItem($id)
    {
        $this->getModel()->beforeDeleteItem($id);
        $queries = [
            'where' =>
                [$this->getConfig('primary_key'), $id],
        ];
        $table = $this->builder($queries);
        $rst = $table->delete();
        $this->getModel()->afterDeleteItem($id);

        return $rst;
    }

    /**
     * Insert many records into the database.
     *
     * @param  array $values
     * @return array
     */
    public function insertMany(array $values)
    {
        $rst = [];
        foreach ($values as $idx => $val) {
            $rst[] = $this->insertItem($val);
        }

        return $rst;
    }

    /**
     * Insert a new record and get the value of the primary key.
     *
     * @param  array $values
     * @return int
     */
    public function insertItem(array $values)
    {
        $values = $this->getModel()->beforeInsertItem($values);

        $primary_key = strtolower($this->getConfig('primary_key', 'id'));
        unset($values[$primary_key]);

        $table = $this->builder();
        $rst = $table->insertGetId($values);

        $this->getModel()->afterInsertItem($rst);

        return $rst;
    }

    /**
     * Execute the query and get the first result.
     *
     * @param  array $columns
     * @param array $queries
     * @return array
     */
    public function first(array $columns = ['*'], array $queries = [])
    {
        $table = $this->builder($queries);
        $table->select($columns);
        $rst = $table->first();

        return $rst;
    }

    /**
     * Retrieve the "count" result of the query.
     *
     * @param array $queries
     * @return int
     */
    public function count(array $queries = [])
    {
        $table = $this->builder($queries);
        $rst = $table->count();

        return $rst;
    }

    /**
     * Retrieve the maximum value of a given column.
     *
     * @param  string $column
     * @param array $queries
     * @return float|int
     */
    public function max($column, array $queries = [])
    {
        $table = $this->builder($queries);
        $rst = $table->max($column);

        return $rst;
    }

    /**
     * Retrieve the minimum value of a given column.
     *
     * @param  string $column
     * @param array $queries
     * @return float|int
     */
    public function min($column, array $queries = [])
    {
        $table = $this->builder($queries);
        $rst = $table->min($column);

        return $rst;
    }

    /**
     * Retrieve the average of the values of a given column.
     *
     * @param  string $column
     * @param array $queries
     * @return float|int
     */
    public function avg($column, array $queries = [])
    {
        $table = $this->builder($queries);
        $rst = $table->avg($column);

        return $rst;
    }

    /**
     * Retrieve the sum of the values of a given column.
     *
     * @param  string $column
     * @param array $queries
     * @return float|int
     */
    public function sum($column, array $queries = [])
    {
        $table = $this->builder($queries);
        $rst = $table->sum($column);

        return $rst;
    }

}