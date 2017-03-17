<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/7 0007
 * Time: 16:58
 */

namespace app\api;

use app\api\GraphQL\VlssApp;
use app\api\GraphQL\VlssSceneItem;
use TinyWeb\Base\BaseApi;

class VlssMgr extends BaseApi
{
    const TIME_CACHE = 300;

    public function getApp($id)
    {
        $app = VlssApp::getItem($id);
        return ['app' => $app];
    }

    public function setSceneItemSort($id, $scene_sort){
        return [
            'update' => VlssSceneItem::setDataById($id, [
                'scene_sort' => $scene_sort,
            ]),
        ];
    }

}