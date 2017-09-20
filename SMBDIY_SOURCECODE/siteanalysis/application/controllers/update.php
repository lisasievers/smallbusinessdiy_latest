<?php
class update extends CI_Controller
{
      
    public function __construct()
    {
        parent::__construct();   
        $this->load->database();
        $this->load->model('basic');
        set_time_limit(0);
    }

    public function index()
    {
        $this->v3_4to3_5();
    }

    public function v3_4to3_5()
    {
        $lines='ALTER TABLE `ip_domain_info` ADD `organization` VARCHAR(100) NOT NULL AFTER `isp`';       
        // Loop through each line
        $lines=explode(";", $lines);
        $count=0;
        foreach ($lines as $line) 
        {
            $count++;      
            $this->db->query($line);
        }
        echo "SiteSpy has been updated to v3.5 successfully.".$count." queries executed.";
    }


    public function v3_2to3_3()
    {
        $lines='CREATE TABLE IF NOT EXISTS `ad_config` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `section1_html` longtext,
          `section1_html_mobile` longtext,
          `section2_html` longtext,
          `section3_html` longtext,
          `section4_html` longtext,
          `status` enum("0","1") NOT NULL DEFAULT "1",
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8';


       
        // Loop through each line

        $lines=explode(";", $lines);
        $count=0;
        foreach ($lines as $line) 
        {
            $count++;      
            $this->db->query($line);
        }
        echo "SiteSpy has been updated to v3.3 successfully.".$count." queries executed.";
    }

    public function v1_to_v1_1()
    {
        // writting client js
        $client_js_content=file_get_contents('js/analytics_js/client.js');
        $client_js_content_new=str_replace("base_url_replace/", site_url(), $client_js_content);
        file_put_contents('js/analytics_js/client.js', $client_js_content_new, LOCK_EX);
        // writting client js

        $sql="ALTER TABLE domain ADD add_date DATE NOT NULL AFTER table_name;";
        $this->basic->execute_complex_query($sql);

        $current_config=array();
        $current_config=$this->basic->get_data("payment_config");
        if(count($current_config)==0) 
        {
            $sql="INSERT INTO payment_config (id ,paypal_email ,monthly_fee ,currency ,deleted)
            VALUES (1 , 'yourPaypalemail@example.com', '0', 'USD', '0');";
            $this->basic->execute_complex_query($sql);
        }
        echo "SiteSpy has been updated to v1.1 successfully.";    
    }

    public function v2to_v2_1()
    {

        $lines='ALTER TABLE  `web_common_info` ADD  `yahoo_back_link_count` VARCHAR( 150 ) NULL AFTER  `google_back_link_count`;
        ALTER TABLE  `web_common_info` ADD  `bing_back_link_count` VARCHAR( 150 ) NULL AFTER  `yahoo_back_link_count`;
        ALTER TABLE  `web_common_info`  ADD  `similar_site` TEXT NULL AFTER  `avg_status`;
        ALTER TABLE `config` ADD `mobile_ready_api_key` VARCHAR( 100 ) NOT NULL AFTER `moz_secret_key`;
        ALTER TABLE `web_common_info` ADD `mobile_ready_data` TEXT NOT NULL ;
        ALTER TABLE `web_common_info` CHANGE `mobile_ready_data` `mobile_ready_data` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `web_common_info` ENGINE = MYISAM;
        ALTER TABLE `web_common_info` ADD `sites_in_same_ip` LONGTEXT NOT NULL;
        ALTER TABLE `web_common_info` CHANGE `mobile_ready_data` `mobile_ready_data` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
        CHANGE `sites_in_same_ip` `sites_in_same_ip` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
        ALTER TABLE `package` ADD `monthly_limit` TEXT NULL AFTER `module_ids` ,
        ADD `bulk_limit` TEXT NULL AFTER `monthly_limit`;
        UPDATE `package` SET `package_name` = "Trial",
        `module_ids` = "9,5,11,6,8,16,7,10,15,14,12,4,3,13,1,2",
        `monthly_limit` = \'{"9":0,"5":0,"11":0,"6":0,"8":0,"16":"0","7":0,"10":0,"15":0,"14":0,"12":0,"4":0,"3":0,"13":0,"1":0,"2":0}\',
        `bulk_limit` = \'{"9":0,"5":0,"11":0,"6":0,"8":0,"16":"0","7":0,"10":0,"15":0,"14":0,"12":0,"4":0,"3":0,"13":0,"1":0,"2":0}\',
        `price` = "Trial" WHERE `package`.`id` =1;
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
         update users set package_id=0 where user_type="Admin"';


       
        // Loop through each line

        $lines=explode(";", $lines);
        $count=0;
        foreach ($lines as $line) 
        {
            $count++;      
            $this->db->query($line);
        }
        echo "SiteSpy has been updated to v2.1 successfully.".$count." queries executed.";    
    }

    public function v2_2to_v3_0()
    {

        // writting client js
        $client_js_content=file_get_contents('js/analytics_js/client.js');
        $client_js_content_new=str_replace("base_url_replace/", site_url(), $client_js_content);
        file_put_contents('js/analytics_js/client.js', $client_js_content_new, LOCK_EX);
        // writting client js

        $lines='ALTER TABLE `web_common_info` ADD INDEX ( `user_id` , `domain_name` ); 

        ALTER TABLE `antivirus_scan_info` ADD INDEX  `scan_info` (  `user_id` ,  `scanned_at` ,  `domain_name` );

        ALTER TABLE `backlink_generator` ADD INDEX  `backlink_generator` (  `user_id` ,  `generated_at` ,  `domain_name` );

        ALTER TABLE `backlink_search` ADD INDEX ( `user_id` , `searched_at` , `domain_name` ); 

        ALTER TABLE `domain` ADD INDEX ( `user_id` );

        ALTER TABLE `social_info` ADD INDEX ( `user_id` , `search_at` , `domain_name` );

        ALTER TABLE `alexa_info` ADD INDEX ( `user_id` , `checked_at` , `domain_name` );

        ALTER TABLE `alexa_info_full` ADD INDEX ( `user_id` , `searched_at` , `domain_name` );

        ALTER TABLE `dmoz_info` ADD INDEX ( `user_id` , `checked_at` , `domain_name` );

        ALTER TABLE `similar_web_info` ADD INDEX ( `user_id` , `searched_at` , `domain_name` );

        ALTER TABLE `expired_domain_list` ADD `sync_at` DATE NOT NULL AFTER `auction_end_date`; 

        ALTER TABLE `website_ping` ADD INDEX ( `user_id` , `ping_at` )';


       
        // Loop through each line

        $lines=explode(";", $lines);
        $count=0;
        foreach ($lines as $line) 
        {
            $count++;      
            $this->db->query($line);
        }
        echo "SiteSpy has been updated to v3.0 successfully.".$count." queries executed.";
    }


    function v_3_1to3_2()
    {
        $lines="ALTER TABLE `web_common_info` ADD INDEX ( `user_id` , `domain_name` ); 

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
        ALTER TABLE `payment_config` ADD `stripe_publishable_key` VARCHAR( 150 ) NOT NULL AFTER `stripe_secret_key`";


       
        // Loop through each line

        $lines=explode(";", $lines);
        $count=0;
        foreach ($lines as $line) 
        {
            $count++;      
            $this->db->query($line);
        }
        echo "SiteSpy has been updated to v3.2 successfully.".$count." queries executed.";
    }


    public function v_3_1to3_2_1() // 3.2 to 3.2 fix version (new users : 3.2 only)
    {
        $lines="ALTER TABLE `web_common_info` ADD INDEX ( `user_id` , `domain_name` ); 

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

        ALTER TABLE `expired_domain_list` ADD `sync_at` DATE NOT NULL AFTER `auction_end_date`";


       
        // Loop through each line

        $lines=explode(";", $lines);
        $count=0;
        foreach ($lines as $line) 
        {
            $count++;      
            $this->db->query($line);
        }
        echo "SiteSpy has been updated to v3.2 successfully.".$count." queries executed.";
    }



    function delete_update()
    {
        unlink(APPPATH."controllers/update.php");
    }
 


}
