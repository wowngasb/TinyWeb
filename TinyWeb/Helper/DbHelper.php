<?php

namespace TinyWeb\Helper;

use Illuminate\Database\Capsule\Manager;
use TinyWeb\Application;

class DbHelper extends Manager
{
    /**
     * @return Manager
     */
    public static function initDb()
    {
        if (!empty(self::$instance)) {
            return self::$instance;
        }
        $app = Application::app();
        $db_config = [
            'driver' => 'mysql',
            'host' => $app->getEnv('ENV_MYSQL_HOST'),
            'port' => $app->getEnv('ENV_MYSQL_PORT'),
            'database' => $app->getEnv('ENV_MYSQL_DB'),
            'username' => $app->getEnv('ENV_MYSQL_USER'),
            'password' => $app->getEnv('ENV_MYSQL_PASS'),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => ''
        ];

        $db = new DbHelper();
        $db->addConnection($db_config);
        $db->setAsGlobal();
        return self::$instance;
    }

    public function getConnection($connection = null)
    {
        $app = Application::app();
        $db_config = [
            'driver' => 'mysql',
            'host' => $app->getEnv('ENV_MYSQL_HOST'),
            'port' => $app->getEnv('ENV_MYSQL_PORT'),
            'database' => $app->getEnv('ENV_MYSQL_DB'),
            'username' => $app->getEnv('ENV_MYSQL_USER'),
            'password' => $app->getEnv('ENV_MYSQL_PASS'),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => ''
        ];

        $key = is_string($connection) ? $connection : substr(md5(serialize($connection)), 0, 8);

        if ($connection != null) {
            if (is_string($connection)) {
                $db_config['database'] = $key;
            } else {
                $db_config = $connection;
            }
        }

        parent::addConnection($db_config, $key);

        return $this->manager->connection($key);
    }
}


