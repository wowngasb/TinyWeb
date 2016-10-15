<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/1 0001
 * Time: 20:28
 */

namespace app\common\Base;


use TinyWeb\Controller\ControllerFis;
use TinyWeb\Plugin\LogTrait;
use TinyWeb\Plugin\RpcTrait;

abstract class BaseController extends ControllerFis
{

    use LogTrait, RpcTrait;
    protected static $detail_log = false;

    public function beforeAction()
    {
        parent::beforeAction();
    }

}