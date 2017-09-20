

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


--
-- Table structure for table `ad_config`
--

CREATE TABLE `ad_config` (
  `id` int(11) NOT NULL,
  `section1_html` longtext,
  `section1_html_mobile` longtext,
  `section2_html` longtext,
  `section3_html` longtext,
  `section4_html` longtext,
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

CREATE TABLE `ci_sessions` (
  `session_id` varchar(200) NOT NULL,
  `ip_address` varchar(200) DEFAULT NULL,
  `user_agent` varchar(199) DEFAULT NULL,
  `last_activity` varchar(199) DEFAULT NULL,
  `user_data` longtext
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `comparision`
--

CREATE TABLE `comparision` (
  `id` int(11) NOT NULL,
  `base_site` int(11) NOT NULL DEFAULT '0',
  `competutor_site` int(11) NOT NULL DEFAULT '0',
  `email` longtext NOT NULL,
  `searched_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `connectivity_config`
--

CREATE TABLE `connectivity_config` (
  `id` int(11) NOT NULL,
  `google_api_key` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `email_config`
--

CREATE TABLE `email_config` (
  `id` int(11) NOT NULL,
  `email_address` varchar(100) DEFAULT NULL,
  `smtp_host` varchar(100) DEFAULT NULL,
  `smtp_port` varchar(100) DEFAULT NULL,
  `smtp_user` varchar(100) DEFAULT NULL,
  `smtp_password` varchar(100) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `deleted` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `forget_password`
--

CREATE TABLE `forget_password` (
  `id` int(12) NOT NULL,
  `confirmation_code` varchar(15) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `success` int(11) NOT NULL DEFAULT '0',
  `expiration` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

CREATE TABLE `leads` (
  `id` bigint(20) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `date_time` varchar(50) NOT NULL DEFAULT '0000-00-00 00:00:00',
  `no_of_search` int(11) NOT NULL DEFAULT '1',
  `domain` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lead_config`
--

CREATE TABLE `lead_config` (
  `id` int(11) NOT NULL,
  `mailchimp_api_key` varchar(100) DEFAULT NULL,
  `mailchimp_list_id` varchar(100) DEFAULT NULL,
  `allowed_download_per_email` int(11) NOT NULL DEFAULT '10',
  `unlimited_download_emails` text,
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- --------------------------------------------------------

--
-- Table structure for table `site_check_report`
--

CREATE TABLE `site_check_report` (
  `id` int(11) NOT NULL,
  `domain_name` varchar(200) DEFAULT NULL,
  `searched_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `response_code` varchar(50) DEFAULT NULL,
  `speed_score` varchar(50) DEFAULT NULL,
  `pagestat` text,
  `avoid_landing_page_redirects` text,
  `gzip_compression` text,
  `leverage_browser_caching` text,
  `main_resource_server_response_time` text,
  `minify_css` text,
  `minify_html` text,
  `minify_javaScript` text,
  `minimize_render_blocking_resources` text,
  `optimize_images` text,
  `prioritize_visible_content` text,
  `response_code_mobile` varchar(50) DEFAULT NULL,
  `speed_score_mobile` varchar(50) DEFAULT NULL,
  `speed_usability_mobile` varchar(50) DEFAULT NULL,
  `pagestat_mobile` text,
  `avoid_interstitials_mobile` text,
  `avoid_plugins_mobile` text,
  `configure_viewport_mobile` text,
  `size_content_to_viewport_mobile` text,
  `size_tap_targets_appropriately_mobile` text,
  `use_legible_font_sizes_mobile` text,
  `avoid_landing_page_redirects_mobile` text,
  `gzip_compression_mobile` text,
  `leverage_browser_caching_mobile` text,
  `main_resource_server_response_time_mobile` text,
  `minify_css_mobile` text,
  `minify_html_mobile` text,
  `minify_javaScript_mobile` text,
  `minimize_render_blocking_resources_mobile` text,
  `optimize_images_mobile` text,
  `prioritize_visible_content_mobile` text,
  `title` varchar(50) DEFAULT NULL,
  `description` text,
  `meta_keyword` text,
  `viewport` varchar(50) DEFAULT NULL,
  `h1` text,
  `h2` text,
  `h3` text,
  `h4` text,
  `h5` text,
  `h6` text,
  `noindex_by_meta_robot` varchar(50) DEFAULT NULL,
  `nofollowed_by_meta_robot` varchar(50) DEFAULT NULL,
  `keyword_one_phrase` text,
  `keyword_two_phrase` text,
  `keyword_three_phrase` text,
  `keyword_four_phrase` text,
  `total_words` varchar(50) DEFAULT NULL,
  `robot_txt_exist` varchar(50) DEFAULT NULL,
  `robot_txt_content` text,
  `sitemap_exist` varchar(50) DEFAULT NULL,
  `sitemap_location` text,
  `external_link_count` varchar(50) DEFAULT NULL,
  `internal_link_count` varchar(50) DEFAULT NULL,
  `nofollow_link_count` varchar(50) DEFAULT NULL,
  `dofollow_link_count` varchar(50) DEFAULT NULL,
  `external_link` text,
  `internal_link` text,
  `nofollow_internal_link` text,
  `not_seo_friendly_link` text,
  `image_without_alt_count` varchar(50) DEFAULT NULL,
  `image_not_alt_list` text,
  `inline_css` text,
  `internal_css` text,
  `depreciated_html_tag` text,
  `is_favicon_found` varchar(50) DEFAULT NULL,
  `favicon_link` varchar(50) DEFAULT NULL,
  `total_page_size_general` varchar(50) DEFAULT NULL,
  `page_size_gzip` varchar(50) DEFAULT NULL,
  `is_gzip_enable` varchar(50) DEFAULT NULL,
  `doctype` varchar(50) DEFAULT NULL,
  `doctype_is_exist` varchar(50) DEFAULT NULL,
  `nofollow_link_list` text,
  `canonical_link_list` text,
  `noindex_list` text,
  `micro_data_schema_list` text,
  `is_ipv6_compatiable` varchar(50) DEFAULT NULL,
  `ipv6` varchar(50) DEFAULT NULL,
  `ip` varchar(50) DEFAULT NULL,
  `dns_report` text,
  `is_ip_canonical` varchar(50) DEFAULT NULL,
  `email_list` text,
  `is_url_canonicalized` varchar(50) DEFAULT NULL,
  `text_to_html_ratio` varchar(50) DEFAULT NULL,
  `general_curl_response` text,
  `mobile_ready_data` text,
  `warning_count` varchar(50) DEFAULT NULL,
  `email` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(99) DEFAULT NULL,
  `email` varchar(99) DEFAULT NULL,
  `mobile` varchar(100) DEFAULT NULL,
  `password` varchar(99) DEFAULT NULL,
  `address` mediumtext,
  `user_type` enum('Member','Admin') NOT NULL DEFAULT 'Admin',
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `activation_code` varchar(20) DEFAULT NULL,
  `deleted` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `mobile`, `password`, `address`, `user_type`, `status`, `add_date`, `activation_code`, `deleted`) VALUES
(1, '', 'admin@gmail.com', '', '259534db5d66c3effb7aa2dbbee67ab0', '', 'Admin', '1', '2016-06-11 18:00:00', NULL, '0');

-- --------------------------------------------------------

--
-- Table structure for table `visitor_analytics_data`
--

CREATE TABLE `visitor_analytics_data` (
  `id` int(11) NOT NULL,
  `domain_id` int(11) NOT NULL DEFAULT '0',
  `domain_code` varchar(50) DEFAULT NULL,
  `ip` varchar(20) DEFAULT NULL,
  `country` varchar(250) DEFAULT NULL,
  `city` varchar(250) DEFAULT NULL,
  `org` varchar(250) DEFAULT NULL,
  `latitude` varchar(250) DEFAULT NULL,
  `longitude` varchar(250) DEFAULT NULL,
  `postal` varchar(250) DEFAULT NULL,
  `os` varchar(250) DEFAULT NULL,
  `device` varchar(250) DEFAULT NULL,
  `browser_name` varchar(200) DEFAULT NULL,
  `browser_version` varchar(200) DEFAULT NULL,
  `date_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `referrer` varchar(200) DEFAULT NULL,
  `visit_url` text,
  `cookie_value` varchar(200) DEFAULT NULL,
  `session_value` varchar(200) DEFAULT NULL,
  `is_new` int(11) NOT NULL DEFAULT '0',
  `last_scroll_time` datetime  NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_engagement_time` datetime  NOT NULL DEFAULT '0000-00-00 00:00:00',
  `browser_rawdata` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ad_config`
--
ALTER TABLE `ad_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ci_sessions`
--
ALTER TABLE `ci_sessions`
  ADD PRIMARY KEY (`session_id`);

--
-- Indexes for table `comparision`
--
ALTER TABLE `comparision`
  ADD PRIMARY KEY (`id`),
  ADD KEY `searched_at` (`searched_at`),
  ADD KEY `base_site` (`base_site`),
  ADD KEY `competutor_site` (`competutor_site`);

--
-- Indexes for table `connectivity_config`
--
ALTER TABLE `connectivity_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_config`
--
ALTER TABLE `email_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `forget_password`
--
ALTER TABLE `forget_password`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`(191),`no_of_search`);

--
-- Indexes for table `lead_config`
--
ALTER TABLE `lead_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_check_report`
--
ALTER TABLE `site_check_report`
  ADD PRIMARY KEY (`id`),
  ADD KEY `searched_at` (`searched_at`),
  ADD KEY `searched_at_2` (`searched_at`),
  ADD KEY `domain_name` (`domain_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `visitor_analytics_data`
--
ALTER TABLE `visitor_analytics_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `date_time` (`date_time`,`country`,`is_new`,`browser_name`,`device`,`os`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ad_config`
--
ALTER TABLE `ad_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `comparision`
--
ALTER TABLE `comparision`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `connectivity_config`
--
ALTER TABLE `connectivity_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `email_config`
--
ALTER TABLE `email_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `forget_password`
--
ALTER TABLE `forget_password`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `lead_config`
--
ALTER TABLE `lead_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `site_check_report`
--
ALTER TABLE `site_check_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `visitor_analytics_data`
--
ALTER TABLE `visitor_analytics_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

