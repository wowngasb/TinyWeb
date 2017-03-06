<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/17 0017
 * Time: 16:14
 */

namespace app\common\Base;


use TinyWeb\Helper\DbHelper;

class BaseOrmModel extends BaseModel
{

    public static $__tablename__ = '';

    public static function getItem($filed, $value){
        return DbHelper::table(static::$__tablename__)->where(strtolower($filed), $value)->first();
    }

}