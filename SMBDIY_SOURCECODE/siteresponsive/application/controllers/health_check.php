<?php

require_once("home.php");

class health_check extends Home
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($base_site="")
    {
      $this->report();
    }

    public function report($id=0,$domain="")
    {
      //echo 'r';exit;
       
       if($id==0) exit();
       $where['where'] = array('id'=>$id);
       $data["site_info"] = $this->basic->get_data("site_check_report",$where);

       if(isset($data["site_info"][0])) $page_title= strtolower($data["site_info"][0]["domain_name"]);
       else exit();

       $data["page_title"]=str_replace(array("www.","http://","https://"), "", $page_title);

       $data['seo_meta_description']="web site healthy check report of ".$page_title." by ".$this->config->item("product_short_name");
       $project_url=$this->config->item('project_url');
       $data["body"]="site/report";
       $data['topmenu']=array('Home' => $project_url,'About Us' => $project_url.'/#aboutus','Products' => $project_url.'/#product','Resources' => $project_url.'/#resource','Blog' => $project_url.'/blog');
       $data['bmenu1']=array('Account' => $project_url.'/#','Support' => $project_url.'/#','Product Catalog' => $project_url.'/#');
       $data['bmenu2']= array('Sitemap' => $project_url.'/#','Find Domain' => $project_url.'/#','Whois Search' => $project_url.'/#');
       $this->load->helper('cookie');
       $data['username'] = get_cookie('name');
       $this->load->library("site_check");
       $this->config->load('recommendation_config');
       $this->_site_viewcontroller($data);
    }

       public function report_responsive($id=0,$domain="")
    {
      //echo 's';exit; 
       if($id==0) exit();
       $where['where'] = array('id'=>$id);
       $data["site_info"] = $this->basic->get_data("site_check_report",$where);

       if(isset($data["site_info"][0])) $page_title= strtolower($data["site_info"][0]["domain_name"]);
       else exit();

       $data["page_title"]=str_replace(array("www.","http://","https://"), "", $page_title);

       $data['seo_meta_description']="web site healthy check report of ".$page_title." by ".$this->config->item("product_short_name");
       $project_url=$this->config->item('project_url');
       $data["body"]="site/report_responsive";
       $data['topmenu']=array('Home' => $project_url,'About Us' => $project_url.'/#aboutus','Products' => $project_url.'/#product','Resources' => $project_url.'/#resource','Blog' => $project_url.'/blog');
       $data['bmenu1']=array('Account' => $project_url.'/#','Support' => $project_url.'/#','Product Catalog' => $project_url.'/#');
       $data['bmenu2']= array('Sitemap' => $project_url.'/#','Find Domain' => $project_url.'/#','Whois Search' => $project_url.'/#');
    
       $this->load->library("site_check");
       $this->config->load('recommendation_config');
       $this->_havesite_viewcontroller($data);
    }
       public function report_seo($id=0,$domain="")
    {
       
       if($id==0) exit();
       $where['where'] = array('id'=>$id);
       $data["site_info"] = $this->basic->get_data("site_check_report",$where);

       if(isset($data["site_info"][0])) $page_title= strtolower($data["site_info"][0]["domain_name"]);
       else exit();

       $data["page_title"]=str_replace(array("www.","http://","https://"), "", $page_title);

       $data['seo_meta_description']="web site healthy check report of ".$page_title." by ".$this->config->item("product_short_name");
       $project_url=$this->config->item('project_url');
       $data["body"]="site/report_seo";
       $data['topmenu']=array('Home' => $project_url,'About Us' => $project_url.'/#aboutus','Products' => $project_url.'/#product','Resources' => $project_url.'/#resource','Blog' => $project_url.'/blog');
    $data['bmenu1']=array('Account' => $project_url.'/#','Support' => $project_url.'/#','Product Catalog' => $project_url.'/#');
    $data['bmenu2']= array('Sitemap' => $project_url.'/#','Find Domain' => $project_url.'/#','Whois Search' => $project_url.'/#');
    
       $this->load->library("site_check");
       $this->config->load('recommendation_config');
       $this->_havesite_viewcontroller($data);
    }

    public function report_pdf($id=0,$domain="")
    {
       
       if($id==0) exit();
       $where['where'] = array('id'=>$id);
       $data["site_info"] = $this->basic->get_data("site_check_report",$where);


       if(isset($data["site_info"][0])) $page_title= strtolower($data["site_info"][0]["domain_name"]);
       else exit();

       $data["page_title"]=str_replace(array("www.","http://","https://"), "", $page_title);       
       $this->load->library("site_check");
       $data["load_css_js"]=1;
       $this->config->load('recommendation_config');
       
       ob_start();
       $this->load->view("site/report",$data); 
       ob_get_contents();
       $html=ob_get_clean();   
       include("mpdf/mpdf.php");
       $mpdf=new mpdf('utf-8','Letter','','arialms');
       $mpdf->addPage();
       $mpdf->SetDisplayMode('fullpage');
       $mpdf->writeHTML($html);       
       $domain=str_replace("/","", $data["page_title"]);
       $domain=trim($domain);
       $download_id=$this->session->userdata('download_id_front').$this->_random_number_generator(10);
       $file_name="download/health_check_report_".$domain."_".$download_id.".pdf";
       $mpdf->output($file_name, 'F');
       return $file_name;
          
    }
    public function report_pdf_responsive($id=0,$domain="",$to="")
    {
       
       if($id==0) exit();
       $where['where'] = array('id'=>$id);
       $data["site_info"] = $this->basic->get_data("site_check_report",$where);


       if(isset($data["site_info"][0])) $page_title= strtolower($data["site_info"][0]["domain_name"]);
       else exit();

       $data["page_title"]=str_replace(array("www.","http://","https://"), "", $page_title);       
       $this->load->library("site_check");
       $data["load_css_js"]=1;
       $this->config->load('recommendation_config');
       //return ($this->load->view("site/report_responsive",$data));
       ob_start();
       $this->load->view("site/report_responsive",$data); 
       ob_get_contents();
       $html=ob_get_clean();   
       include("mpdf/mpdf.php");
       $mpdf=new mpdf('utf-8','Letter','','arialms');
       $mpdf->addPage();
       $mpdf->SetDisplayMode('fullpage');
       $mpdf->writeHTML(utf8_encode($html));       
       $domain=str_replace("/","", $data["page_title"]);
       $domain=trim($domain);
       $download_id=$this->session->userdata('download_id_front').$this->_random_number_generator(10);
       $file_name="download/health_check_report_".$domain."_".$download_id.".pdf";
       $mpdf->output($file_name, 'F');
      // $mpdf->output('../../filename.pdf','F');
       // echo 'fname'.$file_name; exit;
       //return $file_name;
       //echo $to;exit;

                $from = $this->config->item('institute_email');
                $subject = 'Website Testing Report';
               // $message='sample report';

                //$this->email->attach($content, 'attachment', $filename, 'application/pdf');
                //$message = 'Hi, '.$resname.'<br/>Your Registered Email: ' .$resemail. '<br/>Your Temporary password: password<br/>Please signin and get more reports <a href="http://localhost/sitehome/">Sign In</a>';
                $this->_mail_sender_report($from, $to = $to, $subject, $message=$file_name, $mask = $from,$html=1);
               // $this->session->set_userdata('mail_sent', 1);
                //echo '1';
          
    }
        public function report_pdf_audit($id=0,$domain="",$to="")
    {
       
       if($id==0) exit();
       $where['where'] = array('id'=>$id);
       $data["site_info"] = $this->basic->get_data("site_check_report",$where);


       if(isset($data["site_info"][0])) $page_title= strtolower($data["site_info"][0]["domain_name"]);
       else exit();

       $data["page_title"]=str_replace(array("www.","http://","https://"), "", $page_title);       
       $this->load->library("site_check");
       $data["load_css_js"]=1;
       $this->config->load('recommendation_config');
       
       ob_start();
       $this->load->view("site/report_seo",$data); 
       ob_get_contents();
       $html=ob_get_clean();   
       include("mpdf/mpdf.php");
       $mpdf=new mpdf('utf-8','Letter','','arialms');
       $mpdf->addPage();
       $mpdf->SetDisplayMode('fullpage');
       $mpdf->writeHTML($html);       
       $domain=str_replace("/","", $data["page_title"]);
       $domain=trim($domain);
       $download_id=$this->session->userdata('download_id_front').$this->_random_number_generator(10);
       $file_name="download/health_check_report_".$domain."_".$download_id.".pdf";
       $mpdf->output($file_name, 'F');
       //return $file_name;
       $from = $this->config->item('institute_email');
                $subject = 'Website Testing Report';
               // $message='sample report';

                //$this->email->attach($content, 'attachment', $filename, 'application/pdf');
                //$message = 'Hi, '.$resname.'<br/>Your Registered Email: ' .$resemail. '<br/>Your Temporary password: password<br/>Please signin and get more reports <a href="http://localhost/sitehome/">Sign In</a>';
                $this->_mail_sender_report($from, $to = $to, $subject, $message=$file_name, $mask = $from,$html=1);
              

          
    }
             
   
    public function send_download_link()
    {
        if($_POST)
        {
            $lead_config=$this->basic->get_data("lead_config");
            if(is_array($lead_config) && isset($lead_config[0]))
            {
              $allowed_download_per_email=$lead_config[0]["allowed_download_per_email"];
              $unlimited_download_emails=$lead_config[0]["unlimited_download_emails"];
            }
            else
            {
              $allowed_download_per_email=10;  
              $unlimited_download_emails="";
            }
            
            $unlimited_download_emails=explode(',',$unlimited_download_emails);
            
            $email=$this->input->post("email");
            $name=$this->input->post("name");
            $id=$this->input->post("hidden_id");

           
            $data=array("firstname"=>$name,"email"=>$email);

            $where['where'] = array('id'=>$id);
            $data["site_info"] = $this->basic->get_data("site_check_report",$where);
            $domain="";
            if(isset($data["site_info"][0])) $domain= strtolower($data["site_info"][0]["domain_name"]);
            $domain=str_replace(array("www.","http://","https://"), "", $domain);  
            
            
            if($this->basic->is_exist($table="leads",$where=array("email"=>$email,"no_of_search >="=>$allowed_download_per_email),$select="id"))
            {
              if(!in_array($email,$unlimited_download_emails))
              $ret_val= "0"; // crossed limit
              else $ret_val="1"; 
            }
            else $ret_val="1";
            
            if($ret_val=="1")
            {               
                if($this->basic->is_exist($table="leads",$where=array("email"=>$email),$select="id"))
                {
                    $this->basic->execute_complex_query("UPDATE leads SET name='".$name."',no_of_search=no_of_search+1,domain=trim(both ',' from concat(domain, ', ".$domain."')),date_time='".date("Y-d-m G:i:s")."' WHERE email='".$email."'");
                    $this->basic->execute_complex_query("UPDATE site_check_report SET email=trim(both ',' from concat(email, ', ".$email."')) WHERE id='".$id."'");
                    $ret_val= "2"; // updated               
                }
                else 
                {
                    $this->basic->insert_data("leads",array("name"=>$name,"domain"=>$domain,"email"=>$email,"date_time"=>date("Y-d-m G:i:s")));            
                    $this->load->library("google");
                    $this->google->syncMailchimp($data);
                    $ret_val= "3"; // inserted
                }

                $file_name=$this->report_pdf($id);
                $product=$this->config->item('product_name');
                $subject=$product." | "."Health Check Report : ".$domain;
                $download_link="<a href='".base_url().$file_name."'> health check report of ".$domain."</a>";
                $message="Hello {$name}, <br/> Thank you for using {$product}.<br/> Please follow the link to download report: {$download_link}<br/><br/><br/>{$product} Team";

                $this->_mail_sender($from = '', $to = $email, $subject, $message, $mask = $product, $html = 1);
            }          

            echo $ret_val;
            
        }
    }



    public function direct_download()
    {
        if($_POST)
        {
            
            $id=$this->input->post("hidden_id");            
            $file_name=$this->report_pdf($id);
            $download_link=base_url().$file_name;
            echo '<div class="box-body chart-responsive minus"><div class="col-xs-12"><div class="alert text-center" style="font-size:18px">'.$this->lang->line("pdf report has been generated"). '<br/> <br/><a href="'.$download_link.'" target="_BLANK" style="font-size:20px"> <i class="fa fa-cloud-download"></i> '.$this->lang->line("click here to download").'</a></div></div></div>';
                 
        }
    }

    public function direct_download_audit()
    {
        if($_POST)
        {
            
            $id=$this->input->post("hidden_id");            
            $file_name=$this->report_pdf_audit($id);
            $download_link=base_url().$file_name;
            echo '<div class="box-body chart-responsive minus"><div class="col-xs-12"><div class="alert text-center" style="font-size:18px">'.$this->lang->line("pdf report has been generated"). '<br/> <br/><a href="'.$download_link.'" target="_BLANK" style="font-size:20px"> <i class="fa fa-cloud-download"></i> '.$this->lang->line("click here to download").'</a></div></div></div>';
                 
        }
    }
        public function direct_download_responsive()
    {
        if($_POST)
        {
            
            $id=$this->input->post("hidden_id");            
            $file_name=$this->report_pdf_responsive($id);
            $download_link=base_url().$file_name;
            echo '<div class="box-body chart-responsive minus"><div class="col-xs-12"><div class="alert text-center" style="font-size:18px">'.$this->lang->line("pdf report has been generated"). '<br/> <br/><a href="'.$download_link.'" target="_BLANK" style="font-size:20px"> <i class="fa fa-cloud-download"></i> '.$this->lang->line("click here to download").'</a></div></div></div>';
                 
        }
    }
    public function comparison_report($id=0)
    {
       
       if($id==0) exit();

       $where['where'] = array('comparision.id'=>$id);
       $select=array("comparision.base_site","comparision.competutor_site","comparision.searched_at","comparision.id as id");
       $data["comparision_info"] = $this->basic->get_data("comparision",$where,$select);
       if(!isset($data["comparision_info"][0])) exit();    

       $where['where'] = array('id'=>$data["comparision_info"][0]["base_site"]);
       $data["site_info"] = $this->basic->get_data("site_check_report",$where);
       if(!isset($data["site_info"][0])) exit();

       $where['where'] = array('id'=>$data["comparision_info"][0]["competutor_site"]);
       $data["site_info2"] = $this->basic->get_data("site_check_report",$where);
       if(!isset($data["site_info2"][0])) exit();
  

       $data["comparision_info"][0]["base_domain"]=$data["site_info"][0]["domain_name"];
       $data["comparision_info"][0]["competutor_domain"]=$data["site_info2"][0]["domain_name"];
       $page_title= strtolower($data["comparision_info"][0]["base_domain"])." Vs ".strtolower($data["comparision_info"][0]["competutor_domain"]);

       $page_title=str_replace(array("www.","http://","https://"), "", $page_title);
       $data["page_title"]=$page_title;       
       $data['seo_meta_description']="web site healthy check report of ".$page_title." by ".$this->config->item("product_short_name");
       $this->load->helper('cookie');
       $data['username'] = get_cookie('name');
       $data["body"]="site/comparison_report";
       $this->load->library("site_check");
       $this->config->load('recommendation_config');
       $this->_site_viewcontroller($data);
    }



    public function comparision_report_pdf($id=0)
    {
       
       if($id==0) exit();
     
       $where['where'] = array('comparision.id'=>$id);
       $select=array("comparision.base_site","comparision.competutor_site","comparision.searched_at","comparision.id as id");
       $data["comparision_info"] = $this->basic->get_data("comparision",$where,$select);
       if(!isset($data["comparision_info"][0])) exit();
       
       $where['where'] = array('id'=>$data["comparision_info"][0]["base_site"]);
       $data["site_info"] = $this->basic->get_data("site_check_report",$where);
       if(!isset($data["site_info"][0])) exit();

       $where['where'] = array('id'=>$data["comparision_info"][0]["competutor_site"]);
       $data["site_info2"] = $this->basic->get_data("site_check_report",$where);
       if(!isset($data["site_info2"][0])) exit();

       $data["comparision_info"][0]["base_domain"]=$data["site_info"][0]["domain_name"];
       $data["comparision_info"][0]["competutor_domain"]=$data["site_info2"][0]["domain_name"];
       $page_title= strtolower($data["comparision_info"][0]["base_domain"])." Vs ".strtolower($data["comparision_info"][0]["competutor_domain"]);

       $page_title=str_replace(array("www.","http://","https://"), "", $page_title);
       $data["page_title"]=$page_title;       
    
       $this->load->library("site_check");
       $data["load_css_js"]=1;
       $this->config->load('recommendation_config');
       
       ob_start();
       $this->load->view("site/comparison_report",$data); 
       ob_get_contents();
       $html=ob_get_clean();   
       include("mpdf/mpdf.php");
       $mpdf=new mpdf('utf-8','Letter','','arialms');
       $mpdf->addPage();
       $mpdf->SetDisplayMode('fullpage');
       $mpdf->writeHTML($html);       
       $domain=str_replace(array("/"," "),"", $data["page_title"]);
       $domain=trim($domain);
       $download_id=$this->session->userdata('download_id_front').$this->_random_number_generator(10);
       $file_name="download/comparision_report_".$domain."_".$download_id.".pdf";
       $mpdf->output($file_name, 'F');
       return $file_name;
          
    }



    public function send_download_link_comparision()
    {
        if($_POST)
        {
            $lead_config=$this->basic->get_data("lead_config");
            if(is_array($lead_config) && isset($lead_config[0]))
            {
              $allowed_download_per_email=$lead_config[0]["allowed_download_per_email"];
              $unlimited_download_emails=$lead_config[0]["unlimited_download_emails"];
            }
            else
            {
              $allowed_download_per_email=10;  
              $unlimited_download_emails="";
            }
            
            $unlimited_download_emails=explode(',',$unlimited_download_emails);
            
            $email=$this->input->post("email");
            $name=$this->input->post("name");
            $id=$this->input->post("hidden_id");

           
            $data=array("firstname"=>$name,"email"=>$email);

            $where['where'] = array('comparision.id'=>$id);
            $join=array('site_check_report as base_site_table'=>"base_site_table.id=comparision.base_site,left",'site_check_report as competutor_site_table'=>"competutor_site_table.id=comparision.competutor_site,left");
            $select=array("base_site_table.domain_name as base_domain","competutor_site_table.domain_name as competutor_domain","comparision.base_site","comparision.competutor_site","comparision.searched_at","comparision.id as id");
            $comparision_info = $this->basic->get_data("comparision",$where,$select,$join);

            $domain="";
            if(isset($comparision_info[0]))
            {
              $domain= strtolower($comparision_info[0]["base_domain"]).", ".strtolower($comparision_info[0]["competutor_domain"]);
              $domain=str_replace(array("www.","http://","https://"), "", $domain);
            }             
            
            if($this->basic->is_exist($table="leads",$where=array("email"=>$email,"no_of_search >="=>$allowed_download_per_email),$select="id"))
            {
              if(!in_array($email,$unlimited_download_emails))
              $ret_val= "0"; // crossed limit
              else $ret_val="1"; 
            }
            else $ret_val="1";
            
            if($ret_val=="1")
            {               
                if($this->basic->is_exist($table="leads",$where=array("email"=>$email),$select="id"))
                {
                    $this->basic->execute_complex_query("UPDATE leads SET name='".$name."',no_of_search=no_of_search+1,domain=trim(both ',' from concat(domain, ', ".$domain."')),date_time='".date("Y-d-m G:i:s")."' WHERE email='".$email."'");
                    $ret_val= "2"; // updated               
                }
                else 
                {
                    $this->basic->insert_data("leads",array("name"=>$name,"domain"=>$domain,"email"=>$email,"date_time"=>date("Y-d-m G:i:s")));            
                    $this->load->library("google");
                    $this->google->syncMailchimp($data);
                    $ret_val= "3"; // inserted
                }

                $this->basic->execute_complex_query("UPDATE comparision SET email=trim(both ',' from concat(email, ', ".$email."')) WHERE id='".$id."'");
       
                $file_name=$this->comparision_report_pdf($id);
                $product=$this->config->item('product_name');
                $subject=$product." | "."Health Comparison Report : ".str_replace(", "," Vs ", $domain);
                $download_link="<a href='".base_url().$file_name."'> health comparison report of ".str_replace(", "," Vs ", $domain)."</a>";
                $message="Hello {$name}, <br/> Thank you for using {$product}.<br/> Please follow the link to download report: {$download_link}<br/><br/><br/>{$product} Team";

                $this->_mail_sender($from = '', $to = $email, $subject, $message, $mask = $product, $html = 1);
            }          

            echo $ret_val;
            
        }
    }



    public function direct_download_comparision()
    {
        if($_POST)
        {            
            $id=$this->input->post("hidden_id");
            $file_name=$this->comparision_report_pdf($id);
            $download_link=base_url().$file_name;
            echo '<div class="box-body chart-responsive minus"><div class="col-xs-12"><div class="alert text-center" style="font-size:18px">'.$this->lang->line("pdf report has been generated"). '<br/> <br/><a href="'.$download_link.'" target="_BLANK" style="font-size:20px"> <i class="fa fa-cloud-download"></i> '.$this->lang->line("click here to download").'</a></div></div></div>';
           
        }
    }


    public function have_site_users()
    {
        if($_POST)
        {            
        $resname=trim($this->input->post('resname', true));
        $resemail=trim($this->input->post('resemail', true));
        $chk=$this->checkuser($resemail);
        if($chk != 0){
        //New user
           $data = array(
                'name'=>$resname,
                'first_name'=>$resname,
                'email'=>$resemail,
                'password'=>md5('password'),
                'type'=>'Member',
                'user_type'=>'Admin',
                'active'=>1,
                'user_from'=>'haveweb',
                'status'=>1,
                'deleted'=>'0',
                'email_notification'=>'on',
                'profile_icon'=>'dude.png'
                );

           $this->db->insert('users',$data); 
           
           $id=$this->input->post("hidden_id");   
           $reporttype=$this->input->post("report"); 
           //Send mail registraton details
              $from = $this->config->item('institute_email');
              $project_url = $this->config->item('project_url');
              $subject = 'Registration with SmallbusinessDIY';
              $message = 'Hi, '.$resname.'<br/>Your Registered Email: ' .$resemail. '<br/>Your Temporary password: password<br/>Please signin and get more reports <a href="'.$project_url.'">Sign In</a>';
              $reg_status=$this->_mail_sender($from, $to = $resemail, $subject, $message, $mask = $from,$html=1);
              $this->session->set_userdata('mail_sent', 1);
           //response   
              if($reg_status){
                 if($reporttype=='responsive'){         
                   $this->report_pdf_responsive($id,$domain='',$to=$resemail);
                 }
                 else
                 {
                   $this->report_pdf_audit($id,$domain='',$to=$resemail);
                 }
                 echo "1";
            }
       //end custom     
        }
      else //Existing user
      {
           $id=$this->input->post("hidden_id");            
           $reporttype=$this->input->post("report"); 
           if($reporttype=='responsive'){         
           $this->report_pdf_responsive($id,$domain='',$to=$resemail);
           }
           else
           {
              $this->report_pdf_audit($id,$domain='',$to=$resemail);
           }
        echo "2";
      }
    }
}
  public function checkuser($email) {
   $query = $this->db->get_where('users', array('email' => $email));
   //print_r($query->num_rows ()); exit;
   if ($query->num_rows () > 0) {
      return "0"; //yes
    } else {
      return "1"; //no
    }
  }
 

  
}
