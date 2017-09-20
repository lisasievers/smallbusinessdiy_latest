-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 03, 2016 at 03:06 PM
-- Server version: 5.1.41
-- PHP Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `test_sitespy`
--

-- --------------------------------------------------------

--
-- Table structure for table `alexa_info`
--

CREATE TABLE IF NOT EXISTS `alexa_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain_name` varchar(250) NOT NULL,
  `reach_rank` varchar(150) DEFAULT NULL,
  `country` varchar(150) DEFAULT NULL,
  `country_rank` varchar(150) DEFAULT NULL,
  `traffic_rank` varchar(150) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `checked_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `alexa_info`
--


-- --------------------------------------------------------

--
-- Table structure for table `alexa_info_full`
--

CREATE TABLE IF NOT EXISTS `alexa_info_full` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `domain_name` varchar(250) NOT NULL,
  `global_rank` varchar(20) DEFAULT NULL,
  `country_rank` varchar(20) DEFAULT NULL,
  `country` varchar(20) DEFAULT NULL,
  `traffic_rank_graph` text,
  `country_name` text,
  `country_percent_visitor` text,
  `country_in_rank` text,
  `bounce_rate` varchar(20) DEFAULT NULL,
  `page_view_per_visitor` varchar(20) DEFAULT NULL,
  `daily_time_on_the_site` varchar(20) DEFAULT NULL,
  `visitor_percent_from_searchengine` varchar(20) DEFAULT NULL,
  `search_engine_percentage_graph` text,
  `keyword_name` text NOT NULL,
  `keyword_percent_of_search_traffic` text,
  `upstream_site_name` text,
  `upstream_percent_unique_visits` text,
  `linking_in_site_name` text NOT NULL,
  `total_site_linking_in` varchar(20) DEFAULT NULL,
  `linking_in_site_address` text,
  `subdomain_name` text,
  `subdomain_percent_visitors` text,
  `status` varchar(20) NOT NULL,
  `searched_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `alexa_info_full`
--


-- --------------------------------------------------------

--
-- Table structure for table `antivirus_scan_info`
--

CREATE TABLE IF NOT EXISTS `antivirus_scan_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain_name` varchar(250) NOT NULL,
  `google_status` varchar(100) DEFAULT NULL,
  `macafee_status` varchar(100) DEFAULT NULL,
  `avg_status` varchar(100) DEFAULT NULL,
  `norton_status` varchar(100) DEFAULT NULL,
  `scanned_at` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `antivirus_scan_info`
--


-- --------------------------------------------------------

--
-- Table structure for table `backlink_generator`
--

CREATE TABLE IF NOT EXISTS `backlink_generator` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` text NOT NULL,
  `domain_name` varchar(250) NOT NULL,
  `response_code` varchar(50) DEFAULT NULL,
  `status` enum('successful','failed') NOT NULL DEFAULT 'successful',
  `user_id` int(11) NOT NULL,
  `generated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `backlink_generator`
--


-- --------------------------------------------------------

--
-- Table structure for table `backlink_search`
--

CREATE TABLE IF NOT EXISTS `backlink_search` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `domain_name` varchar(250) NOT NULL,
  `backlink_count` varchar(100) NOT NULL,
  `searched_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `backlink_search`
--


-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `session_id` varchar(200) NOT NULL,
  `ip_address` varchar(200) NOT NULL,
  `user_agent` varchar(199) NOT NULL,
  `last_activity` varchar(199) NOT NULL,
  `user_data` longtext CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ci_sessions`
--


-- --------------------------------------------------------

--
-- Table structure for table `common_info`
--

CREATE TABLE IF NOT EXISTS `common_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `domain_code` varchar(200) NOT NULL,
  `reach_rank` varchar(150) DEFAULT NULL COMMENT 'get_alexa_rank',
  `country` varchar(150) DEFAULT NULL COMMENT 'get_alexa_rank',
  `country_rank` varchar(150) DEFAULT NULL COMMENT 'get_alexa_rank',
  `traffic_rank` varchar(150) DEFAULT NULL COMMENT 'get_alexa_rank',
  `dmoz_listed_or_not` int(5) DEFAULT NULL,
  `fb_total_share` varchar(150) DEFAULT NULL,
  `fb_total_like` varchar(150) DEFAULT NULL,
  `fb_total_comment` varchar(150) DEFAULT NULL,
  `g_back_link_count` varchar(150) DEFAULT NULL,
  `g_index_count` varchar(150) DEFAULT NULL,
  `g_page_rank` varchar(150) DEFAULT NULL,
  `title` text COMMENT 'meta tags',
  `description` text COMMENT 'meta tags',
  `keywords` text COMMENT 'meta tags',
  `language` text COMMENT 'meta tags',
  `is_registered` varchar(150) DEFAULT NULL COMMENT 'who is data',
  `tech_email` varchar(150) DEFAULT NULL COMMENT 'who is data',
  `admin_email` varchar(150) DEFAULT NULL COMMENT 'who is data',
  `name_servers` varchar(250) DEFAULT NULL COMMENT 'who is data',
  `created_at` date DEFAULT NULL COMMENT 'who is data',
  `sponsor` varchar(150) DEFAULT NULL COMMENT 'who is data',
  `changed_at` date DEFAULT NULL,
  `expire_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `common_info`
--


-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `google_safety_api` text,
  `moz_access_id` varchar(100) DEFAULT NULL,
  `moz_secret_key` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `config`
--


-- --------------------------------------------------------

--
-- Table structure for table `config_proxy`
--

CREATE TABLE IF NOT EXISTS `config_proxy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `proxy` varchar(100) DEFAULT NULL,
  `port` varchar(20) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `admin_permission` varchar(100) NOT NULL DEFAULT 'only me',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `config_proxy`
--


-- --------------------------------------------------------

--
-- Table structure for table `dmoz_info`
--

CREATE TABLE IF NOT EXISTS `dmoz_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain_name` varchar(150) NOT NULL,
  `listed_or_not` varchar(150) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `checked_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `dmoz_info`
--


-- --------------------------------------------------------

--
-- Table structure for table `domain`
--

CREATE TABLE IF NOT EXISTS `domain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `domain_name` varchar(250) NOT NULL,
  `domain_code` varchar(250) NOT NULL,
  `js_code` text NOT NULL,
  `table_name` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `domain`
--


-- --------------------------------------------------------

--
-- Table structure for table `email_config`
--

CREATE TABLE IF NOT EXISTS `email_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `email_address` varchar(100) NOT NULL,
  `smtp_host` varchar(100) NOT NULL,
  `smtp_port` varchar(100) NOT NULL,
  `smtp_user` varchar(100) NOT NULL,
  `smtp_password` varchar(100) NOT NULL,
  `status` enum('0','1') NOT NULL,
  `deleted` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `email_config`
--


-- --------------------------------------------------------

--
-- Table structure for table `expired_domain_list`
--

CREATE TABLE IF NOT EXISTS `expired_domain_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain_name` varchar(250) DEFAULT NULL,
  `auction_type` enum('pre_release','pending_delete','public_auction') DEFAULT NULL,
  `auction_end_date` datetime DEFAULT NULL,
  `page_rank` int(11) DEFAULT NULL,
  `google_index` varchar(15) DEFAULT NULL,
  `yahoo_index` varchar(15) DEFAULT NULL,
  `bing_index` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `expired_domain_list`
--


-- --------------------------------------------------------

--
-- Table structure for table `forget_password`
--

CREATE TABLE IF NOT EXISTS `forget_password` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `confirmation_code` varchar(15) CHARACTER SET latin1 NOT NULL,
  `email` varchar(100) CHARACTER SET latin1 NOT NULL,
  `success` int(11) NOT NULL DEFAULT '0',
  `expiration` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `forget_password`
--


-- --------------------------------------------------------

--
-- Table structure for table `googleplus_info`
--

CREATE TABLE IF NOT EXISTS `googleplus_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain_id` varchar(150) NOT NULL,
  `share_count` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `googleplus_info`
--


-- --------------------------------------------------------

--
-- Table structure for table `google_adword`
--

CREATE TABLE IF NOT EXISTS `google_adword` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `keyword` varchar(250) CHARACTER SET utf8 NOT NULL,
  `location` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `language` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `proxy` text CHARACTER SET utf8,
  `title` text NOT NULL,
  `description` text CHARACTER SET utf8,
  `link` text CHARACTER SET utf8,
  `scraped_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `google_adword`
--


-- --------------------------------------------------------

--
-- Table structure for table `ip_country_list`
--

CREATE TABLE IF NOT EXISTS `ip_country_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_range_start` varchar(15) DEFAULT NULL,
  `ip_range_end` varchar(15) DEFAULT NULL,
  `ip_range_start_int` int(11) DEFAULT NULL,
  `ip_range_end_int` int(11) DEFAULT NULL,
  `country_code` varchar(15) DEFAULT NULL,
  `country_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `ip_country_list`
--


-- --------------------------------------------------------

--
-- Table structure for table `ip_domain_info`
--

CREATE TABLE IF NOT EXISTS `ip_domain_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(100) DEFAULT NULL,
  `isp` varchar(100) DEFAULT NULL,
  `domain_name` varchar(250) NOT NULL,
  `country` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `time_zone` varchar(100) DEFAULT NULL,
  `latitude` varchar(100) DEFAULT NULL,
  `longitude` int(100) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `searched_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `ip_domain_info`
--


-- --------------------------------------------------------

--
-- Table structure for table `ip_info`
--

CREATE TABLE IF NOT EXISTS `ip_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain_id` varchar(150) NOT NULL,
  `isp` varchar(150) NOT NULL,
  `ip` varchar(150) NOT NULL,
  `city` varchar(150) NOT NULL,
  `region` varchar(150) NOT NULL,
  `country` varchar(150) NOT NULL,
  `time_zone` varchar(150) NOT NULL,
  `longitude` varchar(150) NOT NULL,
  `latitude` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `ip_info`
--


-- --------------------------------------------------------

--
-- Table structure for table `ip_same_site`
--

CREATE TABLE IF NOT EXISTS `ip_same_site` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(100) DEFAULT NULL,
  `website` longtext,
  `user_id` int(11) NOT NULL,
  `searched_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `ip_same_site`
--


-- --------------------------------------------------------

--
-- Table structure for table `keyword_position`
--

CREATE TABLE IF NOT EXISTS `keyword_position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `domain_name` varchar(250) CHARACTER SET utf8 NOT NULL,
  `keyword` varchar(250) CHARACTER SET utf8 NOT NULL,
  `location` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `language` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `proxy` text CHARACTER SET utf8,
  `google_position` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `google_top_site_url` longtext CHARACTER SET utf8,
  `bing_position` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `bing_top_site_url` text CHARACTER SET utf8,
  `yahoo_position` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `yahoo_top_site_url` text CHARACTER SET utf8,
  `searched_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `keyword_position`
--


-- --------------------------------------------------------

--
-- Table structure for table `keyword_suggestion`
--

CREATE TABLE IF NOT EXISTS `keyword_suggestion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `keyword` varchar(100) NOT NULL,
  `google_suggestion` text,
  `bing_suggestion` text,
  `yahoo_suggestion` text,
  `wiki_suggestion` text,
  `amazon_suggestion` text,
  `searched_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `keyword_suggestion`
--


-- --------------------------------------------------------

--
-- Table structure for table `link_analysis`
--

CREATE TABLE IF NOT EXISTS `link_analysis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `external_link_count` varchar(50) DEFAULT NULL,
  `internal_link_count` varchar(50) DEFAULT NULL,
  `nofollow_count` varchar(50) DEFAULT NULL,
  `do_follow_count` varchar(50) DEFAULT NULL,
  `external_link` longtext,
  `internal_link` longtext,
  `searched_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `link_analysis`
--


-- --------------------------------------------------------

--
-- Table structure for table `meta_tag_info`
--

CREATE TABLE IF NOT EXISTS `meta_tag_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain_id` varchar(150) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `keywords` text NOT NULL,
  `language` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `meta_tag_info`
--


-- --------------------------------------------------------

--
-- Table structure for table `moz_info`
--

CREATE TABLE IF NOT EXISTS `moz_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` text NOT NULL,
  `mozrank_subdomain_normalized` varchar(150) NOT NULL,
  `mozrank_subdomain_raw` varchar(150) NOT NULL,
  `mozrank_url_normalized` varchar(150) NOT NULL,
  `mozrank_url_raw` varchar(150) NOT NULL,
  `http_status_code` varchar(150) NOT NULL,
  `domain_authority` varchar(150) NOT NULL,
  `page_authority` varchar(150) NOT NULL,
  `external_equity_links` varchar(150) NOT NULL,
  `links` varchar(150) NOT NULL,
  `user_id` int(11) NOT NULL,
  `checked_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `moz_info`
--


-- --------------------------------------------------------

--
-- Table structure for table `native_api`
--

CREATE TABLE IF NOT EXISTS `native_api` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `api_key` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `native_api`
--


-- --------------------------------------------------------

--
-- Table structure for table `page_status`
--

CREATE TABLE IF NOT EXISTS `page_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` text NOT NULL,
  `user_id` varchar(222) NOT NULL,
  `http_code` varchar(20) NOT NULL,
  `status` varchar(50) NOT NULL,
  `total_time` varchar(50) NOT NULL,
  `namelookup_time` varchar(50) NOT NULL,
  `connect_time` varchar(50) NOT NULL,
  `speed_download` varchar(50) NOT NULL,
  `check_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `page_status`
--


-- --------------------------------------------------------

--
-- Table structure for table `payment_config`
--

CREATE TABLE IF NOT EXISTS `payment_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paypal_email` varchar(250) NOT NULL,
  `monthly_fee` double NOT NULL,
  `currency` enum('USD','AUD','CAD','EUR','ILS','NZD','RUB','SGD','SEK') NOT NULL,
  `deleted` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `payment_config`
--


-- --------------------------------------------------------

--
-- Table structure for table `search_engine_index`
--

CREATE TABLE IF NOT EXISTS `search_engine_index` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `domain_name` varchar(250) CHARACTER SET utf8 NOT NULL,
  `google_index` varchar(20) DEFAULT NULL,
  `bing_index` varchar(20) DEFAULT NULL,
  `yahoo_index` varchar(20) DEFAULT NULL,
  `checked_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `search_engine_index`
--


-- --------------------------------------------------------

--
-- Table structure for table `search_engine_page_rank`
--

CREATE TABLE IF NOT EXISTS `search_engine_page_rank` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `domain_name` varchar(250) CHARACTER SET utf8 NOT NULL,
  `google_page_rank` varchar(20) DEFAULT NULL,
  `checked_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `search_engine_page_rank`
--


-- --------------------------------------------------------

--
-- Table structure for table `similar_web_info`
--

CREATE TABLE IF NOT EXISTS `similar_web_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `domain_name` varchar(250) NOT NULL,
  `global_rank` varchar(20) DEFAULT NULL,
  `country_rank` varchar(20) DEFAULT NULL,
  `country` varchar(20) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `category_rank` varchar(20) DEFAULT NULL,
  `total_visit` varchar(50) DEFAULT NULL,
  `time_on_site` varchar(50) DEFAULT NULL,
  `page_views` varchar(50) DEFAULT NULL,
  `traffic_country` text,
  `traffic_country_percentage` text,
  `direct_traffic` varchar(50) DEFAULT NULL,
  `referral_traffic` varchar(50) DEFAULT NULL,
  `search_traffic` varchar(50) DEFAULT NULL,
  `social_traffic` varchar(50) DEFAULT NULL,
  `mail_traffic` varchar(50) DEFAULT NULL,
  `display_traffic` varchar(50) DEFAULT NULL,
  `top_referral_site` text,
  `top_destination_site` text,
  `organic_search_percentage` varchar(20) DEFAULT NULL,
  `paid_search_percentage` varchar(20) DEFAULT NULL,
  `top_organic_keyword` text,
  `top_paid_keyword` text,
  `social_site_name` text,
  `social_site_percentage` text,
  `bounce_rate` varchar(20) DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `searched_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `similar_web_info`
--


-- --------------------------------------------------------

--
-- Table structure for table `social_info`
--

CREATE TABLE IF NOT EXISTS `social_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain_name` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `user_id` varchar(222) NOT NULL,
  `reddit_score` varchar(222) DEFAULT NULL,
  `reddit_up` varchar(222) DEFAULT NULL,
  `reddit_dowon` varchar(222) DEFAULT NULL,
  `linked_in_share` varchar(222) DEFAULT NULL,
  `pinterest_pin` varchar(222) DEFAULT NULL,
  `buffer_share` varchar(222) DEFAULT NULL,
  `fb_like` varchar(222) DEFAULT NULL,
  `fb_share` varchar(222) DEFAULT NULL,
  `fb_comment` varchar(222) DEFAULT NULL,
  `google_plus_count` varchar(222) DEFAULT NULL,
  `stumbleupon_view` varchar(222) DEFAULT NULL,
  `stumbleupon_like` varchar(222) DEFAULT NULL,
  `stumbleupon_comment` varchar(222) DEFAULT NULL,
  `stumbleupon_list` varchar(222) DEFAULT NULL,
  `xing_share_count` varchar(222) DEFAULT NULL,
  `search_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `social_info`
--


-- --------------------------------------------------------

--
-- Table structure for table `transaction_history`
--

CREATE TABLE IF NOT EXISTS `transaction_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `verify_status` varchar(200) NOT NULL,
  `first_name` varchar(250) CHARACTER SET utf8 NOT NULL,
  `last_name` varchar(250) CHARACTER SET utf8 NOT NULL,
  `paypal_email` varchar(200) NOT NULL,
  `receiver_email` varchar(200) NOT NULL,
  `country` varchar(100) NOT NULL,
  `payment_date` varchar(250) NOT NULL,
  `payment_type` varchar(100) NOT NULL,
  `transaction_id` varchar(150) NOT NULL,
  `paid_amount` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cycle_start_date` date NOT NULL,
  `cycle_expired_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `transaction_history`
--


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(99) NOT NULL,
  `email` varchar(99) NOT NULL,
  `mobile` varchar(100) NOT NULL,
  `password` varchar(99) NOT NULL,
  `address` text NOT NULL,
  `user_type` enum('Member','Admin') NOT NULL,
  `status` enum('1','0') NOT NULL,
  `add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `activation_code` varchar(20) DEFAULT NULL,
  `expired_date` datetime NOT NULL,
  `deleted` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `mobile`, `password`, `address`, `user_type`, `status`, `add_date`, `activation_code`, `expired_date`, `deleted`) VALUES
(1, 'Xerone IT', 'admin@gmail.com', '+88 01729 853 6452', '259534db5d66c3effb7aa2dbbee67ab0', 'Rajshahi', 'Admin', '1', '2016-01-01 00:00:00', NULL, '0000-00-00 00:00:00', '0');

-- --------------------------------------------------------

--
-- Table structure for table `website_ping`
--

CREATE TABLE IF NOT EXISTS `website_ping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `blog_name` varchar(100) DEFAULT NULL,
  `blog_url` varchar(250) DEFAULT NULL,
  `blog_url_to_ping` text,
  `blog_rss_feed_url` text,
  `ping_url` text NOT NULL,
  `response` varchar(100) NOT NULL,
  `ping_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `website_ping`
--


-- --------------------------------------------------------

--
-- Table structure for table `web_common_info`
--

CREATE TABLE IF NOT EXISTS `web_common_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `search_at` datetime NOT NULL,
  `screenshot` varchar(250) DEFAULT NULL,
  `domain_name` varchar(250) NOT NULL,
  `global_rank` varchar(150) DEFAULT NULL COMMENT 'alexa_info',
  `country` varchar(150) DEFAULT NULL COMMENT 'alexa_info',
  `country_rank` varchar(150) DEFAULT NULL COMMENT 'alexa_info',
  `traffic_rank_graph` text COMMENT 'alexa_info',
  `country_name` text COMMENT 'alexa_info',
  `country_percent_visitor` text COMMENT 'alexa_info',
  `country_in_rank` text COMMENT 'alexa_info',
  `bounce_rate` text COMMENT 'alexa_info',
  `page_view_per_visitor` text COMMENT 'alexa_info',
  `daily_time_on_the_site` text COMMENT 'alexa_info',
  `visitor_percent_from_searchengine` text COMMENT 'alexa_info',
  `search_engine_percentage_graph` text COMMENT 'alexa_info',
  `keyword_name` text COMMENT 'alexa_info',
  `keyword_percent_of_search_traffic` text COMMENT 'alexa_info',
  `upstream_site_name` text COMMENT 'alexa_info',
  `upstream_percent_unique_visits` text COMMENT 'alexa_info',
  `linking_in_site_name` text COMMENT 'alexa_info',
  `total_site_linking_in` text COMMENT 'alexa_info',
  `linking_in_site_address` text COMMENT 'alexa_info',
  `subdomain_name` text COMMENT 'alexa_info',
  `subdomain_percent_visitors` text COMMENT 'alexa_info',
  `status` varchar(100) NOT NULL COMMENT 'alexa_info',
  `similar_web_global_rank` varchar(150) DEFAULT NULL COMMENT 'similar web info',
  `similar_web_country_rank` varchar(150) DEFAULT NULL COMMENT 'similar web info',
  `similar_web_country` varchar(150) DEFAULT NULL COMMENT 'similar web info',
  `similar_web_category` varchar(150) DEFAULT NULL COMMENT 'similar web info',
  `similar_web_category_rank` varchar(150) DEFAULT NULL COMMENT 'similar web info',
  `similar_web_total_visit` varchar(150) DEFAULT NULL COMMENT 'similar web info',
  `similar_web_time_on_site` varchar(150) DEFAULT NULL COMMENT 'similar web info',
  `similar_web_page_views` varchar(150) DEFAULT NULL COMMENT 'similar web info',
  `similar_web_traffic_country` text COMMENT 'similar web info',
  `similar_web_traffic_country_percentage` text COMMENT 'similar web info',
  `similar_web_direct_traffic` varchar(150) DEFAULT NULL COMMENT 'similar web info',
  `similar_web_referral_traffic` varchar(150) DEFAULT NULL COMMENT 'similar web info',
  `similar_web_search_traffic` varchar(150) DEFAULT NULL COMMENT 'similar web info',
  `similar_web_social_traffic` varchar(150) DEFAULT NULL COMMENT 'similar web info',
  `similar_web_mail_traffic` varchar(150) DEFAULT NULL COMMENT 'similar web info',
  `similar_web_display_traffic` varchar(150) DEFAULT NULL COMMENT 'similar web info',
  `similar_web_top_referral_site` text COMMENT 'similar web info',
  `similar_web_top_destination_site` text COMMENT 'similar web info',
  `similar_web_organic_search_percentage` varchar(150) DEFAULT NULL COMMENT 'similar web info',
  `similar_web_paid_search_percentage` varchar(150) DEFAULT NULL COMMENT 'similar web info',
  `similar_web_top_organic_keyword` text COMMENT 'similar web info',
  `similar_web_top_paid_keyword` text COMMENT 'similar web info',
  `similar_web_social_site_name` text COMMENT 'similar web info',
  `similar_web_social_site_percentage` text COMMENT 'similar web info',
  `similar_web_bounce_rate` varchar(150) DEFAULT NULL COMMENT 'similar web info',
  `similar_web_status` varchar(150) DEFAULT NULL COMMENT 'similar web info',
  `title` text,
  `h1` text COMMENT 'meta tag info',
  `h2` text COMMENT 'meta tag info',
  `h3` text COMMENT 'meta tag info',
  `h4` text COMMENT 'meta tag info',
  `h5` text COMMENT 'meta tag info',
  `h6` text COMMENT 'meta tag info',
  `blocked_by_robot_txt` varchar(20) DEFAULT NULL COMMENT 'meta tag info',
  `meta_tag_information` text COMMENT 'meta tag info',
  `blocked_by_meta_robot` varchar(20) DEFAULT NULL COMMENT 'meta tag info',
  `nofollowed_by_meta_robot` varchar(20) DEFAULT NULL COMMENT 'meta tag info',
  `one_phrase` text COMMENT 'meta tag info',
  `two_phrase` text COMMENT 'meta tag info',
  `three_phrase` text COMMENT 'meta tag info',
  `four_phrase` text COMMENT 'meta tag info',
  `total_words` int(11) NOT NULL DEFAULT '0',
  `dmoz_listed_or_not` varchar(150) DEFAULT NULL,
  `fb_total_share` varchar(150) DEFAULT NULL,
  `fb_total_like` varchar(150) DEFAULT NULL,
  `fb_total_comment` varchar(150) DEFAULT NULL,
  `google_back_link_count` varchar(150) DEFAULT NULL,
  `google_index_count` varchar(150) DEFAULT NULL,
  `google_page_rank` varchar(150) DEFAULT NULL,
  `bing_index_count` varchar(150) DEFAULT NULL,
  `yahoo_index_count` varchar(150) DEFAULT NULL,
  `whois_is_registered` varchar(150) DEFAULT NULL,
  `whois_tech_email` varchar(150) DEFAULT NULL,
  `whois_admin_email` varchar(150) DEFAULT NULL,
  `whois_name_servers` varchar(150) DEFAULT NULL,
  `whois_created_at` date NOT NULL,
  `whois_sponsor` varchar(150) DEFAULT NULL,
  `whois_changed_at` date NOT NULL,
  `whois_expire_at` date NOT NULL,
  `whois_registrar_url` varchar(150) DEFAULT NULL,
  `whois_registrant_name` varchar(150) DEFAULT NULL,
  `whois_registrant_organization` varchar(150) DEFAULT NULL,
  `whois_registrant_street` varchar(150) DEFAULT NULL,
  `whois_registrant_city` varchar(150) DEFAULT NULL,
  `whois_registrant_state` varchar(150) DEFAULT NULL,
  `whois_registrant_postal_code` varchar(150) DEFAULT NULL,
  `whois_registrant_email` varchar(150) DEFAULT NULL,
  `whois_registrant_country` varchar(150) DEFAULT NULL,
  `whois_registrant_phone` varchar(150) DEFAULT NULL,
  `whois_admin_name` varchar(150) DEFAULT NULL,
  `whois_admin_street` varchar(150) DEFAULT NULL,
  `whois_admin_city` varchar(150) DEFAULT NULL,
  `whois_admin_state` varchar(150) DEFAULT NULL,
  `whois_admin_postal_code` varchar(150) DEFAULT NULL,
  `whois_admin_country` varchar(150) DEFAULT NULL,
  `whois_admin_phone` varchar(150) DEFAULT NULL,
  `googleplus_share_count` varchar(150) DEFAULT NULL,
  `pinterest_pin` varchar(150) DEFAULT NULL,
  `stumbleupon_total_view` varchar(150) DEFAULT NULL,
  `stumbleupon_total_comment` varchar(150) DEFAULT NULL,
  `stumbleupon_total_like` varchar(150) DEFAULT NULL,
  `stumbleupon_total_list` varchar(150) DEFAULT NULL,
  `linkedin_share_count` varchar(150) DEFAULT NULL,
  `buffer_share_count` varchar(150) DEFAULT NULL,
  `reddit_score` varchar(150) DEFAULT NULL,
  `reddit_ups` varchar(150) DEFAULT NULL,
  `reddit_downs` varchar(150) DEFAULT NULL,
  `xing_share_count` varchar(150) DEFAULT NULL,
  `moz_subdomain_normalized` varchar(150) DEFAULT NULL,
  `moz_subdomain_raw` varchar(150) DEFAULT NULL,
  `moz_url_normalized` varchar(150) DEFAULT NULL,
  `moz_url_raw` varchar(150) DEFAULT NULL,
  `moz_http_status_code` varchar(150) DEFAULT NULL,
  `moz_domain_authority` varchar(150) DEFAULT NULL,
  `moz_page_authority` varchar(150) DEFAULT NULL,
  `moz_external_equity_links` varchar(150) DEFAULT NULL,
  `moz_links` varchar(150) DEFAULT NULL,
  `ipinfo_isp` varchar(150) DEFAULT NULL,
  `ipinfo_ip` varchar(150) DEFAULT NULL,
  `ipinfo_city` varchar(150) DEFAULT NULL,
  `ipinfo_region` varchar(150) DEFAULT NULL,
  `ipinfo_country` varchar(150) DEFAULT NULL,
  `ipinfo_time_zone` varchar(150) DEFAULT NULL,
  `ipinfo_longitude` varchar(150) DEFAULT NULL,
  `ipinfo_latitude` varchar(150) DEFAULT NULL,
  `macafee_status` varchar(150) DEFAULT NULL,
  `norton_status` varchar(150) DEFAULT NULL,
  `google_safety_status` varchar(150) DEFAULT NULL,
  `avg_status` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `web_common_info`
--


-- --------------------------------------------------------

--
-- Table structure for table `whois_search`
--

CREATE TABLE IF NOT EXISTS `whois_search` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `domain_name` varchar(250) NOT NULL,
  `owner_email` varchar(250) NOT NULL,
  `tech_email` varchar(250) NOT NULL,
  `admin_email` varchar(250) NOT NULL,
  `registrant_email` varchar(200) NOT NULL,
  `registrant_name` varchar(250) DEFAULT NULL,
  `registrant_organization` varchar(250) DEFAULT NULL,
  `registrant_street` text NOT NULL,
  `registrant_city` varchar(100) NOT NULL,
  `registrant_state` varchar(100) NOT NULL,
  `registrant_postal_code` varchar(20) NOT NULL,
  `registrant_country` varchar(100) NOT NULL,
  `registrant_phone` varchar(20) NOT NULL,
  `registrar_url` text NOT NULL,
  `admin_name` varchar(250) NOT NULL,
  `admin_street` text NOT NULL,
  `admin_city` varchar(50) NOT NULL,
  `admin_state` varchar(50) NOT NULL,
  `admin_postal_code` varchar(25) NOT NULL,
  `admin_country` varchar(50) NOT NULL,
  `admin_phone` varchar(25) NOT NULL,
  `is_registered` varchar(50) NOT NULL,
  `namve_servers` varchar(250) NOT NULL,
  `created_at` date NOT NULL,
  `sponsor` varchar(250) NOT NULL,
  `changed_at` varchar(250) NOT NULL,
  `expire_at` varchar(250) NOT NULL,
  `scraped_time` datetime NOT NULL,
  `rawdata` text NOT NULL,
  `bulk_track_code` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE domain ADD add_date DATE NOT NULL AFTER table_name;

INSERT INTO payment_config (id ,paypal_email ,monthly_fee ,currency ,deleted)
      VALUES (1 , 'yourPaypalemail@example.com', '0', 'USD', '0');
        
  
CREATE TABLE IF NOT EXISTS `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(250) CHARACTER SET latin1 DEFAULT NULL,
  `deleted` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

INSERT INTO `modules` (`id`, `module_name`, `deleted`) VALUES
(1, 'Visitor Analysis', '0'),
(2, 'Website Analysis', '0'),
(3, 'Social Network Analysis', '0'),
(4, 'Rank & Index Analysis', '0'),
(5, 'Domain Analysis', '0'),
(6, 'IP Analysis', '0'),
(7, 'Link Analysis', '0'),
(8, 'Keyword Analysis', '0'),
(9, 'Backlink & Ping', '0'),
(10, 'Malware Scan', '0'),
(11, 'Google Adwords Scraper', '0'),
(12, 'Plagiarism Check', '0'),
(13, 'Utilities', '0'),
(14, 'Native Widget', '0'),
(15, 'Native api', '0'),
(16, 'Keyword Position Tracking', '0');


CREATE TABLE `package` (
`id` INT NOT NULL AUTO_INCREMENT ,
`package_name` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`module_ids` VARCHAR( 250 ) NOT NULL ,
`price` FLOAT NOT NULL ,
`deleted` INT NOT NULL ,
PRIMARY KEY ( `id` )
) ENGINE = InnoDB ;

ALTER TABLE `package` CHANGE `deleted` `deleted` ENUM( '0', '1' ) NOT NULL;
ALTER TABLE `package` ADD `validity` INT NOT NULL AFTER `price`;
ALTER TABLE `package` ADD `is_default` ENUM( '0', '1' ) NOT NULL AFTER `validity`;
ALTER TABLE `package` CHANGE `is_default` `is_default` ENUM( '0', '1' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `package` CHANGE `price` `price` VARCHAR( 20 ) NOT NULL DEFAULT '0';
INSERT INTO `package` (`id`, `package_name`, `module_ids`, `price`, `validity`, `is_default`, `deleted`) VALUES (1, 'Trial', '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20', 'Trial', '7', '1', '0');

ALTER TABLE `users` ADD `package_id` INT NOT NULL AFTER `expired_date`;

ALTER TABLE `google_adword` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `keyword_position` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `package` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `page_status` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `whois_search` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `expired_domain_list` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `transaction_history` ADD `package_id` INT NOT NULL;
ALTER TABLE `payment_config` DROP `monthly_fee`;



CREATE TABLE `keyword_position_set` (
`id` INT NOT NULL AUTO_INCREMENT ,
`keyword` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`website` VARCHAR( 250 ) NOT NULL ,
`language` VARCHAR( 250 ) NOT NULL ,
`country` VARCHAR( 250 ) NOT NULL ,
`user_id` INT NOT NULL ,
`add_date` DATETIME NOT NULL ,
`deleted` INT NOT NULL ,
PRIMARY KEY ( `id` )
) ENGINE = InnoDB ;
ALTER TABLE `keyword_position_set` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;


CREATE TABLE IF NOT EXISTS `keyword_position_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `google_position` varchar(100) NOT NULL,
  `bing_position` varchar(100) NOT NULL,
  `yahoo_position` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;







ALTER TABLE  `web_common_info` ADD  `yahoo_back_link_count` VARCHAR( 150 ) NULL AFTER  `google_back_link_count` , ADD  `bing_back_link_count` VARCHAR( 150 ) NULL AFTER  `yahoo_back_link_count` ,  ADD  `similar_site` TEXT NULL AFTER  `avg_status`;
ALTER TABLE `config` ADD `mobile_ready_api_key` VARCHAR( 100 ) NOT NULL AFTER `moz_secret_key`;
ALTER TABLE `web_common_info` ADD `mobile_ready_data` TEXT NOT NULL ;
ALTER TABLE `web_common_info` CHANGE `mobile_ready_data` `mobile_ready_data` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `web_common_info` ENGINE = MYISAM;
ALTER TABLE `web_common_info` ADD `sites_in_same_ip` LONGTEXT NOT NULL;
ALTER TABLE `web_common_info` CHANGE `mobile_ready_data` `mobile_ready_data` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
CHANGE `sites_in_same_ip` `sites_in_same_ip` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE `package` ADD `monthly_limit` TEXT NULL AFTER `module_ids` ,
ADD `bulk_limit` TEXT NULL AFTER `monthly_limit`;
UPDATE `package` SET `package_name` = 'Trial',
`module_ids` = '9,5,11,6,8,16,7,10,15,14,12,4,3,13,1,2',
`monthly_limit` = '{"9":0,"5":0,"11":0,"6":0,"8":0,"16":"0","7":0,"10":0,"15":0,"14":0,"12":0,"4":0,"3":0,"13":0,"1":0,"2":0}',
`bulk_limit` = '{"9":0,"5":0,"11":0,"6":0,"8":0,"16":"0","7":0,"10":0,"15":0,"14":0,"12":0,"4":0,"3":0,"13":0,"1":0,"2":0}',
`price` = 'Trial' WHERE `package`.`id` =1;

CREATE TABLE `usage_log` (
`id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`module_id` INT NOT NULL ,
`user_id` INT NOT NULL ,
`usage_month` INT NOT NULL ,
`usage_year` YEAR NOT NULL ,
`usage_count` INT NOT NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE
 ALGORITHM = UNDEFINED
 VIEW `view_usage_log`
 (id,module_id,user_id,usage_month,usage_year,usage_count)
 AS select * from usage_log where `usage_month`=MONTH(curdate()) and `usage_year`= YEAR(curdate()) ;



ALTER TABLE `web_common_info` ADD INDEX ( `user_id` , `domain_name` ); 

ALTER TABLE `antivirus_scan_info` ADD INDEX  `scan_info` (  `user_id` ,  `scanned_at` ,  `domain_name` );

ALTER TABLE `backlink_generator` ADD INDEX  `backlink_generator` (  `user_id` ,  `generated_at` ,  `domain_name` );

ALTER TABLE `backlink_search` ADD INDEX ( `user_id` , `searched_at` , `domain_name` ); 

ALTER TABLE `domain` ADD INDEX ( `user_id` );

ALTER TABLE `social_info` ADD INDEX ( `user_id` , `search_at` , `domain_name` );

ALTER TABLE `alexa_info` ADD INDEX ( `user_id` , `checked_at` , `domain_name` );

ALTER TABLE `alexa_info_full` ADD INDEX ( `user_id` , `searched_at` , `domain_name` );

ALTER TABLE `dmoz_info` ADD INDEX ( `user_id` , `checked_at` , `domain_name` );

ALTER TABLE `similar_web_info` ADD INDEX ( `user_id` , `searched_at` , `domain_name` );   

ALTER TABLE `website_ping` ADD INDEX ( `user_id` , `ping_at` );

ALTER TABLE `expired_domain_list` ADD `sync_at` DATE NOT NULL AFTER `auction_end_date`;


INSERT INTO `modules` (`id`, `module_name`, `deleted`) VALUES (NULL, 'Code Minifier', '0');
INSERT INTO `modules` (`id`, `module_name`, `deleted`) VALUES (NULL, 'URL Shortener', '0');

CREATE TABLE `url_shortener` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`long_url` TEXT NULL ,
`short_url` TEXT NULL ,
`add_date` DATE NOT NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `url_shortener` ADD `user_id` INT( 11 ) NOT NULL;

CREATE TABLE `ip_v6_check` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`ipv6` VARCHAR( 200 ) NULL ,
`searched_at` DATETIME NOT NULL
) ENGINE = MYISAM ;

ALTER TABLE `ip_v6_check` ADD `ip` VARCHAR( 200 ) NULL ,
ADD `is_ipv6_compatiable` VARCHAR( 10 ) NOT NULL DEFAULT '0';
ALTER TABLE `ip_v6_check` ADD `domain_name` TEXT NULL AFTER `id`;
ALTER TABLE `ip_v6_check` ADD `user_id` INT NOT NULL DEFAULT '0' AFTER `is_ipv6_compatiable`;
ALTER TABLE `ip_v6_check` CHANGE `is_ipv6_compatiable` `is_ipv6_support` VARCHAR( 10 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '0';
CREATE TABLE `login_config` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`api_id` VARCHAR( 250 ) NULL ,
`api_secret` VARCHAR( 250 ) NULL ,
`google_client_id` VARCHAR( 250 ) NULL ,
`google_client_secret` VARCHAR( 250 ) NULL ,
`status` ENUM( '0', '1' ) NOT NULL DEFAULT '1',
`deleted` ENUM( '0', '1' ) NOT NULL DEFAULT '0'
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `login_config` ADD `app_name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `id` ;
ALTER TABLE `login_config` CHANGE `google_client_id` `google_client_id` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `page_status` CHANGE `url` `url` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `ip_v6_check` CHANGE `domain_name` `domain_name` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE `payment_config` CHANGE `currency` `currency` ENUM( 'USD', 'AUD', 'CAD', 'EUR', 'ILS', 'NZD', 'RUB', 'SGD', 'SEK', 'BRL' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

ALTER TABLE `transaction_history` ADD `stripe_card_source` TEXT NOT NULL; 

ALTER TABLE `payment_config` ADD `stripe_secret_key` VARCHAR( 150 ) NOT NULL AFTER `paypal_email`;

ALTER TABLE `payment_config` ADD `stripe_publishable_key` VARCHAR( 150 ) NOT NULL AFTER `stripe_secret_key`;



CREATE TABLE IF NOT EXISTS `ad_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section1_html` longtext,
  `section1_html_mobile` longtext,
  `section2_html` longtext,
  `section3_html` longtext,
  `section4_html` longtext,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
