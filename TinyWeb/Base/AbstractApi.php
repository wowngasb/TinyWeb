<?php

namespace TinyWeb\Base;

use TinyWeb\Helper\RedisHelper;
use TinyWeb\Traits\CacheTrait;
use TinyWeb\Traits\LogTrait;
use TinyWeb\Traits\RpcTrait;

abstract class AbstractApi extends AbstractContext {

    use LogTrait, RpcTrait, CacheTrait;

    /**
     * 过滤常见的 API参数  子类按照顺序依次调用父类此方法
     * @return array  处理后的 API 执行参数 将用于调用方法
     */
    public function beforeApi() {
        false && func_get_args();

        self::_apiLimitByTimeRange("ADMIN_KEY_", 60, 100);   //api 调用次数检查

    }

    /*
     * 不同的API会有不同的调用次数限制, 请检查返回 header 中的如下字段
     * header 字段	描述
     */
    public static function _apiLimitByTimeRange($api_key, $range_sec = 300, $max_num = 100, $tag = 'all')
    {
        $testRst = self::_apiLimitByTimeRangeTest($api_key, $range_sec, $max_num, $tag);
        if( empty($testRst) ){
            header("http/1.1 403 Forbidden");
            exit();
        }
        foreach ($testRst as $key => $val) {
            header("X-Rate-{$key}: {$val}");
        }
        /*
        header("X-Rate-LimitTag: {$tag}");  //限制规则分类 all 代表总数限制
        header("X-Rate-LimitNum: {$max_num}");  //限制调用次数，超过后服务器会返回 403 错误
        header("X-Rate-Remaining: {$remaining}");  //当时间段中还剩下的调用次数
        header("X-Rate-TimeRange: {$range_sec}");  //限制时间范围长度 单位 秒
        header("X-Rate-TimeReset: {$reset_date}");  //限制重置时间 unix time
        */
    }

    /**
     * API 调用次数限制
     * @param $api_key
     * @param int $range_sec
     * @param int $max_num
     * @param string $tag
     * @return array
     */
    public static function _apiLimitByTimeRangeTest($api_key, $range_sec = 300, $max_num = 100, $tag = 'all')
    {
        $max_num = intval($max_num);
        $range_sec = intval($range_sec);
        $range_sec = $range_sec > 0 ? $range_sec : 1;
        $max_num = $max_num > 0 ? $max_num : 1;
        $rKey = "BaseApiRateLimit:{$api_key}:api_count_{$tag}";
        $tKey = "BaseApiRateLimit:{$api_key}:api_reset_{$tag}";
        $redis = RedisHelper::getInstance();

        $check = $redis->exists($rKey);
        if ($check) {
            $redis->incr($rKey);
            $count = $redis->get($rKey);
            if ($count > $max_num) {
                return [];
            }
        } else {
            $redis->incr($rKey);
            $redis->expire($rKey, $range_sec);
            $redis->set($tKey, time() + $range_sec, $range_sec * 10);
        }
        $remaining = $max_num - $redis->get($rKey);
        $reset = $redis->get($tKey);
        $reset = !empty($reset) ? $reset : time() + $range_sec;
        $reset_date = gmdate('D, d M Y H:i:s T', $reset);

        return [
            'LimitTag' => $tag,
            'LimitNum' => $max_num,
            'Remaining' => $remaining,
            'TimeRange' => $range_sec,
            'TimeReset' => $reset_date,
        ];
    }

}