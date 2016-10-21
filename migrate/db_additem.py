# coding: utf-8
from app import db, models

def add(obj):
    db.session.add(obj)

def ci():
    db.session.commit()

##u = models.AdminUser('admin', '123', 1)
##add(u)

for i in range(1, 5):
    u = models.AdminUser('sub%s' % (i,), '123', 2)
    add(u)
ci()