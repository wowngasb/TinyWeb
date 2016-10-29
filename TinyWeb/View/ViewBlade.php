<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/18 0018
 * Time: 14:27
 */

namespace TinyWeb\View;

use Philo\Blade\Blade;
use TinyWeb\BaseAbstract;
use TinyWeb\ViewInterface;


class ViewBlade implements ViewInterface
{

    protected $_tpl_vars = [];

    /**
     * @return \Illuminate\View\Factory
     */
    public static function getBlade(){
        $blade = new Blade(ROOT_PATH, CACHE_PATH);
        return $blade->view();
    }

    /**
     * 渲染一个视图模板, 得到结果
     * @param string $widget_path 视图模板的文件, 绝对路径, 一般这个路径由Controller提供
     * @param array $tpl_vars 关联数组, 模板变量
     * @return string
     */
    public static function  widget($widget_path, array $tpl_vars = [])
    {
        return self::getBlade()->file($widget_path, $tpl_vars)->render();
    }

    /**
     * 渲染一个视图模板, 并直接输出给请求端
     * @param string $view_path 视图模板的文件, 绝对路径, 一般这个路径由Controller提供
     * @param array $tpl_vars 关联数组, 模板变量
     */
    public static function  display($view_path, array $tpl_vars = [])
    {
        echo self::getBlade()->file($view_path, $tpl_vars)->render();
    }

    /**
     * 添加 模板变量
     * @param mixed $name 字符串或者关联数组, 如果为字符串, 则$value不能为空, 此字符串代表要分配的变量名. 如果为数组, 则$value须为空, 此参数为变量名和值的关联数组.
     * @param mixed $value 分配的模板变量值
     * @return ViewInterface
     */
    public function assign($name, $value = null)
    {
        if( is_array($name) ){
            $this->_tpl_vars = array_merge($this->_tpl_vars, $name);
            return $this;
        }
        $this->_tpl_vars[$name] = $value;
        return $this;
    }

    /**
     * 获取所有 模板变量
     * @return array
     */
    public function getAssign()
    {
        return $this->_tpl_vars;
    }
}