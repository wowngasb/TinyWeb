# coding: utf-8
from sqlalchemy import BigInteger, Column, DateTime, Index, Integer, SmallInteger, String, Text, text, TIMESTAMP
from sqlalchemy.ext.declarative import declarative_base

from app import db, app

Base = db.Model

class VlssApp(Base):
    __tablename__ = 'vlss_app'

    vlss_id = Column(Integer, primary_key=True)   #虚拟演播厅自增id
    login_name = Column(String(16), nullable=False, unique=True)   #用户管理后台登录名
    password = Column(String(16), nullable=False)   #用户管理后台登录名
    access_id = Column(String(64), nullable=False)   #奥点云access_id
    access_key = Column(String(64), nullable=False)   #奥点云access_key
    aodian_uin = Column(Integer, nullable=False, index=True)   #奥点云 uin
    dms_sub_key = Column(String(64), nullable=False)   #DMS sub_key
    dms_pub_key = Column(String(64), nullable=False)   #DMS pub_key
    dms_s_key = Column(String(64), nullable=False)   #DMS s_key
    lcps_host = Column(String(128), nullable=False, index=True)   #导播台域名  不带http://前缀 和 结尾/
    state = Column(SmallInteger, nullable=False, server_default=text("'0'"))   #1正常，2冻结，9删除
    last_login_id = Column(String(32), nullable=False, server_default=text("''"))   #用户上次登录ip
    login_count = Column(Integer, nullable=False, server_default=text("'0'"))   #用户管理后台登录次数 登陆一次+1
    create_time = Column(DateTime, nullable=False)   #记录创建时间
    uptime = Column(TIMESTAMP, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"))   #更新时间


class VlssSceneGroup(Base):
    __tablename__ = 'vlss_scene_group'

    group_id = Column(Integer, primary_key=True)
    vlss_id = Column(Integer, nullable=False, index=True)   #虚拟演播厅id
    group_name = Column(String(32), nullable=False)   #场景组名称
    state = Column(SmallInteger, nullable=False, server_default=text("'0'"))   #1正常，9删除
    create_time = Column(DateTime, nullable=False)   #记录创建时间
    uptime = Column(TIMESTAMP, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"))   #更新时间


class VlssSceneItem(Base):
    __tablename__ = 'vlss_scene_item'

    scene_id = Column(Integer, primary_key=True)
    vlss_id = Column(Integer, nullable=False, index=True)   #虚拟演播厅id
    group_id = Column(Integer, nullable=False, index=True, server_default=text("'0'"))   #所属场景组id
    scene_name = Column(String(32), nullable=False)   #场景名称
    scene_config = Column(Text, nullable=False)   #场景配置 格式为 json 字符串
    scene_type = Column(String(16), nullable=False, server_default=text("'0'"))   #场景类型1弹幕;2预告;3封面;4投票;5片尾;6记分牌;7弹幕2;8LOGO;9字幕;10通用场景;足球场景
    scene_sort = Column(Integer, nullable=False, server_default=text("'10'"))   #场景叠加排序
    state = Column(SmallInteger, nullable=False, server_default=text("'0'"))   #1正常,2隐藏,9删除
    create_time = Column(DateTime, nullable=False)   #记录创建时间
    uptime = Column(TIMESTAMP, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"))   #更新时间


class VlssSceneTemplate(Base):
    __tablename__ = 'vlss_scene_template'

    template_id = Column(Integer, primary_key=True)
    vlss_id = Column(Integer, nullable=False, index=True)   #虚拟演播厅id
    template_name = Column(String(16), nullable=False)   #模板名称
    switch_config = Column(Text, nullable=False)   #模版配置 格式为 json 字符串
    front_pic = Column(String(255), nullable=False)
    back_pic = Column(String(255), nullable=False)
    state = Column(SmallInteger, nullable=False, server_default=text("'0'"))   #1正常,9删除
    create_time = Column(DateTime, nullable=False)   #记录创建时间
    uptime = Column(TIMESTAMP, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"))   #更新时间