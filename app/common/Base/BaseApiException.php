<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/9 0009
 * Time: 11:47
 */

namespace app\common\Base;


use Exception;

abstract class BaseApiException extends BaseException
{

    public static $errno;

    public function __construct($message, Exception $previous = null)
    {
        parent::__construct($message, static::$errno, $previous);
    }


}