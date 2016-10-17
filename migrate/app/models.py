# coding: utf-8
from sqlalchemy import BigInteger, Column, DateTime, Index, Integer, SmallInteger, String, Text, text
from sqlalchemy.ext.declarative import declarative_base

from app import db, app

Base = db.Model


class BlogCategory(Base):
    __tablename__ = 'blog_categories'

    id = Column(BigInteger, primary_key=True)
    user_id = Column(BigInteger, nullable=False, index=True)
    cate_title = Column(String(32, u'utf8_unicode_ci'), nullable=False)
    description = Column(String(255, u'utf8_unicode_ci'), nullable=False, server_default=text("''"))
    rank = Column(SmallInteger, nullable=False, index=True, server_default=text("'0'"))
    state = Column(SmallInteger, nullable=False, server_default=text("'0'"))
    created_at = Column(DateTime, nullable=False, index=True, server_default=text("CURRENT_TIMESTAMP"))
    updated_at = Column(DateTime, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"))
    delete_at = Column(DateTime, nullable=False, server_default=text("'0000-00-00 00:00:00'"))


class BlogComment(Base):
    __tablename__ = 'blog_comments'

    id = Column(BigInteger, primary_key=True)
    user_id = Column(BigInteger, nullable=False, index=True)
    post_id = Column(BigInteger, nullable=False, index=True)
    comment_id = Column(BigInteger, nullable=False, index=True)
    content_text = Column(Text(collation=u'utf8_unicode_ci'), nullable=False)
    content_html = Column(Text(collation=u'utf8_unicode_ci'), nullable=False)
    state = Column(SmallInteger, nullable=False, server_default=text("'0'"))
    created_at = Column(DateTime, nullable=False, index=True, server_default=text("CURRENT_TIMESTAMP"))
    updated_at = Column(DateTime, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"))
    deleted_at = Column(DateTime, nullable=False, server_default=text("'0000-00-00 00:00:00'"))


class BlogNotification(Base):
    __tablename__ = 'blog_notifications'

    id = Column(BigInteger, primary_key=True)
    user_id = Column(BigInteger, nullable=False, index=True)
    post_id = Column(BigInteger, nullable=False, index=True)
    state = Column(SmallInteger, nullable=False, server_default=text("'0'"))
    read_at = Column(DateTime, nullable=False, server_default=text("'0000-00-00 00:00:00'"))
    created_at = Column(DateTime, nullable=False, index=True, server_default=text("CURRENT_TIMESTAMP"))
    updated_at = Column(DateTime, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"))
    deleted_at = Column(DateTime, nullable=False, server_default=text("'0000-00-00 00:00:00'"))


class BlogPostTag(Base):
    __tablename__ = 'blog_post_tag'

    id = Column(BigInteger, primary_key=True)
    post_id = Column(BigInteger, nullable=False, index=True)
    tag_id = Column(BigInteger, nullable=False, index=True)
    state = Column(SmallInteger, nullable=False, server_default=text("'0'"))
    created_at = Column(DateTime, nullable=False, server_default=text("CURRENT_TIMESTAMP"))
    updated_at = Column(DateTime, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"))
    deleted_at = Column(DateTime, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"))


class BlogPost(Base):
    __tablename__ = 'blog_posts'

    id = Column(BigInteger, primary_key=True)
    user_id = Column(BigInteger, nullable=False, index=True)
    category_id = Column(BigInteger, nullable=False, index=True)
    title = Column(String(255, u'utf8_unicode_ci'), nullable=False)
    description = Column(String(255, u'utf8_unicode_ci'), nullable=False, server_default=text("''"))
    slug = Column(String(255, u'utf8_unicode_ci'), nullable=False, unique=True)
    content_text = Column(Text(collation=u'utf8_unicode_ci'), nullable=False)
    content_html = Column(Text(collation=u'utf8_unicode_ci'), nullable=False)
    view_count = Column(Integer, nullable=False, server_default=text("'0'"))
    state = Column(SmallInteger, nullable=False, server_default=text("'0'"))
    created_at = Column(DateTime, nullable=False, server_default=text("CURRENT_TIMESTAMP"))
    updated_at = Column(DateTime, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"))
    published_at = Column(DateTime, nullable=False, index=True, server_default=text("'0000-00-00 00:00:00'"))
    deleted_at = Column(DateTime, nullable=False, server_default=text("'0000-00-00 00:00:00'"))


class BlogTag(Base):
    __tablename__ = 'blog_tags'

    id = Column(BigInteger, primary_key=True)
    tag_name = Column(String(32, u'utf8_unicode_ci'), nullable=False, unique=True)
    description = Column(String(255, u'utf8_unicode_ci'), nullable=False)
    state = Column(SmallInteger, nullable=False, server_default=text("'0'"))
    created_at = Column(DateTime, nullable=False, index=True, server_default=text("CURRENT_TIMESTAMP"))
    updated_at = Column(DateTime, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"))
    delete_at = Column(DateTime, nullable=False, server_default=text("'0000-00-00 00:00:00'"))


class TblUser(Base):
    __tablename__ = 'tbl_users'

    id = Column(BigInteger, primary_key=True)
    nick = Column(String(64, u'utf8_unicode_ci'), nullable=False, unique=True)
    email = Column(String(64, u'utf8_unicode_ci'), nullable=False, unique=True)
    password = Column(String(64, u'utf8_unicode_ci'), nullable=False)
    register_from = Column(String(64, u'utf8_unicode_ci'), nullable=False, server_default=text("'web'"))
    github_id = Column(BigInteger, nullable=False, server_default=text("'0'"))
    github_name = Column(String(64, u'utf8_unicode_ci'), nullable=False, server_default=text("''"))
    website = Column(String(64, u'utf8_unicode_ci'), nullable=False, server_default=text("''"))
    real_name = Column(String(64, u'utf8_unicode_ci'), nullable=False, server_default=text("''"))
    description = Column(String(255, u'utf8_unicode_ci'), nullable=False, server_default=text("''"))
    avatar_image = Column(String(255, u'utf8_unicode_ci'), nullable=False, server_default=text("''"))
    state = Column(SmallInteger, nullable=False, server_default=text("'0'"))
    created_at = Column(DateTime, nullable=False, index=True, server_default=text("CURRENT_TIMESTAMP"))
    updated_at = Column(DateTime, nullable=False, server_default=text("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"))
    delete_at = Column(DateTime, nullable=False, server_default=text("'0000-00-00 00:00:00'"))
