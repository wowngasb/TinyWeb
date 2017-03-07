<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/7 0007
 * Time: 11:40
 */

namespace app\common\Dao;


use app\common\Base\BaseOrmModel;

class VlssSceneItemDao extends BaseOrmModel
{
    protected static $_tablename = 'vlss_scene_item';
    protected $_primary_key = 'scene_id';

    protected static function _fixItem($val)
    {
        $val['scene_config'] = !empty($val['scene_config']) ? json_decode($val['scene_config'], true) : [];
        return $val;
    }
}