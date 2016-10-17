<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/9 0009
 * Time: 17:31
 */

namespace app\api;


use app\api\abstracts\BaseApi;
use app\Bootstrap;
use app\common\Base\BaseIDbModel;
use app\common\Exceptions\ApiDbBuilderError;
use app\common\Exceptions\ApiParamsError;
use app\common\DbModels\BlogCategories;
use app\common\DbModels\BlogComments;
use app\common\DbModels\BlogNotifications;
use app\common\DbModels\BlogPostTag;
use app\common\DbModels\BlogPosts;
use app\common\DbModels\BlogTags;
use app\common\DbModels\TblUsers;
use Illuminate\Database\Query\Builder;
use TinyWeb\Helper\DbHelper;

class Orm extends BaseApi
{
    protected static $detail_log = true;

    protected static $table_map = [
        'blog_categories' => [
            'primary_key' => 'id',  // 数据表自增ID
            'default_sort_column' => 'rank',  // 默认排序参数
            'default_sort_direction' => 'asc',  // 默认排序方式 asc 升序 desc 降序
            'sort' => ['user_id', 'cate_title', 'state', 'created_at', 'updated_at', 'delete_at'],
            'model' => BlogCategories::class,
        ],
        'blog_comments' => [
            'primary_key' => 'id',
            'default_sort_column' => 'created_at',  // 默认排序参数
            'default_sort_direction' => 'asc',  // 默认排序方式 asc 升序 desc 降序
            'sort' => ['user_id', 'post_id', 'comment_id', 'state', 'created_at', 'updated_at', 'delete_at'],
            'model' => BlogComments::class,
        ],
        'blog_notifications' => [
            'primary_key' => 'id',
            'default_sort_column' => 'created_at',  // 默认排序参数
            'default_sort_direction' => 'desc',  // 默认排序方式 asc 升序 desc 降序
            'sort' => ['user_id', 'post_id', 'state', 'read_at', 'created_at', 'updated_at', 'delete_at'],
            'model' => BlogNotifications::class,
        ],
        'blog_post_tag' => [
            'primary_key' => 'id',
            'default_sort_column' => 'created_at',  // 默认排序参数
            'default_sort_direction' => 'asc',  // 默认排序方式 asc 升序 desc 降序
            'sort' => ['post_id', 'tag_id', 'state', 'created_at', 'updated_at', 'delete_at'],
            'model' => BlogPostTag::class,
        ],
        'blog_posts' => [
            'primary_key' => 'id',
            'default_sort_column' => 'published_at',  // 默认排序参数
            'default_sort_direction' => 'desc',  // 默认排序方式 asc 升序 desc 降序
            'sort' => ['user_id', 'category_id', 'title', 'slug', 'view_count', 'state', 'created_at', 'updated_at', 'delete_at'],
            'model' => BlogPosts::class,
        ],
        'blog_tags' => [
            'primary_key' => 'id',
            'default_sort_column' => 'created_at',  // 默认排序参数
            'default_sort_direction' => 'asc',  // 默认排序方式 asc 升序 desc 降序
            'sort' => ['tag_name', 'state', 'created_at', 'updated_at', 'delete_at'],
            'model' => BlogTags::class,
        ],
        'tbl_users' => [
            'primary_key' => 'id',
            'default_sort_column' => 'id',  // 默认排序参数
            'default_sort_direction' => 'asc',  // 默认排序方式 asc 升序 desc 降序
            'sort' => ['nick', 'email', 'register_from', 'github_id', 'github_name', 'website', 'real_name', 'state', 'created_at', 'updated_at', 'delete_at'],
            'model' => TblUsers::class,
        ],
    ];

    protected $_current_table = '';
    protected static $allow_func = ['where', 'orWhere', 'whereBetween', 'orWhereBetween', 'whereNotBetween', 'orWhereNotBetween', 'whereIn', 'orWhereIn', 'whereNotIn', 'orWhereNotIn', 'whereNull', 'orWhereNull', 'whereDate', 'whereDay', 'whereMonth', 'whereYear', 'groupBy', 'having', 'orHaving'];
    protected $_current_user = null;

    public function __construct()
    {
        foreach(static::$table_map as $table_name => $val){
            if( empty($val['primary_key']) ){
                throw new ApiDbBuilderError("{$table_name} has empty primary_key");
            }
            if( empty($val['model']) ){
                throw new ApiDbBuilderError("{$table_name} has empty model");
            }
        }
    }

    public function hookAccessAndFilterRequest(array $request, array $origin_request)
    {
        $request = parent::hookAccessAndFilterRequest($request, $origin_request);  //调用父级过滤函数
        $table = isset($origin_request['table']) ? $origin_request['table'] : '';

        $this->setTable($table);
        return $request;  //直接返回请求参数
    }

    /**
     * @param $table_name
     * @return Builder
     * @throws ApiParamsError
     */
    public static function table($table_name)
    {
        if (empty($table_name)) {
            throw new ApiParamsError('table name empty');
        }
        $table_name = strtolower($table_name);
        if (empty(static::$table_map[$table_name])) {
            throw new ApiParamsError("table:{$table_name} not allowed");
        }
        $table = DbHelper::initDb()->table($table_name);
        return $table;
    }

    public function setTable($table_name)
    {
        $this->_current_table = $table_name;
        return $this;
    }

    private function builder(array $queries = [])
    {
        $table = self::table($this->_current_table);
        $table = self::_builderQuery($table, $queries);

        $tmp = ['queries' => $queries, 'sql' => $table->toSql(), 'bindings' => $table->getBindings()];
        Bootstrap::_D($tmp, 'sql');
        return $table;
    }

    private static function _allowQueryFunc($func)
    {
        if (empty($func)) {
            return false;
        }
        if (in_array($func, static::$allow_func)) {
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
                    throw new ApiDbBuilderError("Query:{$func} not allowed");
                }
                call_user_func_array([$table, $func], $params);
            }
        }

        return $table;
    }

    public static function getTableMapConfig($table_name, $key, $default = null)
    {
        $table_name = strtolower($table_name);
        if (empty(static::$table_map[$table_name])) {
            throw new ApiParamsError("table:{$table_name} not allowed");
        }
        return isset(static::$table_map[$table_name][$key]) ? static::$table_map[$table_name][$key] : $default;
    }

    public static function getTableMap($table_name = null, $default = [])
    {
        if (is_null($table_name)) {
            return static::$table_map;
        }
        $table_name = strtolower($table_name);
        if (empty(static::$table_map[$table_name])) {
            throw new ApiParamsError("table:{$table_name} not allowed");
        }
        return isset(static::$table_map[$table_name]) ? static::$table_map[$table_name] : $default;
    }

    public static function allowSortField($table_name, $order_field)
    {
        if (empty($order_field)) {
            throw new ApiParamsError("table:{$table_name} order field empty");
        }
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
        $orderBy[0] = !empty($orderBy[0]) && self::allowSortField($table_name, $orderBy[0]) ? strtolower($orderBy[0]) : $default_sort_column;
        $orderBy[1] = isset($orderBy[1]) ? strtolower($orderBy[1]) : '';
        $orderBy[1] = ($orderBy[1] == 'asc' || $orderBy[1] == 'desc') ? $orderBy[1] : $default_sort_direction;
        return $orderBy;
    }

    /**
     * Pluck a single column's value from the first result of a query.
     * @param string $column
     * @param array $queries
     * @return mixed
     */
    public function pluck($column, array $queries = [])
    {
        $table = $this->builder($queries);
        $rst = $table->pluck($column);

        self::$detail_log && self::debugArgs(func_get_args(), __METHOD__, __CLASS__, __LINE__);
        self::$detail_log && self::debugResult($rst, __METHOD__, __CLASS__, __LINE__);
        return $rst;
    }

    /**
     * Get an array with the values of a given column.
     *
     * @param  string $column
     * @param  string $key
     * @param array $queries
     * @return array
     */
    public function lists($column, $key = null, array $queries = [])
    {
        $table = $this->builder($queries);
        $rst = $table->lists($column, $key);

        self::$detail_log && self::debugArgs(func_get_args(), __METHOD__, __CLASS__, __LINE__);
        self::$detail_log && self::debugResult($rst, __METHOD__, __CLASS__, __LINE__);
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

        self::$detail_log && self::debugArgs(func_get_args(), __METHOD__, __CLASS__, __LINE__);
        self::$detail_log && self::debugResult($rst, __METHOD__, __CLASS__, __LINE__);
        return $rst;
    }

    /**
     * Get a record from the database by id.
     *
     * @param $id
     * @return array
     */
    public function getItem($id){
        $id = $this->getModel()->beforeGetItem($this->_current_user, $id);
        $queries = [
            'where'=>
                [$this->getConfig('primary_key'), $id],
        ];
        $rst = $this->first(['*'], $queries);
        $this->getModel()->afterGetItem($this->_current_user, $rst, $id);

        self::$detail_log && self::debugArgs(func_get_args(), __METHOD__, __CLASS__, __LINE__);
        self::$detail_log && self::debugResult($rst, __METHOD__, __CLASS__, __LINE__);
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
        if(empty($values)){
            throw new ApiParamsError("table:{$this->_current_table} update with empty values");
        }

        $table = $this->builder($queries);
        $primary_key = strtolower($this->getConfig('primary_key', 'id'));
        unset($values[$primary_key]);

        $rst = !empty($values) ? $table->update($values) : 0;

        self::$detail_log && self::debugArgs(func_get_args(), __METHOD__, __CLASS__, __LINE__);
        self::$detail_log && self::debugResult($rst, __METHOD__, __CLASS__, __LINE__);
        return $rst;
    }

    /**
     * update a record from the database by id.
     *
     * @param $id
     * @param array $values
     * @return int
     * @throws ApiDbBuilderError
     * @throws ApiParamsError
     */
    public function updateItem($id, array $values){
        if(empty($values)){
            throw new ApiParamsError("table:{$this->_current_table} updateItem with empty values");
        }
        $id = $this->getModel()->beforeUpdateItem($this->_current_user, $id, $values);

        $queries = [
            'where'=>
                [$this->getConfig('primary_key'), $id],
        ];
        $table = $this->builder($queries);
        $primary_key = strtolower($this->getConfig('primary_key', 'id'));
        unset($values[$primary_key]);

        $rst = $table->update($values);

        $this->getModel()->afterUpdateItem($this->_current_user, $id, $values);

        self::$detail_log && self::debugArgs(func_get_args(), __METHOD__, __CLASS__, __LINE__);
        self::$detail_log && self::debugResult($rst, __METHOD__, __CLASS__, __LINE__);
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

        self::$detail_log && self::debugArgs(func_get_args(), __METHOD__, __CLASS__, __LINE__);
        self::$detail_log && self::debugResult($rst, __METHOD__, __CLASS__, __LINE__);
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

        self::$detail_log && self::debugArgs(func_get_args(), __METHOD__, __CLASS__, __LINE__);
        self::$detail_log && self::debugResult($rst, __METHOD__, __CLASS__, __LINE__);
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
        $id_list = $this->lists($this->getConfig('primary_key'), null, $queries);
        foreach ($id_list as $item_id) {
            $this->getModel()->beforeDeleteItem($this->_current_user, $item_id);
        }

        $table = $this->builder($queries);
        $rst = $table->delete();

        foreach ($id_list as $item_id) {
            $this->getModel()->afterDeleteItem($this->_current_user, $item_id);
        }
        self::$detail_log && self::debugArgs(func_get_args(), __METHOD__, __CLASS__, __LINE__);
        self::$detail_log && self::debugResult($rst, __METHOD__, __CLASS__, __LINE__);
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
        $id = $this->getModel()->beforeDeleteItem($this->_current_user, $id);
        $queries = [
            'where'=>
                [$this->getConfig('primary_key'), $id],
        ];
        $table = $this->builder($queries);
        $rst = $table->delete();

        self::$detail_log && self::debugArgs(func_get_args(), __METHOD__, __CLASS__, __LINE__);
        self::$detail_log && self::debugResult($rst, __METHOD__, __CLASS__, __LINE__);
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
        foreach($values as $idx => $val){
            $rst[] = $this->insertItem($val);
        }
        self::$detail_log && self::debugArgs(func_get_args(), __METHOD__, __CLASS__, __LINE__);
        self::$detail_log && self::debugResult($rst, __METHOD__, __CLASS__, __LINE__);
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
        $values = $this->getModel()->beforeInsertItem($this->_current_user, $values);

        $primary_key = strtolower($this->getConfig('primary_key', 'id'));
        unset($values[$primary_key]);

        $table = $this->builder();
        $rst = $table->insertGetId($values);

        $this->getModel()->afterInsertItem($this->_current_user, $values, $rst);

        self::$detail_log && self::debugArgs(func_get_args(), __METHOD__, __CLASS__, __LINE__);
        self::$detail_log && self::debugResult($rst, __METHOD__, __CLASS__, __LINE__);
        return $rst;
    }

    /**
     * Execute the query and get the first result.
     *
     * @param  array $columns
     * @param array $queries
     * @return mixed|static
     */
    public function first(array $columns = ['*'], array $queries = [])
    {
        $table = $this->builder($queries);
        $table->select($columns);
        $rst = $table->first();

        self::$detail_log && self::debugArgs(func_get_args(), __METHOD__, __CLASS__, __LINE__);
        self::$detail_log && self::debugResult($rst, __METHOD__, __CLASS__, __LINE__);
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

        self::$detail_log && self::debugArgs(func_get_args(), __METHOD__, __CLASS__, __LINE__);
        self::$detail_log && self::debugResult($rst, __METHOD__, __CLASS__, __LINE__);
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

        self::$detail_log && self::debugArgs(func_get_args(), __METHOD__, __CLASS__, __LINE__);
        self::$detail_log && self::debugResult($rst, __METHOD__, __CLASS__, __LINE__);
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

        self::$detail_log && self::debugArgs(func_get_args(), __METHOD__, __CLASS__, __LINE__);
        self::$detail_log && self::debugResult($rst, __METHOD__, __CLASS__, __LINE__);
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

        self::$detail_log && self::debugArgs(func_get_args(), __METHOD__, __CLASS__, __LINE__);
        self::$detail_log && self::debugResult($rst, __METHOD__, __CLASS__, __LINE__);
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

        self::$detail_log && self::debugArgs(func_get_args(), __METHOD__, __CLASS__, __LINE__);
        self::$detail_log && self::debugResult($rst, __METHOD__, __CLASS__, __LINE__);
        return $rst;
    }

    /**
     * @return BaseIDbModel
     * @throws ApiDbBuilderError
     * @throws ApiParamsError
     */
    private function getModel()
    {
        $model = $this->getConfig('model');
        $obj = new $model;
        if( !($obj instanceof BaseIDbModel) ){
            throw new ApiDbBuilderError("{$this->_current_table} model:{$model} must instanceof BaseDbModel");
        }
        return $obj;
    }

    private function getConfig($key, $default = null){
        return self::getTableMapConfig($this->_current_table, $key, $default);
    }

}