<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/25 0025
 * Time: 15:05
 */

namespace app\develop\controllers;


use app\common\Base\BaseController;
use TinyWeb\Application;
use TinyWeb\Helper\ApiHelper;
use TinyWeb\Request;

/**
 * 项目部署控制器，进行一些脚本
 * Class Deploy
 * @package app\controllers
 */
class deploy extends BaseController
{
    public function beforeAction()
    {
        parent::beforeAction();
        if (!index::authDevelopKey()) {  //认证 不通过
            Application::redirect(Request::urlTo(['index', 'index', 'develop']));
        }
    }

    public function runCrontab()
    {
        $script = Request::_get('script');
        $file = ROOT_PATH . "crontab" . DIRECTORY_SEPARATOR . $script;
        if (empty($script) || strpos($script, '..') !== false || !is_file($file)) {
            exit('error script file.');
        }
        include_once($file);
    }

    //初始化 超级管理员
    public function initSuperAdmin()
    {
        $admin_id = 0;  // TODO 判断当前是否存在管理员
        if ($admin_id >= 0) {
            exit('pass');
        }
        $username = Request::_post('username', '');
        $password = Request::_post('password', '');
        if (empty($password) || empty($username)) {
            $html_str = <<<EOT
<form action="" method="POST">
    登陆账号：<input type="text" value="" placeholder="登录名" name="username">
    登录密码：<input type="password" placeholder="密码" name="password">
    <button type="submit">创建超级管理员</button>
</form>
EOT;
            exit($html_str);
        }
        $admin_id = 0;  // TODO 创建管理员帐号
        if ($admin_id > 0) {
            exit("init {$username} ok");
        }
    }

    public function syncEnvConfig()
    {
        echo 'Sync Config Surceased';  // TODO 获取基本配置
    }


    /**
     * 编译根目录api下所有 API 类 生成 js  放到 static/apiMod 下面
     */
    public function buildApiModJs()
    {
        $dev_debug = Request::_get('dev_debug', 0) == 1;
        $api_path = ROOT_PATH . self::join(DIRECTORY_SEPARATOR, [$this->appname, 'api' . DIRECTORY_SEPARATOR]);
        $api_list = ApiHelper::getApiFileList($api_path);
        foreach ($api_list as $key => $val) {
            $class = str_replace('.php', '', $val['name']);
            $out_file = $class . '.js';
            $class_name = "\\{$this->appname}\\api\\{$class}";
            $method_list = ApiHelper::getApiMethodList($class_name);
            $js_str = ApiHelper::model2js($class, $method_list, $dev_debug);
            $out_path = ROOT_PATH . self::join(DIRECTORY_SEPARATOR, [$this->appname, 'static', 'apiMod' . DIRECTORY_SEPARATOR]);
            if (!is_dir($out_path)) {
                mkdir($out_path, 0777, true);
            }
            file_put_contents($out_path . $out_file, $js_str, LOCK_EX);
            $js_len = strlen($js_str);
            echo "build:{$out_file} ({$js_len})<br>";
        }
    }

    //指定API生成单一model.js
    public function actionGetModelJs()
    {
        $dev_debug = Request::_get('dev_debug', 0) == 1;
        $method_list = [];
        $cls = Request::_get('cls', '');
        if (!empty($cls)) {
            $class_name = '\\api\\' . $cls;
            $method_list = ApiHelper::getApiMethodList($class_name);
        }
        echo ApiHelper::model2js($cls, $method_list, $dev_debug);
    }

    //清空memcache缓存
    //public function actionCleanMemcache() {
    //    $mem = MMemcache::getInstance();
    //    $mem->flush();
    //    exit("Clean");
    //}

}