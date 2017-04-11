@echo off
python db_tomodel.py mysql://root:root@127.0.0.1:3306/vlss_demo?charset=gbk > models.py