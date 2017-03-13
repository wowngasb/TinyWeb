<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/12 0012
 * Time: 15:26
 */

namespace TinyWeb\Traits;


use TinyWeb\Base\BaseOrm;
use TinyWeb\Exception\OrmStartUpError;
use TinyWeb\Helper\RedisHelper;

trait CacheTrait
{
    protected static $_time_cache = 30000;

    private static $_cache_dict = [];

    /**
     * @return BaseOrm
     * @throws OrmStartUpError
     */
    protected static function getOrm(){
        if( true ){  //
            throw new OrmStartUpError('must overwrite getOrm to get BaseOrm');
        }
        return null;
    }

    /**
     * 使用redis缓存函数调用的结果 优先使用缓存中的数据
     * @param string $method 所在方法 方便检索
     * @param string $tag redis 缓存tag 表示分类
     * @param \Closure $func 获取结果的调用 没有任何参数  需要有返回结果
     * @param \Closure $filter 判断结果是否可以缓存的调用 参数为 $func 的返回结果 返回值为bool
     * @param int $timeCache 允许的数据缓存时间 为0 表示强制刷新缓存 默认为300 负数表示清空缓存 不执行调用
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
        $timeCache = intval($timeCache);
        $redis = RedisHelper::getInstance();
        $rKey = "BMCache:{$method}:{$tag}";

        if (empty($redis)) {
            LogTrait::error("redis getInstance error", __METHOD__, __CLASS__, __LINE__);
            return $func();
        } else if ($timeCache < 0) {
            $redis->del($rKey);
            return [];
        } else if ($timeCache > 0) {
            $json_str = $redis->get($rKey);
            $json = !empty($json_str) ? json_decode($json_str, true) : [];
            $json['_update_'] = isset($json['_update_']) ? $json['_update_'] : $now;
            if( isset($json['data']) && $now - $json['_update_'] < $timeCache ){
                $log_msg = "cached now:{$now}, method:{$method}, tag:{$tag}, timeCache:{$timeCache}, _update_:{$json['_update_']}";
                $is_log && LogTrait::debug($log_msg, __METHOD__, __CLASS__, __LINE__);
                return $json['data'];
            }
        }

        $json = ['_update_' => $now, 'data' => $func() ];
        if ($filter($json['data'])) {
            ($timeCache > 0) ? $redis->setex($rKey, $timeCache, json_encode(['data' => $json['data'], '_update_' => $now])) : $redis->del($rKey);
            $log_msg = "cache func now:{$now}, method:{$method}, tag:{$tag}, timeCache:{$timeCache}, _update_:{$json['_update_']}";
        } else {
            $log_msg = "filter skip now:{$now}, method:{$method}, tag:{$tag}, timeCache:{$timeCache}, _update_:{$json['_update_']}";
        }
        $is_log && LogTrait::debug($log_msg, __METHOD__, __CLASS__, __LINE__);
        return $json['data'];
    }

    public static function __callStatic($name, $arguments)
    {
        return static::getFiledById($name, $arguments[0]);
    }

    public static function getFiledById($name, $id)
    {
        $info = self::getDataById($id);
        return isset($info[$name]) ? $info[$name] : '';
    }

    public static function getDataById($id, $timeCache = null)
    {
        if(is_null($timeCache)){
            $timeCache = self::$_time_cache;
        }
        $id = intval($id);
        if ($id <= 0) {
            return [];
        }
        if (isset(self::$_cache_dict[$id])) {
            return self::$_cache_dict[$id];
        }
        $data = self::_cacheDataByRedis(__METHOD__, "id[{$id}]", function()use($id){
            $tmp = static::getOrm()->getItem($id);
            return self::_fixData($tmp);
        }, function($data){
            return !empty($data);
        }, $timeCache);

        if(!empty($data)){
            self::$_cache_dict[$id] = $data;
        }
        return $data;
    }

    public static function setDataById($id, array $data){
        $id = intval($id);
        if ($id <= 0) {
            return [];
        }
        if(!empty($data)){
            static::getOrm()->setItem($id, $data);
        }
        return self::getDataById($id, 0);
    }

    protected static function _fixData($data){
        return $data;
    }
}