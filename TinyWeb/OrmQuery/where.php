<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/29 0029
 * Time: 10:14
 */

namespace TinyWeb\OrmQuery;


class where extends AbstractQuery
{

    public $operator = null;
    public $value = null;


    /**
     * where constructor.
     * @param mixed $value
     * @param string $operator
     * @param callable|null $filter 本条件是否生效的回调函数 参数为自身
     */
    public function __construct($value, $operator = '=', callable $filter = null)
    {
        $this->operator = !empty(self::$_allow_operator[$operator]) ? $operator : '=';
        $this->value = $value;

        parent::__construct($filter);
    }

    /**
     * @return array
     */
    protected function _queryArgs()
    {
        return [$this->operator, $this->value];
    }
}