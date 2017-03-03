# coding: utf-8
import os
import json

basedir = os.path.abspath(os.path.dirname(__file__))

def _load_config():
    output = os.popen('php ' + os.path.join(basedir, 'phpconfig.php'))
    return json.load(output)

PHP_CONFIG = _load_config()

SQLALCHEMY_TRACK_MODIFICATIONS = True

SQLALCHEMY_DATABASE_URI = "mysql://%s:%s@%s:%s/%s?charset=utf8" % (PHP_CONFIG['ENV_MYSQL_USER'], PHP_CONFIG['ENV_MYSQL_PASS'], PHP_CONFIG['ENV_MYSQL_HOST'], PHP_CONFIG['ENV_MYSQL_PORT'], PHP_CONFIG['ENV_MYSQL_DB'])

SQLALCHEMY_MIGRATE_REPO = os.path.join(basedir, 'db_repository')