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
     * @param string $key redis 缓存tag 表示分类
     * @param callable $func 获取结果的调用 没有任何参数  需要有返回结果
     * @param callable $filter 判断结果是否可以缓存的调用 参数为 $func 的返回结果 返回值为bool
     * @param int $timeCache 允许的数据缓存时间 0表示返回函数结果并清空缓存  负数表示不执行调用只清空缓存  默认为300
     * @param bool $is_log 是否显示日志
     * @param string $prefix 缓存键 的 前缀
     * @param array $tags 标记数组
     * @return array
     * @throws \phpFastCache\Exceptions\phpFastCacheDriverCheckException
     */
    protected static function _cacheDataByRedis($method, $key, callable $func, callable $filter, $timeCache = null, $is_log = false, $prefix = null, array $tags = [])
    {
        if (empty($key) || empty($method)) {
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

        $rKey = !empty($prefix) ? "{$prefix}:{$method}?{$key}" : "{$method}?{$key}";

        if (empty($mCache)) {
            self::_redisError("CacheManager getInstance error");
            return $func();
        } else if ($timeCache > 0) {  //判断缓存有效期是否在要求之内  数据符合要求直接返回  不再执行 func
            $val = $mCache->getItem($rKey)->get() ?: [];
            if (isset($val['data']) && isset($val['_update_']) && $now - $val['_update_'] < $timeCache) {
                $is_log && self::_redisDebug('cached', $now, $method, $key, $timeCache, $val['_update_'], $tags);
                return $val['data'];
            }
        }

        $val = ['_update_' => $now, 'data' => $timeCache >= 0 ? $func() : []];
        if ($timeCache > 0 && $filter($val['data'])) {   //需要缓存 且缓存世间大于0 保存数据并加上 tags
            $itemObj = $mCache->getItem($rKey)->set($val)->expiresAfter($timeCache);
            !empty($tags) && $itemObj->setTags($tags);
            $mCache->save($itemObj);
            $is_log && self::_redisDebug('cache', $now, $method, $key, $timeCache, $val['_update_'], $tags);
        } else {
            $is_log && self::_redisDebug('filter skip', $now, $method, $key, $timeCache, $val['_update_'], $tags);
        }

        if ($timeCache <= 0) {  //需要清除缓存并清除所有相关tags的缓存
            $mCache->deleteItem($rKey);
            !empty($tags) && $mCache->deleteItemsByTags($tags);
            $is_log && self::_redisDebug('delete', $now, $method, $key, $timeCache, $val['_update_'], $tags);
        }
        return $val['data'];
    }

    protected static function _redisDebug($action, $now, $method, $key, $timeCache, $update, $tags)
    {
        $log_msg = "{$action} now:{$now}, method:{$method}, key:{$key}, timeCache:{$timeCache}, _update_:{$update}";
        if (!empty($tags)) {
            $log_msg .= ", tags:[" . join(',', $tags) . ']';
        }
        LogTrait::debug($log_msg, __METHOD__, __CLASS__, __LINE__);
    }

    protected static function _redisError($log_msg)
    {
        LogTrait::debug($log_msg, __METHOD__, __CLASS__, __LINE__);
    }
}