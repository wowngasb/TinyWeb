<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/25 0025
 * Time: 15:05
 */

namespace app\admin\controllers;



use app\common\Controller;
use TinyWeb\Application;

class index extends Controller
{

    public function beforeAction()
    {
        parent::beforeAction();
        $this->assign('beforeAction', 'check is admin');
        $this->assign(['a1' => 11, 'a2' => 12, 'a3' => 13,]);
    }

    public function index()
    {
        Application::forward($this->request, $this->response, ['index', 'forward', 'admin'], ['name'=>'abc', 'id'=>123456], 'simple');
    }


    public function forward($id, $name, $age=18)
    {
        $this->assign('id', $id);
        $this->assign('name', $name);
        $this->assign('age', $age);

        $this->assign('title', 'Index Test');
        $this->assign('config', Application::getInstance()->getConfig());
        $this->display();
    }

}