<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/19 0019
 * Time: 18:15
 */

namespace app\api;


use TinyWeb\Base\AbstractApi;

class HotFix  extends AbstractApi
{

    public function clearDbCache(){
        return ['db'=>'test'];
    }

}