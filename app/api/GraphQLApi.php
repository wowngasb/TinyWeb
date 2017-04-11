<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/12 0012
 * Time: 17:08
 */

namespace app\api;

use app\api\GraphQL\Types;
use ErrorException;
use TinyWeb\Base\AbstractApi;


use \GraphQL\Schema;
use \GraphQL\GraphQL;
use \GraphQL\Type\Definition\Config;
use \GraphQL\Error\FormattedError;

class GraphQLApi extends AbstractApi
{

    public function exec($query = '{hello}', array $variables = null)
    {
        if (DEV_MODEL == 'DEBUG') {
            // Enable additional validation of type configs
            // (disabled by default because it is costly)
            Config::enableValidation();

            // Catch custom errors (to report them in query results if debugging is enabled)
            $phpErrors = [];
            set_error_handler(function ($severity, $message, $file, $line) use (&$phpErrors) {
                $phpErrors[] = new ErrorException($message, 0, $severity, $file, $line);
            });
        }

        try {
            // GraphQL schema to be passed to query executor:
            $schema = new Schema([
                'query' => Types::query()
            ]);

            $result = GraphQL::execute(
                $schema,
                $query,
                null,
                $this,
                $variables
            );

            // Add reported PHP errors to result (if any)
            if (DEV_MODEL == 'DEBUG' && !empty($phpErrors)) {
                $result['extensions']['phpErrors'] = array_map(['GraphQL\Error\FormattedError', 'createFromPHPError'], $phpErrors);
            }
        } catch (\Exception $error) {
            if ( DEV_MODEL == 'DEBUG' ) {
                $result['extensions']['exception'] = FormattedError::createFromException($error);
            } else {
                throw $error;
            }
        }

        return $result;
    }

}