<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/9 0009
 * Time: 11:01
 */

namespace app\api\OrmDao;


use app\common\Base\BaseOrmModel;

class VlssSceneTemplateDao extends  BaseOrmModel
{
    protected static $_tablename = 'vlss_scene_template';

    protected static function _fixItem($val)
    {
        $val['switch_config'] = !empty($val['switch_config']) ? json_decode($val['switch_config'], true) : [];
        return $val;
    }
}