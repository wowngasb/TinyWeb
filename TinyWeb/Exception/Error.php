<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/25 0025
 * Time: 11:32
 */

namespace TinyWeb\Exception;


use Exception;

class Error extends Exception
{
    public static $errno = 500;

    public function __construct($message, Exception $previous = null)
    {
        parent::__construct($message, static::$errno, $previous);
    }

}