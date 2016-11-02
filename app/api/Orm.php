<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/9 0009
 * Time: 17:31
 */

namespace app\api;

use app\common\Base\BaseApiModel;
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

class Orm extends BaseApiModel
{
    protected static $detail_log = true;
    use OrmTrait;

    /**
     * 必须是一个 静态函数 函数体内不可使用$this
     * @return void
     */
    protected static function initOrm()
    {
        if( self::hasTableMap() ){
            return ;
        }
        $db_name = strtolower(Application::instance()->getEnv('ENV_MYSQL_DB'));
        self::initTableMap([
            "{$db_name}.blog_categories" => [
                'default_sort_column' => 'rank',  // 默认排序参数
                'sort' => ['user_id', 'cate_title', 'state', 'created_at', 'updated_at', 'delete_at'],
                'Model' => BlogCategories::class,
            ],
            "{$db_name}.blog_comments" => [
                'default_sort_column' => 'created_at',  // 默认排序参数
                'sort' => ['user_id', 'post_id', 'comment_id', 'state', 'created_at', 'updated_at', 'delete_at'],
                'Model' => BlogComments::class,
            ],
            "{$db_name}.blog_notifications" => [
                'default_sort_column' => 'created_at',  // 默认排序参数
                'sort' => ['user_id', 'post_id', 'state', 'read_at', 'created_at', 'updated_at', 'delete_at'],
                'Model' => BlogNotifications::class,
            ],
            "{$db_name}.blog_post_tag" => [
                'default_sort_column' => 'created_at',  // 默认排序参数
                'sort' => ['post_id', 'tag_id', 'state', 'created_at', 'updated_at', 'delete_at'],
                'Model' => BlogPostTag::class,
                'attach' => [
                    'tag' => [
                        'uri' => "/api/Orm/{$db_name}.blog_tags.getItem",
                        'params' => [
                            'id' => '%tag_id%'
                        ],
                    ],
                ],
            ],
            "{$db_name}.blog_posts" => [
                'default_sort_column' => 'published_at',  // 默认排序参数
                'default_sort_direction' => 'desc',  // 默认排序方式 asc 升序 desc 降序
                'unique_keys' => ['slug'],
                'sort' => ['user_id', 'category_id', 'title', 'slug', 'view_count', 'state', 'created_at', 'updated_at', 'delete_at'],
                'Model' => BlogPosts::class,
                'attach' => [
                    'category' => [
                        'uri' => "/api/Orm/{$db_name}.blog_categories.getItem",
                        'params' => [
                            'id' => '%category_id%'
                        ],
                    ],
                    'tags' => [
                        'uri' => "/api/Orm/{$db_name}.blog_post_tag.lists",
                        'params' => [
                            'column' => ['tag'],
                            'queries' => ['post_id', '%id%']
                        ],
                    ],
                    'user' => [
                        'uri' => "/api/Orm/{$db_name}.tbl_users.getItem",
                        'params' => [
                            'id' => '%user_id%'
                        ],
                    ],
                ],
            ],
            "{$db_name}.blog_tags" => [
                'default_sort_column' => 'created_at',  // 默认排序参数
                'sort' => ['tag_name', 'state', 'created_at', 'updated_at', 'delete_at'],
                'Model' => BlogTags::class,
            ],
            "{$db_name}.tbl_users" => [
                'sort' => ['nick', 'email', 'register_from', 'github_id', 'github_name', 'website', 'real_name', 'state', 'created_at', 'updated_at', 'delete_at'],
                'Model' => TblUsers::class,
            ],
        ]);
    }

    public function __construct()
    {
        parent::__construct();
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