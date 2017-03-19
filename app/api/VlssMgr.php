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
use phpFastCache\CacheManager;
use TinyWeb\Application;
use TinyWeb\Base\BaseApi;

use Predis\Client as PredisClient;

class VlssMgr extends BaseApi
{
    const TIME_CACHE = 300;

    public function getApp($id)
    {
        $app = VlssApp::getDataById($id);
        return ['app' => $app];
    }

    public function testRedis()
    {

        $test = new PredisClient([
            'host' => Application::getInstance()->getEnv('ENV_REDIS_HOST'),
            'port' => Application::getInstance()->getEnv('ENV_REDIS_PORT', 6379),
            'password' => Application::getInstance()->getEnv('ENV_REDIS_PASS', null),
            'database' => Application::getInstance()->getEnv('ENV_REDIS_DB', null),
        ]);
        $key = 'test';
        $test->set($key, '23123');
        return [$key => $test->get($key)];
    }

    public function testCache()
    {
        $test = CacheManager::getInstance('predis', [
            'host' => Application::getInstance()->getEnv('ENV_REDIS_HOST'),
            'port' => Application::getInstance()->getEnv('ENV_REDIS_PORT', 6379),
            'password' => Application::getInstance()->getEnv('ENV_REDIS_PASS', null),
            'database' => Application::getInstance()->getEnv('ENV_REDIS_DB', null),
        ]);

        $key = '12312312';
        $item = $test->getItem($key);
        $item->set('234234234')->expiresAfter(5000);
        $test->save($item);

        return [$key => $test->getItem($key)];
    }

    public function setSceneItemSort($id, $scene_sort)
    {
        return [
            'update' => VlssSceneItem::setDataById($id, [
                'scene_sort' => $scene_sort,
            ]),
        ];
    }

    public function testSum($a = 1, $b = 2)
    {
        $s_time = microtime(true);

        $sum = self::_cacheDataByRedis(__METHOD__, "a={$a}&b={$b}", function () use ($a, $b) {
            sleep(1);
            return $a + $b;
        }, function ($data) {
            return isset($data);
        });

        $time = microtime(true) - $s_time;
        return [
            'a' => $a,
            'b' => $b,
            'sum' => $sum,
            'use' => $use_str = round($time * 1000, 2) . 'ms',
        ];
    }
}