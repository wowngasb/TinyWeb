<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/17 0017
 * Time: 16:14
 */

namespace app\common\Base;



use TinyWeb\ObserversInterface;
use TinyWeb\Plugin\CurrentUser;

class BaseDbModel extends BaseModel implements ObserversInterface
{
    protected $user;

    /**
     * @param array $values
     * @return array
     */
    public static function beforeInsertItem(array $values)
    {
        false && func_get_args();
        return $values;
    }

    /**
     * @param array $values
     * @param int $item_id
     */
    public static function afterInsertItem(array $values, $item_id)
    {
        false && func_get_args();

    }

    /**
     * @param int $item_id
     * @return int
     */
    public static function beforeDeleteItem($item_id)
    {
        false && func_get_args();
        return $item_id;
    }

    /**
     * @param int $item_id
     */
    public static function afterDeleteItem($item_id)
    {
        false && func_get_args();

    }

    /**
     * @param $item_id
     * @return int
     */
    public static function beforeGetItem($item_id){
        false && func_get_args();
        return $item_id;
    }

    /**
     * @param array $values
     * @param $item_id
     */
    public static function afterGetItem(array &$values, $item_id){
        false && func_get_args();

    }

    /**
     * @param $item_id
     * @param array $values
     * @return int
     */
    public static function beforeUpdateItem($item_id, array $values){
        false && func_get_args();
        return $item_id;
    }

    /**
     * @param array $values
     * @param $item_id
     */
    public static function afterUpdateItem($item_id, array $values){
        false && func_get_args();

    }

    public function hookCurrentUser(CurrentUser $user)
    {
        $this->user = $user;
    }

}