<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/18 0018
 * Time: 11:23
 */

namespace TinyWeb\Plugin;


use Illuminate\Database\Query\Builder;
use TinyWeb\Exception\ApiParamsError;
use TinyWeb\Exception\OrmStartUpError;
use TinyWeb\Helper\DbHelper;
use TinyWeb\ObserversInterface;

trait OrmTrait
{
    private $_current_table = '';
    private $_current_db = '';

    protected static function getMap()
    {
        return [];
    }

    protected static function preTreatmentMap(array $map){
        foreach ($map as $table_name => &$config) {
            $config['attach'] = isset($config['attach']) ? $config['attach'] : [];
            foreach ($config['attach'] as $key => &$item) {
                if( empty($item['uri']) ){
                    throw new OrmStartUpError("table:{$table_name} attach:{$key} empty uri");
                }
                $item['params'] = isset($item['params']) ? $item['params'] : [];
                $item['dependent'] = self::parseAttachDependent($item['params'], []);
            }
        }
        return $map;
    }

    private static function parseAttachDependent(array $params, array $dependent){
        if( empty($params) ){
            return [];
        }
        foreach ($params as $key => $val) {
            if( is_string($key) && strpos($key, '%')===0 ){
                $dependent[] = substr($key, 1, strpos($key, '%', 1));
            }
            if( is_string($val) && strpos($val, '%')===0 ){

            } else if( is_array($val) ){

            }
        }
        return $dependent;
    }

    protected static function getDb()
    {
        return '';
    }

    public function hookCurrentDb($current_db)
    {
        if (empty($current_db)) {
            throw new OrmStartUpError("db:{$current_db} empty name");
        }
        $this->_current_db = $current_db;
    }

    public function hookCurrentTable($current_table)
    {
        if (empty($current_table)) {
            throw new OrmStartUpError("table:{$current_table} empty name");
        }
        $this->_current_table = $current_table;
    }

    /**
     * @param CurrentUser $user
     */
    public function hookCurrentUser(CurrentUser $user)
    {
        $table_map = static::getMap();
        foreach ($table_map as $table_name => $item) {
            $tmp = self::getTableModel($table_name);
            $tmp->hookCurrentUser($user);
        }
    }

    public static function getTableMapConfig($table_name, $key, $default = null)
    {
        $table_map = static::getMap();
        $table_name = strtolower($table_name);
        if (empty($table_map[$table_name])) {
            throw new ApiParamsError("table:{$table_name} not allowed");
        }
        return isset($table_map[$table_name][$key]) ? $table_map[$table_name][$key] : $default;
    }

    public static function getTableMap($table_name = null, $default = [])
    {
        $table_map = static::getMap();
        if (is_null($table_name)) {
            return $table_map;
        }
        $table_name = strtolower($table_name);
        if (empty($table_map[$table_name])) {
            throw new ApiParamsError("table:{$table_name} not allowed");
        }
        return isset($table_map[$table_name]) ? $table_map[$table_name] : $default;
    }


    /**
     * @return ObserversInterface
     * @throws OrmStartUpError
     * @throws ApiParamsError
     */
    private function getModel()
    {
        return $this->getTableModel($this->_current_table);
    }

    /**
     * @param string $table_name
     * @return ObserversInterface
     * @throws OrmStartUpError
     */
    private static function getTableModel($table_name)
    {
        return self::getTableMapConfig($table_name, 'DbModel');
    }

    /**
     * @param string $table_name
     * @param ObserversInterface $model
     * @return ObserversInterface
     */
    private static function setTableModel($table_name, ObserversInterface $model)
    {
        $table_map = static::getMap();
        $table_map[$table_name]['DbModel'] = $model;
    }

    private function getConfig($key, $default = null)
    {
        return self::getTableMapConfig($this->_current_table, $key, $default);
    }

    /**
     * @param string $db_name
     * @param string $table_name
     * @return Builder
     * @throws ApiParamsError
     */
    protected static function table($db_name, $table_name)
    {
        $table_map = static::getMap();
        if (empty($db_name)) {
            throw new ApiParamsError('db name empty');
        }
        if (empty($table_name)) {
            throw new ApiParamsError('table name empty');
        }
        $table_name = strtolower($table_name);
        if (empty($table_map[$table_name])) {
            throw new ApiParamsError("table:{$table_name} not allowed");
        }
        $table = DbHelper::initDb()->connection($db_name)->table($table_name);
        return $table;
    }

    protected function builder(array $queries = [])
    {
        $table = self::table($this->_current_db, $this->_current_table);
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

    private static function _builderQuery(Builder $table, array $queries)
    {
        if (empty($queries)) {
            return $table;
        }
        $query_list = [];
        foreach ($queries as $func => $val) {
            if (self::_allowQueryFunc($func)) {
                foreach ($val as $params) {
                    $query_list[] = [$func => $params];
                }
            } else if (is_int($func)) {
                $query_list[] = $val;
            }
        }

        foreach ($query_list as $query) {
            foreach ($query as $func => $params) {
                if (!self::_allowQueryFunc($func)) {
                    throw new OrmStartUpError("query:{$func} not allowed");
                }
                call_user_func_array([$table, $func], $params);
            }
        }

        return $table;
    }

    public static function allowSortField($table_name, $order_field)
    {
        if (empty($order_field)) {
            return false;
        }
        $order_field = strtolower($order_field);
        $sort = self::getTableMapConfig($table_name, 'sort', []);
        if (is_string($sort) && trim($sort) == '*') {
            return true;
        } else if (is_array($sort)) {
            return in_array($order_field, $sort);
        }
        return false;
    }

    private static function fixParamsOrderBy($table_name, array $orderBy)
    {
        $primary_key = strtolower(self::getTableMapConfig($table_name, 'primary_key', ''));
        $default_sort_column = strtolower(self::getTableMapConfig($table_name, 'default_sort_column', $primary_key));
        $default_sort_direction = strtolower(self::getTableMapConfig($table_name, 'default_sort_direction', 'asc'));
        $orderBy = empty($orderBy) ? [$default_sort_column, $default_sort_direction] : $orderBy;
        $orderBy[0] = isset($orderBy[0]) && self::allowSortField($table_name, $orderBy[0]) ? strtolower($orderBy[0]) : $default_sort_column;
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
        $item = $this->first([$column, ], $queries);
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
        $orderBy = self::fixParamsOrderBy($this->_current_table, $orderBy);
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
        $rst = $this->first(['*'], $queries);  // afterGetItem 已在 first 中处理

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
            throw new ApiParamsError("table:{$this->_current_table} update with empty values");
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
            throw new ApiParamsError("table:{$this->_current_table} updateItem with empty values");
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

        $rst = $this->getModel()->afterGetItem($rst);
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