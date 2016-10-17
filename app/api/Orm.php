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
use app\common\Exceptions\ApiDbBuilderError;
use app\common\Exceptions\ApiParamsError;
use app\common\Models\BlogCategories;
use app\common\Models\BlogComments;
use app\common\Models\BlogNotifications;
use app\common\Models\BlogPostTag;
use app\common\Models\BlogPosts;
use app\common\Models\BlogTags;
use app\common\Models\TblUsers;
use app\common\Traits\DbMiddleWare;
use Illuminate\Database\Query\Builder;
use TinyWeb\Helper\DbHelper;

class Orm extends BaseApi
{
    protected static $detail_log = true;

    private static $table_map = [
        'xblog.blog_categories' => [
            'primary_key' => 'id',  // 数据表自增ID
            'default_sort_column' => 'rank',  // 默认排序参数
            'default_sort_direction' => 'asc',  // 默认排序方式 asc 升序 desc 降序
            'sort' => ['user_id', 'cate_title', 'state', 'created_at', 'updated_at', 'delete_at'],
            'model' => BlogCategories::class,
        ],
        'xblog.blog_comments' => [
            'primary_key' => 'id',
            'default_sort_column' => 'created_at',  // 默认排序参数
            'default_sort_direction' => 'asc',  // 默认排序方式 asc 升序 desc 降序
            'sort' => ['user_id', 'post_id', 'comment_id', 'state', 'created_at', 'updated_at', 'delete_at'],
            'model' => BlogComments::class,
        ],
        'xblog.blog_notifications' => [
            'primary_key' => 'id',
            'default_sort_column' => 'created_at',  // 默认排序参数
            'default_sort_direction' => 'desc',  // 默认排序方式 asc 升序 desc 降序
            'sort' => ['user_id', 'post_id', 'state', 'read_at', 'created_at', 'updated_at', 'delete_at'],
            'model' => BlogNotifications::class,
        ],
        'xblog.blog_post_tag' => [
            'primary_key' => 'id',
            'default_sort_column' => 'created_at',  // 默认排序参数
            'default_sort_direction' => 'asc',  // 默认排序方式 asc 升序 desc 降序
            'sort' => ['post_id', 'tag_id', 'state', 'created_at', 'updated_at', 'delete_at'],
            'model' => BlogPostTag::class,
        ],
        'xblog.blog_posts' => [
            'primary_key' => 'id',
            'default_sort_column' => 'published_at',  // 默认排序参数
            'default_sort_direction' => 'desc',  // 默认排序方式 asc 升序 desc 降序
            'sort' => ['user_id', 'category_id', 'title', 'slug', 'view_count', 'state', 'created_at', 'updated_at', 'delete_at'],
            'model' => BlogPosts::class,
        ],
        'xblog.blog_tags' => [
            'primary_key' => 'id',
            'default_sort_column' => 'created_at',  // 默认排序参数
            'default_sort_direction' => 'asc',  // 默认排序方式 asc 升序 desc 降序
            'sort' => ['tag_name', 'state', 'created_at', 'updated_at', 'delete_at'],
            'model' => BlogTags::class,
        ],
        'xblog.tbl_users' => [
            'primary_key' => 'id',
            'default_sort_column' => 'id',  // 默认排序参数
            'default_sort_direction' => 'asc',  // 默认排序方式 asc 升序 desc 降序
            'sort' => ['nick', 'email', 'register_from', 'github_id', 'github_name', 'website', 'real_name', 'state', 'created_at', 'updated_at', 'delete_at'],
            'model' => TblUsers::class,
        ],
    ];

    protected $_current_table = '';
    protected static $allow_func = ['where', 'orWhere', 'whereBetween', 'orWhereBetween', 'whereNotBetween', 'orWhereNotBetween', 'whereIn', 'orWhereIn', 'whereNotIn', 'orWhereNotIn', 'whereNull', 'orWhereNull', 'whereDate', 'whereDay', 'whereMonth', 'whereYear', 'groupBy', 'having', 'orHaving'];

    public function __construct()
    {
        foreach(static::$table_map as $table_name => $val){
            if( empty($val['primary_key']) ){
                throw new ApiDbBuilderError("{$table_name} has empty primary_key");
            }
            if( isset($val['model']) ){
                $tmp = new $val['model'];
                self::_bindModelToTable($table_name, $tmp);
            } else {
                self::_bindModelToTable($table_name, null);
            }
        }
    }

    private static function _bindModelToTable($table_name, DbMiddleWare $model)
    {
        self::$table_map[$table_name]['model'] = $model;
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
            throw new ApiParamsError('table name not empty');
        }
        $table_name = strtolower($table_name);
        if (empty(static::$table_map[$table_name])) {
            throw new ApiParamsError('table name not allowed');
        }
        $tmp = explode('.', $table_name);
        $table = DbHelper::initDb()->connection($tmp[0])->table($tmp[1]);
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
            throw new ApiParamsError('table name not allowed');

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
            throw new ApiParamsError('table name not allowed');

        }
        return isset(static::$table_map[$table_name]) ? static::$table_map[$table_name] : $default;
    }

    public static function allowSortField($table_name, $order_field)
    {
        if (empty($order_field)) {
            throw new ApiParamsError('order field empty');
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
     * Update a record in the database.
     *
     * @param array $values
     * @param array $queries
     * @return int
     */
    public function update(array $values, array $queries = [])
    {
        $table = $this->builder($queries);
        $primary_key = strtolower(self::getTableMapConfig($this->_current_table, 'primary_key', 'id'));
        unset($values[$primary_key]);

        $rst = !empty($values) ? $table->update($values) : 0;

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
     * @return int
     */
    public function delete(array $queries = [])
    {
        $table = $this->builder($queries);
        $rst = $table->delete();

        self::$detail_log && self::debugArgs(func_get_args(), __METHOD__, __CLASS__, __LINE__);
        self::$detail_log && self::debugResult($rst, __METHOD__, __CLASS__, __LINE__);
        return $rst;
    }

    /**
     * Insert a new record into the database.
     *
     * @param  array $values
     * @return bool
     */
    public function insert(array $values)
    {
        $table = $this->builder();
        $primary_key = strtolower(self::getTableMapConfig($this->_current_table, 'primary_key', 'id'));
        unset($values[$primary_key]);
        $rst = !empty($values) ? $table->insert($values) : false;

        self::$detail_log && self::debugArgs(func_get_args(), __METHOD__, __CLASS__, __LINE__);
        self::$detail_log && self::debugResult($rst, __METHOD__, __CLASS__, __LINE__);
        return $rst;
    }

    /**
     * Insert a new record and get the value of the primary key.
     *
     * @param  array $values
     * @param  string $sequence
     * @return int
     */
    public function insertGetId(array $values, $sequence = null)
    {
        $table = $this->builder();
        $rst = $table->insertGetId($values, $sequence);

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

}