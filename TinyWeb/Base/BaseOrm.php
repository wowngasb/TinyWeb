<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/17 0017
 * Time: 16:14
 */

namespace TinyWeb\Base;

use TinyWeb\Application;
use TinyWeb\Exception\OrmStartUpError;
use TinyWeb\Helper\DbHelper;
use TinyWeb\Traits\CacheTrait;
use TinyWeb\Traits\LogTrait;
use TinyWeb\Traits\RpcTrait;

/**
 * Class BaseOrmModel
 * array $where  检索条件数组 格式为 dict 每个元素都表示一个检索条件  条件之间为 and 关系
 * ① [  `filed` => `value`, ]   例如 ['votes' => 100, ]
 *    key不为数值，value不是数组   表示 某个字段为某值的检索 对应 ->where('votes', 100)
 * ② [  `filed` => [``, ``], ]   例如 ['votes' => ['>', 100], ]
 *    key不为数值的元素 表示 某个字段为某值的检索 对应  ->where('votes', '>', 100)
 * ③ [ [``, ``], ]
 *    key为数值的元素 表示 使用某种检索
 * 例如 [   ['whereBetween', 'votes', [1, 100]],  ]   对应  ->whereBetween('votes', [1, 100])
 * 例如 [   ['whereIn', 'id', [1, 2, 3]],  ]   对应  ->whereIn('id', [1, 2, 3])
 * 例如 [   ['whereNull', 'updated_at'],  ]   对应  ->whereNull('updated_at')
 * @package app\common\Base
 */
abstract class BaseOrm
{
    use LogTrait, RpcTrait, CacheTrait;

}