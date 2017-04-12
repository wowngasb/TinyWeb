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

    state = Column(SmallInteger, nullable=False, doc=u"""
        <enum __doc__="用户状态">
            <option value="1" __doc__="正常">NORMAL</option>
            <option value="2" __doc__="冻结">FROZEN</option>
            <option value="9" __doc__="删除">DELETED</option>
        </enum>""")

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

    p_type = Column(String(32), nullable=False, doc=u"""
        <enum __doc__="RBAC权限类型">
            <option value="MENU" __doc__="表示菜单的访问权限">MENU</option>
            <option value="OPERATION" __doc__="表示功能模块的操作权限">OPERATION</option>
            <option value="ELEMENT" __doc__="表示页面元素的可见性">ELEMENT</option>
            <option value="FILE" __doc__="表示文件的权限">FILE</option>
        </enum>""")

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

    state = Column(SmallInteger, nullable=False, doc=u"""
        <enum __doc__="角色-权限关联状态">
            <option value="1" __doc__="有效">NORMAL</option>
            <option value="2" __doc__="失效">FROZEN</option>
            <option value="9" __doc__="删除">DELETED</option>
        </enum>""")

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

    state = Column(SmallInteger, nullable=False, doc=u"""
        <enum __doc__="演播厅状态">
            <option value="1" __doc__="正常">NORMAL</option>
            <option value="2" __doc__="冻结">FROZEN</option>
            <option value="9" __doc__="删除">DELETED</option>
        </enum>""")

    created_at = Column(DateTime, nullable=False, doc=u"""记录创建时间""")
    updated_at = Column(DateTime, nullable=False, doc=u"""记录更新时间""")


class VlssSceneGroup(Base):
    """演播厅场景组表"""
    __tablename__ = 'vlss_scene_group'

    id = Column(Integer, primary_key=True, doc=u"""自增主键""")

    app_id = Column(Integer, nullable=False, index=True, doc=u"""演播厅id""")

    title = Column(String(32), nullable=False, doc=u"""场景组标题""")

    state = Column(SmallInteger, nullable=False, doc=u"""
        <enum __doc__="演播厅场景组状态">
            <option value="1" __doc__="正常">NORMAL</option>
            <option value="9" __doc__="删除">DELETED</option>
        </enum>""")
        
    created_at = Column(DateTime, nullable=False, doc=u"""记录创建时间""")
    updated_at = Column(DateTime, nullable=False, doc=u"""记录更新时间""")


class VlssSceneItem(Base):
    """演播厅场景元素表"""
    __tablename__ = 'vlss_scene_item'

    id = Column(Integer, primary_key=True, doc=u"""自增主键""")

    group_id = Column(Integer, nullable=False, index=True, doc=u"""场景组id""")

    title = Column(String(32), nullable=False, doc=u"""场景标题""")

    scene_config = Column(Text, nullable=False, doc=u"""场景配置 格式为 json 字符串""")
    scene_type = Column(String(16), nullable=False, doc=u"""
        <enum __doc__="演播厅场景元素 类型">
            <option value="hsms_trailer" __doc__="预告">hsms_trailer</option>
            <option value="hsms_logo" __doc__="台标">hsms_logo</option>
            <option value="hsms_subtitle" __doc__="字幕">hsms_subtitle</option>
        </enum>""")
        
    scene_sort = Column(Integer, nullable=False, doc=u"""场景叠加排序""")

    state = Column(SmallInteger, nullable=False, doc=u"""
        <enum __doc__="演播厅场景元素状态">
            <option value="1" __doc__="正常">SHOW</option>
            <option value="1" __doc__="隐藏">HIDE</option>
            <option value="9" __doc__="删除">DELETED</option>
        </enum>""")

    created_at = Column(DateTime, nullable=False, doc=u"""记录创建时间""")
    updated_at = Column(DateTime, nullable=False, doc=u"""记录更新时间""")


class VlssSceneTemplate(Base):
    """演播厅模版表"""
    __tablename__ = 'vlss_scene_template'

    id = Column(Integer, primary_key=True, doc=u"""自增主键""")

    app_id = Column(Integer, nullable=False, index=True, doc=u"""演播厅id""")

    title = Column(String(32), nullable=False, doc=u"""模板标题""")

    switch_config = Column(Text, nullable=False, doc=u"""
        <json-list __doc__="场景切换按钮配置，数组长度等于切换配置数量">
            <json-object __cls__="VlssSceneBtnSwitchConfig" __doc__="每个切换按钮的具体配置" __attrType__name="string" __attrDoc__name="切换按钮名称">
                <json-attr key="param" __doc__="具体的切换参数">
                    <json-list __doc__="各通道切换配置，数组长度等于导播台通道数">
                        <json-object __cls__="VlssSceneInputVideoSwitchConfig" __doc__="视频通道的切换配置" 
                            __attrType__w="int" __attrDoc__w="视频宽"
                            __attrType__h="int" __attrDoc__h="视频高"
                            __attrType__x="int" __attrDoc__x="视频x坐标"
                            __attrType__y="int" __attrDoc__y="视频y坐标"
                            __attType___v="int" __attrDoc__v="视频音量"
                            __attrType__z="int" __attrDoc__z="视频叠加顺序"
                            __attrType__checked="boolean" __attrDoc__checked="视频是否在画面中"
                            >
                        </json-object>
                    </json-list>
                </json-attr>
            </json-object>
        </json-list>""")
        
    active_switch_name = Column(String(16), doc=u"""当前激活的切换参数名字  对应 switch_config 中的name""")

    front_pic = Column(String(255), nullable=False, doc=u"""前景图片 完整 url""")
    back_pic = Column(String(255), nullable=False, doc=u"""背景图片 完整 url""")

    state = Column(SmallInteger, nullable=False, doc=u"""
        <enum __doc__="演播厅模版状态">
            <option value="1" __doc__="正常">NORMAL</option>
            <option value="9" __doc__="删除">DELETED</option>
        </enum>""")

    created_at = Column(DateTime, nullable=False, doc=u"""记录创建时间""")
    updated_at = Column(DateTime, nullable=False, doc=u"""记录更新时间""")
