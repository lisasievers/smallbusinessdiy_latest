<?php


require_once("home.php"); // loading home controller

/**
* @category controller
* class Admin
*/

class admin extends Home
{

    public $user_id;    

    
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location');        
        if ($this->session->userdata('user_type') != 'Admin')
        redirect('home/login_page', 'location');
        
        $this->load->helper('form');
        $this->load->library('upload');
        $this->load->library('Web_common_report');
        $this->upload_path = realpath(APPPATH . '../upload');
        $this->user_id=$this->session->userdata('user_id');
        set_time_limit(0);

        $this->important_feature();
        $this->periodic_check();

    }


    public function index()
    {
        $this->user_management();
    }

 

    public function user_management()
    {
        $this->load->database();
        $this->load->library('grocery_CRUD');
        $crud = new grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('users');
        $crud->order_by('id');
        $crud->where('users.deleted', '0');
        $crud->set_subject($this->lang->line("user"));
        $crud->set_relation('package_id','package','package_name',array('package.deleted' => '0'));

        $crud->fields('name', 'email', 'mobile', 'password', 'address', 'user_type', 'status');

        $crud->edit_fields('name', 'email', 'mobile', 'address','expired_date','package_id', 'status');

        $crud->add_fields('name', 'email', 'mobile', 'password', 'address', 'user_type', 'status');

        $crud->required_fields('name', 'email', 'mobile', 'password', 'address', 'user_type','expired_date','package_id', 'status');

        $crud->columns('name', 'email', 'mobile', 'address','expired_date','package_id', 'status', 'user_type');

        $crud->field_type('password', 'password');
        $crud->field_type('expired_date', 'date');

        $crud->display_as('name', $this->lang->line('name'));
        $crud->display_as('email', $this->lang->line('email'));
        $crud->display_as('mobile', $this->lang->line('mobile'));
        $crud->display_as('address', $this->lang->line('address'));
        $crud->display_as('status', $this->lang->line('status'));
        $crud->display_as('user_type', $this->lang->line('user type'));
        $crud->display_as('password', $this->lang->line('password'));
        $crud->display_as('package_id', $this->lang->line('package name'));
        $crud->display_as('expired_date', $this->lang->line('expired date')." : yyyy-mm-dd ");
        $crud->unset_texteditor('address');

       
        $crud->set_rules("email",$this->lang->line("email"),'valid_email|callback_unique_email_check['.$this->uri->segment(4).']');


        $images_url = base_url("plugins/grocery_crud/themes/flexigrid/css/images/password.png");
        $crud->add_action('Change User Password', $images_url, 'admin/change_user_password');

        $crud->callback_column('expired_date', array($this, 'expired_date_display_crud'));
        $crud->callback_field('expired_date', array($this, 'expired_date_field_crud'));

        $crud->callback_column('status', array($this, 'status_display_crud'));
        $crud->callback_field('status', array($this, 'status_field_crud'));

        $crud->callback_after_insert(array($this, 'encript_password'));

         $crud->callback_column('user_type', array($this, 'user_type_display_crud'));
       
        
        $crud->unset_read();
        $crud->unset_print();
        $crud->unset_export();

        $output = $crud->render();
        $data['output']=$output;
        $data['page_title'] = $this->lang->line("user management");
        $data['crud']=1;

        $this->_viewcontroller($data);
    }



    public function change_user_password_action()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }
        $id = $this->session->userdata('change_user_password_id');
        // $this->session->unset_userdata('change_member_password_id');
        if ($_POST) {
            $this->form_validation->set_rules('password', '<b>'. $this->lang->line("password").'</b>', 'trim|required');
            $this->form_validation->set_rules('confirm_password', '<b>'. $this->lang->line("confirm password").'</b>', 'trim|required|matches[password]');
        }
        if ($this->form_validation->run() == false) {
            $this->change_user_password($id);
        } else {
            $new_password = $this->input->post('password', true);
            $new_confirm_password = $this->input->post('confirm_password', true);

            $table_change_password = 'users';
            $where_change_passwor = array('id' => $id);
            $data = array('password' => md5($new_password));
            $this->basic->update_data($table_change_password, $where_change_passwor, $data);

            $where['where'] = array('id' => $id);
            $mail_info = $this->basic->get_data('users', $where);
            
            $name = $mail_info[0]['name'];
            $to = $mail_info[0]['email'];
            $password = $new_password;

            $subject = 'Change Password Notification';
            $mask = $this->config->item('product_name');
            $from = $this->config->item('institute_email');
            $url = site_url();

            $message = "Dear {$name},<br/> Your <a href='".$url."'>{$mask}</a> password has been changed. Your new password is: {$password}.<br/><br/> Thank you.";
            $this->_mail_sender($from, $to, $subject, $message, $mask);
            $this->session->set_flashdata('success_message', 1);
                // return $this->config_member();
            redirect('admin/user_management', 'location');
        }
    }



    function unique_email_check($str, $edited_id)
    {
        $email= strip_tags(trim($this->input->post('email',TRUE)));
        if($email==""){
            $s= $this->lang->line("required");
            $s=str_replace("<b>%s</b>","",$s);
            $s="<b>".$this->lang->line("email")."</b> ".$s;
            $this->form_validation->set_message('unique_email_check', $s);
            return FALSE;
        }
        
        if(!isset($edited_id) || !$edited_id)
            $where=array("email"=>$email);
        else        
            $where=array("email"=>$email,"id !="=>$edited_id);
        
        
        $is_unique=$this->basic->is_unique("users",$where,$select='');
        
        if (!$is_unique) {
            $s = $this->lang->line("is_unique");
            $s=str_replace("<b>%s</b>","",$s);
            $s="<b>".$this->lang->line("email")."</b> ".$s;
            $this->form_validation->set_message('unique_email_check', $s);
            return FALSE;
            }
                
        return TRUE;
    }

 

    public function status_field_crud($value, $row)
    {
        if ($value == '') {
            $value = 1;
        }
        return form_dropdown('status', array(0 => $this->lang->line('inactive'), 1 => $this->lang->line('active')), $value, 'class="form-control" id="field-status"');
    }


    public function status_display_crud($value, $row)
    {
        if ($value == 1) {
            return "<span class='label label-success'>".$this->lang->line('active')."</sapn>";
        } else {
            return "<span class='label label-warning'>".$this->lang->line('inactive')."</sapn>";
        }
    }


    public function expired_date_field_crud($value, $row)
    {
        if ($value == '0000-00-00 00:00:00') {
            $value = "";
        }
        else $value=date("Y-m-d",strtotime($value));
        return '<input id="field-expired_date" type="text" maxlength="100" value="'.$value.'" name="expired_date">';
    }

    public function expired_date_display_crud($value, $row)
    {
        if($row->user_type=="Admin") return "N/A";
        if ($value == '0000-00-00 00:00:00') {
            $value = "undefined";
        }
        else $value=date("Y-m-d",strtotime($value));
        return $value;
    }

    public function user_type_display_crud($value, $row)
    {
       $str=$value." user";
       return $this->lang->line($str);        
    }


    public function encript_password($post_array, $primary_key)
    {
        $id = $primary_key;
        $where = array('id'=>$id);
        $password = md5($post_array['password']);
        $table = 'users';
        $data = array('password'=>$password);
        $this->basic->update_data($table, $where, $data);
        return true;
    }


 
    public function change_user_password($id)
    {
        $this->session->set_userdata('change_user_password_id', $id);

        $table = 'users';
        $where['where'] = array('id' => $id);

        $info = $this->basic->get_data($table, $where);

        $data['user_name'] = $info[0]['name'];

        $data['body'] = 'admin/user/change_user_password';
        $data['page_title'] =  $this->lang->line("change password");
        $this->_viewcontroller($data);
    }




    public function notify_members()
    {
        $data['body'] = 'admin/notification/notify_members';
        $data['page_title'] = $this->lang->line("send email to users");
        $this->_viewcontroller($data);
    }

    public function notify_members_data_loader()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 15;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'DESC';
        $order_by_str = $sort." ".$order;

        // setting properties for search
        $first_name = trim($this->input->post('first_name', true));

        $is_searched= $this->input->post('is_searched', true);


        if ($is_searched) {
            // if search occured, saving user input data to session. name of method is important before field
            $this->session->set_userdata('notify_member_first_name', $first_name);
        }
        // saving session data to different search parameter variables
        $search_first_name = $this->session->userdata('notify_member_first_name');

        // creating a blank where_simple array
        $where_simple = array();

        // trimming data
        if ($search_first_name) {
            $where_simple['name like'] = $search_first_name."%";
        }

        $where_simple['deleted'] = '0';
        // $where_simple['user_type !='] = 'Admin';

        $where = array('where' => $where_simple);
        $offset = ($page-1)*$rows;
        $result = array();

        $table = "users";
        $info = $this->basic->get_data($table, $where, $select = '', $join='', $limit = $rows, $start = $offset, $order_by = $order_by_str);

        $total_rows_array = $this->basic->count_row($table, $where, $count = "id");
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($info, $total_result);
    }

    public function send_email_member()
    {   
        
        if($_POST)
        {
            $subject= $this->input->post('subject');
            $content= $this->input->post('content');
            $info=$this->input->post('info');
            $info=json_decode($info,TRUE);
            $count=0;
            
           foreach($info as $member)
            {               
                $email=$member['email'];
                $member_id=$member['id'];                
                $message=$content;
                $from=$this->config->item('institute_email');
                $to=$email;
                $mask=$this->config->item('institute_address1');
                
                if($message=="" || $from=="" || $to=="" || $subject=="") continue;

                if($this->_mail_sender($from,$to,$subject,$message,$mask))  $count++;
               
            }
            echo "<b> $count / ".count($info)." : ".$this->lang->line("email sent successfully")."</b>";
           
        }   
    }


    public function scanAll($myDir)
    {

        $dirTree = array();
        $di = new RecursiveDirectoryIterator($myDir,RecursiveDirectoryIterator::SKIP_DOTS);

        $i=0;
        foreach (new RecursiveIteratorIterator($di) as $filename) {

            $dir = str_replace($myDir, '', dirname($filename));
            $dir = str_replace('/', '>', substr($dir,1));

            $org_dir=str_replace("\\", "/", $dir);

            if($org_dir)
                $file_path = $org_dir. "/". basename($filename);
            else
                $file_path = basename($filename);

            $file_full_path=$myDir."/".$file_path;
            $file_size= filesize($file_full_path);
            $file_modification_time=filemtime($file_full_path);

            $dirTree[$i]['file'] = $file_full_path;
            $dirTree[$i]['size'] = $file_size;
            $dirTree[$i]['time'] =date("Y-m-d H:i:s",$file_modification_time);

            $i++;

        }

        return $dirTree;
    }


    public function delete_junk_file()
    {
        $path=FCPATH."download";
        $dirTree=$this->scanAll($path);

        $number_of_deleted_files = 0;
        $deleted_file_size = 0;
        $todate = date("Y-m-d");
        $previous_day = date("Y-m-d", strtotime("$todate - 1 Days"));
        $last_time = strtotime($previous_day);
        foreach ($dirTree as $value) {
            $junk_file_created_time = strtotime($value['time']);
            if($junk_file_created_time <= $last_time && $value['file']!='index.html'){
                $number_of_deleted_files++;
                $deleted_file_size = $deleted_file_size+$value['size'];
            }
        }
        if($deleted_file_size != 0) $data['total_file_size'] = number_format($deleted_file_size/1024, 2);
        else $data['total_file_size'] = $deleted_file_size;

        $data['total_files'] = $number_of_deleted_files;
        $data['body'] = "admin/junk_file_delete/delete_junk_file";
        $data['page_title'] = $this->lang->line("delete junk files");
        $this->_viewcontroller($data);      
        

   }

   public function delete_junk_file_action()
   {
       $path=FCPATH."download";
       $dirTree=$this->scanAll($path);
       $number_of_deleted_files = 0;
       $deleted_file_size = 0;
       $todate = date("Y-m-d");
       $previous_day = date("Y-m-d", strtotime("$todate - 1 Days"));
       $last_time = strtotime($previous_day);
       foreach ($dirTree as $value) {
            $junk_file_created_time = strtotime($value['time']);
           if($junk_file_created_time <= $last_time  && $value['file']!='index.html'){
                unlink($value['file']);
                $number_of_deleted_files++;
                $deleted_file_size = $deleted_file_size+$value['size'];
           }
       }
       if($deleted_file_size != 0) $deleted_file_size = number_format($deleted_file_size/1024, 2);
       else $deleted_file_size = $deleted_file_size;
       echo "<p class='text-danger'>You have successfully deleted $number_of_deleted_files junk files ($deleted_file_size KB)</p>";
   }

   

}
