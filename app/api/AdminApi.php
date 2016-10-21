<?php
namespace app\api;

use app\api\abstracts\BaseApi;
use app\Bootstrap;
use app\common\Base\BaseException;
use TinyWeb\Exception\ApiParamsError;
use TinyWeb\Helper\DbHelper;

class AdminApi extends BaseApi
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
        return (new Orm('blog_tags'))->get(0, 20, [], ['*'], [
        ]);
    }

    public function testDb2()
    {
        return (new Orm('blog_posts'))->lists('slug');
    }

    public function testDbOther($db, $table)
    {
        return DbHelper::table($table, $db)->first();
    }

    public function testDb3()
    {
        Bootstrap::_D('in api', __METHOD__);
        $orm = new Orm('blog_posts');
        Bootstrap::_D('new orm', __METHOD__);

        return $orm->first(['slug', 'title', 'view_count', 'category', 'tags', 'user'], [
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
        } catch (BaseException $ex) {
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
