<?php
namespace app\api;

use app\api\abstracts\BaseApi;
use app\common\Base\BaseException;
use TinyWeb\Exception\ApiParamsError;

class AdminTest extends BaseApi
{

    protected static $detail_log = true;

    public function __construct()
    {
        parent::__construct();
    }

    protected static function validName($name) {
        if( !preg_match("/^[0-9a-zA-Z]{6,18}$/i", $name) ){
            throw new ApiParamsError('用户名必须是6-18位数字字母组合');
        }
    }

    public function testDb(){
        return (new Orm('blog_tags'))->get(0, 20, [], ['*'], [
            'where'=>[],
        ]);
    }

    public function testDb2(){
        return (new Orm('blog_posts'))->lists('slug');
    }

    public function testDb3(){
        return (new Orm('blog_posts'))->first(['slug','title'],[
            'where'=>[
                'state'=>1,
            ]
        ]);
    }

    /**
     * @param string $name
     * @param int $id
     * @return array
     * @throws ApiParamsError
     */
    public function testApiFirst($name, $id = 123)
    {
        try{
            self::validName($name);
        }
        catch(BaseException $ex){
            throw new ApiParamsError('Validation Failed', $ex);
        }

        if($id <= 0){
            throw new ApiParamsError('id必须大于0');
        } else {
            $rst = ['name' => $name, 'id' => $id];
            $log_msg = "args:" . json_encode($rst);
            self::info($log_msg, __METHOD__, __CLASS__, __LINE__);
        }

        self::$detail_log && self::debugArgs(func_get_args(), __METHOD__, __CLASS__, __LINE__);
        self::$detail_log && self::debugResult($rst, __METHOD__, __CLASS__, __LINE__);
        return $rst;
    }

}
