<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/7 0007
 * Time: 11:39
 */

namespace app\common\Dao;


use app\common\Base\BaseOrmModel;

class VlssSceneGroupDao extends BaseOrmModel
{
    protected static $_tablename = 'vlss_scene_group';
    protected $_primary_key = 'group_id';

    protected static function _fixItem($val)
    {
        return $val;
    }
}