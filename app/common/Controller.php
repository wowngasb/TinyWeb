<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/1 0001
 * Time: 20:28
 */

namespace app\common;


use TinyWeb\Controller\ControllerFis;

class Controller extends ControllerFis
{

    public function beforeAction()
    {
        self::setFisReleaseDir('public');
    }
}