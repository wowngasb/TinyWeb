<?php
namespace app\api;

use app\api\GraphQL\BasicUser;
use TinyWeb\Base\BaseApi;

class UserMgr extends BaseApi
{
    public function getUser($id){
        $user = BasicUser::getDataById($id);
        $login_name = BasicUser::login_name($id);
        return [
            'user' => $user,
            'login_name'=>$login_name,
            'tel' => BasicUser::telephone($id),
            'email' => BasicUser::email($id),
        ];
    }

}
