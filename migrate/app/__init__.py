# coding: utf-8
import hashlib
from flask import Flask
from flask.ext.sqlalchemy import SQLAlchemy
from flask_graphql import GraphQLView
from vlss.schema import schema

app = Flask(__name__)
app.config.from_object('config')
db = SQLAlchemy(app)
from app import models

app.add_url_rule('/graphql', view_func=GraphQLView.as_view('graphql', schema=schema, graphiql=True))

# Optional, for adding batch query support (used in Apollo-Client)
app.add_url_rule('/graphql/batch', view_func=GraphQLView.as_view('graphql', schema=schema, batch=True))

def md5key(pwd):
    def _md5_str(in_str):
        m2 = hashlib.md5()
        m2.update(in_str)
        return m2.hexdigest()

    SECRET_KEY = app.config.get('PHP_CONFIG', {}).get('SECRET_KEY', '')
    tmp = _md5_str(pwd)
    tmp = _md5_str( SECRET_KEY + tmp )
    return _md5_str( tmp + SECRET_KEY )
