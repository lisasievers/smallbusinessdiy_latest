<?php
require_once("home.php"); // loading home controller

class keyword_position_tracking extends Home
{

    public $user_id;    
    public $download_id;    
    
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location');       
        
        $this->load->library('Web_common_report');
        $this->user_id=$this->session->userdata('user_id');
        $this->download_id=$this->session->userdata('download_id');
        set_time_limit(0);

        $this->important_feature();

        $this->member_validity();

        if($this->session->userdata('user_type') != 'Admin' && !in_array(16,$this->module_access))
        redirect('home/login_page', 'location'); 
    }
	
	
	public function index()
    {
        $this->keyword_list();
    }



    public function keyword_tracking_settings()
    {
        $data['body'] = "admin/keyword/keyword_tracking_settings";
        $data['page_title'] = $this->lang->line("keyword tracking settings");
        $data['country_name'] = $this->get_country_names();
        $data['language_name'] = $this->get_language_names(); 
        $where['where'] = array('user_id'=>$this->user_id);
        $number_of_keyword = $this->basic->get_data("keyword_position_set",$where);
        $data['number_of_keyword'] = count($number_of_keyword);       
        $this->_viewcontroller($data);
    }


    public function keyword_tracking_settings_action()
    {
        //************************************************//
        $status=$this->_check_usage($module_id=16,$request=1);
        if($status=="2") 
        {
            // echo $this->lang->line("sorry, your bulk limit is exceeded for this module.")."<a href='".site_url('usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
            $this->session->set_userdata('limit_exceeded',2);
            return $this->keyword_tracking_settings();
        }
        else if($status=="3") 
        {
            // echo $this->lang->line("sorry, your monthly limit is exceeded for this module.")."<a href='".site_url('usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
            $this->session->set_userdata('limit_exceeded',3);
            return $this->keyword_tracking_settings();
        }
        //************************************************//

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }
        if($_POST) {
            // $this->form_validation->set_rules('name', '<b>'.$this->lang->line("name").'</b>', 'trim|required|xss_clean');
            $this->form_validation->set_rules('keyword', '<b>'.$this->lang->line("keyword").'</b>', 'trim|required|xss_clean');
            $this->form_validation->set_rules('website', '<b>'.$this->lang->line("website").'</b>', 'trim|xss_clean|required');
            $this->form_validation->set_rules('country', '<b>'.$this->lang->line("country").'</b>', 'trim|required|xss_clean');
            $this->form_validation->set_rules('language', '<b>'.$this->lang->line("language").'</b>', 'trim|required');

            if($this->form_validation->run() == FALSE)
            {
                return $this->keyword_tracking_settings();
            }
            else 
            {
                $user_id = $this->user_id;
                $keyword = $this->input->post('keyword');
                $website = $this->input->post('website');
                $country = $this->input->post('country');
                $language = $this->input->post('language');

                $data = array(
                    'keyword' => $keyword,
                    'website' => $website,
                    'language' => $language,
                    'country' => $country,
                    'user_id' => $user_id,
                    'add_date' => date("Y-m-d H:i:s"),
                    'deleted' => '0'
                    );
                if($this->basic->insert_data("keyword_position_set",$data))
                {
                    //******************************//
                    // insert data to useges log table
                    $this->_insert_usage_log($module_id=16,$request=1);   
                    //******************************//

                    $this->session->set_userdata("success_message",1);                   
                } else 
                {
                    $this->session->set_userdata("error_message",1);                   
                }
                redirect('keyword_position_tracking/index','location'); 
            }
        }
    }



    public function keyword_position_report()
    {
        $data['body'] = "admin/keyword/keyword_position_report";
        $data['page_title'] = $this->lang->line("keyword position report");
        $where['where'] = array('user_id' => $this->user_id);
        $keywords = $this->basic->get_data("keyword_position_set",$where);
        $keywords_array = array();
        foreach($keywords as $value){
            $keywords_array[$value['id']] = $value['keyword']." | ".$value['website'];
        }

        $data['keywords'] = $keywords_array;
        $this->_viewcontroller($data);
    }


    public function keyword_position_report_data()
    {
        $keyword = $this->input->post("keyword");
        $from_date = $this->input->post("from_date");
        $to_date = $this->input->post("to_date");

        $where['where'] = array(
            "keyword_id" => $keyword,
            "date >=" => date("Y-m-d",strtotime($from_date)),
            "date <=" => date("Y-m-d",strtotime($to_date))
            );
        $join = array(
            "keyword_position_set" => "keyword_position_report.keyword_id=keyword_position_set.id,left"
            );

        $keyword_position = $this->basic->get_data("keyword_position_report",$where,$select="",$join);
        // echo $this->db->last_query();

        $str = '<table class="table table-hover table-striped">
                    <tr>
                        <th>Date</th>
                        <th>Keyword</th>
                        <th>Website</th>
                        <th>Google Position</th>
                        <th>Bing Position</th>
                        <th>Yahoo Position</th>
                    </tr>';
        foreach($keyword_position as $value){
            $str .= '<tr>
                        <td>'.$value['date'].'</td>
                        <td>'.$value['keyword'].'</td>
                        <td>'.$value['website'].'</td>
                        <td>'.$value['google_position'].'</td>
                        <td>'.$value['bing_position'].'</td>
                        <td>'.$value['yahoo_position'].'</td>
                    </tr>';
        }

        $str .= '</table';

        echo $str;
    }


    public function keyword_list()
    {
        $data['body'] = "admin/keyword/keyword_list";
        $data['page_title'] = $this->lang->line("keyword tracking setting");
        $where['where'] = array('user_id'=>$this->user_id);
        $number_of_keyword = $this->basic->get_data("keyword_position_set",$where);
        $data['number_of_keyword'] = count($number_of_keyword);
        $this->_viewcontroller($data);
    }

    public function keyword_list_data()
    {
        $where_simple['deleted'] = "0";
        $where_simple['user_id'] = $this->user_id;
        $where = array('where' => $where_simple);

        $table = "keyword_position_set";
        $info = $this->basic->get_data($table, $where, $select = '', $join='');

        $total_rows_array = $this->basic->count_row($table, $where, $count = "id");
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($info, $total_result);
    }

    public function delete_keyword_action($id)
    {
        $where = array("id"=>$id);
        $data = array('deleted'=>'1');
        if($this->basic->update_data("keyword_position_set",$where,$data)){
            $this->session->set_userdata('delete_success_message',1);            
        }
        else{
            $this->session->set_userdata('delete_error_message',1);           
        }
        redirect('keyword_position_tracking/index','location');  
    }


	
}
?>