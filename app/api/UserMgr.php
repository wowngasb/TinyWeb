<?php
namespace app\api;

use TinyWeb\Base\BaseApi;
use TinyWeb\Exception\ApiParamsError;
use TinyWeb\Exception\Error;
use TinyWeb\Helper\DbHelper;

class UserMgr extends BaseApi
{

    protected static $detail_log = true;

    protected static function validName($name)
    {
        if (!preg_match("/^[0-9a-zA-Z]{6,18}$/i", $name)) {
            throw new ApiParamsError('用户名必须是6-18位数字字母组合');
        }
    }


    public function testDbOther($db, $table)
    {
        return DbHelper::table($table, $db)->first();
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
