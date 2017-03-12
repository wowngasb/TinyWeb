<?php
use app\Bootstrap;
use TinyWeb\Application;

!defined('REQUEST_MICROTIME') && define('REQUEST_MICROTIME', microtime(true));
!defined('ROOT_PATH') && define("ROOT_PATH", dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR );

require(ROOT_PATH . 'vendor/autoload.php');

Bootstrap::bootstrap('app', new Application(require(ROOT_PATH . "config/config.php")))->run();