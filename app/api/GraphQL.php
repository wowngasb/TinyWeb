<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/12 0012
 * Time: 17:08
 */

namespace app\api;


use app\api\GraphQL\Vlss\Types;
use TinyWeb\Base\BaseApi;
use Youshido\GraphQL\Execution\Processor;
use Youshido\GraphQL\Schema\Schema;

class GraphQL extends BaseApi
{

    public function exec($params){
        $schema = new Schema([
            'query' => Types::query()
        ]);
        $processor = new Processor($schema);

        $requestString = isset($params['query']) ? $params['query'] : '{hello}';
        $variableValues = isset($params['variables']) ? $params['variables'] : null;

        $result = $processor->processPayload($requestString, $variableValues)->getResponseData();

        return $result;
    }

}