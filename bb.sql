-- phpMyAdmin SQL Dump
-- version 2.11.9.4
-- http://www.phpmyadmin.net
--
-- Generation Time: Mar 30, 2010 at 03:16 PM
-- Server version: 5.0.88
-- PHP Version: 5.2.12

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- --------------------------------------------------------

--
-- Table structure for table `balance_sheet`
--

CREATE TABLE IF NOT EXISTS `balance_sheet` (
  `id` int(11) NOT NULL auto_increment,
  `description` varchar(255) NOT NULL,
  `amount` float NOT NULL,
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `details` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `be`
--

CREATE TABLE IF NOT EXISTS `be` (
  `id` int(11) NOT NULL auto_increment,
  `word` varchar(255) NOT NULL,
  `count` int(255) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `word` (`word`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=192 ;

-- --------------------------------------------------------

--
-- Table structure for table `counters`
--

CREATE TABLE IF NOT EXISTS `counters` (
  `id` int(11) NOT NULL auto_increment,
  `value` int(11) NOT NULL,
  `lifetime_value` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `goal` int(11) NOT NULL default '100',
  `required` int(11) default NULL COMMENT 'The count value required',
  `frequency` varchar(255) NOT NULL,
  `reward` varchar(255) NOT NULL,
  `reward_increment` int(11) NOT NULL,
  `reward_value` float NOT NULL,
  `priority_id` int(11) NOT NULL,
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `priority_id` (`priority_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=52 ;

-- --------------------------------------------------------

--
-- Table structure for table `priority`
--

CREATE TABLE IF NOT EXISTS `priority` (
  `id` int(11) NOT NULL auto_increment,
  `value` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `pclass` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `value` (`value`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `qna`
--

CREATE TABLE IF NOT EXISTS `qna` (
  `id` int(11) NOT NULL auto_increment,
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `tag_id` int(11) NOT NULL default '82',
  `ranking` int(11) NOT NULL default '5',
  PRIMARY KEY  (`id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=51 ;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=340 ;

-- --------------------------------------------------------

--
-- Table structure for table `tags_relationships`
--

CREATE TABLE IF NOT EXISTS `tags_relationships` (
  `id` int(11) NOT NULL auto_increment,
  `subtag` int(11) NOT NULL,
  `supertag` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `subtag_2` (`subtag`,`supertag`),
  KEY `subtag` (`subtag`),
  KEY `supertag` (`supertag`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=83 ;

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE IF NOT EXISTS `tasks` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `priority_id` int(11) NOT NULL default '5',
  `note` varchar(511) NOT NULL,
  `due_date` datetime NOT NULL,
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `completed` tinyint(1) NOT NULL default '0',
  `active` tinyint(1) NOT NULL default '0',
  `deleted` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `priority` (`priority_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2567 ;

-- --------------------------------------------------------

--
-- Table structure for table `tasks_fulltext`
--

CREATE TABLE IF NOT EXISTS `tasks_fulltext` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`),
  FULLTEXT KEY `name_fulltext` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2567 ;

-- --------------------------------------------------------

--
-- Table structure for table `tasks_tags`
--

CREATE TABLE IF NOT EXISTS `tasks_tags` (
  `id` int(11) NOT NULL auto_increment,
  `task_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `task_tag_id` (`task_id`,`tag_id`),
  KEY `tag_id` (`tag_id`),
  KEY `task_id` (`task_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5858 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `counters`
--
ALTER TABLE `counters`
  ADD CONSTRAINT `counters_ibfk_1` FOREIGN KEY (`priority_id`) REFERENCES `priority` (`id`);

--
-- Constraints for table `qna`
--
ALTER TABLE `qna`
  ADD CONSTRAINT `qna_ibfk_1` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`);

--
-- Constraints for table `tags_relationships`
--
ALTER TABLE `tags_relationships`
  ADD CONSTRAINT `tags_relationships_ibfk_1` FOREIGN KEY (`subtag`) REFERENCES `tags` (`id`),
  ADD CONSTRAINT `tags_relationships_ibfk_2` FOREIGN KEY (`supertag`) REFERENCES `tags` (`id`);

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`priority_id`) REFERENCES `priority` (`id`);

--
-- Constraints for table `tasks_tags`
--
ALTER TABLE `tasks_tags`
  ADD CONSTRAINT `tasks_tags_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tasks_tags_ibfk_3` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;

--
--  REQUIRED PRELIMINARY DATA
--


INSERT INTO `tags` (`name`, `description`) VALUES ('Sample Tag', '');

INSERT INTO `priority` (`id`, `value`, `label`, `pclass`) VALUES
(1, 1, 'Low Priority', 'lowpriority'),
(2, 5, 'Medium Priority', 'medpriority'),
(3, 10, 'High Priority', 'highpriority');
