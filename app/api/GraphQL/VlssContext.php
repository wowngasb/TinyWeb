<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/6 0006
 * Time: 17:08
 */

namespace app\api\GraphQL;


use app\api\GraphQL\Vlss\Types;
use app\common\Base\BaseSchemaModel;
use GraphQL\Schema;

class VlssContext extends BaseSchemaModel
{

    /**
     * @return Schema
     */
    public function buildSchema(){
        return new Schema([
            'query' => Types::query()
        ]);
    }

}