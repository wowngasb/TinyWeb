<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/25 0025
 * Time: 15:05
 */

namespace app\index\controllers;

use app\common\Controller;
use TinyWeb\Application;

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

    public function pageNotFound()
    {
        $error_file = 'error.html';
        $error_str = self::_cacheDataByRedis(__METHOD__, "error_file={$error_file}", function () use ($error_file) {
            return file_get_contents(Application::join('\\', [ROOT_PATH, Application::getInstance()->getAppName(), 'static', $error_file]));
        }, function ($data) {
            return !empty($data);
        });
        $this->response->setResponseCode(404)->appendBody($error_str);
    }

}