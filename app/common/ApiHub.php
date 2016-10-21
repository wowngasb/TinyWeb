<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/18 0018
 * Time: 18:11
 */

namespace app\common;


use app\Bootstrap;
use app\common\Base\BaseModel;
use Exception;
use TinyWeb\Application;
use TinyWeb\Helper\ApiHelper;
use TinyWeb\Request;
use TinyWeb\Response;

class ApiHub extends BaseModel
{

    public static function apiHub(array $routeInfo, array $params)
    {
        $class_name = "\\" . Application::join("\\", [Application::app()->getAppName(), 'api', $routeInfo[0]]);
        $request = Request::getInstance();
        $request_method = $request->getMethod();
        $session_id = $request->getSessionId();
        if ($request_method == 'HEAD' || $request_method == 'GET' || $request_method == 'OPTIONS' || empty($session_id)) {
            $log_msg = "CSRF ignore [{$request_method}] this_url:" . $request->getThisUrl();
            self::debug($log_msg, __METHOD__, __CLASS__, __LINE__);
        } else {
            $csrf = $request->getCsrfToken();
            if ( !Application::strCmp($session_id, Application::decrypt($csrf)) ) {
                $log_msg = "CSRF error [{$request_method}] token:" . $csrf . ", this_url:" . $request->getThisUrl() . ", referer_url:" . $request->getHttpReferer();
                self::error($log_msg, __METHOD__, __CLASS__, __LINE__);
            } else {
                $log_msg = "CSRF pass [{$request_method}] token:" . $csrf . ", this_url:" . $request->getThisUrl() . ", referer_url:" . $request->getHttpReferer();
                self::debug($log_msg, __METHOD__, __CLASS__, __LINE__);
            }
        }
        Bootstrap::_D($log_msg, 'csrf');

        try {
            $json = ApiHelper::api($class_name, $routeInfo[1], $params);
            Bootstrap::_D(['class'=>$class_name, 'method'=>$routeInfo[1], 'params'=>$params, 'result'=>$json], 'api');
        } catch (Exception $ex) {
            Bootstrap::_D((array)$ex, 'api Exception');
            $code = $ex->getCode();  // errno为0 或 无error字段 表示没有错误  errno设置为0 会忽略error字段
            $error = (DEV_MODEL == 'DEBUG') ? ['Exception'=>get_class($ex), 'code' => $ex->getCode(), 'message' => $ex->getMessage(), 'file' => $ex->getFile().' ['.$ex->getLine().']'] : ['code' => $code, 'message' => $ex->getMessage()];
            $json = ['errno' => $code == 0 ? -1 : $code, 'error' =>$error];
            while (!empty($ex) && $ex->getPrevious()) {
                $json['error']['errors'] = isset($json['error']['errors']) ? $json['error']['errors'] : [];
                $ex = $ex->getPrevious();
                $json['error']['errors'][] = (DEV_MODEL == 'DEBUG') ? ['Exception'=>get_class($ex), 'code' => $ex->getCode(), 'message' => $ex->getMessage(), 'file' => $ex->getFile().' ['.$ex->getLine().']'] : ['code' => $ex->getCode(), 'message' => $ex->getMessage()];
            }
        }
        $json['apiVersion'] = '1.0';
        $json_str = isset($params['callback']) && !empty($params['callback']) ? "{$params['callback']}(" . json_encode($json) . ');' : json_encode($json);
        Response::getInstance()->addHeader('Content-Type: application/json;charset=utf-8', false)->apendBody($json_str, $class_name);
    }
}