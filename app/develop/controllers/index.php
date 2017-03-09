<?php

namespace app\develop\controllers;

use app\common\Controller;
use TinyWeb\Application;
use TinyWeb\Request;
use TinyWeb\Response;

class index extends Controller
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
        Application::instance()->forward(['Index', 'auth', 'develop']);
    }

    public function auth()
    {
        $develop_key = Request::_post('develop_key', '');
        Request::set_session('develop_key', $develop_key);
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
            Response::instance()->appendBody($html_str);
        }
    }

    public static function authDevelopKey()
    {
        $tmp_key = md5(rand(0, 999999));
        return Application::strCmp(Application::instance()->getEnv('DEVELOP_KEY', $tmp_key), Request::_session('develop_key', ''));
    }

}