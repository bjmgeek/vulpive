-- phpMyAdmin SQL Dump
-- version 2.10.3deb1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Oct 24, 2007 at 09:02 PM
-- Server version: 5.0.45
-- PHP Version: 5.2.3-1ubuntu6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `vulpive`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `chapter`
-- 

CREATE TABLE IF NOT EXISTS `chapter` (
  `chapter_id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL,
  `comic_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`chapter_id`),
  KEY `comic_id` (`comic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `chapter`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `comic`
-- 

CREATE TABLE IF NOT EXISTS `comic` (
  `comic_id` int(10) unsigned NOT NULL auto_increment,
  `date` date NOT NULL,
  `title` varchar(100) default NULL,
  `path` varchar(100) NOT NULL,
  `is_visible` tinyint(1) NOT NULL default '1',
  `commentary` text,
  PRIMARY KEY  (`comic_id`),
  UNIQUE KEY `date` (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `comic`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `multi_comic`
-- 

CREATE TABLE IF NOT EXISTS `multi_comic` (
  `multi_comic_id` int(10) unsigned NOT NULL auto_increment,
  `comic_id` int(10) unsigned NOT NULL,
  `path` varchar(100) NOT NULL,
  `sort_order` int(2) unsigned NOT NULL default '1',
  PRIMARY KEY  (`multi_comic_id`),
  KEY `comic_id` (`comic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `multi_comic`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `options`
-- 

CREATE TABLE IF NOT EXISTS `options` (
  `option_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL,
  `value` text,
  PRIMARY KEY  (`option_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `options`
-- 

INSERT INTO `options` (`option_id`, `name`, `value`) VALUES 
(1, 'show_calendar', 'true'),
(2, 'show_dropdown', 'true'),
(3, 'sort_dropdown', 'title'),
(4, 'title', 'Vulpive 0.2'),
(5, 'naming_scheme', 'original'),
(6, 'shoutout', ''),
(7, 'enable_commentary', 'false'),
(8, 'max_images', '1'),
(9, 'column_format', 'double');

-- --------------------------------------------------------

-- 
-- Table structure for table `user`
-- 

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `password` varchar(64) default NULL,
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `user`
-- 

INSERT INTO `user` (`user_id`, `name`, `password`) VALUES 
(1, 'root', '*81F5E21E35407D884A6CD4A731AEBFB6AF209E1B');

