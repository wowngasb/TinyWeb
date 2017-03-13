<?php

namespace app\api\OrmDao\Basic;

use TinyWeb\Application;
use TinyWeb\Base\BaseOrm;
use TinyWeb\Exception\OrmParamsError;
use TinyWeb\Traits\CacheTrait;

class User extends BaseOrm
{
    protected static $_table_name = 'basic_user';

    protected static function _fixItem($val)
    {
        if (!empty($val)) {
            unset($val['password']);
        }
        return $val;
    }


    use CacheTrait;

    /**
     * @return BaseOrm
     */
    protected static function getOrm(){
        return static::instance();
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

    private static function _hashPassWord($password)
    {    //使用自定义的 hash 算法 防止彩虹表破解
        $secret_key = Application::instance()->getEnv('CRYPT_KEY', '');
        $tmp = strtolower(md5($password));
        $tmp = strtolower(md5($secret_key . $tmp));
        return strtolower(md5($tmp . $secret_key));
    }

    /**
     * @param int $id
     * @param string $password
     * @return bool
     */
    public static function testPassWord($id, $password)
    {
        $id = intval($id);
        if (empty($password) || $id <= 0) {
            return false;
        }
        $user = self::getItem($id);
        $tmp = self::_hashPassWord($password);
        return Application::strCmp($tmp, $user['password']);
    }

    public static function newItem(array $data)
    {
        if (empty($data['password'])) {
            throw new OrmParamsError('cannot create user without password');
        }
        $data['password'] = self::_hashPassWord($data['password']);
        return parent::newItem($data);
    }

    public static function setItem($id, array $data)
    {
        if (!empty($data['password'])) {
            $data['password'] = self::_hashPassWord($data['password']);
        } else {
            unset($data['password']);
        }
        return parent::setItem($id, $data);
    }
}