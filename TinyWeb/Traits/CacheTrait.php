<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/12 0012
 * Time: 15:26
 */

namespace TinyWeb\Traits;

use phpFastCache\CacheManager;
use TinyWeb\Application;

trait CacheTrait
{
    protected static $_REDIS_DEFAULT_EXPIRES = 300;
    protected static $_REDIS_PREFIX_CACHE = 'BMCache';

    /**
     * 使用redis缓存函数调用的结果 优先使用缓存中的数据
     * @param string $method 所在方法 方便检索
     * @param string $tag redis 缓存tag 表示分类
     * @param \Closure $func 获取结果的调用 没有任何参数  需要有返回结果
     * @param \Closure $filter 判断结果是否可以缓存的调用 参数为 $func 的返回结果 返回值为bool
     * @param int $timeCache 允许的数据缓存时间 为0 表示强制刷新缓存 默认为300 负数表示清空缓存 不执行调用
     * @param bool $is_log 是否显示日志
     * @param string $prefix 缓存键 的 前缀
     * @param array $tags 标记数组
     * @return array
     * @throws \phpFastCache\Exceptions\phpFastCacheDriverCheckException
     */
    protected static function  _cacheDataByRedis($method, $tag, \Closure $func, \Closure $filter, $timeCache = null, $is_log = false, $prefix = null, array $tags = [])
    {
        if (empty($tag) || empty($method)) {
            return $func();
        }
        if (is_null($prefix)) {
            $prefix = static::$_REDIS_PREFIX_CACHE;
        }
        if (is_null($timeCache)) {
            $timeCache = static::$_REDIS_DEFAULT_EXPIRES;
        }
        $method = str_replace('::', '.', $method);
        $now = time();
        $timeCache = intval($timeCache);

        $mCache = CacheManager::getInstance('predis', [
            'host' => Application::getInstance()->getEnv('ENV_REDIS_HOST'),
            'port' => Application::getInstance()->getEnv('ENV_REDIS_PORT', 6379),
            'password' => Application::getInstance()->getEnv('ENV_REDIS_PASS', null),
            'database' => Application::getInstance()->getEnv('ENV_REDIS_DB', null),
        ]);

        $rKey = !empty($prefix) ? "{$prefix}:{$method}?{$tag}" : "{$method}?{$tag}";

        if (empty($mCache)) {
            LogTrait::error("CacheManager getInstance error", __METHOD__, __CLASS__, __LINE__);
            return $func();
        } else if ($timeCache < 0) {
            $mCache->deleteItem($rKey);
            return [];
        } else if ($timeCache > 0) {
            $itemObj = $mCache->getItem($rKey);
            $val = $itemObj->get();
            $val = !empty($val) ? $val : [];
            $val['_update_'] = isset($val['_update_']) ? $val['_update_'] : $now;
            if (isset($val['data']) && $now - $val['_update_'] < $timeCache) {
                $log_msg = "cached now:{$now}, method:{$method}, tag:{$tag}, timeCache:{$timeCache}, _update_:{$val['_update_']}";
                $is_log && LogTrait::debug($log_msg, __METHOD__, __CLASS__, __LINE__);
                return $val['data'];
            }
        }

        $val = ['_update_' => $now, 'data' => $func()];
        if ($filter($val['data'])) {
            if (($timeCache > 0)) {
                $itemObj = $mCache->getItem($rKey);
                $itemObj->set($val)->expiresAfter($timeCache)->setTags($tags);
                $mCache->save($itemObj);
            } else {
                $mCache->deleteItem($rKey);
            }
            if ($is_log) {
                $log_msg = "cache func now:{$now}, method:{$method}, tag:{$tag}, timeCache:{$timeCache}, _update_:{$val['_update_']}";
                $status = $mCache->getStats();
                LogTrait::debug($log_msg . ",status:" . json_encode($status), __METHOD__, __CLASS__, __LINE__);
            }
        } else {
            if ($is_log) {
                $log_msg = "filter skip now:{$now}, method:{$method}, tag:{$tag}, timeCache:{$timeCache}, _update_:{$val['_update_']}";
                $status = $mCache->getStats();
                LogTrait::debug($log_msg . ",status:" . json_encode($status), __METHOD__, __CLASS__, __LINE__);
            }
        }

        return $val['data'];
    }

}