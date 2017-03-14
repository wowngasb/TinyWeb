<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/9 0009
 * Time: 15:24
 */

namespace app\api\OrmDao\Vlss;


use TinyWeb\Base\BaseOrm;

class VlssSceneItem extends BaseOrm
{
    protected static $_table_name = 'vlss_scene_item';

    protected static function _fixItem($val)
    {
        if(!empty($val)){
            $val['scene_config'] = !empty($val['scene_config']) ? json_decode($val['scene_config'], true) : [];
        }
        return $val;
    }
}