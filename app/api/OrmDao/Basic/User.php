<?php

namespace app\api\OrmDao\Basic;

use TinyWeb\Application;
use TinyWeb\Base\BaseOrm;
use TinyWeb\Exception\OrmParamsError;

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