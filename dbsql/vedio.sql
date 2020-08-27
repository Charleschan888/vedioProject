/*
Navicat MySQL Data Transfer

Source Server         : 本地
Source Server Version : 50547
Source Host           : localhost:3306
Source Database       : vedio

Target Server Type    : MYSQL
Target Server Version : 50547
File Encoding         : 65001

Date: 2020-08-27 20:22:27
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for vedio_admin
-- ----------------------------
DROP TABLE IF EXISTS `vedio_admin`;
CREATE TABLE `vedio_admin` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '后台管理员用户表',
  `username` varchar(100) NOT NULL COMMENT '登录用户名',
  `truename` varchar(50) NOT NULL COMMENT '管理员真实姓名',
  `password` varchar(32) NOT NULL COMMENT '管理员密码 使用md5把用户名与密码',
  `gid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '管理员对应的角色组id 默认为0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '管理员的状态：0为正常，1为锁定',
  `add_time` int(10) unsigned NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='后台管理员用户数据表';

-- ----------------------------
-- Table structure for vedio_groups
-- ----------------------------
DROP TABLE IF EXISTS `vedio_groups`;
CREATE TABLE `vedio_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员角色数据表主键',
  `title` varchar(100) NOT NULL COMMENT '角色名称标题',
  `rules` text NOT NULL COMMENT '角色所拥有的权限节点数据，使用json格式存储',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='角色数据表';

-- ----------------------------
-- Table structure for vedio_labels
-- ----------------------------
DROP TABLE IF EXISTS `vedio_labels`;
CREATE TABLE `vedio_labels` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '影片标签表主键id',
  `title` varchar(100) NOT NULL COMMENT '标签主题名称',
  `order` int(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `flag` varchar(50) NOT NULL COMMENT '标签的类型分类，如地区为area等',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '标签的禁用状态：0为正常，1为禁用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 COMMENT='标签数据表';

-- ----------------------------
-- Table structure for vedio_menus
-- ----------------------------
DROP TABLE IF EXISTS `vedio_menus`;
CREATE TABLE `vedio_menus` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '菜单数据表主键id',
  `title` varchar(155) NOT NULL COMMENT '菜单的名称',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '菜单的父级id，默认为0',
  `controller` varchar(30) NOT NULL COMMENT '菜单对应的控制器名称',
  `method` varchar(30) NOT NULL COMMENT '菜单对应的方法名称',
  `ishidden` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '菜单是否隐藏，1为隐藏，0为不隐藏，默认为不隐藏',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '菜单的撞他：0为正常，1为禁用，默认为正常',
  `order` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '菜单的排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8 COMMENT='菜单数据表';

-- ----------------------------
-- Table structure for vedio_site
-- ----------------------------
DROP TABLE IF EXISTS `vedio_site`;
CREATE TABLE `vedio_site` (
  `name` varchar(100) NOT NULL COMMENT '配置的名称key',
  `value` text NOT NULL COMMENT '配置的值，使用json保存'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for vedio_slide
-- ----------------------------
DROP TABLE IF EXISTS `vedio_slide`;
CREATE TABLE `vedio_slide` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '幻灯片数据表主键id',
  `title` varchar(50) NOT NULL COMMENT '幻灯片的标题',
  `url` varchar(255) NOT NULL COMMENT '幻灯片跳转url',
  `img` varchar(255) NOT NULL COMMENT '幻灯片的图片url',
  `type` tinyint(3) unsigned NOT NULL COMMENT '幻灯片的类型：0首页，1综艺等等',
  `order` tinyint(3) unsigned NOT NULL COMMENT '幻灯片排序 0-100',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否显示：0不显示 1显示',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '幻灯片添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='幻灯片数据记录表';

-- ----------------------------
-- Table structure for vedio_vedio
-- ----------------------------
DROP TABLE IF EXISTS `vedio_vedio`;
CREATE TABLE `vedio_vedio` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '影片数据表主键id',
  `title` varchar(100) NOT NULL COMMENT '影片名称',
  `channel_id` int(10) unsigned NOT NULL COMMENT '对应频道标签的id',
  `charge_id` int(10) unsigned NOT NULL COMMENT '对应资费标签的id',
  `area_id` int(10) unsigned NOT NULL COMMENT '对应地区标签的id',
  `cate_id` int(10) unsigned DEFAULT NULL COMMENT '对应分类的id',
  `keywords` varchar(150) NOT NULL COMMENT '影片关键词',
  `desc` varchar(500) NOT NULL COMMENT '影片的描述',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '影片状态：1为正常，0为下线',
  `url` varchar(300) NOT NULL COMMENT '影片的url地址，http或者https开头',
  `cover` varchar(255) NOT NULL COMMENT '影片丰满图片路径',
  `is_vip` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否vip才可看：0为否，1为是',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父级id，默认为0，电影影片为0，并且url不能为空，如果当电视剧影片则需要把子影片录入',
  `is_sub` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有子集影片，0为否，1为是',
  `score` int(3) unsigned NOT NULL COMMENT '影片评分',
  `not_like` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '影片踩数量',
  `like` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '影片点赞数',
  `pv` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '影片的pv值，观看次数',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '影片添加时间',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=69 DEFAULT CHARSET=utf8 COMMENT='影片数据记录表';
