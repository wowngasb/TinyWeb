<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/1 0001
 * Time: 20:28
 */

namespace app\common;


use TinyWeb\Controller\BaseControllerFis;

abstract class Controller extends BaseControllerFis
{

    public function beforeAction()
    {
        parent::beforeAction();
    }

}