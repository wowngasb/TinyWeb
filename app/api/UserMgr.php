<?php
namespace app\api;

use app\api\OrmDao\BasicUser;
use TinyWeb\Base\BaseApi;

class UserMgr extends BaseApi
{
    public function getUser($id){
        $user = BasicUser::getDataById($id);
        $login_name = BasicUser::login_name($id);
        return ['user' => $user, 'login_name'=>$login_name];
    }

}
