<?php

namespace app\develop\controllers;

use app\common\Base\BaseController;
use TinyWeb\Application;
use TinyWeb\Request;
use TinyWeb\Helper\LogHelper;
use TinyWeb\Helper\ApiHelper;

class sysLog extends BaseController
{

    public function beforeAction()
    {
        parent::beforeAction();
        if ( !index::authDevelopKey() ) {  //认证 不通过
            Application::redirect(Request::urlTo(['index', 'index', 'develop']));
        }
    }

    public function index() {
        Application::app()->forward(['syslog', 'showLogDir', 'develop']);
    }

    public function showLogDir()
    {
        $arr_dir = LogHelper::getLogPathArray();
        $arr_dir = self::fixPathData($arr_dir);
        $json_dir = json_encode($arr_dir);

        $this->assign('tool_title', '后台日志查看系统');
        $this->assign('json_dir', $json_dir);
        $this->display();
    }

    public function showLogFile()
    {
        $scroll_to = Request::_get('scroll_to', 'end');
        $path = Request::_get('file', '');
        $file_str = LogHelper::readLogByPath($path);
        $this->assign('file_str', $file_str);
        $this->assign('scroll_to', $scroll_to);
        $this->assign('tool_title', $path);
        $this->display();
    }

    public function ajaxClearLogFile()
    {
        $path = Request::_get('file', '');
        if (empty($path)) {
            $result = ['errno'=>-1, 'msg' => "参数错误"];
            exit(json_encode($result));
        }
        $test = pathinfo($path);
        if ($test['filename'] != date('Y-m-d')) {
            $result = ['errno'=>-2, 'msg' => "不可清空今日以前日志"];
            exit(json_encode($result));
        }

        $rst = LogHelper::clearLogByPath($path);
        if ($rst) {
            $result = ['errno'=>0, 'msg' => "{$path}已清空"];
        } else {
            $result = ['errno'=>-3, 'msg' => "{$path}清空失败"];
        }
        exit(json_encode($result));
    }

    public function downLoadLogFile()
    {
        $path = Request::_get('file', '');
        $file_str = LogHelper::readLogByPath($path);
        $file_name = str_replace('/', '_', $path);
        $file_name = substr($file_name, 1);
        header("Content-type:text/log");
        header("Content-Disposition:attachment;filename=" . $file_name);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo $file_str;
        exit;
    }

    private static function fixPathData($arr_dir)
    {
        /*[{text : '1',id : '1',children: [{text : '11',id : '11',href:'11.html'}]},
          {text : '2',id : '2',expanded : true,children : [
              {text : '21',id : '21',children : [{text : '211',id : '211',href:'211.html'},{text : '212',id : '212',href:'212.html'}]},
              {text : '22',id : '22',href:'22.html', leaf:true}
          ]},
        ];*/
        $rst = [];
        foreach ($arr_dir as $key => $val) {
            $val['ctime_str'] = date('Y-m-d H:i:s', $val['ctime']);
            $val['mtime_str'] = date('Y-m-d H:i:s', $val['mtime']);
            $val['size_str'] = Application::byte2size($val['size']) . 'B';
            if ($val['type'] == 'file') {
                $rst[] = [
                    'text' => $val['name'],
                    'id' => $val['name'],
                    'href' => Request::urlTo(['syslog', 'showlogfile', 'develop'], ['file' => $val['path'], 'scroll_to' => 'end']),
                    'leaf' => true,
                    'file_info' => "create_time : {$val['ctime_str']}, modify_time : {$val['mtime_str']}, size : {$val['size_str']}",
                ];
            } else if ($val['type'] == 'dir') {
                $val['sub'] = isset($val['sub']) ? $val['sub'] : [];
                $rst[] = [
                    'text' => $val['name'],
                    'id' => $val['name'],
                    'href' => '',
                    'leaf' => false,
                    'expanded' => false,
                    'children' => self::fixPathData($val['sub']),
                    'file_info' => "create_time : {$val['ctime_str']}, modify_time : {$val['mtime_str']}, size : {$val['size_str']}",
                ];
            }
        }
        return $rst;
    }

    public function selectApi()
    {
        $api_path = ROOT_PATH . $this->appname . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR;
        $api_list = ApiHelper::getApiFileList($api_path);
        $tmp = [];
        foreach ($api_list as $key => $val) {
            $cls = str_replace('.php', '', $val['name']);
            $tmp[] = [
                'id' => $cls,
                'text' => $cls,
                'leaf' => false,
            ];
        }
        usort($tmp, function ($a, $b) {
            return $a > $b ? 1 : -1;
        });
        $json_api_list = json_encode($tmp);
        $this->assign('json_api_list', $json_api_list);
        $this->assign('tool_title', '后台API调试系统');
        $this->display();
    }

    public function getParamList()
    {
        $args_list = [];
        $note = '';
        $class = Request::_get('cls', '');
        $method = Request::_get('method', '');
        if (!empty($class) && !empty($method)) {
            $class_name = "\\{$this->appname}\\api\\{$class}";
            $args_list = ApiHelper::getApiParamList($class_name, $method);
            $note = ApiHelper::getApiNoteStr($class_name, $method);
        }
        $rst['Args'] = $args_list;
        $rst['Note'] = $note;
        echo json_encode($rst);
    }

    public function getMethodList()
    {
        $tmp = [];
        $class = Request::_get('id', '');
        $method_list = [];
        if (!empty($class)) {
            $class_name = "\\{$this->appname}\\api\\{$class}";
            $method_list = ApiHelper::getApiMethodList($class_name);
        }
        foreach ($method_list as $key => $val) {
            $name = $val['name'];
            if ($name == '__construct' || strpos($name, 'hook', 0) === 0) {
                continue;
            }
            $tmp[] = [
                'id' => $name,
                'text' => $name,
                'leaf' => true,
            ];
        }
        usort($tmp, function ($a, $b) {
            return $a > $b ? 1 : -1;
        });
        echo json_encode($tmp);
    }

}