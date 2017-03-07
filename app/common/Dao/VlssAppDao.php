<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/7 0007
 * Time: 11:39
 */

namespace app\common\Dao;


use app\common\Base\BaseOrmModel;

class VlssAppDao extends BaseOrmModel
{
    protected $_tablename = 'vlss_app';
    protected $_primary_key = 'vlss_id';

    protected static function _fixItem($val)
    {
        unset($val['password']);
        return $val;
    }
}