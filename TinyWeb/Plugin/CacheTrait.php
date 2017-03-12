<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/12 0012
 * Time: 15:26
 */

namespace TinyWeb\Plugin;


use TinyWeb\Helper\RedisHelper;

trait CacheTrait
{
    /**
     * 使用redis缓存函数调用的结果 优先使用缓存中的数据
     * @param string $method 所在方法 方便检索
     * @param string $tag redis 缓存tag 表示分类
     * @param \Closure $func 获取结果的调用 没有任何参数  需要有返回结果
     * @param \Closure $filter 判断结果是否可以缓存的调用 参数为 $func 的返回结果 返回值为bool
     * @param int $timeCache 允许的数据缓存时间 为0 表示不使用缓存 默认为300
     * @param bool $is_log 是否显示日志
     * @return array|null
     */
    protected static function  _cacheDataByRedis($method, $tag, \Closure $func, \Closure $filter, $timeCache = 300, $is_log = false)
    {
        if (empty($tag) || empty($method)) {
            return $func();
        }
        $method = str_replace('\\', '_', $method);
        $method = str_replace('::', '@', $method);
        $now = time();
        $timeCache = intval($timeCache) > 0 ? intval($timeCache) : 0;
        $redis = RedisHelper::getInstance();
        if(empty($redis)){
            LogTrait::error("redis getInstance error", __METHOD__, __CLASS__, __LINE__);
            return  $func();
        }
        $rKey = "BMCache:{$method}:{$tag}";
        $json = ['_update_' => $now];
        $data = [];
        if ($timeCache > 0) {
            $json_str = $redis->get($rKey);
            $json = !empty($json_str) ? json_decode($json_str, true) : [];
            $json['_update_'] = isset($json['_update_']) ? $json['_update_'] : 0;
            $data = isset($json['data']) ? $json['data'] : [];
        }
        if ($timeCache == 0 || !isset($json['data']) || $now - $json['_update_'] > $timeCache) {
            $data = $func();
            $is_cache = $filter($data);
            if ($is_cache) {
                if($timeCache > 0){
                    $redis->setex($rKey, $timeCache, json_encode(['data' => $data, '_update_' => $now]));
                } else {
                    $redis->del($rKey);
                }
                $log_msg = "call func now:{$now}, method:{$method}, tag:{$tag}, timeCache:{$timeCache}, _update_:{$json['_update_']}";
            } else {
                $log_msg = "call func filter now:{$now}, method:{$method}, tag:{$tag}, timeCache:{$timeCache}, _update_:{$json['_update_']}";
            }
            $is_log && LogTrait::debug($log_msg, __METHOD__, __CLASS__, __LINE__);
        } else {
            $log_msg = "cached now:{$now}, method:{$method}, tag:{$tag}, timeCache:{$timeCache}, _update_:{$json['_update_']}";
            $is_log && LogTrait::debug($log_msg, __METHOD__, __CLASS__, __LINE__);
        }

        return $data;
    }
}