<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/29 0029
 * Time: 9:29
 */

namespace TinyWeb\OrmQuery;


class whereNull extends AbstractQuery
{

    /**
     * @return array  返回 $query格式的数组  表示查询参数数组
     */
    protected function _queryArgs()
    {
        return [];
    }
}