<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/17 0017
 * Time: 16:13
 */

namespace app\common\Interfaces;


use app\common\Models\CurrentUser;
use Illuminate\Database\Query\Builder;

interface IDbMiddleWare
{
    /**
     * @param CurrentUser $user
     * @param array $values
     * @return array
     */
    public static function beforeInsertItem(CurrentUser $user, array $values);

    /**
     * @param CurrentUser $user
     * @param array $values
     * @param int $item_id
     */
    public static function afterInsertItem(CurrentUser $user, array $values, $item_id);

    /**
     * @param CurrentUser $user
     * @param int $item_id
     * @return int
     */
    public static function beforeDeleteItem(CurrentUser $user, $item_id);

    /**
     * @param CurrentUser $user
     * @param int $item_id
     */
    public static function afterDeleteItem(CurrentUser $user, $item_id);


}