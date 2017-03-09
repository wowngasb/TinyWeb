<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/9 0009
 * Time: 15:22
 */

namespace app\api\OrmDao\Basic;



use TinyWeb\Base\BaseOrm;

class User  extends BaseOrm
{
    protected static $_tablename = 'basic_user';

    protected static function _fixItem($val)
    {
        if(!empty($val)){
            unset($val['password']);
        }
        return $val;
    }
}