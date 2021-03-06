<?php

namespace app\api;

use app\api\abstracts\BaseApi;
use TinyWeb\Helper\ApiHelper;

/**
 * API类，封装 YAR异步调用hub
 *
 * @package api
 */
class YarApiHub extends BaseApi {
    
    public function __construct() {
        parent::__construct();
    }

    #####################################
    ############### YarHub ###############
    #####################################
    
    public static function _yarHub($args) {
        $_REQUEST['_LOG_TAG'] = 'NONE';  //关闭日志显示 YAR不记录日志 数据量大
        
        $args = self::_decodeArgs($args);
        if( empty($args) ){
            return ['Flag'=>101, 'FlagString'=>'YAR参数错误'];
        }
        $args['intime'] = microtime(true);
        $rst = ApiHelper::api($args['class_name'], $args['method_name'], $args['params']);
        $args['outtime'] = microtime(true);
        $log_msg = "args:" . json_encode($args) . ", rst:" . json_encode($rst);
        self::debug($log_msg, __METHOD__, __CLASS__, __LINE__);
        return $rst;
    }

    #####################################
    ################# test ################
    #####################################
    
    public function workFunc($idx, $default=0) {
        $t1 = microtime(true);
        sleep(1);
        return ['Flag'=>100, 'idx'=>$idx, 'default'=>$default, 'Use'=>round(microtime(true)-$t1, 3) * 1000 . 'ms', 'session_data'=>$_SESSION['session_data'], ];
    }

    public function asyncTest() {
        $t1 = microtime(true);
        $_SESSION['session_data'] = session_id();
        $info = self::_yarApi('\\api\\YarApiHub', 'workFunc', [ 
            ['idx'=>3, 'default'=>4],
            ['idx'=>2, 'default'=>1],
            ['idx'=>1],
        ]);
        return ['Flag'=>100, 'Info'=>$info, 'Use'=>round(microtime(true)-$t1, 3) * 1000 . 'ms', 'session_data'=>$_SESSION['session_data'], ];
    }

    public function syncTest() {
        $t1 = microtime(true);
        $info = self::_syncApi('\\api\\YarApiHub', 'workFunc', [ 
            ['idx'=>3, 'default'=>4],
            ['idx'=>2, 'default'=>1],
            ['idx'=>1],
        ]);
        return ['Flag'=>100, 'Info'=>$info, 'Use'=>round(microtime(true)-$t1, 3) * 1000 . 'ms', 'session_id'=>session_id()];
    }
}
