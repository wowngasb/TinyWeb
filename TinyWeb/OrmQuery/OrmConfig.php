<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/10 0010
 * Time: 1:44
 */

namespace TinyWeb\OrmQuery;


class OrmConfig
{
    public $table_name = '';     //数据表名
    public $primary_key = '';   //数据表主键
    public $max_select = 0;  //最多获取 5000 条记录 防止数据库拉取条目过多
    public $db_name = '';       //数据库名
    public $cache_time = 0;     //数据缓存时间

    /**
     * OrmConfig constructor.
     * @param string $db_name
     * @param string $table_name
     * @param string $primary_key
     * @param int $cache_time
     * @param int $max_select
     */
    public function __construct($db_name, $table_name, $primary_key = 'id', $cache_time = 0, $max_select = 5000)
    {
        $this->db_name = $db_name;
        $this->table_name = $table_name;
        $this->primary_key = $primary_key;
        $this->max_select = $max_select;
        $this->cache_time = $cache_time;
    }
}