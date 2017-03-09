<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/25 0025
 * Time: 14:52
 */

namespace TinyWeb\Controller;


use TinyWeb\Base\BaseController;
use TinyWeb\View\ViewSimple;

class BaseControllerSimple extends BaseController
{
    final public function __construct()
    {
        parent::__construct();
        $this->setView(new ViewSimple());
    }

}