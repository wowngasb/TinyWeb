<?php

namespace app\common\Base;

use TinyWeb\ExecutableEmptyInterface;

abstract class BaseApiModel extends BaseModel implements ExecutableEmptyInterface {

    public function __construct() {
        # code...
    }

    /**
     * 过滤常见的 API参数  子类按照顺序依次调用父类此方法
     * @param $request  array  执行API的参数
     * @param $origin_request  array  原始http请求参数$_REQUSET
     * @return array  处理后的 API 执行参数 将用于调用方法
     */
    public function hookAccessAndFilterRequest(array $request, array $origin_request) {
        false && func_get_args();

        return $request;  //直接返回请求参数
    }
    
}