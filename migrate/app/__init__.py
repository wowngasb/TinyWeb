# coding: utf-8
import hashlib
from flask import Flask
from flask.ext.sqlalchemy import SQLAlchemy

app = Flask(__name__)
app.config.from_object('config')
db = SQLAlchemy(app)
from app import models

def md5key(pwd):
    def _md5_str(in_str):
        m2 = hashlib.md5()
        m2.update(in_str)
        return m2.hexdigest()

    SECRET_KEY = app.config.get('PHP_CONFIG', {}).get('SECRET_KEY', '')
    tmp = _md5_str(pwd)
    tmp = _md5_str( SECRET_KEY + tmp )
    return _md5_str( tmp + SECRET_KEY )
