<?php
namespace app\api;

use app\api\abstracts\BaseApi;
use app\common\Base\BaseException;
use app\common\Exceptions\ApiParamsError;

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

    public function testDb($admin_id){
        return (new Orm())->setTable('dyy_admin.room_list')->get(0, 20, ['room_id', 'asc'], ['*'], [
            'where'=>[
                ['admin_id', $admin_id],
                ['live_state', 0],
            ],
            'whereIn'=>[
                ['state', [1, 2, 9]],
            ],
            'whereNotIn'=>[
                ['video_type', [0, 3]],
            ],
        ]);
    }

    public function testDb2($room_id){
        return (new Orm())->setTable('wx_ktv.channels_info')->first(['*'], [
            ['roomId', $room_id],
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
