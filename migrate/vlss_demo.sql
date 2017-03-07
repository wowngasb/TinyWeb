/*
Navicat MySQL Data Transfer

Source Server         : test25
Source Server Version : 50548
Source Host           : 121.40.128.45:3306
Source Database       : vlss_demo

Target Server Type    : MYSQL
Target Server Version : 50548
File Encoding         : 65001

Date: 2017-03-07 18:25:24
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
-- Records of migrate_version
-- ----------------------------
INSERT INTO `migrate_version` VALUES ('database repository', 'E:\\xampp\\TinyWeb\\migrate\\db_repository', '0');

-- ----------------------------
-- Table structure for vlss_app
-- ----------------------------
DROP TABLE IF EXISTS `vlss_app`;
CREATE TABLE `vlss_app` (
  `vlss_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '虚拟演播厅自增id',
  `login_name` varchar(16) NOT NULL COMMENT '用户管理后台登录名',
  `password` varchar(32) NOT NULL COMMENT '用户管理后台登录名',
  `access_id` varchar(64) NOT NULL COMMENT '奥点云access_id',
  `access_key` varchar(64) NOT NULL COMMENT '奥点云access_key',
  `aodian_uin` int(10) unsigned NOT NULL COMMENT '奥点云 uin',
  `dms_sub_key` varchar(64) NOT NULL COMMENT 'DMS sub_key',
  `dms_pub_key` varchar(64) NOT NULL COMMENT 'DMS pub_key',
  `dms_s_key` varchar(64) NOT NULL COMMENT 'DMS s_key',
  `lcps_host` varchar(128) NOT NULL COMMENT '导播台域名  不带http://前缀 和 结尾/',
  `active_group_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '激活的场景组id',
  `active_template_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '激活的场景模版id',
  `state` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '1正常，2冻结，9删除',
  `last_login_ip` varchar(32) NOT NULL DEFAULT '' COMMENT '用户上次登录ip',
  `login_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户管理后台登录次数 登陆一次+1',
  `create_time` datetime NOT NULL COMMENT '记录创建时间',
  `uptime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`vlss_id`),
  UNIQUE KEY `login_name_udx` (`login_name`) USING BTREE,
  KEY `aodian_uin_idx` (`aodian_uin`),
  KEY `lcps_host_idx` (`lcps_host`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of vlss_app
-- ----------------------------
INSERT INTO `vlss_app` VALUES ('2', 'demo', '4e6c655d530efbdd08e81b1d0ae1dc3c', '111576745758', 'fVYGq1S0gnrvoxZv77msq577jx7MQq3n', '13830', 'sub_eae37e48dab5f305516d07788eaaea60', 'pub_5bfb7a0ced7adb2ce454575747762679', 's_ceb80d29276f78653df081e5a9f0ac76', '123.8887.lcps.aodianyun.com', '0', '0', '1', '', '0', '2017-03-06 10:23:57', '2017-03-06 10:24:52');

-- ----------------------------
-- Table structure for vlss_scene_group
-- ----------------------------
DROP TABLE IF EXISTS `vlss_scene_group`;
CREATE TABLE `vlss_scene_group` (
  `group_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `vlss_id` int(11) unsigned NOT NULL COMMENT '虚拟演播厅id',
  `group_name` varchar(32) NOT NULL COMMENT '场景组名称',
  `state` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '1正常，9删除',
  `create_time` datetime NOT NULL COMMENT '记录创建时间',
  `uptime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`group_id`),
  KEY `admin_idx` (`vlss_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of vlss_scene_group
-- ----------------------------
INSERT INTO `vlss_scene_group` VALUES ('1', '2', 'test1', '1', '2017-03-06 11:34:25', '2017-03-06 11:35:21');
INSERT INTO `vlss_scene_group` VALUES ('2', '2', 'test2', '1', '2017-03-06 11:34:25', '2017-03-06 11:35:21');
INSERT INTO `vlss_scene_group` VALUES ('3', '2', 'test3', '1', '2017-03-06 11:34:25', '2017-03-06 11:35:21');
INSERT INTO `vlss_scene_group` VALUES ('4', '2', 'test4', '1', '2017-03-06 11:34:25', '2017-03-06 11:35:21');

-- ----------------------------
-- Table structure for vlss_scene_item
-- ----------------------------
DROP TABLE IF EXISTS `vlss_scene_item`;
CREATE TABLE `vlss_scene_item` (
  `scene_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `vlss_id` int(11) unsigned NOT NULL COMMENT '虚拟演播厅id',
  `group_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '所属场景组id',
  `scene_name` varchar(32) NOT NULL COMMENT '场景名称',
  `scene_config` text NOT NULL COMMENT '场景配置 格式为 json 字符串',
  `scene_type` varchar(16) NOT NULL DEFAULT '' COMMENT '场景类型',
  `scene_sort` int(11) unsigned NOT NULL DEFAULT '10' COMMENT '场景叠加排序',
  `state` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '1正常,2隐藏,9删除',
  `create_time` datetime NOT NULL COMMENT '记录创建时间',
  `uptime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`scene_id`),
  KEY `admin_idx` (`vlss_id`) USING BTREE,
  KEY `scene_idx` (`group_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of vlss_scene_item
-- ----------------------------
INSERT INTO `vlss_scene_item` VALUES ('1', '2', '1', '预告', '{\"position\": \"2\", \"interval\": \"5\", \"contents\": [{\"title1\": \"11\", \"image\": \"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/81f56530c3c655ab4d499d02178ce89f.png\", \"title2\": \"112323\"}, {\"title1\": \"\\u82b1\", \"image\": \"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/bc61e36619c8807f7c09e28d43001e43.png\", \"title2\": \"\\u9b42\\u7275\\u68a6\\u8426 \"}, {\"title1\": \"\\u5de6\", \"image\": \"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/90ce8a80effd29abb5c69bee8be66172.png\", \"title2\": \"\\u6211\"}]}', 'hsms-trailer', '2', '1', '2017-03-06 12:09:53', '2017-03-06 12:10:49');
INSERT INTO `vlss_scene_item` VALUES ('2', '2', '1', '图片', '{\"src\": \"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/0be50bc3a2cdbc1987b598d36e4a946e.png\", \"style\": {\"opacity\": \"1\", \"width\": \"20%\", \"top\": \"26%\", \"height\": \"10%\", \"left\": \"39%\"}}', 'hsms-logo', '3', '1', '2017-03-06 12:09:53', '2017-03-06 12:10:49');
INSERT INTO `vlss_scene_item` VALUES ('3', '2', '1', '字幕1-1', '{\"fixedText\": {\"color\": \"#1DCD9C\", \"text\": \"\\u7c89\\u8272\\u7c89\\u8272\", \"shadow\": \"0\", \"align\": \"left\"}, \"backgound\": \"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/8fb26f08142738c4a7e5115252f328fb.png\", \"scrollText\": {\"color\": \"#AB102D\", \"text\": \"\\u5927\\u5bb6\\u90fd\\u5173\\u6ce8\\u4e00\\u4e0b\", \"shadow\": \"0\", \"speed\": \"40\", \"scrollTimes\": \"0\"}}', 'hsms-subtitle', '3', '1', '2017-03-06 12:09:53', '2017-03-06 12:10:49');
INSERT INTO `vlss_scene_item` VALUES ('4', '2', '1', '台标', '{\"url\": \"http://1436.long-vod.cdn.aodianyun.com/mfs/1436/wis/0x0/4a131ca49fc6362af23618f085f4d62b.png\", \"position\": \"1\"}', 'hsms-tvlogo', '1', '1', '2017-03-06 12:09:53', '2017-03-06 12:10:49');
INSERT INTO `vlss_scene_item` VALUES ('5', '2', '1', '记分牌', '{\"homeNamedText\": {\"text\": \"\\u708e\\u5e1d\"}, \"awayNameText\": {\"text\": \"\\u9ec4\\u5e1d\"}, \"backgound\": \"http://test25.aodianyun.com/aae/hsms/assets/img/hsms-scoreboard/scoreboard.png\", \"scoreText\": {\"text\": \"3\\uff1a0\"}, \"clockText\": {\"control\": \"playTime\", \"stext\": \"00\", \"mtext\": \"10\"}}', 'hsms-scoreboard', '1', '1', '2017-03-06 12:09:53', '2017-03-06 12:10:49');

-- ----------------------------
-- Table structure for vlss_scene_template
-- ----------------------------
DROP TABLE IF EXISTS `vlss_scene_template`;
CREATE TABLE `vlss_scene_template` (
  `template_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `vlss_id` int(11) unsigned NOT NULL COMMENT '虚拟演播厅id',
  `template_name` varchar(16) NOT NULL COMMENT '模板名称',
  `switch_config` text NOT NULL COMMENT '模版配置 格式为 json 字符串',
  `front_pic` varchar(255) NOT NULL,
  `back_pic` varchar(255) NOT NULL,
  `state` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '1正常,9删除',
  `create_time` datetime NOT NULL COMMENT '记录创建时间',
  `uptime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`template_id`),
  KEY `vlls_idx` (`vlss_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of vlss_scene_template
-- ----------------------------
INSERT INTO `vlss_scene_template` VALUES ('1', '2', '默认模版1', '[{\"checked\": \"1\", \"h\": \"100\", \"w\": \"208\", \"v\": \"100\", \"y\": \"202\", \"x\": \"107\", \"z\": \"3\"}, {\"checked\": \"1\", \"h\": \"115\", \"w\": \"262\", \"v\": \"0\", \"y\": \"115\", \"x\": \"236\", \"z\": \"2\"}, {\"checked\": \"0\", \"h\": \"100\", \"w\": \"100\", \"v\": \"0\", \"y\": \"0\", \"x\": \"0\", \"z\": \"1\"}]', 'http://test25.aodianyun.com/dist/studio/static/desk01.png', 'http://test25.aodianyun.com/dist/studio/static/bg01.jpg', '1', '2017-03-06 11:27:25', '2017-03-06 11:28:21');
INSERT INTO `vlss_scene_template` VALUES ('2', '2', '默认模版2', '[{\"checked\": \"1\", \"h\": \"169\", \"w\": \"214\", \"v\": \"100\", \"y\": \"120\", \"x\": \"324\", \"z\": \"3\"}, {\"checked\": \"1\", \"h\": \"131\", \"w\": \"210\", \"v\": \"0\", \"y\": \"103\", \"x\": \"263\", \"z\": \"2\"}, {\"checked\": \"1\", \"h\": \"118\", \"w\": \"194\", \"v\": \"0\", \"y\": \"29\", \"x\": \"35\", \"z\": \"1\"}]', 'http://test25.aodianyun.com/dist/studio/static/desk02.png', 'http://test25.aodianyun.com/dist/studio/static/bg02.jpg', '1', '2017-03-06 11:27:25', '2017-03-06 11:28:21');
INSERT INTO `vlss_scene_template` VALUES ('3', '2', '默认模版3', '[{\"checked\": \"1\", \"h\": \"180\", \"w\": \"320\", \"v\": \"100\", \"y\": \"118\", \"x\": \"267\", \"z\": \"3\"}, {\"checked\": \"1\", \"h\": \"133\", \"w\": \"210\", \"v\": \"0\", \"y\": \"58\", \"x\": \"34\", \"z\": \"2\"}, {\"checked\": \"0\", \"h\": \"100\", \"w\": \"100\", \"v\": \"0\", \"y\": \"0\", \"x\": \"0\", \"z\": \"1\"}]', 'http://test25.aodianyun.com/dist/studio/static/desk03.png', 'http://test25.aodianyun.com/dist/studio/static/bg03.jpg', '1', '2017-03-06 11:27:25', '2017-03-06 11:28:21');