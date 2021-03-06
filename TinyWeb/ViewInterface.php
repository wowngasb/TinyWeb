<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/25 0025
 * Time: 13:42
 */

namespace TinyWeb;


interface ViewInterface
{

    /**
     * 渲染一个视图模板, 得到结果
     * @param string $widget_path 视图模板的文件, 绝对路径, 一般这个路径由Controller提供
     * @param array $tpl_vars 关联数组, 模板变量
     * @return string
     */
    public static function  widget($widget_path, array $tpl_vars = []);

    /**
     * 渲染一个视图模板, 并直接输出给请求端
     * @param string $view_path 视图模板的文件, 绝对路径, 一般这个路径由Controller提供
     * @param array $tpl_vars 关联数组, 模板变量
     */
    public static function  display($view_path, array $tpl_vars = []);

    /**
     * 添加 模板变量
     * @param mixed $name 字符串或者关联数组, 如果为字符串, 则$value不能为空, 此字符串代表要分配的变量名. 如果为数组, 则$value须为空, 此参数为变量名和值的关联数组.
     * @param mixed $value 分配的模板变量值
     * @return ViewInterface
     */
    public function assign($name, $value = null);

    /**
     * 获取所有 模板变量
     * @return array
     */
    public function getAssign();
}