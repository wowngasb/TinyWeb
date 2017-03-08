<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/8 0008
 * Time: 13:28
 */

namespace app\api\GraphQL\Vlss;

use app\common\Base\BaseSchemaAppContext;
use GraphQL\Schema;
use TinyWeb\CurrentUserInterface;
use TinyWeb\Request;

class AppContext extends BaseSchemaAppContext
{

    /**
     * @param Request $request
     * @param CurrentUser $user
     */
    public function __construct(Request $request, CurrentUser $user)
    {
        parent::__construct($request, $user);
    }

    /**
     * @return Schema
     */
    public function schema()
    {
        return new Schema([
            'query' => Types::query()
        ]);
    }
}