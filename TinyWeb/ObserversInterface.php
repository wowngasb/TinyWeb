<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/25 0025
 * Time: 13:42
 */

namespace TinyWeb;


use Illuminate\Database\Query\Builder;
use TinyWeb\Plugin\CurrentUser;

interface ObserversInterface
{
    /**
     * @param CurrentUser $user
     */
    public function hookCurrentUser(CurrentUser $user);


    ##########################################
    ################插入处理##################
    ##########################################
    /**
     * @param array $values
     * @return array
     */
    public static function beforeInsertItem(array $values);

    /**
     * @param int $item_id
     */
    public static function afterInsertItem($item_id);


    ##########################################
    ################删除处理##################
    ##########################################
    /**
     * @param int $item_id
     */
    public static function beforeDeleteItem($item_id);

    /**
     * @param int $item_id
     */
    public static function afterDeleteItem($item_id);

    ##########################################
    ################获取处理##################
    ##########################################
    /**
     * @param int $item_id
     */
    public static function beforeGetItem($item_id);

    /**
     * @param array $values
     * @return array
     */
    public static function afterGetItem(array $values);

    ##########################################
    ################更新处理##################
    ##########################################
    /**
     * @param int $item_id
     * @param array $values
     * @return array
     */
    public static function beforeUpdateItem($item_id, array $values);

    /**
     * @param int $item_id
     * @param array $values
     */
    public static function afterUpdateItem($item_id, array $values);

    ##########################################
    ###############批量更新处理################
    ##########################################
    /**
     * @param array $id_list
     * @param array $values
     * @return array
     */
    public function beforeUpdateMany(array $id_list, array $values);

    /**
     * @param array $id_list
     * @param array $values
     */
    public function afterUpdateMany(array $id_list, array $values);

    ##########################################
    ###############批量获取处理################
    ##########################################
    /**
     * @param array $id_list
     */
    public function beforeGetMany(array $id_list);

    /**
     * @param array $id_list
     * @param array $rst
     * @return array
     */
    public function afterGetMany(array $id_list, array $rst);

    ##########################################
    ###############批量获取处理################
    ##########################################
    /**
     * @param Builder $table
     * @param array $queries
     * @return Builder
     */
    public function beforeBuilderQueries(Builder $table, array $queries);

    /**
     * @param Builder $table
     * @param array $queries
     * @return Builder
     */
    public function afterBuilderQueries(Builder $table, array $queries);

}