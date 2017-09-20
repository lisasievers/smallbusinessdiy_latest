<?php 
require_once("home.php"); // loading home controller

class Code_minifier extends Home
{

    public $user_id;    
    
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location');       
 
        $this->user_id=$this->session->userdata('user_id');
        set_time_limit(0);

        $this->important_feature();
        $this->member_validity();
        $this->load->library('code_minifier_library');
        if($this->session->userdata('user_type') != 'Admin' && !in_array(17,$this->module_access))
        redirect('home/login_page', 'location'); 
    }


    public function index()
    {
    	$a = file_get_contents('application/controllers/style.css');
    	echo $this->code_minifier_library->minify_css($a);
    }

    public function html_minifier()
    {
        $data['body'] = 'admin/code_minifier/html_minifier';
        $data['page_title'] = $this->lang->line('HTML Minifier');
        $this->_viewcontroller($data);
    }


    public function html_minifier_textarea()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        $code = $this->input->post('code');
        echo $this->code_minifier_library->minify_html($code);
    }


    public function html_minifier_action()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        $output_dir = FCPATH."upload/html/";
        if (isset($_FILES["myfile"])) {

            $ret = array();
            $error =$_FILES["myfile"]["error"];
            //You need to handle  both cases
            //If Any browser does not support serializing of multiple files using FormData() 
            if(!is_array($_FILES["myfile"]["name"])) //single file
            {
                $fileName = $_FILES["myfile"]["name"];
                move_uploaded_file($_FILES["myfile"]["tmp_name"],$output_dir.$fileName);

                $file_content=file_get_contents("upload/html/".$fileName);
                $file_content_new=$this->code_minifier_library->minify_html($file_content);
                file_put_contents("upload/html/".$fileName, $file_content_new, LOCK_EX);


                $ret[]= $fileName;
            }
            else  //Multiple files, file[]
            {
              $fileCount = count($_FILES["myfile"]["name"]);
              for($i=0; $i < $fileCount; $i++)
              {
                $fileName = $_FILES["myfile"]["name"][$i];
                move_uploaded_file($_FILES["myfile"]["tmp_name"][$i],$output_dir.$fileName);

                $file_content=file_get_contents("upload/html/".$fileName);
                $file_content_new=$this->code_minifier_library->minify_html($file_content);
                file_put_contents("upload/html/".$fileName, $file_content_new, LOCK_EX);

                $ret[]= $fileName;
              }
            
            }
            echo json_encode($ret);

        }
    }

    public function delete_html()
    {
        $output_dir = FCPATH."upload/html/";
        if(isset($_POST["op"]) && $_POST["op"] == "delete" && isset($_POST['name']))
        {
            $fileName =$_POST['name'];
            $fileName=str_replace("..",".",$fileName); //required. if somebody is trying parent folder files    
            $filePath = $output_dir. $fileName;
            if (file_exists($filePath)) 
            {
                unlink($filePath);
            }
            echo "Deleted File ".$fileName."<br>";
        }
    }


    public function download_html($fileName='')
    {
        $fileName = urldecode($fileName);
        if($fileName!='')
        {
            $fileName=str_replace("..",".",$fileName); //required. if somebody is trying parent folder files
            $file = FCPATH."upload/html/".$fileName;
            $file = str_replace("..","",$file);
            if (file_exists($file)) {
                $fileName =str_replace(" ","",$fileName);
                header('Content-Description: File Transfer');
                header('Content-Disposition: attachment; filename='.$fileName);
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                // ob_clean();
                // flush();
                readfile($file);
                unlink($file);
                exit;
            }
        }
    }


    public function css_minifier()
    {
        $data['body'] = 'admin/code_minifier/css_minifier';
        $data['page_title'] = $this->lang->line('CSS code minifier');
        $this->_viewcontroller($data);
    }


    public function css_minifier_textarea()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        $code = $this->input->post('code');
        echo $this->code_minifier_library->minify_css($code);
    }


    public function css_minifier_action()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        $output_dir = FCPATH."upload/css/";
        if (isset($_FILES["myfile"])) {

            $ret = array();
            $error =$_FILES["myfile"]["error"];
            //You need to handle  both cases
            //If Any browser does not support serializing of multiple files using FormData() 
            if(!is_array($_FILES["myfile"]["name"])) //single file
            {
                $fileName = $_FILES["myfile"]["name"];
                move_uploaded_file($_FILES["myfile"]["tmp_name"],$output_dir.$fileName);

                $file_content=file_get_contents("upload/css/".$fileName);
                $file_content_new=$this->code_minifier_library->minify_css($file_content);
                file_put_contents("upload/css/".$fileName, $file_content_new, LOCK_EX);


                $ret[]= $fileName;
            }
            else  //Multiple files, file[]
            {
              $fileCount = count($_FILES["myfile"]["name"]);
              for($i=0; $i < $fileCount; $i++)
              {
                $fileName = $_FILES["myfile"]["name"][$i];
                move_uploaded_file($_FILES["myfile"]["tmp_name"][$i],$output_dir.$fileName);

                $file_content=file_get_contents("upload/css/".$fileName);
                $file_content_new=$this->code_minifier_library->minify_css($file_content);
                file_put_contents("upload/css/".$fileName, $file_content_new, LOCK_EX);

                $ret[]= $fileName;
              }
            
            }
            echo json_encode($ret);

        }
    }

    public function delete_css()
    {
        $output_dir = FCPATH."upload/css/";
        if(isset($_POST["op"]) && $_POST["op"] == "delete" && isset($_POST['name']))
        {
            $fileName =$_POST['name'];
            $fileName=str_replace("..",".",$fileName); //required. if somebody is trying parent folder files    
            $filePath = $output_dir. $fileName;
            if (file_exists($filePath)) 
            {
                unlink($filePath);
            }
            echo "Deleted File ".$fileName."<br>";
        }
    }


    public function download_css($fileName='')
    {
        $fileName = urldecode($fileName);
        if($fileName!='')
        {
            $fileName=str_replace("..",".",$fileName); //required. if somebody is trying parent folder files
            $file = FCPATH."upload/css/".$fileName;
            $file = str_replace("..","",$file);
            if (file_exists($file)) {
                $fileName =str_replace(" ","",$fileName);
                header('Content-Description: File Transfer');
                header('Content-Disposition: attachment; filename='.$fileName);
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                // ob_clean();
                // flush();
                readfile($file);
                unlink($file);
                exit;
            }
        }
    }


    
    public function js_minifier()
    {
        $data['body'] = 'admin/code_minifier/js_minifier';
        $data['page_title'] = $this->lang->line('Js code minifier');
        $this->_viewcontroller($data);
    }


    public function js_minifier_textarea()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        $code = $this->input->post('code');
        echo $this->code_minifier_library->minify_js($code);
    }


    public function js_minifier_action()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        $output_dir = FCPATH."upload/js/";
        if (isset($_FILES["myfile"])) {

            $ret = array();
            $error =$_FILES["myfile"]["error"];
            //You need to handle  both cases
            //If Any browser does not support serializing of multiple files using FormData() 
            if(!is_array($_FILES["myfile"]["name"])) //single file
            {
                $fileName = $_FILES["myfile"]["name"];
                move_uploaded_file($_FILES["myfile"]["tmp_name"],$output_dir.$fileName);

                $file_content=file_get_contents("upload/js/".$fileName);
                $file_content_new=$this->code_minifier_library->minify_js($file_content);
                file_put_contents("upload/js/".$fileName, $file_content_new, LOCK_EX);


                $ret[]= $fileName;
            }
            else  //Multiple files, file[]
            {
              $fileCount = count($_FILES["myfile"]["name"]);
              for($i=0; $i < $fileCount; $i++)
              {
                $fileName = $_FILES["myfile"]["name"][$i];
                move_uploaded_file($_FILES["myfile"]["tmp_name"][$i],$output_dir.$fileName);

                $file_content=file_get_contents("upload/js/".$fileName);
                $file_content_new=$this->code_minifier_library->minify_js($file_content);
                file_put_contents("upload/js/".$fileName, $file_content_new, LOCK_EX);

                $ret[]= $fileName;
              }
            
            }
            echo json_encode($ret);

        }
    }

    public function delete_js()
    {
        $output_dir = FCPATH."upload/js/";
        if(isset($_POST["op"]) && $_POST["op"] == "delete" && isset($_POST['name']))
        {
            $fileName =$_POST['name'];
            $fileName=str_replace("..",".",$fileName); //required. if somebody is trying parent folder files    
            $filePath = $output_dir. $fileName;
            if (file_exists($filePath)) 
            {
                unlink($filePath);
            }
            echo "Deleted File ".$fileName."<br>";
        }
    }


    public function download_js($fileName='')
    {
        $fileName = urldecode($fileName);
        if($fileName!='')
        {
            $fileName=str_replace("..",".",$fileName); //required. if somebody is trying parent folder files
            $file = FCPATH."upload/js/".$fileName;
            $file = str_replace("..","",$file);
            if (file_exists($file)) {
                $fileName =str_replace(" ","",$fileName);
                header('Content-Description: File Transfer');
                header('Content-Disposition: attachment; filename='.$fileName);
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                // ob_clean();
                // flush();
                readfile($file);
                unlink($file);
                exit;
            }
        }
    }



}