# coding: utf-8
from app import models
import json
import sqlalchemy

dir_obj = lambda obj: ((attr, getattr(obj, attr)) for attr in dir(obj))

tbl_list = [tbl for _, tbl in dir_obj(models) if hasattr(tbl, '__tablename__') ]

def info_columns(item):
    tmp = item.property.columns[0]
    need = [
         'autoincrement',
         'default',
         'description',
         'doc',
         'index',
         'is_clause_element',
         'is_literal',
         'is_selectable',
         'key',
         'name',
         'nullable',
         'primary_key',
         'server_default',
         'supports_execution',
         'system',
         'unique',
    ]
    col = {key:getattr(tmp, key, None) for key in need }
    col.setdefault('type', str(getattr(tmp, 'type', 'type')) )
    return col

def get_columns(tbl):
    def _find(seqs, find):
        for idx, item in enumerate(seqs):
            if find(item):
                return idx, item
        return -1, None

    def _move(seqs, find, idx):
        idx = idx if idx>=0 else len(seqs) + idx
        f_idx, f_item = _find(seqs, find)
        if f_idx >= 0 and f_idx != idx:
            tmp = seqs[idx]
            seqs[idx] = f_item
            seqs[f_idx] = tmp

    cols = [info_columns(item) for _, item in dir_obj(tbl) if isinstance(item, sqlalchemy.orm.attributes.InstrumentedAttribute) ]
    _move(cols, lambda col: col['name']=='id', 0)
    _move(cols, lambda col: col['name']=='state', -3)
    _move(cols, lambda col: col['name']=='created_at', -2)
    _move(cols, lambda col: col['name']=='updated_at', -1)
    return cols

tbl_json = []
for tbl in tbl_list:
    tmp = {
        'table_name': tbl.__tablename__,
        'columns': get_columns(tbl),
        'doc': tbl.__doc__.decode('utf-8'),
    }
    tmp['_columns'] = [[col.get('name', ''), col.get('type', ''), col.get('doc', '') if col.get('doc', '') else ''] for col in tmp['columns']]
    tbl_json.append(tmp)

print json.dumps(tbl_json, indent=2)