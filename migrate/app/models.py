# coding: utf-8
from sqlalchemy import BigInteger, Column, DateTime, Index, Integer, SmallInteger, String, Text, text, TIMESTAMP

from app import db, app
Base = db.Model


from app import db, app

Base = db.Model


class BasicUser(Base):
    __tablename__ = 'basic_user'

    id = Column(Integer, primary_key=True, doc=u"""虚拟演播厅自增id""")
    login_name = Column(String(16), nullable=False, unique=True, doc=u"""用户管理后台登录名""")
    password = Column(String(32), nullable=False, doc=u"""用户管理后台登录名""")
    email = Column(String(32), nullable=False, doc=u"""用户邮箱""")
    telephone = Column(String(16), nullable=False, doc=u"""用户手机号""")
    access_id = Column(String(64), nullable=False, doc=u"""奥点云access_id""")
    access_key = Column(String(64), nullable=False, doc=u"""奥点云access_key""")
    aodian_uin = Column(Integer, nullable=False, index=True, doc=u"""奥点云 uin""")
    dms_sub_key = Column(String(64), nullable=False, doc=u"""DMS sub_key""")
    dms_pub_key = Column(String(64), nullable=False, doc=u"""DMS pub_key""")
    dms_s_key = Column(String(64), nullable=False, doc=u"""DMS s_key""")
    state = Column(SmallInteger, nullable=False, server_default=text("'0'"), doc=u"""1正常，2冻结，9删除""")
    last_login_ip = Column(String(32), nullable=False, server_default=text("''"), doc=u"""用户上次登录ip""")
    login_count = Column(Integer, nullable=False, server_default=text("'0'"), doc=u"""用户管理后台登录次数 登陆一次+1""")
    create_time = Column(DateTime, nullable=False, doc=u"""记录创建时间""")
    uptime = Column(TIMESTAMP, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"), doc=u"""更新时间""")


class RbacPermission(Base):
    __tablename__ = 'rbac_permission'

    id = Column(Integer, primary_key=True, doc=u"""自增主键""")
    p_type = Column(String(32, u'utf8_bin'), nullable=False, doc=u"""权限类型 "MENU"表示菜单的访问权限、"OPERATION"表示功能模块的操作权限、"FILE"表示文件的修改权限、"ELEMENT"表示页面元素的可见性""")
    p_key = Column(String(64, u'utf8_bin'), nullable=False, doc=u"""该项权限唯一id 用于区分权限""")
    title = Column(String(64, u'utf8_bin'), nullable=False, index=True)
    description = Column(Text(collation=u'utf8_bin'), nullable=False)
    create_time = Column(DateTime, nullable=False, doc=u"""记录创建时间""")
    uptime = Column(TIMESTAMP, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"), doc=u"""更新时间""")


class RbacRole(Base):
    __tablename__ = 'rbac_role'

    id = Column(Integer, primary_key=True, doc=u"""自增主键""")
    title = Column(String(128, u'utf8_bin'), nullable=False, index=True)
    description = Column(Text(collation=u'utf8_bin'), nullable=False)
    create_time = Column(DateTime, nullable=False, doc=u"""记录创建时间""")
    uptime = Column(TIMESTAMP, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"), doc=u"""更新时间""")


class RbacRolePermission(Base):
    __tablename__ = 'rbac_role_permission'

    id = Column(Integer, primary_key=True, doc=u"""自增主键""")
    role_id = Column(Integer, nullable=False, index=True)
    permission_id = Column(Integer, nullable=False, index=True)
    state = Column(SmallInteger, nullable=False, server_default=text("'0'"), doc=u"""0 未定义  1有效  2失效""")
    create_time = Column(DateTime, nullable=False, doc=u"""记录创建时间""")
    uptime = Column(TIMESTAMP, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"), doc=u"""更新时间""")


class RbacUserRole(Base):
    __tablename__ = 'rbac_user_role'
    __table_args__ = (
        Index('id', 'id', 'role_id'),
    )

    id = Column(Integer, primary_key=True, doc=u"""自增主键""")
    user_id = Column(Integer, nullable=False, index=True)
    role_id = Column(Integer, nullable=False, index=True)
    create_time = Column(DateTime, nullable=False, doc=u"""记录创建时间""")
    uptime = Column(TIMESTAMP, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"), doc=u"""更新时间""")


class VlssApp(Base):
    __tablename__ = 'vlss_app'

    id = Column(Integer, primary_key=True, doc=u"""虚拟演播厅自增id""")
    user_id = Column(Integer, nullable=False, index=True, doc=u"""用户id""")
    lcps_host = Column(String(128), nullable=False, index=True, doc=u"""导播台域名  不带http://前缀 和 结尾/""")
    vlss_name = Column(String(16), nullable=False, doc=u"""演播厅名字""")
    active_group_id = Column(Integer, index=True, doc=u"""激活的场景组id""")
    active_template_id = Column(Integer, index=True, doc=u"""激活的场景模版id""")
    state = Column(SmallInteger, nullable=False, server_default=text("'0'"), doc=u"""1正常，2冻结，9删除""")
    create_time = Column(DateTime, nullable=False, doc=u"""记录创建时间""")
    uptime = Column(TIMESTAMP, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"), doc=u"""更新时间""")


class VlssSceneGroup(Base):
    __tablename__ = 'vlss_scene_group'

    id = Column(Integer, primary_key=True, doc=u"""自增主键""")
    app_id = Column(Integer, nullable=False, index=True, doc=u"""虚拟演播厅id""")
    group_name = Column(String(32), nullable=False, doc=u"""场景组名称""")
    state = Column(SmallInteger, nullable=False, server_default=text("'0'"), doc=u"""1正常,9删除""")
    create_time = Column(DateTime, nullable=False, doc=u"""记录创建时间""")
    uptime = Column(TIMESTAMP, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"), doc=u"""更新时间""")


class VlssSceneItem(Base):
    __tablename__ = 'vlss_scene_item'

    id = Column(Integer, primary_key=True, doc=u"""自增主键""")
    group_id = Column(Integer, nullable=False, index=True, server_default=text("'0'"), doc=u"""所属场景组id""")
    scene_name = Column(String(32), nullable=False, doc=u"""场景名称""")
    scene_config = Column(Text, nullable=False, doc=u"""场景配置 格式为 json 字符串""")
    scene_type = Column(String(16), nullable=False, server_default=text("''"), doc=u"""场景类型""")
    scene_sort = Column(Integer, nullable=False, server_default=text("'10'"), doc=u"""场景叠加排序""")
    state = Column(SmallInteger, nullable=False, server_default=text("'0'"), doc=u"""1正常,2隐藏,9删除""")
    create_time = Column(DateTime, nullable=False, doc=u"""记录创建时间""")
    uptime = Column(TIMESTAMP, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"), doc=u"""更新时间""")


class VlssSceneTemplate(Base):
    __tablename__ = 'vlss_scene_template'

    id = Column(Integer, primary_key=True, doc=u"""自增主键""")
    app_id = Column(Integer, nullable=False, index=True, doc=u"""虚拟演播厅id""")
    template_name = Column(String(16), nullable=False, doc=u"""模板名称""")
    switch_config = Column(Text, nullable=False, doc=u"""模版配置 格式为 json 字符串""")
    active_switch_name = Column(String(16), doc=u"""当前激活的切换参数名字  对应config中的name""")
    front_pic = Column(String(255), nullable=False, doc=u"""前景图片 完整 url""")
    back_pic = Column(String(255), nullable=False, doc=u"""背景图片 完整 url""")
    state = Column(SmallInteger, nullable=False, server_default=text("'0'"), doc=u"""1正常,9删除""")
    create_time = Column(DateTime, nullable=False, doc=u"""记录创建时间""")
    uptime = Column(TIMESTAMP, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"), doc=u"""更新时间""")
