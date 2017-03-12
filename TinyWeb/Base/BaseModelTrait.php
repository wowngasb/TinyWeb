<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/31 0031
 * Time: 14:52
 */

namespace TinyWeb\Base;

use TinyWeb\Helper\RedisHelper;
use TinyWeb\Plugin\CacheTrait;
use TinyWeb\Plugin\LogTrait;
use TinyWeb\Plugin\RpcTrait;

/**
 * Model类，基类
 *
 * @package api\abstracts
 */
trait BaseModelTrait {

    use LogTrait, RpcTrait, CacheTrait;

}