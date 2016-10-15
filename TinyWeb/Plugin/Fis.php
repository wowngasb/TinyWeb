<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/1 0001
 * Time: 17:45
 */

namespace TinyWeb\Plugin;

class Fis
{

    public static function scriptStart()
    {
        ob_start();
        return '';
    }

    public static function scriptEnd()
    {
        $script = ob_get_clean();
        $reg = "/(<script(?:\s+[\s\S]*?[\"'\s\w\/]>|\s*>))([\s\S]*?)(?=<\/script>|$)/i";
        if (preg_match($reg, $script, $matches)) {
            FisResource::addScriptPool($matches[2]);
        } else {
            FisResource::addScriptPool($script);
        }
        return '';
    }

    public static function styleStart()
    {
        ob_start();
        return '';
    }

    public static function styleEnd()
    {
        $style = ob_get_clean();
        $reg = "/(<style(?:\s+[\s\S]*?[\"'\s\w\/]>|\s*>))([\s\S]*?)(?=<\/style>|$)/i";
        if (preg_match($reg, $style, $matches)) {
            FisResource::addStylePool($matches[2]);
        } else {
            FisResource::addStylePool($style);
        }
        return '';
    }

    /**
     * 设置前端加载器
     * @param string $id
     * @return string
     */
    public static function framework($id)
    {
        FisResource::setFramework(FisResource::getUri($id));
        return '';
    }

    /**
     * 加载某个资源及其依赖
     * @param  string $id
     * @return string
     */
    public static function import($id)
    {
        FisResource::load($id);
        return '';
    }

    /**
     * 添加标记位
     * @param  string $type
     * @return string
     */
    public static function placeholder($type)
    {
        return FisResource::placeholder($type);
    }

    /**
     * 加载组件
     * @param  string $id
     * @param  array $tpl_vars
     */
    public static function widget($id, array $tpl_vars = [])
    {
        $path = FISResource::getUri($id);
        if (is_file($path)) {
            extract($tpl_vars, EXTR_OVERWRITE);
            include($path);
            FisResource::load($id);
        }
    }

    /**
     * 渲染页面
     * @param  string $id
     * @param  array $tpl_vars
     */
    public static function display($id, array $tpl_vars)
    {
        $path = FISResource::getUri($id);
        if (is_file($path)) {
            extract($tpl_vars, EXTR_OVERWRITE);
            ob_start();
            include($path);
            $html = ob_get_clean();
            FisResource::load($id); //注意模板资源也要分析依赖，否则可能加载不全
            echo FisResource::renderResponse($html);
        } else {
            trigger_error($id . ' file not found!');
        }
    }

}