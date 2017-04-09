<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/29 0029
 * Time: 9:28
 */

namespace TinyWeb\OrmQuery;


class whereIn extends AbstractQuery
{
    public $values = null;

    /**
     * whereBetween constructor.
     * @param array $values
     * @param callable|null $filter 本条件是否生效的回调函数 参数为自身
     */
    public function __construct(array $values, callable $filter = null)
    {
        $this->values = $values;

        parent::__construct($filter);
    }

    /**
     * @return array  返回 $query格式的数组  表示查询参数数组
     */
    protected function _queryArgs()
    {
        return [$this->values];
    }
}