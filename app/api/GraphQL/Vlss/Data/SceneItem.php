<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/6 0006
 * Time: 17:37
 */

namespace app\api\GraphQL\Vlss\Data;


use app\common\Base\BaseOrmModel;
use GraphQL\Utils;

class SceneItem extends BaseOrmModel
{
     public $scene_id;
    public $vlss_id;   #虚拟演播厅id
    public $group_id;   #所属场景组id
    public $scene_name;   #场景名称
    public $scene_config;  #场景配置 格式为 json 字符串
    public $scene_type;   #场景类型
    public $scene_sort;   #场景叠加排序
    public $state;   #1正常,2隐藏,9删除
    public $create_time;   #记录创建时间
    public $uptime;   #更新时间

    protected static $_tablename = 'vlss_scene_item';

    protected static function _fixItem($val)
    {
        $val['scene_config'] = !empty($val['scene_config']) ? json_decode($val['scene_config'], true) : [];
        return $val;
    }

}