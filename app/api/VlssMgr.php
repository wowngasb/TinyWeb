<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/7 0007
 * Time: 16:58
 */

namespace app\api;

use app\api\OrmDao\Vlss\SceneGroup;
use TinyWeb\Base\BaseApi;

class VlssMgr extends BaseApi
{
    const TIME_CACHE = 300;

    public function getApp($vlss_id)
    {
        $app = SceneGroup::getItem($vlss_id);
        return ['app' => $app];
    }

}