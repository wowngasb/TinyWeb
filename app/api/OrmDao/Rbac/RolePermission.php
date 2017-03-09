<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/9 0009
 * Time: 15:31
 */

namespace app\api\OrmDao\Rbac;


use TinyWeb\Base\BaseOrm;

class RolePermission  extends BaseOrm
{

    protected static $_tablename = 'rbac_role_permission';
}