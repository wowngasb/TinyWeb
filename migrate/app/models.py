# coding: utf-8
from sqlalchemy import BigInteger, Column, DateTime, Index, Integer, SmallInteger, String, Text, text, TIMESTAMP, ForeignKey
from sqlalchemy.ext.declarative import declarative_base

from app import db, app

relationship = db.relationship
Base = db.Model


class BasicUser(Base):
    __tablename__ = 'basic_user'

    id = Column(Integer, primary_key=True)   #虚拟演播厅自增id
    login_name = Column(String(16), nullable=False, unique=True)   #用户管理后台登录名
    password = Column(String(32), nullable=False)   #用户管理后台登录密码
    email = Column(String(32), nullable=False)   #用户邮箱
    telephone = Column(String(16), nullable=False)   #用户手机号
    access_id = Column(String(64), nullable=False)   #奥点云access_id
    access_key = Column(String(64), nullable=False)   #奥点云access_key
    aodian_uin = Column(Integer, nullable=False, index=True)   #奥点云 uin
    dms_sub_key = Column(String(64), nullable=False)   #DMS sub_key
    dms_pub_key = Column(String(64), nullable=False)   #DMS pub_key
    dms_s_key = Column(String(64), nullable=False)   #DMS s_key
    state = Column(SmallInteger, nullable=False, server_default=text("'0'"))   #1正常，2冻结，9删除
    last_login_ip = Column(String(32), nullable=False, server_default=text("''"))   #用户上次登录ip
    login_count = Column(Integer, nullable=False, server_default=text("'0'"))   #用户管理后台登录次数 登陆一次+1
    create_time = Column(DateTime, nullable=False)   #记录创建时间
    uptime = Column(TIMESTAMP, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"))   #更新时间


class RbacPermission(Base):
    __tablename__ = 'rbac_permission'

    id = Column(Integer, primary_key=True)
    p_type = Column(String(32, u'utf8_bin'), nullable=False)   #权限类型 "MENU"表示菜单的访问权限、"OPERATION"表示功能模块的操作权限、"FILE"表示文件的修改权限、"ELEMENT"表示页面元素的可见性
    p_key = Column(String(64, u'utf8_bin'), nullable=False)   #该项权限唯一id 用于区分权限
    title = Column(String(64, u'utf8_bin'), nullable=False, index=True)
    description = Column(Text(collation=u'utf8_bin'), nullable=False)
    create_time = Column(DateTime, nullable=False)   #记录创建时间
    uptime = Column(TIMESTAMP, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"))   #更新时间


class RbacRole(Base):
    __tablename__ = 'rbac_role'

    id = Column(Integer, primary_key=True)
    title = Column(String(128, u'utf8_bin'), nullable=False, index=True)
    description = Column(Text(collation=u'utf8_bin'), nullable=False)
    create_time = Column(DateTime, nullable=False)   #记录创建时间
    uptime = Column(TIMESTAMP, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"))   #更新时间


class RbacRolePermission(Base):
    __tablename__ = 'rbac_role_permission'

    id = Column(Integer, primary_key=True)
    role_id = Column(ForeignKey(u'rbac_role.id', ondelete=u'CASCADE', onupdate=u'CASCADE'), nullable=False, index=True)
    permission_id = Column(ForeignKey(u'rbac_permission.id', ondelete=u'CASCADE', onupdate=u'CASCADE'), nullable=False, index=True)
    state = Column(SmallInteger, nullable=False, server_default=text("'0'"))   #0 未定义  1有效  2失效
    create_time = Column(DateTime, nullable=False)   #记录创建时间
    uptime = Column(TIMESTAMP, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"))   #更新时间

    permission = relationship(u'RbacPermission')
    role = relationship(u'RbacRole')


class RbacUserRole(Base):
    __tablename__ = 'rbac_user_role'
    __table_args__ = (
        Index('id', 'id', 'role_id'),
    )

    id = Column(Integer, primary_key=True)
    user_id = Column(ForeignKey(u'basic_user.id', ondelete=u'CASCADE', onupdate=u'CASCADE'), nullable=False, index=True)
    role_id = Column(ForeignKey(u'rbac_role.id', ondelete=u'CASCADE', onupdate=u'CASCADE'), nullable=False, index=True)
    create_time = Column(DateTime, nullable=False)   #记录创建时间
    uptime = Column(TIMESTAMP, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"))   #更新时间

    role = relationship(u'RbacRole')
    user = relationship(u'BasicUser')


class VlssApp(Base):
    __tablename__ = 'vlss_app'

    id = Column(Integer, primary_key=True)   #虚拟演播厅自增id
    user_id = Column(ForeignKey(u'basic_user.id', ondelete=u'CASCADE', onupdate=u'CASCADE'), nullable=False, index=True)   #用户id
    lcps_host = Column(String(128), nullable=False, index=True)   #导播台域名  不带http://前缀 和 结尾/
    vlss_name = Column(String(16), nullable=False)   #演播厅名字
    active_group_id = Column(ForeignKey(u'vlss_scene_group.id', ondelete=u'SET NULL', onupdate=u'CASCADE'), index=True)   #激活的场景组id
    active_template_id = Column(ForeignKey(u'vlss_scene_template.id', ondelete=u'SET NULL', onupdate=u'CASCADE'), index=True)   #激活的场景模版id
    state = Column(SmallInteger, nullable=False, server_default=text("'0'"))   #1正常，2冻结，9删除
    create_time = Column(DateTime, nullable=False)   #记录创建时间
    uptime = Column(TIMESTAMP, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"))   #更新时间

    active_group = relationship(u'VlssSceneGroup', primaryjoin='VlssApp.active_group_id == VlssSceneGroup.id')
    active_template = relationship(u'VlssSceneTemplate', primaryjoin='VlssApp.active_template_id == VlssSceneTemplate.id')
    user = relationship(u'BasicUser')


class VlssSceneGroup(Base):
    __tablename__ = 'vlss_scene_group'

    id = Column(Integer, primary_key=True)
    vlss_id = Column(ForeignKey(u'vlss_app.id', ondelete=u'CASCADE', onupdate=u'CASCADE'), nullable=False, index=True)   #虚拟演播厅id
    group_name = Column(String(32), nullable=False)   #场景组名称
    state = Column(SmallInteger, nullable=False, server_default=text("'0'"))   #1正常,9删除
    create_time = Column(DateTime, nullable=False)   #记录创建时间
    uptime = Column(TIMESTAMP, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"))   #更新时间

    vlss = relationship(u'VlssApp', primaryjoin='VlssSceneGroup.vlss_id == VlssApp.id')


class VlssSceneItem(Base):
    __tablename__ = 'vlss_scene_item'

    id = Column(Integer, primary_key=True)
    group_id = Column(ForeignKey(u'vlss_scene_group.id', ondelete=u'CASCADE', onupdate=u'CASCADE'), nullable=False, index=True, server_default=text("'0'"))   #所属场景组id
    scene_name = Column(String(32), nullable=False)   #场景名称
    scene_config = Column(Text, nullable=False)   #场景配置 格式为 json 字符串
    scene_type = Column(String(16), nullable=False, server_default=text("''"))   #场景类型
    scene_sort = Column(Integer, nullable=False, server_default=text("'10'"))   #场景叠加排序
    state = Column(SmallInteger, nullable=False, server_default=text("'0'"))   #1正常,2隐藏,9删除
    create_time = Column(DateTime, nullable=False)   #记录创建时间
    uptime = Column(TIMESTAMP, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"))   #更新时间

    group = relationship(u'VlssSceneGroup')


class VlssSceneTemplate(Base):
    __tablename__ = 'vlss_scene_template'

    id = Column(Integer, primary_key=True)
    vlss_id = Column(ForeignKey(u'vlss_app.id', ondelete=u'CASCADE', onupdate=u'CASCADE'), nullable=False, index=True)   #虚拟演播厅id
    template_name = Column(String(16), nullable=False)   #模板名称
    switch_config = Column(Text, nullable=False)   #模版配置 格式为 json 字符串
    front_pic = Column(String(255), nullable=False)
    back_pic = Column(String(255), nullable=False)
    state = Column(SmallInteger, nullable=False, server_default=text("'0'"))   #1正常,9删除
    create_time = Column(DateTime, nullable=False)   #记录创建时间
    uptime = Column(TIMESTAMP, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"))   #更新时间

    vlss = relationship(u'VlssApp', primaryjoin='VlssSceneTemplate.vlss_id == VlssApp.id')
