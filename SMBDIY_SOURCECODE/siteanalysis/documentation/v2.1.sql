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

  update users set package_id=0 where user_type="Admin";