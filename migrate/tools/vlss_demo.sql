/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : vlss_demo

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-03-09 01:28:02
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for migrate_version
-- ----------------------------
DROP TABLE IF EXISTS `migrate_version`;
CREATE TABLE `migrate_version` (
  `repository_id` varchar(250) NOT NULL,
  `repository_path` text,
  `version` int(11) DEFAULT NULL,
  PRIMARY KEY (`repository_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for vlss_app
-- ----------------------------
DROP TABLE IF EXISTS `vlss_app`;
CREATE TABLE `vlss_app` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '虚拟演播厅自增id',
  `login_name` varchar(16) NOT NULL COMMENT '用户管理后台登录名',
  `password` varchar(32) NOT NULL COMMENT '用户管理后台登录名',
  `access_id` varchar(64) NOT NULL COMMENT '奥点云access_id',
  `access_key` varchar(64) NOT NULL COMMENT '奥点云access_key',
  `aodian_uin` int(10) unsigned NOT NULL COMMENT '奥点云 uin',
  `dms_sub_key` varchar(64) NOT NULL COMMENT 'DMS sub_key',
  `dms_pub_key` varchar(64) NOT NULL COMMENT 'DMS pub_key',
  `dms_s_key` varchar(64) NOT NULL COMMENT 'DMS s_key',
  `lcps_host` varchar(128) NOT NULL COMMENT '导播台域名  不带http://前缀 和 结尾/',
  `active_group_id` int(10) unsigned DEFAULT NULL COMMENT '激活的场景组id',
  `active_template_id` int(10) unsigned DEFAULT NULL COMMENT '激活的场景模版id',
  `state` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '1正常，2冻结，9删除',
  `last_login_ip` varchar(32) NOT NULL DEFAULT '' COMMENT '用户上次登录ip',
  `login_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户管理后台登录次数 登陆一次+1',
  `create_time` datetime NOT NULL COMMENT '记录创建时间',
  `uptime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `login_name_udx` (`login_name`) USING BTREE,
  KEY `aodian_uin_idx` (`aodian_uin`),
  KEY `lcps_host_idx` (`lcps_host`),
  KEY `active_template_id` (`active_template_id`),
  KEY `active_group_id` (`active_group_id`),
  CONSTRAINT `vlss_app_ibfk_1` FOREIGN KEY (`active_group_id`) REFERENCES `vlss_scene_group` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `vlss_app_ibfk_2` FOREIGN KEY (`active_template_id`) REFERENCES `vlss_scene_template` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for vlss_scene_group
-- ----------------------------
DROP TABLE IF EXISTS `vlss_scene_group`;
CREATE TABLE `vlss_scene_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `vlss_id` int(11) unsigned NOT NULL COMMENT '虚拟演播厅id',
  `group_name` varchar(32) NOT NULL COMMENT '场景组名称',
  `create_time` datetime NOT NULL COMMENT '记录创建时间',
  `uptime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `admin_idx` (`vlss_id`) USING BTREE,
  CONSTRAINT `vlss_scene_group_ibfk_1` FOREIGN KEY (`vlss_id`) REFERENCES `vlss_app` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for vlss_scene_item
-- ----------------------------
DROP TABLE IF EXISTS `vlss_scene_item`;
CREATE TABLE `vlss_scene_item` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '所属场景组id',
  `scene_name` varchar(32) NOT NULL COMMENT '场景名称',
  `scene_config` text NOT NULL COMMENT '场景配置 格式为 json 字符串',
  `scene_type` varchar(16) NOT NULL DEFAULT '' COMMENT '场景类型',
  `scene_sort` int(11) unsigned NOT NULL DEFAULT '10' COMMENT '场景叠加排序',
  `state` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '1正常,2隐藏,9删除',
  `create_time` datetime NOT NULL COMMENT '记录创建时间',
  `uptime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `scene_idx` (`group_id`) USING BTREE,
  CONSTRAINT `vlss_scene_item_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `vlss_scene_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for vlss_scene_template
-- ----------------------------
DROP TABLE IF EXISTS `vlss_scene_template`;
CREATE TABLE `vlss_scene_template` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `vlss_id` int(11) unsigned NOT NULL COMMENT '虚拟演播厅id',
  `template_name` varchar(16) NOT NULL COMMENT '模板名称',
  `switch_config` text NOT NULL COMMENT '模版配置 格式为 json 字符串',
  `front_pic` varchar(255) NOT NULL,
  `back_pic` varchar(255) NOT NULL,
  `state` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '1正常,9删除',
  `create_time` datetime NOT NULL COMMENT '记录创建时间',
  `uptime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `vlls_idx` (`vlss_id`) USING BTREE,
  CONSTRAINT `vlss_scene_template_ibfk_1` FOREIGN KEY (`vlss_id`) REFERENCES `vlss_app` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
