<?php
class Blocker {
 
    function Blocker(){
    }
    
    /**
     * This function used to block the every request except allowed ip address
     */
    function requestBlocker(){
        //echo $_SERVER["REMOTE_ADDR"]; exit; 
        /*if($_SERVER["REMOTE_ADDR"] != "127.0.0.1"){
            echo "not allowed";
            die;
        }*/
          $userid = get_cookie('user_id');
    }
}
?>