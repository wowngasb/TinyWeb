<?php
use app\Bootstrap;
use TinyWeb\Application;

define("ROOT_PATH", dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR );

require(ROOT_PATH . 'vendor/autoload.php');

Bootstrap::bootstrap( new Application(require(ROOT_PATH . "config/config.php")) )->run();