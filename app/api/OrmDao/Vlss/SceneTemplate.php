<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/9 0009
 * Time: 15:23
 */

namespace app\api\OrmDao\Vlss;


use TinyWeb\Base\BaseOrm;

class SceneTemplate extends BaseOrm
{
    protected static $_table_name = 'vlss_scene_template';

    protected static function _fixItem($val)
    {
        if(!empty($val)) {
            $val['switch_config'] = !empty($val['switch_config']) ? json_decode($val['switch_config'], true) : [];
        }
        return $val;
    }
}