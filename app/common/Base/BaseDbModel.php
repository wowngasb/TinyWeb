<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/17 0017
 * Time: 16:14
 */

namespace app\common\Base;

use app\common\Interfaces\IDbMiddleWare;
use app\common\Models\CurrentUser;

class BaseIDbModel extends BaseModel implements IDbMiddleWare
{

    /**
     * @param CurrentUser $user
     * @param array $values
     * @return array
     */
    public static function beforeInsertItem(CurrentUser $user, array $values)
    {
        false && func_get_args();
        return $values;
    }

    /**
     * @param CurrentUser $user
     * @param array $values
     * @param int $item_id
     */
    public static function afterInsertItem(CurrentUser $user, array $values, $item_id)
    {
        false && func_get_args();

    }

    /**
     * @param CurrentUser $user
     * @param int $item_id
     * @return int
     */
    public static function beforeDeleteItem(CurrentUser $user, $item_id)
    {
        false && func_get_args();
        return $item_id;
    }

    /**
     * @param CurrentUser $user
     * @param int $item_id
     */
    public static function afterDeleteItem(CurrentUser $user, $item_id)
    {
        false && func_get_args();

    }

    /**
     * @param CurrentUser $user
     * @param $item_id
     * @return int
     */
    public static function beforeGetItem(CurrentUser $user, $item_id){
        false && func_get_args();
        return $item_id;
    }

    /**
     * @param CurrentUser $user
     * @param array $values
     * @param $item_id
     */
    public static function afterGetItem(CurrentUser $user, array &$values, $item_id){
        false && func_get_args();

    }

    /**
     * @param CurrentUser $user
     * @param $item_id
     * @param array $values
     * @return int
     */
    public static function beforeUpdateItem(CurrentUser $user, $item_id, array $values){
        false && func_get_args();
        return $item_id;
    }

    /**
     * @param CurrentUser $user
     * @param array $values
     * @param $item_id
     */
    public static function afterUpdateItem(CurrentUser $user, $item_id, array $values){
        false && func_get_args();

    }

}