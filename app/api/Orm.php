<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/9 0009
 * Time: 17:31
 */

namespace app\api;


use app\api\abstracts\BaseApi;
use app\common\Base\BaseDbModel;
use app\common\DbModels\BlogCategories;
use app\common\DbModels\BlogComments;
use app\common\DbModels\BlogNotifications;
use app\common\DbModels\BlogPostTag;
use app\common\DbModels\BlogPosts;
use app\common\DbModels\BlogTags;
use app\common\DbModels\TblUsers;
use TinyWeb\Application;
use TinyWeb\Exception\OrmStartUpError;
use TinyWeb\Plugin\CurrentUser;
use TinyWeb\Plugin\OrmTrait;

class Orm extends BaseApi
{
    protected static $detail_log = true;
    use OrmTrait;

    private static $table_map = [
        'blog_categories' => [
            'default_sort_column' => 'rank',  // 默认排序参数
            'sort' => ['user_id', 'cate_title', 'state', 'created_at', 'updated_at', 'delete_at'],
            'Model' => BlogCategories::class,
        ],
        'blog_comments' => [
            'default_sort_column' => 'created_at',  // 默认排序参数
            'sort' => ['user_id', 'post_id', 'comment_id', 'state', 'created_at', 'updated_at', 'delete_at'],
            'Model' => BlogComments::class,
        ],
        'blog_notifications' => [
            'default_sort_column' => 'created_at',  // 默认排序参数
            'sort' => ['user_id', 'post_id', 'state', 'read_at', 'created_at', 'updated_at', 'delete_at'],
            'Model' => BlogNotifications::class,
        ],
        'blog_post_tag' => [
            'default_sort_column' => 'created_at',  // 默认排序参数
            'sort' => ['post_id', 'tag_id', 'state', 'created_at', 'updated_at', 'delete_at'],
            'Model' => BlogPostTag::class,
        ],
        'blog_posts' => [
            'default_sort_column' => 'published_at',  // 默认排序参数
            'default_sort_direction' => 'desc',  // 默认排序方式 asc 升序 desc 降序
            'sort' => ['user_id', 'category_id', 'title', 'slug', 'view_count', 'state', 'created_at', 'updated_at', 'delete_at'],
            'Model' => BlogPosts::class,
        ],
        'blog_tags' => [
            'default_sort_column' => 'created_at',  // 默认排序参数
            'sort' => ['tag_name', 'state', 'created_at', 'updated_at', 'delete_at'],
            'Model' => BlogTags::class,
        ],
        'tbl_users' => [
            'sort' => ['nick', 'email', 'register_from', 'github_id', 'github_name', 'website', 'real_name', 'state', 'created_at', 'updated_at', 'delete_at'],
            'Model' => TblUsers::class,
        ],
    ];
    protected static $db_name = '';

    protected static function getMap(){
        return static::$table_map;
    }

    protected static function getDb(){
        return static::$db_name;
    }

    public function __construct($current_table = null, $current_db = null)
    {
        $current_db = is_null($current_db) ? Application::app()->getEnv('ENV_MYSQL_DB') : $current_db;
        $this->hookCurrentDb($current_db);
        if( !is_null($current_table) ){
            $this->hookCurrentTable($current_table);
        }

        foreach (static::$table_map as $table_name => &$val) {
            $val['primary_key'] = !isset($val['primary_key']) ? 'id' : $val['primary_key'];  // 主键默认为 id
            if (empty($val['primary_key'])) {
                throw new OrmStartUpError("table:{$table_name} has empty primary_key");
            }
            $val['Model'] = !isset($val['Model']) ? BaseDbModel::class : $val['Model'];  // 主键默认为 BaseDbModel
            if (empty($val['Model'])) {
                throw new OrmStartUpError("{$table_name} has empty Model");
            }
            self::setTableModel($table_name, new $val['Model']);
            $val['default_sort_column'] = isset($val['default_sort_column']) ? $val['default_sort_column'] : $val['primary_key'];
            $val['default_sort_direction'] = isset($val['default_sort_direction']) ? $val['default_sort_direction'] : 'asc';
            $val['default_sort_direction'] = $val['default_sort_direction'] == 'desc' ? 'desc' : 'asc';
        }
        $user = new CurrentUser();
        $this->hookCurrentUser($user);
    }

    public function hookAccessAndFilterRequest(array $request, array $origin_request)
    {
        $request = parent::hookAccessAndFilterRequest($request, $origin_request);  //调用父级过滤函数
        $table_name = isset($origin_request['table']) ? $origin_request['table'] : '';
        $this->hookCurrentTable($table_name);
        $user = new CurrentUser();
        $this->hookCurrentUser($user);
        return $request;  //直接返回请求参数
    }
}