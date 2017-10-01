-- phpMyAdmin SQL Dump
-- version phpStudy 2014
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2017 年 08 月 09 日 02:53
-- 服务器版本: 5.5.53
-- PHP 版本: 5.4.45

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `erp3`
--

-- --------------------------------------------------------

--
-- 表的结构 `admin_users`
--

CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '用户名',
  `password` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT 'MD5加密后密码',
  `usertype` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '用户类型，区分权限用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='用户表，记录可以登录系统的所有用户，以及其密码、用户类型' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `usertype`) VALUES
(1, 'admin', 'e10adc3949ba59abbe56e057f20f883e', '1');

-- --------------------------------------------------------

--
-- 表的结构 `admin_zuser`
--

CREATE TABLE IF NOT EXISTS `admin_zuser` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `zname` varchar(100) DEFAULT NULL COMMENT '租户名称',
  `zuser` varchar(100) DEFAULT NULL COMMENT '租户管理员名称',
  `tel` varchar(100) DEFAULT NULL COMMENT '租户电话',
  `address` varchar(255) DEFAULT NULL COMMENT '租户地址',
  `db_prefix` varchar(50) DEFAULT NULL COMMENT '数据库前缀',
  `tpye` varchar(100) DEFAULT NULL COMMENT '类型',
  `zstauts` varchar(100) DEFAULT '0' COMMENT '状态:0未激活 1：激活',
  `bei1` varchar(200) DEFAULT NULL,
  `bei2` varchar(200) DEFAULT NULL,
  `bei3` varchar(200) DEFAULT NULL,
  `bei4` varchar(200) DEFAULT NULL,
  `bei5` varchar(200) DEFAULT NULL,
  `bei6` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
