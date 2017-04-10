/*
Navicat MySQL Data Transfer

Source Server         : loacalhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : vlss_demo

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-04-09 23:58:45
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `basic_user`
-- ----------------------------
DROP TABLE IF EXISTS `basic_user`;
CREATE TABLE `basic_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login_name` varchar(16) NOT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(32) NOT NULL,
  `telephone` varchar(16) NOT NULL,
  `aodian_uin` int(11) NOT NULL,
  `access_id` varchar(64) NOT NULL,
  `access_key` varchar(64) NOT NULL,
  `lss_app` varchar(32) NOT NULL,
  `dms_id` int(11) NOT NULL,
  `dms_sub_key` varchar(64) NOT NULL,
  `dms_pub_key` varchar(64) NOT NULL,
  `dms_s_key` varchar(64) NOT NULL,
  `state` smallint(6) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_basic_user_login_name` (`login_name`),
  KEY `ix_basic_user_aodian_uin` (`aodian_uin`),
  KEY `ix_basic_user_telephone` (`telephone`),
  KEY `ix_basic_user_lss_app` (`lss_app`),
  KEY `ix_basic_user_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of basic_user
-- ----------------------------
INSERT INTO `basic_user` VALUES ('1', 'demo', '59c2c7a4643b3fb769ac78e13d58c397', 'demo@demo.aom', '15066661234', '13830', '111576745758', 'fVYGq1S0gnrvoxZv77msq577jx7MQq3n', 'dyy_281_438', '11300', 'sub_eae37e48dab5f305516d07788eaaea60', 'pub_5bfb7a0ced7adb2ce454575747762679', 's_ceb80d29276f78653df081e5a9f0ac76', '1', '2017-04-09 17:33:26', '2017-04-09 17:33:26');

-- ----------------------------
-- Table structure for `migrate_version`
-- ----------------------------
DROP TABLE IF EXISTS `migrate_version`;
CREATE TABLE `migrate_version` (
  `repository_id` varchar(250) NOT NULL,
  `repository_path` text,
  `version` int(11) DEFAULT NULL,
  PRIMARY KEY (`repository_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of migrate_version
-- ----------------------------
INSERT INTO `migrate_version` VALUES ('database repository', 'E:\\xampp\\TinyWeb\\migrate\\db_repository', '1');

-- ----------------------------
-- Table structure for `rbac_permission`
-- ----------------------------
DROP TABLE IF EXISTS `rbac_permission`;
CREATE TABLE `rbac_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `p_type` varchar(32) NOT NULL,
  `p_key` varchar(64) NOT NULL,
  `title` varchar(64) NOT NULL,
  `description` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ix_rbac_permission_p_key` (`p_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of rbac_permission
-- ----------------------------

-- ----------------------------
-- Table structure for `rbac_role`
-- ----------------------------
DROP TABLE IF EXISTS `rbac_role`;
CREATE TABLE `rbac_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(32) NOT NULL,
  `description` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of rbac_role
-- ----------------------------

-- ----------------------------
-- Table structure for `rbac_role_permission`
-- ----------------------------
DROP TABLE IF EXISTS `rbac_role_permission`;
CREATE TABLE `rbac_role_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `state` smallint(6) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ix_rbac_role_permission_role_id` (`role_id`),
  KEY `ix_rbac_role_permission_permission_id` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of rbac_role_permission
-- ----------------------------

-- ----------------------------
-- Table structure for `rbac_user_role`
-- ----------------------------
DROP TABLE IF EXISTS `rbac_user_role`;
CREATE TABLE `rbac_user_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ix_rbac_user_role_user_id` (`user_id`),
  KEY `ix_rbac_user_role_role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of rbac_user_role
-- ----------------------------

-- ----------------------------
-- Table structure for `record_console_login`
-- ----------------------------
DROP TABLE IF EXISTS `record_console_login`;
CREATE TABLE `record_console_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `login_ip` varchar(32) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ix_record_console_login_user_id` (`user_id`),
  KEY `ix_record_console_login_login_ip` (`login_ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of record_console_login
-- ----------------------------

-- ----------------------------
-- Table structure for `vlss_app`
-- ----------------------------
DROP TABLE IF EXISTS `vlss_app`;
CREATE TABLE `vlss_app` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `lcps_host` varchar(128) NOT NULL,
  `title` varchar(16) NOT NULL,
  `active_group_id` int(11) DEFAULT NULL,
  `active_template_id` int(11) DEFAULT NULL,
  `state` smallint(6) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ix_vlss_app_user_id` (`user_id`),
  KEY `ix_vlss_app_lcps_host` (`lcps_host`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of vlss_app
-- ----------------------------
INSERT INTO `vlss_app` VALUES ('1', '1', '123.8887.lcps.aodianyun.com', 'demo', null, null, '1', '2017-04-09 17:33:26', '2017-04-09 17:33:26');

-- ----------------------------
-- Table structure for `vlss_scene_group`
-- ----------------------------
DROP TABLE IF EXISTS `vlss_scene_group`;
CREATE TABLE `vlss_scene_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` int(11) NOT NULL,
  `title` varchar(32) NOT NULL,
  `state` smallint(6) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ix_vlss_scene_group_app_id` (`app_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of vlss_scene_group
-- ----------------------------
INSERT INTO `vlss_scene_group` VALUES ('1', '1', 'group1', '1', '2017-04-09 17:33:27', '2017-04-09 17:33:27');
INSERT INTO `vlss_scene_group` VALUES ('2', '1', 'group2', '1', '2017-04-09 17:33:27', '2017-04-09 17:33:27');
INSERT INTO `vlss_scene_group` VALUES ('3', '1', 'group3', '1', '2017-04-09 17:33:27', '2017-04-09 17:33:27');
INSERT INTO `vlss_scene_group` VALUES ('4', '1', 'group4', '1', '2017-04-09 17:33:27', '2017-04-09 17:33:27');

-- ----------------------------
-- Table structure for `vlss_scene_item`
-- ----------------------------
DROP TABLE IF EXISTS `vlss_scene_item`;
CREATE TABLE `vlss_scene_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `title` varchar(32) NOT NULL,
  `scene_config` text NOT NULL,
  `scene_type` varchar(16) NOT NULL,
  `scene_sort` int(11) NOT NULL,
  `state` smallint(6) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ix_vlss_scene_item_group_id` (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of vlss_scene_item
-- ----------------------------
INSERT INTO `vlss_scene_item` VALUES ('1', '1', '预告', '{\"position\": \"2\", \"interval\": \"5\", \"contents\": [{\"title1\": \"11\", \"image\": \"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/81f56530c3c655ab4d499d02178ce89f.png\", \"title2\": \"112323\"}, {\"title1\": \"\\u82b1\", \"image\": \"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/bc61e36619c8807f7c09e28d43001e43.png\", \"title2\": \"\\u9b42\\u7275\\u68a6\\u8426 \"}, {\"title1\": \"\\u5de6\", \"image\": \"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/90ce8a80effd29abb5c69bee8be66172.png\", \"title2\": \"\\u6211\"}]}', 'hsms-trailer', '1', '1', '2017-04-09 17:33:27', '2017-04-09 17:33:27');
INSERT INTO `vlss_scene_item` VALUES ('2', '1', '图片', '{\"src\": \"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/0be50bc3a2cdbc1987b598d36e4a946e.png\", \"style\": {\"opacity\": \"1\", \"width\": \"20%\", \"top\": \"26%\", \"height\": \"10%\", \"left\": \"39%\"}}', 'hsms-logo', '2', '1', '2017-04-09 17:33:27', '2017-04-09 17:33:27');
INSERT INTO `vlss_scene_item` VALUES ('3', '1', '字幕', '{\"fixedText\": {\"color\": \"#1DCD9C\", \"text\": \"\\u7c89\\u8272\\u7c89\\u8272\", \"shadow\": \"0\", \"align\": \"left\"}, \"backgound\": \"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/8fb26f08142738c4a7e5115252f328fb.png\", \"scrollText\": {\"color\": \"#AB102D\", \"text\": \"\\u5927\\u5bb6\\u90fd\\u5173\\u6ce8\\u4e00\\u4e0b\", \"shadow\": \"0\", \"speed\": \"40\", \"scrollTimes\": \"0\"}}', 'hsms-subtitle', '3', '1', '2017-04-09 17:33:27', '2017-04-09 17:33:27');
INSERT INTO `vlss_scene_item` VALUES ('4', '2', '预告', '{\"position\": \"2\", \"interval\": \"5\", \"contents\": [{\"title1\": \"11\", \"image\": \"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/81f56530c3c655ab4d499d02178ce89f.png\", \"title2\": \"112323\"}, {\"title1\": \"\\u82b1\", \"image\": \"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/bc61e36619c8807f7c09e28d43001e43.png\", \"title2\": \"\\u9b42\\u7275\\u68a6\\u8426 \"}, {\"title1\": \"\\u5de6\", \"image\": \"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/90ce8a80effd29abb5c69bee8be66172.png\", \"title2\": \"\\u6211\"}]}', 'hsms-trailer', '1', '1', '2017-04-09 17:33:27', '2017-04-09 17:33:27');
INSERT INTO `vlss_scene_item` VALUES ('5', '2', '图片', '{\"src\": \"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/0be50bc3a2cdbc1987b598d36e4a946e.png\", \"style\": {\"opacity\": \"1\", \"width\": \"20%\", \"top\": \"26%\", \"height\": \"10%\", \"left\": \"39%\"}}', 'hsms-logo', '2', '1', '2017-04-09 17:33:27', '2017-04-09 17:33:27');
INSERT INTO `vlss_scene_item` VALUES ('6', '2', '字幕', '{\"fixedText\": {\"color\": \"#1DCD9C\", \"text\": \"\\u7c89\\u8272\\u7c89\\u8272\", \"shadow\": \"0\", \"align\": \"left\"}, \"backgound\": \"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/8fb26f08142738c4a7e5115252f328fb.png\", \"scrollText\": {\"color\": \"#AB102D\", \"text\": \"\\u5927\\u5bb6\\u90fd\\u5173\\u6ce8\\u4e00\\u4e0b\", \"shadow\": \"0\", \"speed\": \"40\", \"scrollTimes\": \"0\"}}', 'hsms-subtitle', '3', '1', '2017-04-09 17:33:27', '2017-04-09 17:33:27');
INSERT INTO `vlss_scene_item` VALUES ('7', '3', '预告', '{\"position\": \"2\", \"interval\": \"5\", \"contents\": [{\"title1\": \"11\", \"image\": \"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/81f56530c3c655ab4d499d02178ce89f.png\", \"title2\": \"112323\"}, {\"title1\": \"\\u82b1\", \"image\": \"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/bc61e36619c8807f7c09e28d43001e43.png\", \"title2\": \"\\u9b42\\u7275\\u68a6\\u8426 \"}, {\"title1\": \"\\u5de6\", \"image\": \"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/90ce8a80effd29abb5c69bee8be66172.png\", \"title2\": \"\\u6211\"}]}', 'hsms-trailer', '1', '1', '2017-04-09 17:33:27', '2017-04-09 17:33:27');
INSERT INTO `vlss_scene_item` VALUES ('8', '3', '图片', '{\"src\": \"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/0be50bc3a2cdbc1987b598d36e4a946e.png\", \"style\": {\"opacity\": \"1\", \"width\": \"20%\", \"top\": \"26%\", \"height\": \"10%\", \"left\": \"39%\"}}', 'hsms-logo', '2', '1', '2017-04-09 17:33:27', '2017-04-09 17:33:27');
INSERT INTO `vlss_scene_item` VALUES ('9', '3', '字幕', '{\"fixedText\": {\"color\": \"#1DCD9C\", \"text\": \"\\u7c89\\u8272\\u7c89\\u8272\", \"shadow\": \"0\", \"align\": \"left\"}, \"backgound\": \"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/8fb26f08142738c4a7e5115252f328fb.png\", \"scrollText\": {\"color\": \"#AB102D\", \"text\": \"\\u5927\\u5bb6\\u90fd\\u5173\\u6ce8\\u4e00\\u4e0b\", \"shadow\": \"0\", \"speed\": \"40\", \"scrollTimes\": \"0\"}}', 'hsms-subtitle', '3', '1', '2017-04-09 17:33:27', '2017-04-09 17:33:27');
INSERT INTO `vlss_scene_item` VALUES ('10', '4', '预告', '{\"position\": \"2\", \"interval\": \"5\", \"contents\": [{\"title1\": \"11\", \"image\": \"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/81f56530c3c655ab4d499d02178ce89f.png\", \"title2\": \"112323\"}, {\"title1\": \"\\u82b1\", \"image\": \"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/bc61e36619c8807f7c09e28d43001e43.png\", \"title2\": \"\\u9b42\\u7275\\u68a6\\u8426 \"}, {\"title1\": \"\\u5de6\", \"image\": \"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/90ce8a80effd29abb5c69bee8be66172.png\", \"title2\": \"\\u6211\"}]}', 'hsms-trailer', '1', '1', '2017-04-09 17:33:27', '2017-04-09 17:33:27');
INSERT INTO `vlss_scene_item` VALUES ('11', '4', '图片', '{\"src\": \"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/0be50bc3a2cdbc1987b598d36e4a946e.png\", \"style\": {\"opacity\": \"1\", \"width\": \"20%\", \"top\": \"26%\", \"height\": \"10%\", \"left\": \"39%\"}}', 'hsms-logo', '2', '1', '2017-04-09 17:33:27', '2017-04-09 17:33:27');
INSERT INTO `vlss_scene_item` VALUES ('12', '4', '字幕', '{\"fixedText\": {\"color\": \"#1DCD9C\", \"text\": \"\\u7c89\\u8272\\u7c89\\u8272\", \"shadow\": \"0\", \"align\": \"left\"}, \"backgound\": \"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/8fb26f08142738c4a7e5115252f328fb.png\", \"scrollText\": {\"color\": \"#AB102D\", \"text\": \"\\u5927\\u5bb6\\u90fd\\u5173\\u6ce8\\u4e00\\u4e0b\", \"shadow\": \"0\", \"speed\": \"40\", \"scrollTimes\": \"0\"}}', 'hsms-subtitle', '3', '1', '2017-04-09 17:33:27', '2017-04-09 17:33:27');

-- ----------------------------
-- Table structure for `vlss_scene_template`
-- ----------------------------
DROP TABLE IF EXISTS `vlss_scene_template`;
CREATE TABLE `vlss_scene_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` int(11) NOT NULL,
  `title` varchar(32) NOT NULL,
  `switch_config` text NOT NULL,
  `active_switch_name` varchar(16) DEFAULT NULL,
  `front_pic` varchar(255) NOT NULL,
  `back_pic` varchar(255) NOT NULL,
  `state` smallint(6) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ix_vlss_scene_template_app_id` (`app_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of vlss_scene_template
-- ----------------------------
INSERT INTO `vlss_scene_template` VALUES ('1', '1', '模版1', '[{\"name\": \"btn1\", \"param\": [{\"checked\": true, \"h\": 100, \"w\": 208, \"v\": 100, \"y\": 202, \"x\": 107, \"z\": 3}, {\"checked\": true, \"h\": 115, \"w\": 262, \"v\": 0, \"y\": 115, \"x\": 236, \"z\": 2}, {\"checked\": false, \"h\": 100, \"w\": 100, \"v\": 0, \"y\": 0, \"x\": 0, \"z\": 1}]}, {\"name\": \"btn2\", \"param\": [{\"checked\": true, \"h\": 100, \"w\": 208, \"v\": 100, \"y\": 202, \"x\": 107, \"z\": 3}, {\"checked\": true, \"h\": 115, \"w\": 262, \"v\": 0, \"y\": 115, \"x\": 236, \"z\": 2}, {\"checked\": false, \"h\": 100, \"w\": 100, \"v\": 0, \"y\": 0, \"x\": 0, \"z\": 1}]}]', null, 'http://test25.aodianyun.com/dist/studio/static/desk01.png', 'http://test25.aodianyun.com/dist/studio/static/bg01.jpg', '1', '2017-04-09 17:33:26', '2017-04-09 17:33:26');
INSERT INTO `vlss_scene_template` VALUES ('2', '1', '模版2', '[{\"name\": \"btn1\", \"param\": [{\"checked\": true, \"h\": 100, \"w\": 208, \"v\": 100, \"y\": 202, \"x\": 107, \"z\": 3}, {\"checked\": true, \"h\": 115, \"w\": 262, \"v\": 0, \"y\": 115, \"x\": 236, \"z\": 2}, {\"checked\": false, \"h\": 100, \"w\": 100, \"v\": 0, \"y\": 0, \"x\": 0, \"z\": 1}]}, {\"name\": \"btn2\", \"param\": [{\"checked\": true, \"h\": 100, \"w\": 208, \"v\": 100, \"y\": 202, \"x\": 107, \"z\": 3}, {\"checked\": true, \"h\": 115, \"w\": 262, \"v\": 0, \"y\": 115, \"x\": 236, \"z\": 2}, {\"checked\": false, \"h\": 100, \"w\": 100, \"v\": 0, \"y\": 0, \"x\": 0, \"z\": 1}]}]', null, 'http://test25.aodianyun.com/dist/studio/static/desk02.png', 'http://test25.aodianyun.com/dist/studio/static/bg02.jpg', '1', '2017-04-09 17:33:27', '2017-04-09 17:33:27');
INSERT INTO `vlss_scene_template` VALUES ('3', '1', '模版3', '[{\"name\": \"btn1\", \"param\": [{\"checked\": true, \"h\": 100, \"w\": 208, \"v\": 100, \"y\": 202, \"x\": 107, \"z\": 3}, {\"checked\": true, \"h\": 115, \"w\": 262, \"v\": 0, \"y\": 115, \"x\": 236, \"z\": 2}, {\"checked\": false, \"h\": 100, \"w\": 100, \"v\": 0, \"y\": 0, \"x\": 0, \"z\": 1}]}, {\"name\": \"btn2\", \"param\": [{\"checked\": true, \"h\": 100, \"w\": 208, \"v\": 100, \"y\": 202, \"x\": 107, \"z\": 3}, {\"checked\": true, \"h\": 115, \"w\": 262, \"v\": 0, \"y\": 115, \"x\": 236, \"z\": 2}, {\"checked\": false, \"h\": 100, \"w\": 100, \"v\": 0, \"y\": 0, \"x\": 0, \"z\": 1}]}]', null, 'http://test25.aodianyun.com/dist/studio/static/desk03.png', 'http://test25.aodianyun.com/dist/studio/static/bg03.jpg', '1', '2017-04-09 17:33:27', '2017-04-09 17:33:27');
