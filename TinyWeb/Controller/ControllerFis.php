<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/25 0025
 * Time: 14:52
 */

namespace TinyWeb\Controller;


use TinyWeb\Application;
use TinyWeb\Base\AbstractController;
use TinyWeb\Func;
use TinyWeb\Request;
use TinyWeb\Response;
use TinyWeb\View\ViewFis;

abstract class ControllerFis extends AbstractController
{
    private static $fis_release_dir = 'public';   //Fis3发布的目录  必须放置在 ROOT_PATH 下面

    /**
     * ControllerFis constructor.
     * @param Request $request
     * @param Response $response
     */
    final public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);

        $this->setView(new ViewFis());
    }

    /**
     * @param string $fis_release_dir
     */
    public static function setFisReleaseDir($fis_release_dir)
    {
        self::$fis_release_dir = $fis_release_dir;
    }

    /**
     * @param string $tpl_path
     */
    protected function display($tpl_path = '')
    {
        ViewFis::initFisResource(self::$fis_release_dir);
        if (empty($tpl_path)) {
            $tpl_path = Func::joinNotEmpty('/', [$this->routeInfo[2], 'views', $this->routeInfo[0], $this->routeInfo[1] . '.php']);
        } else {
            $tpl_path = Func::joinNotEmpty('/', [$this->routeInfo[2], 'views', $tpl_path]);
        }
        $view = $this->getView();
        $params = $view->getAssign();
        static::fire('preDisplay', [$this, $tpl_path, $params]);
        $params['request'] = $this->request;
        $view->display($tpl_path, $params);
    }

    protected function widget($tpl_path, array $params)
    {
        ViewFis::initFisResource(static::$fis_release_dir);

        $tpl_path = strtolower(trim($tpl_path));
        if (empty($tpl_path)) {
            return '';
        }

        $routeInfo = $this->routeInfo;
        $appname = $this->appname;
        $params['routeInfo'] = $routeInfo;
        $params['appname'] = $appname;
        $tpl_path = Func::joinNotEmpty(DIRECTORY_SEPARATOR, ['widget', $tpl_path]);

        static::fire('preWidget', [$this, $tpl_path, $params]);
        $params['request'] = $this->request;
        $buffer = $this->getView()->widget($tpl_path, $params);
        return $buffer;
    }

}