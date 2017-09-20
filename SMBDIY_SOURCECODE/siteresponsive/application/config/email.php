<?php
/*** GMAIL CONFIGURATION  ***/
   $config['protocol'] = 'smtp';
    $config['smtp_host'] = 'ssl://smtp.gmail.com'; //change this
    $config['smtp_port'] = '465';
    $config['smtp_user'] = 'owlclick@gmail.com'; //change this
    $config['smtp_pass'] = '2o11owl&^'; //change this
    $config['mailtype'] = 'html';
    $config['charset'] = 'iso-8859-1';
    $config['wordwrap'] = TRUE;
    $config['newline'] = "\r\n";
    
/*** EC2 SME CONFIGURATION  ***/
  /*  $config['protocol'] = 'smtp';
    $config['smtp_host'] = 'email-smtp.us-east-1.amazonaws.com'; //change this
    $config['smtp_port'] = '465';
    $config['smtp_user'] = 'AKIAJQ3663SAZGZDLXMQ'; //change this
    $config['smtp_pass'] = 'AuGXTv2tbDDjj1wxMoKfpJQZ527sR8q5TgXlUIxESuO6'; //change this
    $config['mailtype'] = 'html';
    $config['charset'] = 'iso-8859-1';
    $config['wordwrap'] = TRUE;
    $config['newline'] = "\r\n";
    */
/*** CODEIGNITER EMAIL CONFIGURATION   ****/
	/* $config['protocol'] = 'sendmail';
	//$config['mailpath'] = '/usr/sbin/sendmail';
	$config['mailpath'] = 'C:\sendmail\sendmail.exe';
	$config['charset'] = 'iso-8859-1';
	$config['wordwrap'] = TRUE; */
	
?>