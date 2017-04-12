# coding: utf-8
from app import db, models, md5key
import datetime, json

user_data = {
    'login_name': 'demo',
    'password': md5key('demo'),
    'email': 'demo@demo.aom',
    'telephone': '15066661234',
    'access_id': '111576745758',
    'access_key': 'fVYGq1S0gnrvoxZv77msq577jx7MQq3n',
    'aodian_uin': 13830,
    'dms_sub_key': 'sub_eae37e48dab5f305516d07788eaaea60',
    'dms_pub_key': 'pub_5bfb7a0ced7adb2ce454575747762679',
    'dms_s_key': 's_ceb80d29276f78653df081e5a9f0ac76',
    'dms_id': 11300,
    'lss_app': 'dyy_281_438',
    'created_at':datetime.datetime.now(),
    'updated_at':datetime.datetime.now(),
    'state': 1,
}


db.session.add(   #写入 用户信息
    models.BasicUser(**user_data)
) if not models.BasicUser.query.filter_by(login_name = user_data['login_name']).first() else None
db.session.commit()

user = models.BasicUser.query.filter_by(login_name = user_data['login_name']).first()

vlss_list_data = [
    {
        'title': 'demo',
        'lcps_host': '123.8887.lcps.aodianyun.com',
        'user_id': user.id,
        'created_at':datetime.datetime.now(),
        'updated_at':datetime.datetime.now(),
        'state': 1,
    },
]

[db.session.add(   #写入 vlss app
        models.VlssApp(**vlss)
    ) for vlss in vlss_list_data if not models.VlssApp.query.filter_by(user_id=user.id, lcps_host=vlss['lcps_host']).first()]

db.session.commit()
vlss_list = models.VlssApp.query.filter_by(user_id = user.id).all()

template_list_data = [
    {
        "switch_config":json.dumps([
            {
                "name": "btn1",
                "param": [
                    {"w":208,"h":100,"x":107,"y":202,"checked":True,"v":100,"z":3},
                    {"w":262,"h":115,"x":236,"y":115,"checked":True,"v":0,"z":2},
                    {"w":100,"h":100,"x":0,"y":0,"checked":False,"v":0,"z":1}
                ],
            },
            {
                "name": "btn2",
                "param": [
                    {"w":208,"h":100,"x":107,"y":202,"checked":True,"v":100,"z":3},
                    {"w":262,"h":115,"x":236,"y":115,"checked":True,"v":0,"z":2},
                    {"w":100,"h":100,"x":0,"y":0,"checked":False,"v":0,"z":1}
                ],
            }
        ]),
        "front_pic":"http://test25.aodianyun.com/dist/studio/static/desk01.png",
        "back_pic":"http://test25.aodianyun.com/dist/studio/static/bg01.jpg",
        "title":"模版1",
    },
    {
        "switch_config":json.dumps([
            {
                "name": "btn1",
                "param": [
                    {"w":208,"h":100,"x":107,"y":202,"checked":True,"v":100,"z":3},
                    {"w":262,"h":115,"x":236,"y":115,"checked":True,"v":0,"z":2},
                    {"w":100,"h":100,"x":0,"y":0,"checked":False,"v":0,"z":1}
                ],
            },
            {
                "name": "btn2",
                "param": [
                    {"w":208,"h":100,"x":107,"y":202,"checked":True,"v":100,"z":3},
                    {"w":262,"h":115,"x":236,"y":115,"checked":True,"v":0,"z":2},
                    {"w":100,"h":100,"x":0,"y":0,"checked":False,"v":0,"z":1}
                ],
            }
        ]),
        "front_pic":"http://test25.aodianyun.com/dist/studio/static/desk02.png",
        "back_pic":"http://test25.aodianyun.com/dist/studio/static/bg02.jpg",
        "title":"模版2",
    },
    {
        "switch_config": json.dumps([
            {
                "name": "btn1",
                "param": [
                    {"w":208,"h":100,"x":107,"y":202,"checked":True,"v":100,"z":3},
                    {"w":262,"h":115,"x":236,"y":115,"checked":True,"v":0,"z":2},
                    {"w":100,"h":100,"x":0,"y":0,"checked":False,"v":0,"z":1}
                ],
            },
            {
                "name": "btn2",
                "param": [
                    {"w":208,"h":100,"x":107,"y":202,"checked":True,"v":100,"z":3},
                    {"w":262,"h":115,"x":236,"y":115,"checked":True,"v":0,"z":2},
                    {"w":100,"h":100,"x":0,"y":0,"checked":False,"v":0,"z":1}
                ],
            }
        ]),
        "front_pic":"http://test25.aodianyun.com/dist/studio/static/desk03.png",
        "back_pic":"http://test25.aodianyun.com/dist/studio/static/bg03.jpg",
        "title":"模版3",
    }
]

for vlss in vlss_list:
    for template in template_list_data:
        tmp = {
            'app_id': vlss.id,
            'created_at':datetime.datetime.now(),
            'updated_at':datetime.datetime.now(),
            'state': 1,
        }
        if not models.VlssSceneTemplate.query.filter_by(title=template['title']).first():
            template.update(tmp)
            #为每个 vlss_app 写入 模版 信息
            db.session.add( models.VlssSceneTemplate(**template) )

db.session.commit()

group_list_data = [
    {
        'title': 'group1',
    },
    {
        'title': 'group2',
    },
    {
        'title': 'group3',
    },
    {
        'title': 'group4',
    }
]

for vlss in vlss_list:
    for group in group_list_data:
        tmp = {
            'app_id': vlss.id,
            'created_at':datetime.datetime.now(),
            'updated_at':datetime.datetime.now(),
            'state': 1,
        }
        if not models.VlssSceneGroup.query.filter_by(title=group['title']).first():
            group.update(tmp)
            #为每个 vlss_app 写入 场景组 信息
            db.session.add( models.VlssSceneGroup(**group) )

db.session.commit()

scene_list_data = [
    {
        "title":"预告",
        "scene_type":"hsms_trailer",
        "scene_sort":1,
        "scene_config": json.dumps({
            "position":"2",
            "interval":"5",
            "contents":[
                {"image":"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/81f56530c3c655ab4d499d02178ce89f.png","title1":"11","title2":"112323"},
                {"image":"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/bc61e36619c8807f7c09e28d43001e43.png","title1":"花","title2":"魂牵梦萦 "},
                {"image":"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/90ce8a80effd29abb5c69bee8be66172.png","title1":"左","title2":"我"}
            ]
        })
    },
    {
        "title":"图片",
        "scene_type":"hsms_logo",
        "scene_sort":2,
        "scene_config": json.dumps({
            "src":"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/0be50bc3a2cdbc1987b598d36e4a946e.png",
            "style":{"left":"39%","top":"26%","width":"20%","height":"10%","opacity":"1"}
        })
    },
    {
        "title":"字幕",
        "scene_type":"hsms_subtitle",
        "scene_sort":3,
        "scene_config": json.dumps({
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
        })
    }
]

for vlss in vlss_list:
    group_list = models.VlssSceneGroup.query.filter_by(app_id = vlss.id).all()
    for group in group_list:
        tmp = {
            'group_id': group.id,
            'created_at':datetime.datetime.now(),
            'updated_at':datetime.datetime.now(),
            'state': 1,
        }
        for scene in scene_list_data:
            if not models.VlssSceneItem.query.filter_by(title=scene['title'], group_id=group.id).first():
                scene.update(tmp)
                #为每个 vlss_app 写入 场景组 信息
                db.session.add( models.VlssSceneItem(**scene) )

db.session.commit()