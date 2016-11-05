<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/25 0025
 * Time: 14:52
 */

namespace TinyWeb\Controller;


use TinyWeb\ControllerAbstract;
use TinyWeb\Response;
use TinyWeb\View\ViewBlade;

class ControllerBlade extends ControllerAbstract
{
    final public function __construct()
    {
        parent::__construct();
        $this->setView(new ViewBlade());
    }


}