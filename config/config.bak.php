<?php
if(!defined('DEV_MODEL')){
    define('DEV_MODEL', 'DEBUG');  //开发模式  DEBUG 调试  PRODUCT 产品
    //define('XHPROF', 'XHPROF:CPU+MEMORY+BUILTINS');  //开启XHPROF性能分析
    define('PLUGIN_PATH', ROOT_PATH . 'plugin' . DIRECTORY_SEPARATOR);
    define('CACHE_PATH', ROOT_PATH . 'cache' . DIRECTORY_SEPARATOR);  //缓存文件存放地址
    define('LOG_PATH', ROOT_PATH . 'logs' . DIRECTORY_SEPARATOR);  //日志文件存放地址
    define('LOG_LEVEL', 'DEBUG');  //日志记录级别['ALL' => 0, 'DEBUG' => 10, 'INFO' => 20, 'WARN' => 30, 'ERROR' => 40, 'FATAL' => 50, 'OFF' => 60,]
    define('DEVELOP_KEY', 'TinyWebDev');  //开发者模式 key  用于使用开发者后台
    define('SYSTEM_HOST', (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on" ? 'https://' : 'http://') . "{$_SERVER['HTTP_HOST']}/");  //HTTP HOST 常量
}

return [
    'ENV_MYSQL_DB' => 'vlss_demo',
    'ENV_MYSQL_USER' => 'dyy_root',
    'ENV_MYSQL_PASS' => 'dyy@root',
    'ENV_MYSQL_HOST' => '121.40.128.45',
    'ENV_MYSQL_PORT' => 3306,
    'ENV_REDIS_HOST' => '121.40.128.45',
    'ENV_REDIS_PORT' => 6379,
    'ENV_REDIS_PASS' => 'f56f3c26ce7cee8F01e',
    'CRYPT_KEY' => 'F^SR%^":3q4HJDG^$@^#',
];

