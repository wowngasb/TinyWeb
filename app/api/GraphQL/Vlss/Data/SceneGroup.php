<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/6 0006
 * Time: 17:36
 */

namespace app\api\GraphQL\Vlss\Data;


use app\common\Base\BaseOrmModel;
use GraphQL\Utils;

class SceneGroup extends BaseOrmModel
{
    public $group_id;
    public $vlss_id;   #虚拟演播厅id
    public $group_name;   #场景组名称
    public $state;   #1正常，9删除
    public $create_time;   #记录创建时间
    public $uptime;  #更新时间

    protected static $_tablename = 'vlss_scene_group';

    protected static function _fixItem($val)
    {
        return $val;
    }
}