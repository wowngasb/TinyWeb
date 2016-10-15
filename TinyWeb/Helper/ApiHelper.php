<?php

namespace TinyWeb\Helper;

use TinyWeb\Application;
use TinyWeb\Exception\ApiError;
use TinyWeb\Plugin\LogTrait;
use TinyWeb\Request;

class ApiHelper
{

    use LogTrait;

    private static $ignore_method_dict = [
        'log' => 1,
        'debug' => 1,
        'debugargs' => 1,
        'debugresult' => 1,
        'info' => 1,
        'warn' => 1,
        'error' => 1,
        'fatal' => 1,
    ];

    public static function api($class_name, $method, $args_input, $init_params = [])
    {
        $t1 = microtime(true);
        $request = Request::getInstance();
        $reflection = new \ReflectionMethod($class_name, $method);
        if (self::is_ignore_method($method) || $reflection->isProtected() || $reflection->isPrivate()) {
            throw new ApiError('can not found api.', -1);
        }
        $args = self::fix_args(self::getApiMethodArgs($reflection), $args_input);

        $instance = empty($init_params) ? (new \ReflectionClass($class_name))->newInstanceArgs() : (new \ReflectionClass($class_name))->newInstanceArgs($init_params);

        $args = self::_hasMethod($class_name, 'hookAccessAndFilterRequest') ? $instance->hookAccessAndFilterRequest($args, $args_input) : $args;  //所有API类继承于BaseApi，默认行为直接原样返回参数不作处理
        $request->setParams($args);
        $data = !empty($args) ? $reflection->invokeArgs($instance, $args) : $reflection->invoke($instance);

        $t2 = microtime(true);
        if (defined('DEV_MODEL') && DEV_MODEL == 'DEBUG') {
            $data['help'] = [];  //调试模式下添加辅助信息
            $data['help']['referer'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
            $data['help']['runtime'] = round($t2 - $t1, 3) * 1000 . 'ms';
            $data['help']['timestamp'] = time();
        }
        self::api_log(!empty($_REQUEST['_LOG_TAG']) ? $_REQUEST['_LOG_TAG'] : '', $class_name, $method, $args, $data);
        unset($data['help']);
        return $data;
    }

    public static function fixActionParams($obj, $func, $params)
    {
        $reflection = new \ReflectionMethod($obj, $func);
        $args = self::fix_args(self::getApiMethodArgs($reflection), $params);
        return $args;
    }

    private static function fix_args($param, $args_input)
    {  //根据函数的参数设置和$args_input修复默认参数并调整API参数顺序
        $tmp_args = [];
        foreach ($param as $key => $arg) {
            $arg_name = $arg['name'];
            if (isset($args_input[$arg_name])) {
                $tmp = $args_input[$arg_name];
                if ($arg['isArray'] && !is_array($tmp)) {
                    $tmp = [$tmp];   //参数要求为数组，把单个参数包装为数组
                }
                $tmp_args[$arg_name] = $tmp;
            } else {
                $tmp_args[$arg_name] = $arg['isOptional'] ? $arg['defaultValue'] : '';   //参数未给出时优先使用函数的默认参数，如果无默认参数这设置为空字符串
            }
        }
        return $tmp_args;
    }

    public static function _getClassName($class_name)
    {
        $tmp = explode('\\', $class_name);
        return end($tmp);
    }

    public static function _hasMethod($class_name, $method_name)
    {
        $class_name = strval($class_name);
        $method_name = strval($method_name);
        if (empty($class_name) || empty($method_name)) {
            return false;
        }
        $rc = new \ReflectionClass($class_name);
        return $rc->hasMethod($method_name);
    }

    private static function api_log($tag, $class_name, $method, $param, $rst)
    {
        $class = self::_getClassName($class_name);
        $log = LogHelper::create("rpc_{$class}");
        $rst_str = json_encode($rst);
        $param_str = json_encode($param);
        $info_msg = "{$method}@args:{$param_str}, rst:{$rst_str}";
        if ( (isset($rst['errno']) && $rst['errno'] == 0) || !isset($rst['error']) ) {
            $tag = !empty($tag) ? $tag : 'DEBUG';
            $log->writeLog($info_msg, $tag);
        } else {
            $tag = !empty($tag) ? $tag : 'ERROR';
            $log->writeLog($info_msg, $tag);
        }
    }

    public static function model2js($cls, $method_list, $dev_debug=true)
    {
        $log_msg = "build API.js@{$cls}, method:" . json_encode($method_list);
        self::debug($log_msg, __METHOD__, __CLASS__, __LINE__);

        $debug = (defined('DEV_MODEL') && DEV_MODEL == 'DEBUG') ? 'true' : 'false';
        $js_str = <<<EOT
function {$cls}Helper(){
    var _this = this;
    this.DEBUG = {$debug};
    this._log_func = (typeof console != "undefined" && typeof console.info == "function" && typeof console.warn == "function") ? {INFO: console.info.bind(console), ERROR: console.warn.bind(console)} : {};
    this.exports = {};
    
    this.formatDate = function(){
        var now = new Date(new Date().getTime());
        var year = now.getFullYear();
        var month = now.getMonth()+1;
        var date = now.getDate();
        var hour = now.getHours();
        var minute = now.getMinutes();
        if(minute < 10){
            minute = '0' + minute.toString();
        } 
        var seconds = now.getSeconds()
        if(seconds < 10){
            seconds = '0' + seconds.toString();
        }
        return year+"-"+month+"-"+date+" "+hour+":"+minute+":"+seconds;
    }
    
    this.rfcApi = function(type, url, args, success, error, log){
        var start_time = new Date().getTime();
        if( typeof CSRF_TOKEN != "undefined" && CSRF_TOKEN ){
            args.csrf = CSRF_TOKEN;
        }
        $.ajax({
            type: type,
            url: url,
            data: args,
            dataType: 'json',
            success:
                function(data) {
                    var use_time = Math.round( (new Date().getTime() - start_time) );
                    if(data.errno == 0 || typeof data.error == "undefined" ){
                        log('INFO', use_time, args, data);
                        typeof(success) == 'function' && success(data);
                    } else {
                        log('ERROR', use_time, args, data);
                        typeof(error) == 'function' && error(data);
                    }
                }
        });
    }

EOT;

        foreach ($method_list as $key => $val) {
            $name = $val['name'];
            $doc_str = $dev_debug ? $val['doc'] : '';
            $args = json_encode(self::getExampleArgsByParameters($val['param']));
            $args_str = $dev_debug ? "this.{$name}_args = {$args};\n    this.exports.{$name}_args = this.{$name}_args;" : '';
            $func_item = <<<EOT

    {$doc_str}
    this.{$name} = function(args, success, error) {
        var log = function(tag, use_time, args, data){
            var f = _this._log_func[tag]; typeof args.csrf != "undefined" && delete args.csrf;
            _this.DEBUG && f && f(_this.formatDate(), '['+tag+'] {$cls}.{$name}('+use_time+'ms)', 'args:', args, 'data:', data);
        }
        return _this.rfcApi('POST', '/api/{$cls}/{$name}' ,args, success, error, log);
    }
    this.exports.{$name} = this.{$name};
    {$args_str}

EOT;
            $js_str .= $func_item;
        }
        $js_str .= <<<EOT
}

if( typeof window.{$cls} == "undefined" ){
    window.{$cls} = new {$cls}Helper();
    if(typeof exports == "undefined"){
        var exports = {};
    }
    for(var key in {$cls}.exports){
        exports[key] = {$cls}.exports[key];
    }
}
EOT;
        return $js_str;
    }

    public static function getExampleArgsByParameters($param)
    {
        $tmp_args = [];
        foreach ($param as $key => $arg) {
            $name = $arg['name'];
            $tmp = '?';
            $tmp = $arg['isArray'] ? ['?', '...',] : $tmp;
            $tmp = $arg['isOptional'] ? $arg['defaultValue'] : $tmp;
            $tmp_args[$name] = $tmp;
        }
        return empty($tmp_args) ? null : $tmp_args;
    }

    public static function getApiParamList($class_name, $method)
    {
        if (empty($class_name) || empty($method)) {
            return [];
        }
        $reflection = new \ReflectionMethod($class_name, $method);
        $param = $reflection->getParameters();
        $tmp_args = [];
        foreach ($param as $arg) {
            $name = $arg->name;
            $tmp = ['name' => $name];
            $tmp['is_array'] = $arg->isArray();
            $tmp['is_optional'] = $arg->isOptional();
            $tmp['optional'] = $tmp['is_optional'] ? $arg->getDefaultValue() : '';
            $tmp_args[] = $tmp;
        }
        return $tmp_args;
    }

    public static function getApiNoteStr($class_name, $method)
    {
        if (empty($class_name) || empty($method)) {
            return '';
        }
        $reflection = new \ReflectionMethod($class_name, $method);
        return $reflection->getDocComment();
    }

    public static function getApiMethodList($class_name)
    {
        if (empty($class_name)) {
            return [];
        }
        $class = new \ReflectionClass($class_name);
        $method_list = [];
        $all_method_list = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($all_method_list as $key => $val) {
            $name = strtolower($val->getName());
            if (self::is_ignore_method($name)) {
                continue;
            } else {
                $method_list[] = [
                    'name' => $val->getName(),
                    'doc' => $val->getDocComment(),
                    'param' => self::getApiMethodArgs($val),
                ];
            }
        }
        return $method_list;
    }

    public static function getApiMethodArgs(\ReflectionMethod $reflection)
    {
        $param_obj = [];
        foreach ($reflection->getParameters() as $p) {
            $isOptional = $p->isOptional();
            $param_obj[] = [
                'name' => $p->name,
                'isArray' => $p->isArray(),
                'isOptional' => $isOptional,
                'defaultValue' => $isOptional ? $p->getDefaultValue() : null,
            ];
        }
        return $param_obj;
    }

    private static function is_ignore_method($name)
    {
        if ($name == '__construct' || stripos($name, 'hook', 0) === 0 || stripos($name, 'crontab', 0) === 0 || stripos($name, '_', 0) === 0) {
            return true;
        }
        $name = strtolower($name);
        return (isset(self::$ignore_method_dict[$name]) && !empty(self::$ignore_method_dict[$name]));
    }

    public static function getApiFileList($path, $base_path = '')
    {
        if (empty($base_path)) {
            $base_path = $path;
        }

        if (!is_dir($path) || !is_readable($path)) {
            return [];
        }

        $result = [];
        $allfiles = scandir($path);  //获取目录下所有文件与文件夹 
        foreach ($allfiles as $key => $filename) {  //遍历一遍目录下的文件与文件夹 
            if ($filename == '.' || $filename == '..') {
                continue;
            }
            $fullname = $path . '/' . $filename;  //得到完整文件路径
            $file_item = [
                'name' => $filename,
                'fullname' => $fullname,
                'ctime' => filectime($fullname),
                'mtime' => filemtime($fullname),
                'path' => str_replace($base_path, '', $fullname),
            ];
            if (is_file($fullname)) {
                $file_item['type'] = 'file';
                $file_item['size'] = filesize($fullname);
                $result[] = $file_item;
            }
        }
        return $result;
    }

} 