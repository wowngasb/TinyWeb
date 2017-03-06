# coding: utf-8
from app import db, models, md5key
import datetime

vlss_list = [
    ('demo', 'demo', '111576745758', 'fVYGq1S0gnrvoxZv77msq577jx7MQq3n', 13830, 'sub_eae37e48dab5f305516d07788eaaea60', 'pub_5bfb7a0ced7adb2ce454575747762679', 's_ceb80d29276f78653df081e5a9f0ac76', '123.8887.lcps.aodianyun.com'),
]

[db.session.add(
    models.VlssApp(
        login_name=item[0],
        password=md5key(item[1]),
        access_id=item[2],
        access_key=item[3],
        aodian_uin=item[4],
        dms_sub_key=item[5],
        dms_pub_key=item[6],
        dms_s_key=item[7],
        lcps_host=item[8],
        create_time=datetime.datetime.now(),
        state=1)
    ) for item in vlss_list if not models.VlssApp.query.filter_by(login_name=item[0]).first()]

db.session.commit()
