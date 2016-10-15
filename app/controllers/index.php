<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/25 0025
 * Time: 15:05
 */

namespace app\controllers;



use app\common\Base\BaseController;

class index extends BaseController
{

    public function beforeAction()
    {
        parent::beforeAction();
        $this->assign('beforeAction', 'test');
        $this->assign(['a1' => 11, 'a2' => 12, 'a3' => 13,]);
    }

    public function index()
    {
        $this->assign('title', 'Index Test');
        $this->display();
    }

}