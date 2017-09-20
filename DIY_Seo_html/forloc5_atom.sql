-- phpMyAdmin SQL Dump
-- version 4.0.10.14
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Jul 04, 2017 at 11:46 PM
-- Server version: 5.6.34-log
-- PHP Version: 5.6.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `forloc5_atom`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` text,
  `pass` text,
  `admin_name` text,
  `admin_logo` text,
  `admin_reg_date` text,
  `admin_reg_ip` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `user`, `pass`, `admin_name`, `admin_logo`, `admin_reg_date`, `admin_reg_ip`) VALUES
(1, 'lisa@smallbusinessdiy.com', '8264564ea5b9f2d3d19d3fd178e0ad29', 'Lisa', 'dist/img/SmallBusinessDIYLogo.png', '15th January 2017', '1.23.139.98');

-- --------------------------------------------------------

--
-- Table structure for table `admin_history`
--

CREATE TABLE IF NOT EXISTS `admin_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `last_date` varchar(255) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `browser` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `admin_history`
--

INSERT INTO `admin_history` (`id`, `last_date`, `ip`, `browser`) VALUES
(1, '14th January 2015', '117.206.74.112', 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0'),
(2, '14th January 2015', '117.206.74.110', 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0'),
(3, '15th January 2015', '117.206.74.112', 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0'),
(4, '15th January 2017', '1.23.139.98', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36'),
(5, '15th January 2017', '1.23.88.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36'),
(6, '15th January 2017', '108.208.197.196', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.95 Safari/537.36'),
(7, '15th January 2017', '1.23.88.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36'),
(8, '15th January 2017', '108.208.197.196', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.95 Safari/537.36'),
(9, '19th April 2017', '72.201.240.127', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Safari/537.36'),
(10, '4th May 2017', '72.201.240.127', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Safari/537.36'),
(11, '4th May 2017', '108.208.197.196', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.81 Safari/537.36'),
(12, '5th May 2017', '106.51.3.86', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.96 Safari/537.36'),
(13, '5th May 2017', '108.208.197.196', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.81 Safari/537.36'),
(14, '4th July 2017', '106.51.3.86', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Safari/537.36');

-- --------------------------------------------------------

--
-- Table structure for table `ads`
--

CREATE TABLE IF NOT EXISTS `ads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text_ads` text,
  `ad720x90` text,
  `ad250x300` text,
  `ad250x125` text,
  `ad480x60` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `ads`
--

INSERT INTO `ads` (`id`, `text_ads`, `ad720x90`, `ad250x300`, `ad250x125`, `ad480x60`) VALUES
(1, '\r\n<br />Try Pro IP locator Script Today! <a title="Get Pro IP locator Script" href="http://prothemes.biz/index.php?route=product/product&path=65&product_id=59">CLICK HERE</a> <br /><br />\r\n\r\nGet 20,000 Unique Traffic for $5 [Limited Time Offer] - <a title="Get 20,000 Unique Traffic" href="http://prothemes.biz">Buy Now! CLICK HERE</a><br /><br />\r\n\r\nCustom OpenVPN GUI - Get Now for $26 ! <a title="Custom OpenVPN GUI" href="http://codecanyon.net/item/custom-openvpn-gui-pro-edition/9904287?ref=Rainbowbalaji">CLICK HERE</a><br />', '<img class="imageres" src="/theme/default/img/720x90Ad.png" />', '<img class="imageres" src="/theme/default/img/250x300Ad.png" />', '<img class="imageres" src="/theme/default/img/250x125Ad.png" />', '<img class="imageres" src="/theme/default/img/468x70Ad.png" />');

-- --------------------------------------------------------

--
-- Table structure for table `ban_user`
--

CREATE TABLE IF NOT EXISTS `ban_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(255) DEFAULT NULL,
  `last_date` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `ban_user`
--

INSERT INTO `ban_user` (`id`, `ip`, `last_date`) VALUES
(1, '2.2.2.2', '17th January 2015');

-- --------------------------------------------------------

--
-- Table structure for table `capthca`
--

CREATE TABLE IF NOT EXISTS `capthca` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cap_e` varchar(255) DEFAULT NULL,
  `cap_c` varchar(255) DEFAULT NULL,
  `mode` varchar(255) DEFAULT NULL,
  `mul` varchar(255) DEFAULT NULL,
  `allowed` text,
  `color` mediumtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `capthca`
--

INSERT INTO `capthca` (`id`, `cap_e`, `cap_c`, `mode`, `mul`, `allowed`, `color`) VALUES
(1, 'off', 'off', 'Normal', 'off', 'ABCDEFGHJKLMNPRSTUVWXYZabcdefghjkmnprstuvwxyz234567891', '#FFFFFF');

-- --------------------------------------------------------

--
-- Table structure for table `image_path`
--

CREATE TABLE IF NOT EXISTS `image_path` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `logo_path` text,
  `fav_path` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `image_path`
--

INSERT INTO `image_path` (`id`, `logo_path`, `fav_path`) VALUES
(1, 'uploads/SMBDIY2.png', 'theme/default/img/favicon.ico');

-- --------------------------------------------------------

--
-- Table structure for table `interface`
--

CREATE TABLE IF NOT EXISTS `interface` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `theme` text,
  `lang` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `interface`
--

INSERT INTO `interface` (`id`, `theme`, `lang`) VALUES
(1, 'default', 'en.php');

-- --------------------------------------------------------

--
-- Table structure for table `mail`
--

CREATE TABLE IF NOT EXISTS `mail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `smtp_host` text,
  `smtp_username` text,
  `smtp_password` text,
  `smtp_port` text,
  `protocol` text,
  `auth` text,
  `socket` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `mail`
--

INSERT INTO `mail` (`id`, `smtp_host`, `smtp_username`, `smtp_password`, `smtp_port`, `protocol`, `auth`, `socket`) VALUES
(1, '', '', '', '', '1', 'true', 'ssl');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance`
--

CREATE TABLE IF NOT EXISTS `maintenance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `maintenance_mode` text,
  `maintenance_mes` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `maintenance`
--

INSERT INTO `maintenance` (`id`, `maintenance_mode`, `maintenance_mes`) VALUES
(1, 'off', 'We expect to be back within the hour.&lt;br/&gt;\r\nSorry for the inconvenience.');

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `posted_date` text,
  `page_name` text,
  `meta_des` text,
  `meta_tags` text,
  `page_title` text,
  `page_content` text,
  `header_show` text,
  `footer_show` text,
  `page_url` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `posted_date`, `page_name`, `meta_des`, `meta_tags`, `page_title`, `page_content`, `header_show`, `footer_show`, `page_url`) VALUES
(1, '17th June 2015', 'About', 'About our company', 'about, company info, about me', 'About US', '&lt;p&gt;&lt;strong&gt;[Please edit this page. Goto Admin Panel -&amp;gt; Pages]&lt;/strong&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;', '', '', 'about');

-- --------------------------------------------------------

--
-- Table structure for table `page_view`
--

CREATE TABLE IF NOT EXISTS `page_view` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` varchar(255) DEFAULT NULL,
  `tpage` varchar(255) DEFAULT NULL,
  `tvisit` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=103 ;

--
-- Dumping data for table `page_view`
--

INSERT INTO `page_view` (`id`, `date`, `tpage`, `tvisit`) VALUES
(1, '15th January 2017', '18', '3'),
(2, '16th January 2017', '1', '1'),
(3, '18th January 2017', '2', '2'),
(4, '20th January 2017', '1', '1'),
(5, '22nd January 2017', '1', '1'),
(6, '24th January 2017', '1', '1'),
(7, '26th January 2017', '6', '2'),
(8, '28th January 2017', '1', '1'),
(9, '30th January 2017', '1', '1'),
(10, '31st January 2017', '1', '1'),
(11, '2nd February 2017', '1', '1'),
(12, '4th February 2017', '1', '1'),
(13, '6th February 2017', '1', '1'),
(14, '7th February 2017', '19', '1'),
(15, '8th February 2017', '1', '1'),
(16, '9th February 2017', '1', '1'),
(17, '10th February 2017', '8', '2'),
(18, '12th February 2017', '1', '1'),
(19, '14th February 2017', '1', '1'),
(20, '16th February 2017', '2', '1'),
(21, '18th February 2017', '1', '1'),
(22, '20th February 2017', '1', '1'),
(23, '22nd February 2017', '1', '1'),
(24, '24th February 2017', '1', '1'),
(25, '25th February 2017', '9', '1'),
(26, '26th February 2017', '1', '1'),
(27, '28th February 2017', '1', '1'),
(28, '2nd March 2017', '1', '1'),
(29, '3rd March 2017', '6', '1'),
(30, '4th March 2017', '1', '1'),
(31, '6th March 2017', '1', '1'),
(32, '8th March 2017', '1', '1'),
(33, '10th March 2017', '1', '1'),
(34, '12th March 2017', '1', '1'),
(35, '14th March 2017', '10', '7'),
(36, '16th March 2017', '2', '2'),
(37, '17th March 2017', '1', '1'),
(38, '18th March 2017', '1', '1'),
(39, '20th March 2017', '1', '1'),
(40, '21st March 2017', '1', '1'),
(41, '22nd March 2017', '1', '1'),
(42, '24th March 2017', '1', '1'),
(43, '25th March 2017', '1', '1'),
(44, '27th March 2017', '5', '4'),
(45, '28th March 2017', '1', '1'),
(46, '31st March 2017', '2', '2'),
(47, '2nd April 2017', '1', '1'),
(48, '4th April 2017', '1', '1'),
(49, '5th April 2017', '1', '1'),
(50, '5th April 2017', '1', '1'),
(51, '6th April 2017', '2', '2'),
(52, '7th April 2017', '1', '1'),
(53, '8th April 2017', '1', '1'),
(54, '10th April 2017', '1', '1'),
(55, '12th April 2017', '1', '1'),
(56, '14th April 2017', '1', '1'),
(57, '16th April 2017', '1', '1'),
(58, '18th April 2017', '1', '1'),
(59, '19th April 2017', '58', '3'),
(60, '20th April 2017', '1', '1'),
(61, '22nd April 2017', '3', '2'),
(62, '23rd April 2017', '21', '2'),
(63, '24th April 2017', '1', '1'),
(64, '26th April 2017', '1', '1'),
(65, '28th April 2017', '1', '1'),
(66, '30th April 2017', '1', '1'),
(67, '2nd May 2017', '1', '1'),
(68, '3rd May 2017', '16', '5'),
(69, '4th May 2017', '14', '3'),
(70, '5th May 2017', '24', '3'),
(71, '6th May 2017', '2', '2'),
(72, '8th May 2017', '1', '1'),
(73, '10th May 2017', '1', '1'),
(74, '12th May 2017', '1', '1'),
(75, '13th May 2017', '1', '1'),
(76, '14th May 2017', '1', '1'),
(77, '15th May 2017', '1', '1'),
(78, '16th May 2017', '2', '1'),
(79, '18th May 2017', '1', '1'),
(80, '20th May 2017', '1', '1'),
(81, '21st May 2017', '1', '1'),
(82, '22nd May 2017', '28', '3'),
(83, '23rd May 2017', '5', '2'),
(84, '24th May 2017', '2', '2'),
(85, '26th May 2017', '1', '1'),
(86, '28th May 2017', '1', '1'),
(87, '30th May 2017', '1', '1'),
(88, '31st May 2017', '1', '1'),
(89, '2nd June 2017', '1', '1'),
(90, '4th June 2017', '3', '2'),
(91, '6th June 2017', '1', '1'),
(92, '14th June 2017', '1', '1'),
(93, '16th June 2017', '3', '2'),
(94, '18th June 2017', '1', '1'),
(95, '20th June 2017', '1', '1'),
(96, '22nd June 2017', '2', '2'),
(97, '23rd June 2017', '2', '1'),
(98, '28th June 2017', '4', '3'),
(99, '29th June 2017', '17', '2'),
(100, '30th June 2017', '1', '1'),
(101, '2nd July 2017', '1', '1'),
(102, '4th July 2017', '7', '2');

-- --------------------------------------------------------

--
-- Table structure for table `pr02`
--

CREATE TABLE IF NOT EXISTS `pr02` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `api_type` text,
  `wordLimit` text,
  `minChar` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `pr02`
--

INSERT INTO `pr02` (`id`, `api_type`, `wordLimit`, `minChar`) VALUES
(1, '2', '1000', '30');

-- --------------------------------------------------------

--
-- Table structure for table `pr24`
--

CREATE TABLE IF NOT EXISTS `pr24` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `moz_access_id` text,
  `moz_secret_key` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `pr24`
--

INSERT INTO `pr24` (`id`, `moz_access_id`, `moz_secret_key`) VALUES
(1, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `recent_history`
--

CREATE TABLE IF NOT EXISTS `recent_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `visitor_ip` text,
  `tool_name` text,
  `user` text,
  `date` text,
  `intDate` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=36 ;

--
-- Dumping data for table `recent_history`
--

INSERT INTO `recent_history` (`id`, `visitor_ip`, `tool_name`, `user`, `date`, `intDate`) VALUES
(1, '1.23.139.98', 'Article Rewriter', 'kajal', '01/15/2017 01:03:03AM', '01/15/2017'),
(2, '108.208.197.196', 'Online Ping Website Tool', 'Guest', '02/07/2017 04:03:44PM', '02/07/2017'),
(3, '108.208.197.196', 'Alexa Rank Checker', 'Guest', '02/07/2017 04:04:07PM', '02/07/2017'),
(4, '108.208.197.196', 'Alexa Rank Checker', 'Guest', '02/07/2017 04:04:30PM', '02/07/2017'),
(5, '108.208.197.196', 'Domain Age Checker', 'Guest', '02/07/2017 04:05:02PM', '02/07/2017'),
(6, '108.208.197.196', 'Domain Age Checker', 'Guest', '02/07/2017 04:05:20PM', '02/07/2017'),
(7, '108.208.197.196', 'Webpage Screen Resolution Simulator', 'Guest', '02/07/2017 04:07:38PM', '02/07/2017'),
(8, '123.136.225.70', 'Webpage Screen Resolution Simulator', 'Guest', '02/10/2017 09:33:24AM', '02/10/2017'),
(9, '108.208.197.196', 'Google Index Checker', 'Guest', '02/25/2017 04:20:50PM', '02/25/2017'),
(10, '108.208.197.196', 'Google Index Checker', 'Guest', '02/25/2017 04:21:08PM', '02/25/2017'),
(11, '72.201.240.127', 'Meta Tags Analyzer', 'zee', '04/19/2017 05:55:43PM', '04/19/2017'),
(12, '108.208.197.196', 'Page Speed Checker', 'Guest', '04/19/2017 06:55:05PM', '04/19/2017'),
(13, '108.208.197.196', 'Backlink Maker', 'Guest', '04/19/2017 07:23:50PM', '04/19/2017'),
(14, '108.208.197.196', 'Backlink Maker', 'Guest', '04/19/2017 07:27:30PM', '04/19/2017'),
(15, '72.201.240.127', 'Domain into IP', 'Lisa', '04/19/2017 09:25:35PM', '04/19/2017'),
(16, '108.208.197.196', 'Article Rewriter', 'Guest', '05/03/2017 01:58:00PM', '05/03/2017'),
(17, '108.208.197.196', 'Meta Tags Analyzer', 'Guest', '05/03/2017 01:58:14PM', '05/03/2017'),
(18, '108.208.197.196', 'Meta Tags Analyzer', 'Guest', '05/03/2017 01:58:32PM', '05/03/2017'),
(19, '108.208.197.196', 'Meta Tags Analyzer', 'Guest', '05/03/2017 01:58:59PM', '05/03/2017'),
(20, '108.208.197.196', 'Meta Tags Analyzer', 'Guest', '05/03/2017 01:59:21PM', '05/03/2017'),
(21, '108.208.197.196', 'Meta Tags Analyzer', 'Guest', '05/03/2017 01:59:45PM', '05/03/2017'),
(22, '72.201.240.127', 'Robots.txt Generator', 'zeeshan', '05/05/2017 04:29:18PM', '05/05/2017'),
(23, '106.51.3.86', 'Meta Tag Generator', 'Guest', '05/22/2017 02:17:53AM', '05/22/2017'),
(24, '106.51.3.86', 'Article Rewriter', 'Guest', '05/22/2017 02:18:13AM', '05/22/2017'),
(25, '106.51.3.86', 'Plagiarism Checker', 'Guest', '05/22/2017 04:18:37AM', '05/22/2017'),
(26, '106.51.3.86', 'Word Counter', 'Guest', '05/22/2017 04:18:47AM', '05/22/2017'),
(27, '122.165.79.113', 'Plagiarism Checker', 'hari3710', '05/22/2017 11:51:14PM', '05/22/2017'),
(28, '122.165.79.113', 'Backlink Maker', 'hari3710', '05/22/2017 11:51:33PM', '05/22/2017'),
(29, '122.165.79.113', 'Meta Tag Generator', 'hari3710', '05/22/2017 11:51:55PM', '05/22/2017'),
(30, '122.165.79.113', 'Alexa Rank Checker', 'Guest', '06/29/2017 03:29:07AM', '06/29/2017'),
(31, '122.165.79.113', 'Alexa Rank Checker', 'Guest', '06/29/2017 03:29:56AM', '06/29/2017'),
(32, '122.165.79.113', 'Alexa Rank Checker', 'Guest', '06/29/2017 03:30:28AM', '06/29/2017'),
(33, '122.165.79.113', 'Word Counter', 'Guest', '06/29/2017 03:30:42AM', '06/29/2017'),
(34, '122.165.79.113', 'Online Ping Website Tool', 'Guest', '06/29/2017 03:30:51AM', '06/29/2017'),
(35, '122.165.79.113', 'Plagiarism Checker', 'Guest', '06/29/2017 03:32:53AM', '06/29/2017');

-- --------------------------------------------------------

--
-- Table structure for table `seo_tools`
--

CREATE TABLE IF NOT EXISTS `seo_tools` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tool_name` text,
  `tool_url` text,
  `uid` text,
  `icon_name` text,
  `meta_title` text,
  `meta_des` text,
  `meta_tags` text,
  `about_tool` text,
  `captcha` text,
  `tool_show` text,
  `tool_no` text,
  `tool_login` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=52 ;

--
-- Dumping data for table `seo_tools`
--

INSERT INTO `seo_tools` (`id`, `tool_name`, `tool_url`, `uid`, `icon_name`, `meta_title`, `meta_des`, `meta_tags`, `about_tool`, `captcha`, `tool_show`, `tool_no`, `tool_login`) VALUES
(1, 'Article Rewriter', 'article-rewriter', 'PR01', 'icons/article_rewriter.png', '100% Free Article Rewriter', '', 'article rewriter, spinner, article rewriter online', '<p>Enter more information about the Article Rewriter tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '1', 'no'),
(2, 'Plagiarism Checker', 'plagiarism-checker', 'PR02', 'icons/plagiarism_checker.png', 'Advance Plagiarism Checker', '', 'seo plagiarism checker, detector, plagiarism, plagiarism seo tools', '<p>Enter more information about the Plagiarism Checker tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '2', 'no'),
(3, 'Backlink Maker', 'backlink-maker', 'PR03', 'icons/backlink_maker.png', 'Backlink Maker', '', 'backlink maker, backlinks, link maker, backlink maker online', '<p>Enter more information about the Backlink Maker tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '3', 'no'),
(4, 'Meta Tag Generator', 'meta-tag-generator', 'PR04', 'icons/meta_tag_generator.png', 'Easy Meta Tag Generator', '', 'meta generator, seo tags, online meta tag generator, meta tag generator free', '<p>Enter more information about the Meta Tag Generator tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '4', 'no'),
(5, 'Meta Tags Analyzer', 'meta-tags-analyzer', 'PR05', 'icons/meta_tags_analyzer.png', 'Meta Tags Analyzer', '', 'analyze meta tags, get meta tags', '<p>Enter more information about the Meta Tags Analyzer tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '5', 'no'),
(6, 'Keyword Position Checker', 'keyword-position-checker', 'PR06', 'icons/keyword_position_checker.png', 'Free Keyword Position Checker', '', 'keyword position, keywords position checker, online keywords position checker', '<p>Enter more information about the Keyword Position Checker tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '6', 'no'),
(7, 'Robots.txt Generator', 'robots-txt-generator', 'PR07', 'icons/robots_txt_generator.png', 'Robots.txt Generator', '', 'robots.txt generator, online robots.txt generator, generate robots.txt free', '<p>Enter more information about the Robots.txt Generator tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '7', 'no'),
(8, 'XML Sitemap Generator', 'xml-sitemap-generator', 'PR08', 'icons/sitemap.png', 'Free Online XML Sitemap Generator', '', 'generate xml sitemap free, seo sitemap, xml', '<p>Enter more information about the XML Sitemap Generator tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '8', 'no'),
(9, 'Backlink Checker', 'backlink-checker', 'PR09', 'icons/backlink_checker.png', '100% Free Backlink Checker', '', 'free backlink checker online, online backlink checker', '<p>Enter more information about the Backlink Checker tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '9', 'no'),
(10, 'Alexa Rank Checker', 'alexa-rank-checker', 'PR10', 'icons/alexa.png', 'Alexa Rank Checker', '', 'get world rank, alexa, alexa site rank', '<p>Enter more information about the Alexa Rank Checker tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '10', 'no'),
(11, 'Word Counter', 'word-counter', 'PR11', 'icons/word_counter.png', 'Simple Word Counter', '', 'word calculator, word counter, character counter online', '<p>Enter more information about the Word Counter tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '11', 'no'),
(12, 'Online Ping Website Tool', 'online-ping-website-tool', 'PR12', 'icons/ping_tool.png', 'Online Ping Website Tool', '', 'website ping tool, free website ping tool, online ping tool', '<p>Enter more information about the Online Ping Website Tool tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '12', 'no'),
(13, 'Link Analyzer', 'link-analyzer-tool', 'PR13', 'icons/link_analyzer.png', 'Free Link Analyzer Tool', '', 'link analysis tool, analyse links website, analyze links free, online link analyzer, ', '<p>Enter more information about the Link Analyzer tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '13', 'no'),
(14, 'PageRank Checker', 'google-pagerank-checker', 'PR14', 'icons/pagerank.png', 'Google PageRank Checker', '', 'pagerank, pr quality, pagerank lookup, pagerank calculator', '<p>Enter more information about the PageRank Checker tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'no', '14', 'no'),
(15, 'My IP Address', 'my-ip-address', 'PR15', 'icons/my_IP_address.png', 'Your IP Address Information', '', 'ip address locator, my static ip, my ip', '<p>Enter more information about the My IP Address tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '15', 'no'),
(16, 'Keyword Density Checker', 'keyword-density-checker', 'PR16', 'icons/keyword_density_checker.png', 'Keyword Density Checker', '', 'keyword density formula, online keyword density checker, wordpress keyword density checker', '<p>Enter more information about the Keyword Density Checker tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '16', 'no'),
(17, 'Google Malware Checker', 'google-malware-checker', 'PR17', 'icons/google_malware.png', 'Google Malware Checker', '', 'google malicious site check, google request malware review, malware site finder', '<p>Enter more information about the Google Malware Checker tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '17', 'no'),
(18, 'Domain Age Checker', 'domain-age-checker', 'PR18', 'icons/domain_age_checker.png', 'Domain Age Checker', '', 'get domain age, aged domain finder, domain age finder', '<p>Enter more information about the Domain Age Checker tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '18', 'no'),
(19, 'Whois Checker', 'whois-checker', 'PR19', 'icons/whois_checker.png', 'Online Whois Checker', '', 'whois lookup, domain whois, whois checker', '<p>Enter more information about the Whois Checker tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '19', 'no'),
(20, 'Domain into IP', 'domain-into-ip', 'PR20', 'icons/domain_into_IP.png', 'Domain into IP', '', 'host ip, domain into ip, host ip lookup', '<p>Enter more information about the Domain into IP tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '20', 'no'),
(21, 'Dmoz Listing Checker', 'dmoz-listing-checker', 'PR21', 'icons/dmoz.png', 'Dmoz Listing Checker', '', 'seo dmoz, dmoz checker, get dmoz', '<p>Enter more information about the Dmoz Listing Checker tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '21', 'no'),
(22, 'URL Rewriting Tool', 'url-rewriting-tool', 'PR22', 'icons/url_rewriting.png', 'URL Rewriting Tool', '', 'htaccess rewriting, url rewriting, seo urls', '<p>Enter more information about the URL Rewriting Tool tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '22', 'no'),
(23, 'www Redirect Checker', 'www-redirect-checker', 'PR23', 'icons/www_redirect_checker.png', 'www Redirect Checker', '', '302 redirect checker, seo friendly redirect, www redirect', '<p>Enter more information about the www Redirect Checker tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '23', 'no'),
(24, 'Mozrank Checker', 'mozrank-checker', 'PR24', 'icons/moz.png', 'Mozrank Checker', '', 'moz rank, seo moz, seo rank checker', '<p>Enter more information about the Mozrank Checker tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '24', 'no'),
(25, 'URL Encoder / Decoder', 'url-encoder-decoder', 'PR25', 'icons/url_encoder_decoder.png', 'Online URL Encoder / Decoder', '', 'online urlencode, urldecode online, http encoder', '<p>Enter more information about the URL Encoder / Decoder tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '25', 'no'),
(26, 'Server Status Checker', 'server-status-checker', 'PR26', 'icons/server_status_checker.png', 'Server Status Checker', '', 'check server status, my server status, status of my server', '<p>Enter more information about the Server Status Checker tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '26', 'no'),
(27, 'Webpage Screen Resolution Simulator', 'webpage-screen-resolution-simulator', 'PR27', 'icons/webpage_screen_resolution_simulator.png', 'Webpage Screen Resolution Simulator', '', 'browser size simulator, test browser resolution, screen size tester', '<p>Enter more information about the Webpage Screen Resolution Simulator tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '27', 'no'),
(28, 'Page Size Checker', 'page-size-checker', 'PR28', 'icons/page_size_checker.png', 'Page Size Checker', '', 'check website size, find web page size, webpage size calculator', '<p>Enter more information about the Page Size Checker tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '28', 'no'),
(29, 'Reverse IP Domain Checker', 'reverse-ip-domain-checker', 'PR29', 'icons/reverse_ip_domain.png', 'Reverse IP Domain Checker', '', 'reverse ip lookup, reverse dns lookup, lookup website', '<p>Enter more information about the Reverse IP Domain Checker tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '29', 'no'),
(30, 'Blacklist Lookup', 'blacklist-lookup', 'PR30', 'icons/denied.png', 'Blacklist Lookup', '', 'blacklist checker, site blacklist, spamhaus blacklist lookup', '<p>Enter more information about the Blacklist Lookup tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '30', 'no'),
(31, 'AVG Antivirus Checker', 'avg-antivirus-checker', 'PR31', 'icons/avg_antivirus.png', 'Free AVG Antivirus Checker', '', 'antivirus lookup, free virus checker, avg online', '<p>Enter more information about the AVG Antivirus Checker tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '31', 'no'),
(32, 'Link Price Calculator', 'link-price-calculator', 'PR32', 'icons/link_price_calculator.png', 'Link Price Calculator', '', 'seo price calculator, link worth calculator, check price of domain', '<p>Enter more information about the Link Price Calculator tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '32', 'no'),
(33, 'Website Screenshot Generator', 'website-screenshot-generator', 'PR33', 'icons/website_screenshot_generator.png', 'Website Screenshot Generator', '', 'browser screenshot generator, website snapshot generator, website thumbnail', '<p>Enter more information about the Website Screenshot Generator tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '33', 'no'),
(34, 'Domain Hosting Checker', 'domain-hosting-checker', 'PR34', 'icons/domain_hosting_checker.png', 'Get your Domain Hosting Checker', '', 'get hosting name, hosting isp name, domain hosting', '<p>Enter more information about the Domain Hosting Checker tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '34', 'no'),
(35, 'Get Source Code of Webpage', 'get-source-code-of-webpage', 'PR35', 'icons/source_code.png', 'Get Source Code of Webpage', '', 'web page source code, source of web page, get source code', '<p>Enter more information about the Get Source Code of Webpage tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '35', 'no'),
(36, 'Google Index Checker', 'google-index-checker', 'PR36', 'icons/google_index_checker.png', 'Google Index Checker', '', 'google site index checker, google index search, check google index online', '<p>Enter more information about the Google Index Checker tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '36', 'no'),
(37, 'Website Links Count Checker', 'website-links-count-checker', 'PR37', 'icons/links_count_checker.png', 'Website Links Count Checker', '', 'online links counter, get webpage links, link extract', '<p>Enter more information about the Website Links Count Checker tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '37', 'no'),
(38, 'Class C Ip Checker', 'class-c-ip-checker', 'PR38', 'icons/class_c_ip.png', 'Class C Ip Checker', '', 'class c ip address, class c rang, get class c ip', '<p>Enter more information about the Class C Ip Checker tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '38', 'no'),
(39, 'Online Md5 Generator', 'online-md5-generator', 'PR39', 'icons/online_md5_generator.png', 'Online Md5 Generator', '', 'create md5 hash, calculate md5 hash online, md5 key generator', '<p>Enter more information about the Online Md5 Generator tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '39', 'no'),
(40, 'Page Speed Checker', 'page-speed-checker', 'PR40', 'icons/page_speed.png', 'Page Speed Checker', '', 'page load speed, web page speed, faster page load', '<p>Enter more information about the Page Speed Checker tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '40', 'no'),
(41, 'Code to Text Ratio Checker', 'code-to-text-ratio-checker', 'PR41', 'icons/code_to_text.png', 'Code to Text Ratio Checker', '', 'code to text ratio html, webpage text ratio, online ratio checker', '<p>Enter more information about the Code to Text Ratio Checker tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '41', 'no'),
(42, 'Find DNS records', 'find-dns-records', 'PR42', 'icons/dns.png', 'Find DNS records', '', 'dns record checker, get dns of my domain, dns lookup', '<p>Enter more information about the Find DNS records tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '42', 'no'),
(43, 'What is my Browser', 'what-is-my-browser', 'PR43', 'icons/what_is_my_browser.png', 'What is my Browser', '', 'what is a browser, get browser info, detect browser', '<p>Enter more information about the What is my Browser tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '43', 'no'),
(44, 'Email Privacy', 'email-privacy', 'PR44', 'icons/email_privacy.png', 'Email Privacy', '', 'email privacy issues, email security, email privacy at web page', '<p>Enter more information about the Email Privacy tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '44', 'no'),
(45, 'Google Cache Checker', 'google-cache-checker', 'PR45', 'icons/google_cache.png', 'Google Cache Checker', '', 'cache checker, google cache, web page cache', '<p>Enter more information about the Google Cache Checker tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '45', 'no'),
(46, 'Broken Links Finder', 'broken-links-finder', 'PR46', 'icons/broken_links.png', 'Broken Links Finder', '', '404 links, broken links, broken web page links', '<p>Enter more information about the Broken Links Finder tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '46', 'no'),
(47, 'Search Engine Spider Simulator', 'spider-simulator', 'PR47', 'icons/spider_simulator.png', 'Search Engine Spider Simulator', '', 'spider simulator, web crawler simulator, search engine spider', '<p>Enter more information about the Search Engine Spider Simulator tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '47', 'no'),
(48, 'Keywords Suggestion Tool', 'keywords-suggestion-tool', 'PR48', 'icons/keywords_suggestion.png', 'Keywords Suggestion Tool', '', 'keywords suggestion, suggestion tool, keywords maker', '<p>Enter more information about the Keywords Suggestion Tool tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '48', 'no'),
(49, 'Domain Authority Checker', 'domain-authority-checker', 'PR49', 'icons/domain_authority.png', 'Bulk Domain Authority Checker', '', 'domain authority, seo moz, domain score', '<p>Enter more information about the Domain Authority Checker tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '49', 'no'),
(50, 'Page Authority Checker', 'page-authority-checker', 'PR50', 'icons/page_authority.png', 'Bulk Page Authority Checker', '', 'page authority, moz rank check, page score', '<p>Enter more information about the Page Authority Checker tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '50', 'no'),
(51, 'Pagespeed Insights Checker', 'pagespeed-insights-checker', 'SD51', 'icons/google_pagespeed.png', 'Google Pagespeed Insights Checker', '', 'pagespeed, pagespeed google, insights score', '<p>Enter more information about the Pagespeed Insights Checker tool! </p> <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>', 'no', 'yes', '51', 'no');

-- --------------------------------------------------------

--
-- Table structure for table `sitemap_options`
--

CREATE TABLE IF NOT EXISTS `sitemap_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `priority` varchar(255) DEFAULT NULL,
  `changefreq` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `sitemap_options`
--

INSERT INTO `sitemap_options` (`id`, `priority`, `changefreq`) VALUES
(1, '0.9', 'weekly');

-- --------------------------------------------------------

--
-- Table structure for table `site_info`
--

CREATE TABLE IF NOT EXISTS `site_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` mediumtext,
  `des` text,
  `keyword` mediumtext,
  `site_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `twit` text,
  `face` text,
  `gplus` text,
  `ga` text,
  `copyright` text,
  `footer_tags` text,
  `doForce` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `site_info`
--

INSERT INTO `site_info` (`id`, `title`, `des`, `keyword`, `site_name`, `email`, `twit`, `face`, `gplus`, `ga`, `copyright`, `footer_tags`, `doForce`) VALUES
(1, 'Small Business DIY Dr.', 'Small Business DIY Tools are a bundled collection of best seo tools. We offer all of these tools free of charge.', 'seo tools, small business DIY, seo, free seo, small business tools', 'SmallBusinessDIYDr.', 'lisa@smallbusinessdiy.com', 'https://twitter.com/', 'https://www.facebook.com/', 'https://plus.google.com/', 'UA-', 'Copyright Â© 2017  SmallbusinessDIYTools.com. All rights reserved.', 'seo tools, plagiarism, seo, rewriter, backlinks', 'a:2:{i:0;b:0;i:1;b:0;}');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `oauth_uid` text,
  `username` text,
  `email_id` text,
  `full_name` text,
  `platform` text,
  `password` text,
  `verified` text,
  `picture` text,
  `date` text,
  `ip` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `oauth_uid`, `username`, `email_id`, `full_name`, `platform`, `password`, `verified`, `picture`, `date`, `ip`) VALUES
(1, '0', 'kajal', 'kajalkachhadiyacizo@gmail.com', 'kajal kachhadiya', 'Direct', '9ac1f6215432199f56f4983f42d9e4cf', '1', 'NONE', '15th January 2017', '1.23.139.98'),
(2, '0', 'Lisa', 'lisasievers.ca@gmail.com', 'Lisa Sievers', 'Direct', 'a17c908256c4a53692299f9ead89169e', '1', 'NONE', '7th February 2017', '108.208.197.196'),
(3, '0', 'zee', 'manoswati@hotmail.com', 'Zeeshan kk', 'Direct', '8264564ea5b9f2d3d19d3fd178e0ad29', '1', 'NONE', '19th April 2017', '72.201.240.127'),
(4, '0', 'Hariharan', 'hari.mitteam@gmail.com', 'HARIHARAN GOVINDARAJU', 'Direct', 'f3597f3a04fd2d65274d835db56aa24d', '0', 'NONE', '5th May 2017', '106.51.3.86'),
(5, '0', 'zeeshan', 'manoswati@gmail.com', 'Zeeshan S', 'Direct', '8264564ea5b9f2d3d19d3fd178e0ad29', '1', 'NONE', '5th May 2017', '72.201.240.127'),
(6, '0', 'hari3710', 'thamaraiselvan@wemagination.net', 'thamarai', 'Direct', 'f3597f3a04fd2d65274d835db56aa24d', '1', 'NONE', '22nd May 2017', '122.165.79.113');

-- --------------------------------------------------------

--
-- Table structure for table `user_input_history`
--

CREATE TABLE IF NOT EXISTS `user_input_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `visitor_ip` text,
  `tool_name` text,
  `user` text,
  `date` text,
  `user_input` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `user_input_history`
--

INSERT INTO `user_input_history` (`id`, `visitor_ip`, `tool_name`, `user`, `date`, `user_input`) VALUES
(1, '108.208.197.196', 'Alexa Rank Checker', 'Guest', '02/07/2017 04:04:18PM', 'http://www.bulldogledlighting.com'),
(2, '108.208.197.196', 'Domain Age Checker', 'Guest', '02/08/2017 12:05:10AM', 'http://bulldog-lighting.com'),
(3, '108.208.197.196', 'Domain Age Checker', 'Guest', '02/08/2017 12:05:28AM', 'http://1dash.com'),
(4, '108.208.197.196', 'Google Index Checker', 'Guest', '02/25/2017 04:20:58PM', 'http://www.1dash.com'),
(5, '108.208.197.196', 'Page Speed Checker', 'Guest', '04/19/2017 06:55:52PM', 'http://www.1dash.com'),
(6, '108.208.197.196', 'Meta Tags Analyzer', 'Guest', '05/03/2017 01:58:21PM', 'http://1dash.com'),
(7, '108.208.197.196', 'Meta Tags Analyzer', 'Guest', '05/03/2017 01:58:49PM', 'http://bulldogledlighting.com'),
(8, '108.208.197.196', 'Meta Tags Analyzer', 'Guest', '05/03/2017 01:59:06PM', 'http://aeelectrical.com'),
(9, '108.208.197.196', 'Meta Tags Analyzer', 'Guest', '05/03/2017 01:59:38PM', 'http://lociq.com'),
(10, '122.165.79.113', 'Alexa Rank Checker', 'Guest', '06/29/2017 03:29:17AM', 'http://www.habiletechnologies.com'),
(11, '122.165.79.113', 'Alexa Rank Checker', 'Guest', '06/29/2017 03:30:12AM', 'http://www.smallbusinessdiy.com');

-- --------------------------------------------------------

--
-- Table structure for table `user_settings`
--

CREATE TABLE IF NOT EXISTS `user_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `enable_reg` text,
  `enable_oauth` text,
  `visitors_limit` text,
  `fb_app_id` text,
  `fb_app_secret` text,
  `g_client_id` text,
  `g_client_secret` text,
  `g_redirect_uri` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `user_settings`
--

INSERT INTO `user_settings` (`id`, `enable_reg`, `enable_oauth`, `visitors_limit`, `fb_app_id`, `fb_app_secret`, `g_client_id`, `g_client_secret`, `g_redirect_uri`) VALUES
(1, 'on', 'on', '0', '314358688908517', 'eb32a4991926f4bfa6045925be1d8d77', '789601184518-ggtl2rk8hqemadfttt85mte5j8hkqqo9.apps.googleusercontent.com', 'B7wIA4xPUmjh3thZba5tVXEw', 'http://atom.smallbusinessdiy.com/?route=google');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
