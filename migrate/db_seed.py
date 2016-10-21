# coding: utf-8
from app import db, models

tag_list = [
    ('php', 'php is php'),
    ('java', 'java great'),
    ('javascript', 'javascript hehe'),
    ('vue', 'Intuitive, Fast and Composable MVVM for building interactive interfaces.')
]

[db.session.add(models.BlogTag(tag_name=item[0], description=item[1], state=1)) for item in tag_list if not models.BlogTag.query.filter_by(tag_name=item[0]).first()]

user_list = [
    ('小明', 'xiaoming@123.com', '123456'),
    ('小红', 'xiaohong@123.com', '123456'),
    ('小刚', 'xiaogang@123.com', '123456'),
]

[db.session.add(models.TblUser(nick=item[0], email=item[1], password=item[2], register_from='dev', state=1)) for item in user_list if not models.TblUser.query.filter_by(email=item[1]).first()]

db.session.commit()

for item in user_list:
    user = models.TblUser.query.filter_by(email=item[1]).first()
    if not models.BlogCategory.query.filter_by(user_id=user.id, cate_title='未分类').first():
        db.session.add(models.BlogCategory(cate_title='未分类', description='默认分类', user_id=user.id, state=1))

category_list = [
    ('开发', 'xiaoming@123.com', 'xiaoming 开发'),
    ('测试', 'xiaoming@123.com', 'xiaoming 测试'),
    ('动态', 'xiaogang@123.com', 'xiaogang 动态'),
]


for item in category_list:
    user = models.TblUser.query.filter_by(email=item[1]).first()
    if not models.BlogCategory.query.filter_by(user_id=user.id, cate_title=item[0]).first():
        db.session.add(models.BlogCategory(cate_title=item[0], description=item[2], user_id=user.id, state=1))

db.session.commit()

post_list = [
    ('开发', 'xiaoming@123.com', 'xiaoming 开发 第一篇文章', 'first-page', """
用户信息页和头像
回顾
在上一章中，我们已经完成了登录系统，因此我们可以使用 OpenIDs 登录以及登出。

今天，我们将要完成个人信息页。首先，我们将创建用户信息页，显示用户信息以及最近的 blog。作为其中一部分，我们将会学习到显示用户头像。接着，我们将要用户 web 表单用来编辑用户信息。

用户信息页
创建一个用户信息不需要引入新的概念。我们只要创建一个新的视图函数以及与它配套的 HTML 模版。

这里就是视图函数(文件 app/views.py):
""", ['php', 'vue']),
    ('测试', 'xiaoming@123.com', 'xiaoming 测试 测试文章', 'test-page', """
<div>Microblog:
     <a href="{{ url_for('index') }}">Home</a>
     {% if g.user.is_authenticated() %}
     | <a href="{{ url_for('user', nickname = g.user.nickname) }}">Your Profile</a>
     | <a href="{{ url_for('logout') }}">Logout</a>
     {% endif %}
 </div>
""", ['javascript', 'java']),
    ('动态', 'xiaogang@123.com', 'xiaogang 新闻', 'test-news', """
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader
| for our application. We just need to utilize it! We'll require it
| into the script here so that we do not have to worry about the
| loading of any our classes "manually". Feels great to relax.
""", ['java', 'vue']),
]


for item in post_list:
    user = models.TblUser.query.filter_by(email=item[1]).first()
    category = models.BlogCategory.query.filter_by(user_id=user.id, cate_title=item[0]).first()
    tag_list = [models.BlogTag.query.filter_by(tag_name=tag).first() for tag in item[5]]
    if not models.BlogPost.query.filter_by(user_id=user.id, slug=item[3]).first():
        db.session.add(models.BlogPost(user_id=user.id, category_id=category.id, title=item[2], slug=item[3], content_text=item[4], content_html=item[4], state=1))
        db.session.commit()
        post = models.BlogPost.query.filter_by(user_id=user.id, slug=item[3]).first()
        for  tag in tag_list:
            db.session.add(models.BlogPostTag(post_id=post.id, tag_id=tag.id, state=1))
        db.session.commit()

db.session.commit()