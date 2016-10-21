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
use app\common\Models\CurrentUser;
use TinyWeb\Application;
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
            'attach' => [
                'tag' => [
                    'uri' => '/api/Orm/blog_tags.getItem',
                    'params' => [
                        'id' => '%tag_id%'
                    ],
                ],
            ],
        ],
        'blog_posts' => [
            'default_sort_column' => 'published_at',  // 默认排序参数
            'default_sort_direction' => 'desc',  // 默认排序方式 asc 升序 desc 降序
            'unique_keys' => ['slug'],
            'sort' => ['user_id', 'category_id', 'title', 'slug', 'view_count', 'state', 'created_at', 'updated_at', 'delete_at'],
            'Model' => BlogPosts::class,
            'attach' => [
                'category' => [
                    'uri' => '/api/Orm/blog_categories.getItem',
                    'params' => [
                        'id' => '%category_id%'
                    ],
                ],
                'tags' => [
                    'uri' => '/api/Orm/blog_post_tag.lists',
                    'params' => [
                        'column' => ['tag'],
                        'queries' => ['post_id', '%id%']
                    ],
                ],
                'user' => [
                    'uri' => '/api/Orm/tbl_users.getItem',
                    'params' => [
                        'id' => '%user_id%'
                    ],
                ],
            ],
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
    private static $_has_pre_treatment = false;

    protected static function getMap(){
        return self::$table_map;
    }

    public function __construct($current_table = null, $current_db = null, $default_model = BaseDbModel::class)
    {
        $current_db = is_null($current_db) ? Application::app()->getEnv('ENV_MYSQL_DB') : $current_db;
        $this->hookCurrentDb($current_db);
        if( !is_null($current_table) ){
            $this->hookCurrentTable($current_table);
        }
        if( !self::$_has_pre_treatment ){
            self::$table_map = self::preTreatmentMap(self::$table_map, $default_model);
            self::$_has_pre_treatment = true;
        }
        $user = new CurrentUser();
        $this->hookCurrentUser($user);
    }

    public function hookAccessAndFilterRequest(array $request, array $origin_request)
    {
        $request = parent::hookAccessAndFilterRequest($request, $origin_request);  //调用父级过滤函数
        $table_name = isset($origin_request['table']) ? $origin_request['table'] : null;
        if(!is_null($table_name)){
            $this->hookCurrentTable($table_name);
        }

        $user = new CurrentUser();
        $this->hookCurrentUser($user);
        return $request;  //直接返回请求参数
    }


}