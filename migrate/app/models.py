# coding: utf-8
from sqlalchemy import BigInteger, Column, DateTime, Integer, SmallInteger, String, Text

from app import db, app
Base = db.Model


from app import db, app

Base = db.Model


class BasicUser(Base):
    """用户基本信息表"""
    __tablename__ = 'basic_user'

    id = Column(Integer, primary_key=True, doc=u"""虚拟演播厅自增id""")
    
    login_name = Column(String(16), nullable=False, unique=True, index=True, doc=u"""用户管理后台登录名""")
    password = Column(String(32), nullable=False, doc=u"""用户管理后台登录密码""")
    email = Column(String(32), nullable=False, index=True, doc=u"""用户邮箱""")
    telephone = Column(String(16), nullable=False, index=True, doc=u"""用户手机号""")
    
    aodian_uin = Column(Integer, nullable=False, index=True, doc=u"""奥点云 uin""")
    access_id = Column(String(64), nullable=False, doc=u"""奥点云 access_id""")
    access_key = Column(String(64), nullable=False, doc=u"""奥点云 access_key""")
    lss_app = Column(String(32), nullable=False, index=True, doc=u"""LSS app""")
    
    dms_id = Column(Integer, nullable=False, doc=u"""DMS id""")
    dms_sub_key = Column(String(64), nullable=False, doc=u"""DMS sub_key""")
    dms_pub_key = Column(String(64), nullable=False, doc=u"""DMS pub_key""")
    dms_s_key = Column(String(64), nullable=False, doc=u"""DMS s_key""")
    
    state = Column(SmallInteger, nullable=False, doc=u"""1正常，2冻结，9删除""")
    
    created_at = Column(DateTime, nullable=False, doc=u"""记录创建时间""")
    updated_at = Column(DateTime, nullable=False, doc=u"""记录更新时间""")

class RecordConsoleLogin(Base):
    """用户管理后台登陆记录表"""
    __tablename__ = 'record_console_login'

    id = Column(Integer, primary_key=True, doc=u"""自增主键""")

    user_id = Column(Integer, nullable=False, index=True)
    login_ip = Column(String(32), nullable=False, index=True, doc=u"""用户管理后台登录ip""")
    
    created_at = Column(DateTime, nullable=False, doc=u"""记录创建时间""")
    updated_at = Column(DateTime, nullable=False, doc=u"""记录更新时间""")
    
class RbacPermission(Base):
    """RBAC权限表"""
    __tablename__ = 'rbac_permission'

    id = Column(Integer, primary_key=True, doc=u"""自增主键""")
    
    p_type = Column(String(32), nullable=False, doc=u"""权限类型 "MENU"表示菜单的访问权限、"OPERATION"表示功能模块的操作权限、"FILE"表示文件的权限、"ELEMENT"表示页面元素的可见性""")
    p_key = Column(String(64), nullable=False, index=True, doc=u"""权限标识 用于区分权限""")
    
    title = Column(String(64), nullable=False, doc=u"""权限标题""")
    description = Column(Text, nullable=False, doc=u"""权限描述""")

    created_at = Column(DateTime, nullable=False, doc=u"""记录创建时间""")
    updated_at = Column(DateTime, nullable=False, doc=u"""记录更新时间""")


class RbacRole(Base):
    """RBAC角色表"""
    __tablename__ = 'rbac_role'

    id = Column(Integer, primary_key=True, doc=u"""自增主键""")
    
    title = Column(String(32), nullable=False, doc=u"""角色标题""")
    description = Column(Text, nullable=False, doc=u"""角色描述""")
    
    created_at = Column(DateTime, nullable=False, doc=u"""记录创建时间""")
    updated_at = Column(DateTime, nullable=False, doc=u"""记录更新时间""")


class RbacRolePermission(Base):
    """RBAC角色-权限关联表"""
    
    __tablename__ = 'rbac_role_permission'

    id = Column(Integer, primary_key=True, doc=u"""自增主键""")
    
    role_id = Column(Integer, nullable=False, index=True)
    permission_id = Column(Integer, nullable=False, index=True)
    
    state = Column(SmallInteger, nullable=False, doc=u"""1有效，2失效，9删除""")
    
    created_at = Column(DateTime, nullable=False, doc=u"""记录创建时间""")
    updated_at = Column(DateTime, nullable=False, doc=u"""记录更新时间""")


class RbacUserRole(Base):
    """RBAC用户-角色关联表"""
    __tablename__ = 'rbac_user_role'

    id = Column(Integer, primary_key=True, doc=u"""自增主键""")
    
    user_id = Column(Integer, nullable=False, index=True)
    role_id = Column(Integer, nullable=False, index=True)
    
    created_at = Column(DateTime, nullable=False, doc=u"""记录创建时间""")
    updated_at = Column(DateTime, nullable=False, doc=u"""记录更新时间""")


class VlssApp(Base):
    """演播厅基本信息表"""
    __tablename__ = 'vlss_app'

    id = Column(Integer, primary_key=True, doc=u"""自增主键""")
    
    user_id = Column(Integer, nullable=False, index=True, doc=u"""用户id""")
    lcps_host = Column(String(128), nullable=False, index=True, doc=u"""导播台域名  不带http://前缀 和 结尾/""")
    title = Column(String(16), nullable=False, doc=u"""演播厅标题""")
    
    active_group_id = Column(Integer, doc=u"""激活的场景组id""")
    active_template_id = Column(Integer, doc=u"""激活的场景模版id""")
    
    state = Column(SmallInteger, nullable=False, doc=u"""1正常，2冻结，9删除""")
    
    created_at = Column(DateTime, nullable=False, doc=u"""记录创建时间""")
    updated_at = Column(DateTime, nullable=False, doc=u"""记录更新时间""")


class VlssSceneGroup(Base):
    """演播厅场景组表"""
    __tablename__ = 'vlss_scene_group'

    id = Column(Integer, primary_key=True, doc=u"""自增主键""")
    
    app_id = Column(Integer, nullable=False, index=True, doc=u"""演播厅id""")
    
    title = Column(String(32), nullable=False, doc=u"""场景组标题""")
    
    state = Column(SmallInteger, nullable=False, doc=u"""1正常，9删除""")
    
    created_at = Column(DateTime, nullable=False, doc=u"""记录创建时间""")
    updated_at = Column(DateTime, nullable=False, doc=u"""记录更新时间""")


class VlssSceneItem(Base):
    """演播厅场景元素表"""
    __tablename__ = 'vlss_scene_item'

    id = Column(Integer, primary_key=True, doc=u"""自增主键""")
    
    group_id = Column(Integer, nullable=False, index=True, doc=u"""场景组id""")
    
    title = Column(String(32), nullable=False, doc=u"""场景标题""")
    
    scene_config = Column(Text, nullable=False, doc=u"""场景配置 格式为 json 字符串""")
    scene_type = Column(String(16), nullable=False, doc=u"""场景类型""")
    scene_sort = Column(Integer, nullable=False, doc=u"""场景叠加排序""")
    
    state = Column(SmallInteger, nullable=False, doc=u"""1正常,2隐藏,9删除""")
    
    created_at = Column(DateTime, nullable=False, doc=u"""记录创建时间""")
    updated_at = Column(DateTime, nullable=False, doc=u"""记录更新时间""")


class VlssSceneTemplate(Base):
    """演播厅模版表"""
    __tablename__ = 'vlss_scene_template'

    id = Column(Integer, primary_key=True, doc=u"""自增主键""")
    
    app_id = Column(Integer, nullable=False, index=True, doc=u"""演播厅id""")
    
    title = Column(String(32), nullable=False, doc=u"""模板标题""")
    
    switch_config = Column(Text, nullable=False, doc=u"""模版配置 格式为 json 字符串""")
    active_switch_name = Column(String(16), doc=u"""当前激活的切换参数名字  对应 switch_config 中的name""")
    
    front_pic = Column(String(255), nullable=False, doc=u"""前景图片 完整 url""")
    back_pic = Column(String(255), nullable=False, doc=u"""背景图片 完整 url""")
    
    state = Column(SmallInteger, nullable=False, doc=u"""1正常,9删除""")
    
    created_at = Column(DateTime, nullable=False, doc=u"""记录创建时间""")
    updated_at = Column(DateTime, nullable=False, doc=u"""记录更新时间""")
