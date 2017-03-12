<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/7 0007
 * Time: 16:58
 */

namespace app\api;

use app\api\OrmDao\Vlss\App;
use app\api\OrmDao\Vlss\SceneGroup;
use app\api\OrmDao\Vlss\SceneItem;
use app\api\OrmDao\Vlss\SceneTemplate;
use TinyWeb\Base\BaseApi;

class VlssMgr extends BaseApi
{
    const TIME_CACHE = 300;


    public function test($vlss_id)
    {
        $app = $this->_getApp($vlss_id, 0);
        return ['vlssApp' => $app];
    }

    public function _getApp($id, $timeCache = self::TIME_CACHE)
    {
        return self::_cacheDataByRedis(__METHOD__, "App[{$id}]", function () use ($id) {
            return App::getItem($id);
        }, function ($data) {
            return !empty($data);
        }, $timeCache);
    }

    public function _setApp($id, $lcps_host, $vlss_name, $active_group_id, $active_template_id, $state){

    }

    public function _getSceneGroup($id, $timeCache = self::TIME_CACHE)
    {
        return self::_cacheDataByRedis(__METHOD__, "SceneGroup[{$id}]", function () use ($id) {
            return SceneGroup::getItem($id);
        }, function ($data) {
            return !empty($data);
        }, $timeCache);
    }

    public function _setSceneGroup($id, $group_name, $state){

    }

    public function _getSceneItem($id, $timeCache = self::TIME_CACHE)
    {
        return self::_cacheDataByRedis(__METHOD__, "SceneItem[{$id}]", function () use ($id) {
            return SceneItem::getItem($id);
        }, function ($data) {
            return !empty($data);
        }, $timeCache);
    }

    public function _setSceneItem($id, $scene_name, $scene_config, $scene_sort, $state){

    }

    public function _getSceneTemplate($id, $timeCache = self::TIME_CACHE)
    {
        return self::_cacheDataByRedis(__METHOD__, "SceneTemplate[{$id}]", function () use ($id) {
            return SceneTemplate::getItem($id);
        }, function ($data) {
            return !empty($data);
        }, $timeCache);
    }

    public function _setSceneTemplate($id, $template_name, $switch_config, $front_pic, $back_pic, $state){

    }

}