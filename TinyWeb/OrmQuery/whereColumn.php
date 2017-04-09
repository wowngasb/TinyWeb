<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/29 0029
 * Time: 9:31
 */

namespace TinyWeb\OrmQuery;


class whereColumn extends AbstractQuery
{

    public $first = null;
    public $second = null;
    public $operator = null;

    /**
     * whereBetween constructor.
     * @param string $first
     * @param string $second
     * @param string $operator
     * @param callable|null $filter 本条件是否生效的回调函数 参数为自身
     */
    public function __construct($first, $second, $operator = '=', callable $filter = null)
    {
        $this->first = $first;
        $this->second = $second;
        $this->operator = !empty(self::$_allow_operator[$operator]) ? $operator : '=';

        parent::__construct($filter);
    }

    /**
     * @return array  返回 $query格式的数组  表示查询参数数组
     */
    protected function _queryArgs()
    {
        return [$this->first, $this->operator, $this->second];
    }



}