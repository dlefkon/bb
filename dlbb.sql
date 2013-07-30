-- phpMyAdmin SQL Dump
-- version 3.3.10
-- http://www.phpmyadmin.net
--
-- Host: mysql.allverticals.com
-- Generation Time: Jun 25, 2011 at 07:10 PM
-- Server version: 5.1.53
-- PHP Version: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `dlbb`
--

-- --------------------------------------------------------

--
-- Table structure for table `dlbb_item`
--

CREATE TABLE IF NOT EXISTS `dlbb_item` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `priority` int(11) NOT NULL DEFAULT '2',
  `note` varchar(4096) NOT NULL,
  `due_date` datetime DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `completed` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `count` int(10) NOT NULL DEFAULT '1',
  `max_children` int(11) DEFAULT NULL,
  `home_link` tinyint(1) NOT NULL DEFAULT '0',
  `tag` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`item_id`),
  UNIQUE KEY `name` (`name`),
  KEY `count` (`count`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=54 ;

--
-- Dumping data for table `dlbb_item`
--

INSERT INTO `dlbb_item` (`item_id`, `user_id`, `name`, `priority`, `note`, `due_date`, `updated`, `completed`, `active`, `count`, `max_children`, `home_link`, `tag`) VALUES
(1, 0, 'test2', 2, 'if delete item, delete parent & child relations ... add ''ondelete cascade''\nMake so doesn''t allow adding same parent and child ... add constraints\nif click child or parent, refresh item .. so note et al change\nGet so always shows total count event if no children .. and make sure count is correct including count of present item\nFix multiple saves occuring\nstay at top of page when click 24 hrs\nchange to cookie\n(Save when press enter key on note)\n\nadd pan and pink paint/primer, brushes\n\n', '0000-00-00 00:00:00', '2011-06-19 13:03:19', 0, 0, 5, 5, 0, 0),
(5, 0, 'test4', 2, '', NULL, '2011-05-18 00:00:53', 0, 0, 1, NULL, 0, 0),
(6, 0, 'FFFg', 2, '', NULL, '2011-06-19 10:54:07', 0, 0, 1, NULL, 0, 0),
(7, 0, 'Aef', 0, '', NULL, '2011-05-21 14:45:22', 0, 0, 7, NULL, 0, 0),
(15, 0, 'Iiii', 2, '', NULL, '2011-06-18 14:21:37', 0, 0, 2, NULL, 0, 0),
(16, 0, 'Efefe', 2, '', NULL, '2011-06-24 22:32:56', 0, 0, 2, NULL, 0, 1),
(18, 0, 'Bdgwer', 2, 'Make so doesn''t allow adding same parent and child/subchildren who are also parents ... add constraints \ndon''t show already selected parents/children or that item in dropdowns\nDon''t clear name when removing child (and parent)\nFix multiple saves occuring\nstay at top of page when click 24 hrs\nchange to cookie\n(Save when press enter key on note)\n(Remove tag again .. save space on home, remove field, can do search for these)\n\nadd pan and pink paint/primer, brushes\n\n', NULL, '2011-06-19 13:08:41', 0, 0, 1, NULL, 0, 0),
(20, 0, 'Wef', 2, '', NULL, '2011-05-21 20:31:22', 0, 0, 1, NULL, 0, 0),
(21, 0, 'Wefeee', 2, '', NULL, '2011-05-22 22:31:49', 0, 0, 4, NULL, 0, 0),
(22, 0, 'Wefwefewf', 2, '', NULL, '2011-05-21 20:34:03', 0, 0, 1, NULL, 0, 0),
(23, 0, 'Okokok', 2, 'wd\n', NULL, '2011-06-20 19:31:47', 0, 0, 1, NULL, 1, 0),
(24, 0, 'Yjyjyjyj', 2, '', NULL, '2011-05-21 21:05:15', 0, 0, 1, NULL, 0, 0),
(25, 0, 'Wfwfe', 2, '', NULL, '2011-05-21 22:19:51', 0, 0, 1, NULL, 0, 0),
(26, 0, 'Wefweweq', 2, '', NULL, '2011-05-21 22:21:53', 0, 0, 2, NULL, 0, 0),
(28, 0, 'Efefe323r23r', 2, 'if delete item, delete parent & child relations ... add ''ondelete cascade''\nMake so doesn''t allow adding same parent and child ... add constraints\nif click child or parent, refresh item .. so note et al change\nGet so always shows total count event if no children .. and make sure count is correct including count of present item\nFix multiple saves occuring\nstay at top of page when click 24 hrs\nchange to cookie\n(Save when press enter key on note)\n\nadd pan and pink paint/primer, brushes\n\n', NULL, '2011-06-19 13:03:27', 0, 0, 1, NULL, 0, 0),
(29, 0, 'Wefwef2', 2, '', NULL, '2011-05-21 22:37:08', 0, 0, 1, NULL, 0, 0),
(30, 0, '4t4t', 2, '', NULL, '2011-06-24 22:44:39', 0, 0, 1, NULL, 0, 1),
(31, 0, 'Okokokok]', 2, 'if delete item, delete parent & child relations ... add ''ondelete cascade''\nMake so doesn''t allow adding same parent and child ... add constraints\nif click child or parent, refresh item .. so note et al change\nGet so always shows total count event if no children .. and make sure count is correct including count of present item\nFix multiple saves occuring\nstay at top of page when click 24 hrs\nchange to cookie\n(Save when press enter key on note)\n\nadd pan and pink paint/primer, brushes\n\n', NULL, '2011-06-19 13:03:36', 0, 0, 1, NULL, 0, 0),
(35, 0, '24 Hours', 2, '', NULL, '2011-06-25 08:49:23', 0, 0, 1, NULL, 1, 0),
(40, 0, 'Wrfrefwef', 2, '', NULL, '2011-06-05 20:11:11', 0, 0, 2, NULL, 0, 0),
(42, 0, 'Jjjjjjjj', 2, 'qweroij\n', NULL, '2011-06-19 10:51:58', 0, 0, 2, NULL, 0, 0),
(43, 0, 'Eeeeeeeeeeeee', 2, '', NULL, '2011-06-05 20:11:52', 0, 0, 1, NULL, 0, 0),
(44, 0, 'Call tang', 2, 'ijijij\n', NULL, '2011-06-11 04:31:17', 0, 0, 2, NULL, 0, 0),
(47, 0, 'Aaa', 2, '', NULL, '2011-06-18 13:30:50', 0, 0, 1, NULL, 0, 0),
(48, 0, 'Bbb', 1, '', NULL, '2011-06-25 13:26:30', 0, 0, 1, NULL, 1, 0),
(49, 0, 'Jjjj', 2, '', NULL, '2011-06-18 14:18:46', 0, 0, 1, NULL, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `dlbb_item_item`
--

CREATE TABLE IF NOT EXISTS `dlbb_item_item` (
  `item_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `child_item_id` int(11) NOT NULL,
  `parent_item_id` int(11) NOT NULL,
  PRIMARY KEY (`item_item_id`),
  KEY `subitem` (`child_item_id`),
  KEY `superitem` (`parent_item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=131 ;

--
-- Dumping data for table `dlbb_item_item`
--

INSERT INTO `dlbb_item_item` (`item_item_id`, `child_item_id`, `parent_item_id`) VALUES
(128, 23, 47),
(129, 35, 7),
(130, 30, 35);

-- --------------------------------------------------------

--
-- Table structure for table `dlbb_user`
--

CREATE TABLE IF NOT EXISTS `dlbb_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `dlbb_user`
--

INSERT INTO `dlbb_user` (`user_id`, `username`, `ip`, `updated`, `created`) VALUES
(1, 'David', '', '2011-06-25 18:52:00', '0000-00-00 00:00:00');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dlbb_item_item`
--
ALTER TABLE `dlbb_item_item`
  ADD CONSTRAINT `dlbb_item_item_ibfk_4` FOREIGN KEY (`parent_item_id`) REFERENCES `dlbb_item` (`item_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dlbb_item_item_ibfk_3` FOREIGN KEY (`child_item_id`) REFERENCES `dlbb_item` (`item_id`) ON DELETE CASCADE;
