<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/6 0006
 * Time: 17:28
 */

namespace app\api\GraphQL\Vlss\Data;

use GraphQL\Utils;
use TinyWeb\Base\BaseOrm;

class App extends BaseOrm
{
    public $id;   #虚拟演播厅自增id
    public $vlss_name;  #演播厅名字
    public $lcps_host;   #导播台域名  不带http://前缀 和 结尾/
    public $create_time;   #记录创建时间
    public $uptime;   #更新时间

    protected static $_tablename = 'vlss_app';

    protected static function _fixItem($val)
    {
        unset($val['password']);
        return $val;
    }
}
