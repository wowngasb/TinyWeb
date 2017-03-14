<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/12 0012
 * Time: 17:08
 */

namespace app\api;


use app\api\GraphQL\Schema\Schema;
use app\Bootstrap;
use TinyWeb\Base\BaseApi;
use Youshido\GraphQL\Execution\Processor;

class GraphQL extends BaseApi
{

    public function exec($query = '{hello}', array $variables = null)
    {
        $schema = new Schema();
        $processor = new Processor($schema);
        Bootstrap::_D($query, '$query');
        $result = $processor->processPayload($query, $variables)->getResponseData();

        return $result;
    }

}