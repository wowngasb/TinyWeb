<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/31 0031
 * Time: 14:52
 */

namespace app\common\Base;

use TinyWeb\Plugin\LogTrait;
use TinyWeb\Plugin\RpcTrait;

/**
 * Model类，基类
 *
 * @package api\abstracts
 */
abstract class BaseModel {

    use LogTrait, RpcTrait;
    protected static $detail_log = false;

}