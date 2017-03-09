<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/9 0009
 * Time: 15:39
 */

namespace app\api\Conf;


use app\api\OrmDao\Basic\User;
use TinyWeb\Base\BaseModel;

class UserConf extends BaseModel
{
    const TIME_CACHE = 30000;
    private static $_user_dict = [];

    public static function __callStatic($name, $arguments)
    {
        return self::getFiledById($name, $arguments[0]);
    }

    public static function getFiledById($name, $id)
    {
        $info = self::getDataById($id);
        return isset($info[$name]) ? $info[$name] : '';
    }

    public static function getDataById($id, $timeCache = self::TIME_CACHE)
    {
        $id = intval($id);
        if ($id <= 0) {
            return [];
        }
        if (isset(self::$_user_dict[$id])) {
            return self::$_user_dict[$id];
        }
        $data = self::_cacheDataByRedis(__METHOD__, "user[{$id}]", function()use($id){
            $tmp = User::getItem($id);
            return self::_fixData($tmp);
        }, function($data){
            return !empty($data);
        }, $timeCache);

        if(!empty($data)){
            self::$_user_dict[$id] = $data;
        }
        return $data;
    }

    public static function setDataById($id, array $data){
        $id = intval($id);
        if ($id <= 0) {
            return [];
        }
        if(!empty($data)){
            User::setItem($id, $data);
        }
        return self::getDataById($id, 0);
    }

    public static function _fixData($data){
        return $data;
    }

    public static function login_name($id)
    {
        return self::getFiledById('login_name', $id);
    }

    public static function email($id)
    {
        return self::getFiledById('email', $id);
    }

    public static function telephone($id)
    {
        return self::getFiledById('telephone', $id);
    }

    public static function access_id($id)
    {
        return self::getFiledById('access_id', $id);
    }

    public static function access_key($id)
    {
        return self::getFiledById('access_key', $id);
    }

    public static function aodian_uin($id)
    {
        return self::getFiledById('aodian_uin', $id);
    }

    public static function dms_sub_key($id)
    {
        return self::getFiledById('dms_sub_key', $id);
    }

    public static function dms_pub_key($id)
    {
        return self::getFiledById('dms_pub_key', $id);
    }

    public static function dms_s_key($id)
    {
        return self::getFiledById('dms_s_key', $id);
    }

    public static function state($id)
    {
        return self::getFiledById('state', $id);
    }
}