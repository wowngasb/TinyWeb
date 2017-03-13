<?php
namespace app\api;

use app\api\OrmDao\Basic\User;
use TinyWeb\Base\BaseApi;

class UserMgr extends BaseApi
{
    public function getUser($user_id){
        $user = User::getItem($user_id);
        return ['user' => $user];
    }

}
