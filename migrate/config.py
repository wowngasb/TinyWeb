import os
import json

basedir = os.path.abspath(os.path.dirname(__file__))

def load_config():
    output = os.popen('php phpconfig.php')
    return json.loads(output.read())

db_config = load_config()

MYSQL_DB = db_config['ENV_MYSQL_DB']
MYSQL_USER = db_config['ENV_MYSQL_USER']
MYSQL_PASSWD = db_config['ENV_MYSQL_PASS']
MYSQL_HOST = db_config['ENV_MYSQL_HOST']
MYSQL_POST = db_config['ENV_MYSQL_PORT']

SQLALCHEMY_TRACK_MODIFICATIONS = True

SQLALCHEMY_DATABASE_URI = "mysql://%s:%s@%s:%s/%s?charset=utf8" % (MYSQL_USER, MYSQL_PASSWD, MYSQL_HOST, MYSQL_POST, MYSQL_DB)

SQLALCHEMY_MIGRATE_REPO = os.path.join(basedir, 'db_repository')