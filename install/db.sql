-- phpMyAdmin SQL Dump
-- version 3.4.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 16, 2011 at 04:02 PM
-- Server version: 5.1.58
-- PHP Version: 5.3.6-13ubuntu3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dcdash`
--

-- --------------------------------------------------------

--
-- Table structure for table `district`
--

CREATE TABLE IF NOT EXISTS `district` (
  `did` bigint(20) NOT NULL AUTO_INCREMENT,
  `dname` varchar(200) NOT NULL,
  PRIMARY KEY (`did`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `healthpoint`
--

CREATE TABLE IF NOT EXISTS `healthpoint` (
  `hpid` bigint(20) NOT NULL AUTO_INCREMENT,
  `hpcode` int(11) NOT NULL,
  `hpname` varchar(200) NOT NULL,
  `did` bigint(20) NOT NULL,
  `locationlat` float NOT NULL,
  `locationlng` float NOT NULL,
  PRIMARY KEY (`hpid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `logtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `loglevel` varchar(20) NOT NULL,
  `userid` bigint(20) NOT NULL,
  `logtype` varchar(20) NOT NULL,
  `logmsg` text NOT NULL,
  `logip` varchar(20) NOT NULL,
  `logpagephptime` float DEFAULT NULL,
  `logpagequeries` int(11) DEFAULT NULL,
  `logpagemysqltime` float DEFAULT NULL,
  `logagent` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2345 ;

-- --------------------------------------------------------

--
-- Table structure for table `patientcurrent`
--

CREATE TABLE IF NOT EXISTS `patientcurrent` (
  `pcid` bigint(20) NOT NULL AUTO_INCREMENT,
  `pcupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hpcode` varchar(255) NOT NULL,
  `pcurrent` tinyint(1) NOT NULL DEFAULT '1',
  `patid` int(9) NOT NULL,
  PRIMARY KEY (`pcid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=210 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `userid` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(200) NOT NULL,
  `email` varchar(200) DEFAULT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `defaultlang` varchar(2) NOT NULL,
  `user_uri` varchar(255) NOT NULL,
  `hpid` bigint(20) NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;

-- --------------------------------------------------------

--
-- Table structure for table `userprops`
--

CREATE TABLE IF NOT EXISTS `userprops` (
  `propid` bigint(20) NOT NULL AUTO_INCREMENT,
  `userid` bigint(20) NOT NULL,
  `propname` varchar(100) NOT NULL,
  `propvalue` text NOT NULL,
  PRIMARY KEY (`propid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;