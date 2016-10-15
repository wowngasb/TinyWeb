<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/30 0030
 * Time: 18:06
 */

use TinyWeb\Application;

!defined('ROOT_PATH') && define("ROOT_PATH", dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR );
require(ROOT_PATH . 'vendor/autoload.php');
$app = new Application(require(ROOT_PATH . "config/config.php"));

ignore_user_abort(true);   //如果客户端断开连接，不会引起脚本abort.
set_time_limit(59*60);   //脚本执行延时上限 设置为59分钟

echo __FILE__;