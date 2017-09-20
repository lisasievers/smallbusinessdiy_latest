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


update users set package_id=1;