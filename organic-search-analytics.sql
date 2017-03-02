-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Sep 29, 2015 at 11:43 PM
-- Server version: 5.5.34
-- PHP Version: 5.4.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `organic-search-analytics`
--

-- --------------------------------------------------------

--
-- Table structure for table `report_saved`
--

CREATE TABLE IF NOT EXISTS `report_saved` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(256) NOT NULL,
  `name` varchar(256) NOT NULL,
  `category` int(11) NOT NULL,
  `paramaters` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `report_saved_categories`
--

CREATE TABLE IF NOT EXISTS `report_saved_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `description` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `search_analytics`
--

CREATE TABLE IF NOT EXISTS `search_analytics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(256) NOT NULL,
  `date` date NOT NULL,
  `search_engine` varchar(50) NOT NULL,
  `search_type` varchar(24) NOT NULL,
  `device_type` varchar(24) NOT NULL,
  `country` varchar(10) NULL,
  `query` varchar(500) NOT NULL,
  `impressions` int(11) NOT NULL,
  `clicks` int(11) NOT NULL,
  `ctr` float NOT NULL,
  `avg_position` int(11) NOT NULL,
  `avg_position_click` int(11) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(256) NOT NULL,
  `value` varchar(256) NOT NULL,
  `data` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci AUTO_INCREMENT=0 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
