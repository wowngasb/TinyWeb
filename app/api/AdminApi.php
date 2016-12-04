<?php
namespace app\api;

use app\common\Base\BaseApiModel;
use app\common\Models\CurrentUser;
use TinyWeb\Exception\ApiParamsError;
use TinyWeb\Exception\Error;
use TinyWeb\Helper\DbHelper;

class AdminApi extends BaseApiModel
{

    protected static $detail_log = true;

    public function __construct()
    {
        parent::__construct();
    }

    protected static function validName($name)
    {
        if (!preg_match("/^[0-9a-zA-Z]{6,18}$/i", $name)) {
            throw new ApiParamsError('用户名必须是6-18位数字字母组合');
        }
    }

    public function testDb()
    {
        return (new Orm())->hookCurrent('blog_posts')->get(0, 20, [], ['*'], [
        ]);
    }

    public function testDbList()
    {
        return (new Orm())->hookCurrent('blog_posts')->lists('slug');
    }

    public function testDbPluck()
    {
        return (new Orm())->hookCurrent('blog_posts')->pluck('slug');
    }


    public function testDbOther($db, $table)
    {
        return DbHelper::table($table, $db)->first();
    }

    public function testDb3()
    {
        return  (new Orm())->hookCurrent('blog_posts')->first(['slug', 'title', 'view_count',]);
    }

    public function testDbJson()
    {
        $user = new CurrentUser();
        return  (new Orm())->hookCurrentDb('tiny')->hookCurrentTable('blog_posts')->hookCurrentUser($user)->first(['slug', 'title', 'view_count'], [
            ['state', 1],
            ['view_count', '>', 100],
        ]);
    }

    public function testDbJsonAttach()
    {
        $user = new CurrentUser();
        return  (new Orm())->hookCurrentDb('tiny')->hookCurrentTable('blog_posts')->hookCurrentUser($user)->first(['slug', 'title', 'view_count', 'category', 'tags', 'user'], [
            ['state', 1],
            ['view_count', '>', 100],
        ]);
    }

    /**
     * @param string $name
     * @param int $id
     * @return array
     * @throws ApiParamsError
     */
    public function testApiFirst($name, $id = 123)
    {
        try {
            self::validName($name);
        } catch (Error $ex) {
            throw new ApiParamsError('Validation Failed', $ex);
        }

        if ($id <= 0) {
            throw new ApiParamsError('id必须大于0');
        } else {
            $rst = ['name' => $name, 'id' => $id];
            $log_msg = "args:" . json_encode($rst);
            self::info($log_msg, __METHOD__, __CLASS__, __LINE__);
        }

        self::$detail_log && self::debugArgs(func_get_args(), __METHOD__, __CLASS__, __LINE__);
        self::$detail_log && self::debugResult($rst, __METHOD__, __CLASS__, __LINE__);
        return $rst;
    }

}
