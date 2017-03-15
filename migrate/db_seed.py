# coding: utf-8
from app import db, models, md5key
import datetime, json

user_info = {
    'login_name':'demo',
    'password':'demo',
    'email':'demo@demo.aom',
    'telephone': '15068802930',
    'access_id':'111576745758',
    'access_key':'fVYGq1S0gnrvoxZv77msq577jx7MQq3n',
    'aodian_uin':13830,
    'dms_sub_key':'sub_eae37e48dab5f305516d07788eaaea60',
    'dms_pub_key':'pub_5bfb7a0ced7adb2ce454575747762679',
    'dms_s_key':'s_ceb80d29276f78653df081e5a9f0ac76'
}


db.session.add(   #写入 用户信息
    models.BasicUser(
        login_name=user_info['login_name'],
        password=md5key(user_info['password']),
        email=user_info['email'],
        telephone=user_info['telephone'],
        access_id=user_info['access_id'],
        access_key=user_info['access_key'],
        aodian_uin=user_info['aodian_uin'],
        dms_sub_key=user_info['dms_sub_key'],
        dms_pub_key=user_info['dms_pub_key'],
        dms_s_key=user_info['dms_s_key'],
        create_time=datetime.datetime.now(),
        state=1
    )
) if not models.BasicUser.query.filter_by(login_name = user_info['login_name']).first() else None
db.session.commit()

user = models.BasicUser.query.filter_by(login_name = user_info['login_name']).first()

vlss_list = [
    {
        'vlss_name':'demo',
        'lcps_host':'123.8887.lcps.aodianyun.com'
    },
]

[db.session.add(   #写入 vlss app
        models.VlssApp(
            user_id=user.id,
            vlss_name=vlss['vlss_name'],
            lcps_host=vlss['lcps_host'],
            create_time=datetime.datetime.now(),
            state=1
        )
    ) for vlss in vlss_list if not models.VlssApp.query.filter_by(user_id=user.id, lcps_host=vlss['lcps_host']).first()]

db.session.commit()

template_list = [
    {
        "switch_config":[
            {
                "name": "btn1",
                "enable": True,
                "param": [
                    {"w":208,"h":100,"x":107,"y":202,"checked":True,"v":100,"z":3},
                    {"w":262,"h":115,"x":236,"y":115,"checked":True,"v":0,"z":2},
                    {"w":100,"h":100,"x":0,"y":0,"checked":False,"v":0,"z":1}
                ],
            },
            {
                "name": "btn2",
                "enable": False,
                "param": [
                    {"w":208,"h":100,"x":107,"y":202,"checked":True,"v":100,"z":3},
                    {"w":262,"h":115,"x":236,"y":115,"checked":True,"v":0,"z":2},
                    {"w":100,"h":100,"x":0,"y":0,"checked":False,"v":0,"z":1}
                ],
            }
        ],
        "front_pic":"http://test25.aodianyun.com/dist/studio/static/desk01.png",
        "back_pic":"http://test25.aodianyun.com/dist/studio/static/bg01.jpg",
        "template_name":"默认模版1"
    },
    {
        "switch_config":[
            {
                "name": "btn1",
                "enable": True,
                "param": [
                    {"w":208,"h":100,"x":107,"y":202,"checked":True,"v":100,"z":3},
                    {"w":262,"h":115,"x":236,"y":115,"checked":True,"v":0,"z":2},
                    {"w":100,"h":100,"x":0,"y":0,"checked":False,"v":0,"z":1}
                ],
            },
            {
                "name": "btn2",
                "enable": False,
                "param": [
                    {"w":208,"h":100,"x":107,"y":202,"checked":True,"v":100,"z":3},
                    {"w":262,"h":115,"x":236,"y":115,"checked":True,"v":0,"z":2},
                    {"w":100,"h":100,"x":0,"y":0,"checked":False,"v":0,"z":1}
                ],
            }
        ],
        "front_pic":"http://test25.aodianyun.com/dist/studio/static/desk02.png",
        "back_pic":"http://test25.aodianyun.com/dist/studio/static/bg02.jpg",
        "template_name":"默认模版2"
    },
    {
        "switch_config":[
            {
                "name": "btn1",
                "enable": True,
                "param": [
                    {"w":208,"h":100,"x":107,"y":202,"checked":True,"v":100,"z":3},
                    {"w":262,"h":115,"x":236,"y":115,"checked":True,"v":0,"z":2},
                    {"w":100,"h":100,"x":0,"y":0,"checked":False,"v":0,"z":1}
                ],
            },
            {
                "name": "btn2",
                "enable": False,
                "param": [
                    {"w":208,"h":100,"x":107,"y":202,"checked":True,"v":100,"z":3},
                    {"w":262,"h":115,"x":236,"y":115,"checked":True,"v":0,"z":2},
                    {"w":100,"h":100,"x":0,"y":0,"checked":False,"v":0,"z":1}
                ],
            }
        ],
        "front_pic":"http://test25.aodianyun.com/dist/studio/static/desk03.png",
        "back_pic":"http://test25.aodianyun.com/dist/studio/static/bg03.jpg",
        "template_name":"默认模版3"
    }
]

for vlss in vlss_list:
    tmp = models.VlssApp.query.filter_by(vlss_name=vlss['vlss_name']).first()
    if not tmp or not tmp.id:
        continue
    [db.session.add(   #为每个 vlss_app 写入 模版信息
            models.VlssSceneTemplate(
                app_id=tmp.id,
                template_name=template['template_name'],
                front_pic=template['front_pic'],
                back_pic=template['back_pic'],
                switch_config=json.dumps(template['switch_config']),
                create_time=datetime.datetime.now(),
                state=1
            )
        ) for template in template_list  if not models.VlssSceneTemplate.query.filter_by(template_name=template['template_name']).first()]

db.session.commit()

group_list = [
    {
        'group_name': 'test1',
        'scene_list':[
            {
                "scene_name":"预告",
                "scene_type":"hsms-trailer",
                "scene_sort":2,
                "scene_config":{
                    "position":"2",
                    "interval":"5",
                    "contents":[
                        {"image":"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/81f56530c3c655ab4d499d02178ce89f.png","title1":"11","title2":"112323"},
                        {"image":"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/bc61e36619c8807f7c09e28d43001e43.png","title1":"花","title2":"魂牵梦萦 "},
                        {"image":"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/90ce8a80effd29abb5c69bee8be66172.png","title1":"左","title2":"我"}
                    ]
                }
            },
            {
                "scene_name":"图片",
                "scene_type":"hsms-logo",
                "scene_sort":3,
                "scene_config":{
                    "src":"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/0be50bc3a2cdbc1987b598d36e4a946e.png",
                    "style":{"left":"39%","top":"26%","width":"20%","height":"10%","opacity":"1"}
                }
            },
            {
                "scene_name":"字幕",
                "scene_type":"hsms-subtitle",
                "scene_sort":3,
                "scene_config":{
                    "backgound":"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/8fb26f08142738c4a7e5115252f328fb.png",
                    "fixedText":{
                        "text":"粉色粉色",
                        "color":"#1DCD9C",
                        "shadow":"0",
                        "align":"left"
                    },
                    "scrollText":{
                        "text":"大家都关注一下",
                        "color":"#AB102D",
                        "shadow":"0",
                        "speed":"40",
                        "scrollTimes":"0"
                    }
                }
            }
        ]
    },
    {
        'group_name': 'test2',
    },
    {
        'group_name': 'test3',
    },
    {
        'group_name': 'test4',
    }
]

for vlss in vlss_list:
    tmp = models.VlssApp.query.filter_by(vlss_name=vlss['vlss_name']).first()
    if not tmp or not tmp.id:
        continue
    for group in group_list:
        if not models.VlssSceneGroup.query.filter_by(group_name=group['group_name']).first():
            db.session.add(   #为每个 vlss_app 写入 场景组信息
                models.VlssSceneGroup(
                    app_id=tmp.id,
                    group_name=group['group_name'],
                    create_time=datetime.datetime.now(),
                    state=1
                )
            )
            db.session.commit()

        _group = models.VlssSceneGroup.query.filter_by(group_name=group['group_name']).first()
        if not _group or not _group.id:
            continue
        for scene in group.get('scene_list', []):
            if not models.VlssSceneItem.query.filter_by(group_id=_group.id, scene_name=scene['scene_name'], scene_type=scene['scene_type']).first():
                db.session.add(
                    models.VlssSceneItem(  #为每个 vlss_app 的 场景组 写入 场景
                        group_id=_group.id,
                        scene_name=scene['scene_name'],
                        scene_type=scene['scene_type'],
                        scene_sort=scene['scene_sort'],
                        scene_config=json.dumps(scene['scene_config']),
                        create_time=datetime.datetime.now(),
                        state=1
                    )
                )
                db.session.commit()
