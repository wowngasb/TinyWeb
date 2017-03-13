<?php
namespace app\api;

use app\api\OrmDao\Basic\User;
use TinyWeb\Base\BaseApi;

class UserMgr extends BaseApi
{
    public function getUser($id){
        $user = User::getDataById($id);
        $login_name = User::login_name($id);
        return ['user' => $user, 'login_name'=>$login_name];
    }

}
