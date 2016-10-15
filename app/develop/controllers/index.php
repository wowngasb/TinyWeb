<?php

namespace app\develop\controllers;

use app\common\Base\BaseController;
use TinyWeb\Application;
use TinyWeb\Request;

class index extends BaseController
{

    public function beforeAction()
    {
        parent::beforeAction();
        if (self::authDevelopKey()) {  //认证 通过
            Application::redirect(Request::urlTo(['Syslog', 'index', 'develop']));
        }
    }

    public function index()
    {
        Application::app()->forward(['Index', 'auth', 'develop']);
    }

    public function auth()
    {
        $develop_key = Request::_post('develop_key', '');
        $_SESSION['develop_key'] = $develop_key;
        if (self::authDevelopKey()) {  //认证 通过
            Application::redirect(Request::urlTo(['Syslog', 'index', 'develop']));
        } else {
            $err_msg = empty($develop_key) ? 'Input develop key.' : 'Auth failed.';
            $html_str = <<<EOT
<form action="" method="POST">
    Auth：<input type="text" value="{$develop_key}" placeholder="develop_key" name="develop_key">
    <button type="submit">Login</button>
</form>
<span>{$err_msg}</span>
EOT;
            $this->response->apendBody($html_str);
        }
    }

    public static function authDevelopKey()
    {
        return Application::strCmp(DEVELOP_KEY, Request::_session('develop_key', ''));
    }

}