<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/10 0010
 * Time: 9:58
 */

namespace app\common\Exceptions;


use app\common\Base\BaseApiException;

class ApiDbBuilderError extends BaseApiException
{
    public static $errno = -600;
}