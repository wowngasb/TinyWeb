<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/9 0009
 * Time: 13:33
 */

namespace app\common\Exceptions;


use app\common\Base\BaseApiException;
use Exception;

class ApiParamsError extends BaseApiException
{
    public static $errno = -2;

}