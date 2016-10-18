<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/25 0025
 * Time: 13:42
 */

namespace TinyWeb;


use TinyWeb\Plugin\CurrentUser;

interface ObserversInterface
{

    /**
     * @param array $values
     * @return array
     */
    public static function beforeInsertItem(array $values);

    /**
     * @param array $values
     * @param int $item_id
     */
    public static function afterInsertItem(array $values, $item_id);

    /**
     * @param int $item_id
     * @return int
     */
    public static function beforeDeleteItem($item_id);

    /**
     * @param int $item_id
     */
    public static function afterDeleteItem($item_id);

    /**
     * @param $item_id
     * @return int
     */
    public static function beforeGetItem($item_id);

    /**
     * @param array $values
     * @param $item_id
     */
    public static function afterGetItem(array &$values, $item_id);

    /**
     * @param $item_id
     * @param array $values
     * @return int
     */
    public static function beforeUpdateItem($item_id, array $values);
    /**
     * @param array $values
     * @param $item_id
     */
    public static function afterUpdateItem($item_id, array $values);

    public function hookCurrentUser(CurrentUser $user);

}