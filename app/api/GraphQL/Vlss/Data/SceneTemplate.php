<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/6 0006
 * Time: 17:36
 */

namespace app\api\GraphQL\Vlss\Data;


use app\common\Base\BaseOrmModel;
use GraphQL\Utils;

class SceneTemplate extends BaseOrmModel
{

    protected static $__tablename__ = 'vlss_scene_template';

    public $template_id;
    public $vlss_id;   #虚拟演播厅id
    public $template_name;   #模板名称
    public $switch_config;   #模版配置 格式为 json 字符串
    public $front_pic;
    public $back_pic;
    public $state;   #1正常,9删除
    public $create_time;   #记录创建时间
    public $uptime;   #更新时间

    public function __construct(array $data)
    {
        Utils::assign($this, $data);
    }
}