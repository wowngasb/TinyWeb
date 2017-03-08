# coding: utf-8
from sqlalchemy import Column, DateTime, ForeignKey, Integer, SmallInteger, String, Text, text
from sqlalchemy.orm import relationship
from sqlalchemy.ext.declarative import declarative_base


Base = declarative_base()
metadata = Base.metadata


class VlssApp(Base):
    __tablename__ = 'vlss_app'

    id = Column(Integer, primary_key=True)
    login_name = Column(String(16), nullable=False, unique=True)
    password = Column(String(32), nullable=False)
    access_id = Column(String(64), nullable=False)
    access_key = Column(String(64), nullable=False)
    aodian_uin = Column(Integer, nullable=False, index=True)
    dms_sub_key = Column(String(64), nullable=False)
    dms_pub_key = Column(String(64), nullable=False)
    dms_s_key = Column(String(64), nullable=False)
    lcps_host = Column(String(128), nullable=False, index=True)
    active_group_id = Column(ForeignKey(u'vlss_scene_group.id', ondelete=u'SET NULL', onupdate=u'CASCADE'), index=True)
    active_template_id = Column(ForeignKey(u'vlss_scene_template.id', ondelete=u'SET NULL', onupdate=u'CASCADE'), index=True)
    state = Column(SmallInteger, nullable=False, server_default=text("'0'"))
    last_login_ip = Column(String(32), nullable=False, server_default=text("''"))
    login_count = Column(Integer, nullable=False, server_default=text("'0'"))
    create_time = Column(DateTime, nullable=False)
    uptime = Column(DateTime, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"))

    active_group = relationship(u'VlssSceneGroup', primaryjoin='VlssApp.active_group_id == VlssSceneGroup.id')
    active_template = relationship(u'VlssSceneTemplate', primaryjoin='VlssApp.active_template_id == VlssSceneTemplate.id')


class VlssSceneGroup(Base):
    __tablename__ = 'vlss_scene_group'

    id = Column(Integer, primary_key=True)
    vlss_id = Column(ForeignKey(u'vlss_app.id', ondelete=u'CASCADE', onupdate=u'CASCADE'), nullable=False, index=True)
    group_name = Column(String(32), nullable=False)
    create_time = Column(DateTime, nullable=False)
    uptime = Column(DateTime, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"))

    vlss = relationship(u'VlssApp', primaryjoin='VlssSceneGroup.vlss_id == VlssApp.id')


class VlssSceneItem(Base):
    __tablename__ = 'vlss_scene_item'

    id = Column(Integer, primary_key=True)
    group_id = Column(ForeignKey(u'vlss_scene_group.id', ondelete=u'CASCADE', onupdate=u'CASCADE'), nullable=False, index=True, server_default=text("'0'"))
    scene_name = Column(String(32), nullable=False)
    scene_config = Column(Text, nullable=False)
    scene_type = Column(String(16), nullable=False, server_default=text("''"))
    scene_sort = Column(Integer, nullable=False, server_default=text("'10'"))
    state = Column(SmallInteger, nullable=False, server_default=text("'0'"))
    create_time = Column(DateTime, nullable=False)
    uptime = Column(DateTime, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"))

    group = relationship(u'VlssSceneGroup')


class VlssSceneTemplate(Base):
    __tablename__ = 'vlss_scene_template'

    id = Column(Integer, primary_key=True)
    vlss_id = Column(ForeignKey(u'vlss_app.id', ondelete=u'CASCADE', onupdate=u'CASCADE'), nullable=False, index=True)
    template_name = Column(String(16), nullable=False)
    switch_config = Column(Text, nullable=False)
    front_pic = Column(String(255), nullable=False)
    back_pic = Column(String(255), nullable=False)
    state = Column(SmallInteger, nullable=False, server_default=text("'0'"))
    create_time = Column(DateTime, nullable=False)
    uptime = Column(DateTime, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"))

    vlss = relationship(u'VlssApp', primaryjoin='VlssSceneTemplate.vlss_id == VlssApp.id')
