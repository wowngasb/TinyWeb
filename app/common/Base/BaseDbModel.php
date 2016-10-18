<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/17 0017
 * Time: 16:14
 */

namespace app\common\Base;



use Illuminate\Database\Query\Builder;
use TinyWeb\ObserversInterface;
use TinyWeb\Plugin\CurrentUser;

class BaseDbModel extends BaseModel implements ObserversInterface
{
    protected $user;

    public function hookCurrentUser(CurrentUser $user)
    {
        $this->user = $user;
    }

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
     * @param int $item_id
     */
    public static function afterInsertItem($item_id)
    {
        false && func_get_args();

    }

    /**
     * @param int $item_id
     */
    public static function beforeDeleteItem($item_id)
    {
        false && func_get_args();

    }

    /**
     * @param int $item_id
     */
    public static function afterDeleteItem($item_id)
    {
        false && func_get_args();

    }

    /**
     * @param int $item_id
     * @return int
     */
    public static function beforeGetItem($item_id){
        false && func_get_args();

    }

    /**
     * @param array $values
     * @return array
     */
    public static function afterGetItem(array $values){
        false && func_get_args();
        return $values;
    }

    /**
     * @param int $item_id
     * @param array $values
     * @return array
     */
    public static function beforeUpdateItem($item_id, array $values){
        false && func_get_args();
        return $values;
    }

    /**
     * @param int $item_id
     * @param array $values
     * @return array
     */
    public static function afterUpdateItem($item_id, array $values){
        false && func_get_args();
        return $values;
    }



    /**
     * @param array $id_list
     * @param array $values
     * @return array
     */
    public function beforeUpdateMany(array $id_list, array $values)
    {
        false && func_get_args();

        return $values;
    }

    /**
     * @param array $id_list
     * @param array $values
     */
    public function afterUpdateMany(array $id_list, array $values)
    {
        false && func_get_args();
    }

    /**
     * @param array $id_list
     */
    public function beforeGetMany(array $id_list)
    {
        false && func_get_args();


    }

    /**
     * @param array $id_list
     * @param array $values
     * @return array
     */
    public function afterGetMany(array $id_list, array $values)
    {
        false && func_get_args();
        return $values;
    }

    /**
     * @param Builder $table
     * @param array $queries
     * @return Builder
     */
    public function beforeBuilderQueries(Builder $table, array $queries)
    {
        false && func_get_args();

        return $table;
    }

    /**
     * @param Builder $table
     * @param array $queries
     * @return Builder
     */
    public function afterBuilderQueries(Builder $table, array $queries)
    {
        false && func_get_args();

        return $table;
    }
}