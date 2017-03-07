<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/7 0007
 * Time: 11:40
 */

namespace app\common\Dao;


use app\common\Base\BaseOrmModel;

class VlssSceneTemplateDao extends BaseOrmModel
{
    protected static $_tablename = 'vlss_scene_template';
    protected $_primary_key = 'template_id';

    protected static function _fixItem($val)
    {
        $val['switch_config'] = !empty($val['switch_config']) ? json_decode($val['switch_config'], true) : [];
        return $val;
    }
}