<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/10 0010
 * Time: 21:52
 */
class ApplicationTest extends PHPUnit_Framework_TestCase
{

    public function test_bootstrap(){
        $app = new \TinyWeb\Application();
        PHPUnit_Framework_Assert::assertEquals($app, \TinyWeb\Application::getInstance());

        PHPUnit_Framework_Assert::assertFalse($app->isBootstrapCompleted());
        $app = \TinyWeb\Base\AbstractBootstrap::bootstrap('test', $app);

        PHPUnit_Framework_Assert::assertTrue($app->isBootstrapCompleted());
    }

}