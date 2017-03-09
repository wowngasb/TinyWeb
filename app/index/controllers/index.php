<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/25 0025
 * Time: 15:05
 */

namespace app\index\controllers;

use app\common\Controller;

class index extends Controller
{

    public function beforeAction()
    {
        parent::beforeAction();
        $this->assign('beforeAction', 'test');
        $this->assign(['a1' => 1211, 'a2' => 4564412, 'a3' => 13,]);
    }

    public function index()
    {
        $this->assign('title', 'Index Test');
        $this->display();
    }

}