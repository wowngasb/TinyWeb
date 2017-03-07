<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/7 0007
 * Time: 16:58
 */

namespace app\api;

use app\common\Base\BaseApiModel;
use app\common\Dao\VlssAppDao;
use app\common\Dao\VlssSceneGroupDao;
use app\common\Dao\VlssSceneItemDao;

class VlssMgr extends BaseApiModel
{

    public function __construct()
    {
        parent::__construct();
    }

    public function _getApp($vlss_id, $timeCache = 300)
    {
        $app = self::_cacheDataByRedis(__METHOD__, "vlss_id[{$vlss_id}]", function () use ($vlss_id) {
            return VlssAppDao::instance()->getItem($vlss_id);
        }, function ($data) {
            return !empty($data);
        }, $timeCache);

        return $app;
    }

    public function _getGroup($group_id, $timeCache = 300)
    {
        $group = self::_cacheDataByRedis(__METHOD__, "group_id[{$group_id}]", function () use ($group_id) {
            return VlssSceneGroupDao::instance()->getItem($group_id);
        }, function ($data) {
            return !empty($data);
        }, $timeCache);

        return $group;
    }

    public function _getScene($scene_id, $timeCache = 300)
    {
        $scene = self::_cacheDataByRedis(__METHOD__, "scene_id[{$scene_id}]", function () use ($scene_id) {
            return VlssSceneItemDao::instance()->getItem($scene_id);
        }, function ($data) {
            return !empty($data);
        }, $timeCache);

        return $scene;
    }

    public function _getTemplate($template_id, $timeCache = 300)
    {
        $template = self::_cacheDataByRedis(__METHOD__, "template_id[{$template_id}]", function () use ($template_id) {
            return VlssAppDao::instance()->getItem($template_id);
        }, function ($data) {
            return !empty($data);
        }, $timeCache);

        return $template;
    }

}