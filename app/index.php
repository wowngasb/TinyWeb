<?php
use app\Bootstrap;
use TinyWeb\Application;

$config =
Bootstrap::bootstrap('app', new Application(require(dirname(__DIR__) . "/config/config.php")))->run();