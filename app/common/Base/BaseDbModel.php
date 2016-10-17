<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/17 0017
 * Time: 16:14
 */

namespace app\common\Base;

use app\common\Traits\DbMiddleWare;
use Illuminate\Database\Eloquent\Model;

class BaseDbModel extends Model
{
    use DbMiddleWare;

}