<?php
namespace app\api;

use TinyWeb\Base\BaseApi;
use TinyWeb\Exception\ApiParamsError;
use TinyWeb\Exception\Error;
use TinyWeb\Helper\DbHelper;

class UserMgr extends BaseApi
{

    protected static function validName($name)
    {
        if (!preg_match("/^[0-9a-zA-Z]{6,18}$/i", $name)) {
            throw new ApiParamsError('用户名必须是6-18位数字字母组合');
        }
    }

}
