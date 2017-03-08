<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/6 0006
 * Time: 17:28
 */

namespace app\api\GraphQL\Vlss\Data;

use app\common\Base\BaseOrmModel;
use GraphQL\Utils;

class App extends BaseOrmModel
{
    public $vlss_id;   #虚拟演播厅自增id
    public $login_name;   #用户管理后台登录名
    public $password;   #用户管理后台登录名
    public $email;   #用户邮箱
    public $access_id;   #奥点云access_id
    public $access_key;   #奥点云access_key
    public $aodian_uin;   #奥点云 uin
    public $dms_sub_key;   #DMS sub_key
    public $dms_pub_key;   #DMS pub_key
    public $dms_s_key;   #DMS s_key
    public $lcps_host;   #导播台域名  不带http://前缀 和 结尾/
    public $active_group_id;   #激活的场景组id
    public $active_template_id;   #激活的场景模版id
    public $state;   #1正常，2冻结，9删除
    public $last_login_ip;   #用户上次登录ip
    public $login_count;   #用户管理后台登录次数 登陆一次+1
    public $create_time;   #记录创建时间
    public $uptime;   #更新时间

    protected static $_tablename = 'vlss_app';
    protected static $_primary_key = 'vlss_id';
    protected static $_max_select_item_counts = 50;

    protected static function _fixItem($val)
    {
        unset($val['password']);
        return $val;
    }
}
