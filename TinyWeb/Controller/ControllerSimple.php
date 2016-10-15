<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/25 0025
 * Time: 14:52
 */

namespace TinyWeb\Controller;


use TinyWeb\ControllerAbstract;
use TinyWeb\View\ViewSimple;

class ControllerSimple extends ControllerAbstract
{
    public function beforeAction()
    {
        $this->setView(new ViewSimple());
        $this->response->addHeader('Content-Type: text/html;charset=utf-8', true);
    }

}