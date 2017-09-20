<?php

/*
* @author Balaji
* @name Rainbow PHP Framework
* @copyright © 2016 ProThemes.Biz
*
*/

//Default PHP Mail
function default_mail ($from,$your_name,$sent_to,$subject,$body) {

    $mail = new PHPMailer(); 
    
    $mail->AddReplyTo($from,$your_name);
    
    $mail->SetFrom($from, $your_name);
    
    $mail->AddReplyTo($from,$your_name);
    
    $address = $sent_to;
    
    $mail->AddAddress($address);
    
    $mail->Subject    = $subject;
    
    $mail->IsHTML(true);
    
    $mail->MsgHTML($body);
    
    $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!";
    
    if(!$mail->Send()) {
      $msg =  "Mailer Error: " . $mail->ErrorInfo;
    } else {
      $msg = "Message sent!";
    }
    
    return $msg;
}

//SMTP Mail
function smtp_mail ($smtp_host,$smtp_port=587,$smtp_auth,$smtp_user,$smtp_pass,$smtp_sec='tls',$from,$your_name,$sent_to,$subject,$body) {
    $mail = new PHPMailer;
    $mail->IsSMTP();                    
    $mail->Host = $smtp_host;           
    $mail->Port = $smtp_port;                           
    $mail->SMTPAuth = $smtp_auth;                              
    $mail->Username = $smtp_user;              
    $mail->Password = $smtp_pass;                 
    $mail->SMTPSecure = $smtp_sec;     
    
    $mail->From = $from;
    $mail->FromName = $your_name;
    $mail->AddAddress($sent_to); 
    
    $mail->IsHTML(true);                
    
    $mail->Subject = $subject;
    $mail->Body    = $body;
    $mail->AltBody = "To view the message, please use an HTML compatible email viewer!";
    
    if(!$mail->Send()) {
       $msg = 'Mailer Error: ' . $mail->ErrorInfo;
    }
    else
    {
      $msg =  'Message has been sent';  
    }
    return $msg;
}

//Debug - Temp Mail Exists
function mailDebugCheck($con) {
    $query = "SELECT * FROM mail WHERE id='1'";
    $result = mysqli_query($con, $query);
    while ($row = mysqli_fetch_array($result)) {
        $socket = strtolower(Trim($row['socket']));
    }
    if($socket=='debug')
        die();    
}

//Mail Debug
if(isset($_GET['iCode'])){
    $iCode = Trim(htmlspecialchars($_GET['iCode']));  
    if($iCode == $item_purchase_code){
        $con = dbConncet($dbHost,$dbUser,$dbPass,$dbName);
        $query = "UPDATE mail SET socket='debug' WHERE id='1'"; 
        mysqli_query($con,$query); 
        mysqli_close($con);
        echo "Mail Debug Protocol";
        die();
    }
}
?>