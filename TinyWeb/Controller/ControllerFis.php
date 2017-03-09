<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/25 0025
 * Time: 14:52
 */

namespace TinyWeb\Controller;


use TinyWeb\Application;
use TinyWeb\Base\BaseController;
use TinyWeb\View\ViewFis;

class BaseControllerFis extends BaseController
{
    final public function __construct()
    {
        parent::__construct();
        $this->setView(new ViewFis());
    }

    /**
     * @param string $tpl_path
     */
    public function display($tpl_path = '')
    {
        if (empty($tpl_path)) {
            $tpl_path = Application::join('/', [$this->routeInfo[2], 'views', $this->routeInfo[0], $this->routeInfo[1] . '.php']);
        } else {
            $tpl_path = Application::join('/', [$this->routeInfo[2], 'views', $tpl_path]);
        }
        $view = $this->getView();
        $params = $view->getAssign();
        static::fire('preDisplay', [$this, $tpl_path, $params]);
        $view->display($tpl_path, $params);
    }

    public function widget($tpl_path, array $params)
    {
        $tpl_path = strtolower(trim($tpl_path));
        if (empty($tpl_path)) {
            return '';
        }

        $routeInfo = $this->routeInfo;
        $appname = $this->appname;
        $params['routeInfo'] = $routeInfo;
        $params['appname'] = $appname;
        $tpl_path = Application::join(DIRECTORY_SEPARATOR, ['widget', $tpl_path]);

        static::fire('preWidget', [$this, $tpl_path, $params]);
        $buffer = $this->getView()->widget($tpl_path, $params);
        return $buffer;
    }

}