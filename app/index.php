<?php
use app\Bootstrap;
use TinyWeb\Application;

define("ROOT_PATH", dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR );

require(ROOT_PATH . 'vendor/autoload.php');
require(ROOT_PATH . "config/config.php");

Application::ioc('app', function ($tag){
    return Bootstrap::bootstrap($tag, new Application(require(ROOT_PATH . "config/config.php")) );
})->callback(function(){
    Bootstrap::debugCallBack();
})->run();