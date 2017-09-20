<?php

require_once("home.php"); // loading home controller

class expired_domain extends Home
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

        if($this->session->userdata('user_type') != 'Admin' && !in_array(5,$this->module_access))
        redirect('home/login_page', 'location'); 
    }
	
	
	public function index()
    {
        $this->expired_domain();
    }

    public function expired_domain()
    {
        $data['body'] = 'admin/expired/auction';
        $data['page_title'] = $this->lang->line("auction domain list");
        $this->_viewcontroller($data);
    }
    

    public function expired_domain_data()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        redirect('home/access_forbidden', 'location');
        

        $page = isset($_POST['page']) ? intval($_POST['page']) : 15;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'auction_end_date';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'ASC';

        $domain_name = trim($this->input->post("domain_name", true));


        // setting a new properties for $is_searched to set session if search occured
        $is_searched = $this->input->post('is_searched', true);


        if ($is_searched) 
        {
            // if search occured, saving user input data to session. name of method is important before field
            $this->session->set_userdata('expired_domain_domain_name', $domain_name);
        }

        // saving session data to different search parameter variables
        $search_domain_name   = $this->session->userdata('expired_domain_domain_name');

        // creating a blank where_simple array
        $where_simple=array();        
        if ($search_domain_name)    $where_simple['domain_name like ']    = "%".$search_domain_name."%";
  
        $where  = array('where'=>$where_simple);
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "expired_domain_list";
        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');
        $total_rows_array = $this->basic->count_row($table, $where, $count="id", $join='');
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($info, $total_result);
    }



    public function expired_domain_download()
    {
        $all=$this->input->post("all");
        $table = 'expired_domain_list';
        $where=array();
        if($all==0)
        {
            $selected_grid_data = $this->input->post('info', true);
            $json_decode = json_decode($selected_grid_data, true);
            $id_array = array();
            foreach ($json_decode as  $value) 
            {
                $id_array[] = $value['id'];
            }
            $where['where_in'] = array('id' => $id_array);
        }

        $info = $this->basic->get_data($table, $where, $select ='', $join='', $limit='', $start=null, $order_by='id asc');
        $download_id=$this->session->userdata('download_id');
        $fp = fopen("download/expired_domain/expired_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));

        $head=array("Domain", "Auction Type", "Auction End Date", "Sync At");
                    
        fputcsv($fp, $head);

        foreach ($info as  $value) 
        {
        	$write_info = array();
            $write_info[] = $value['domain_name'];
            $write_info[] = $value['auction_type'];
            $write_info[] = $value['auction_end_date'];
            $write_info[] = $value['sync_at'];
            // $write_info[] = $value['page_rank'];
            // $write_info[] = $value['google_index'];
            // $write_info[] = $value['yahoo_index'];
            // $write_info[] = $value['bing_index'];
            
            fputcsv($fp, $write_info);
        }

        fclose($fp);
        $file_name = "download/expired_domain/expired_{$this->user_id}_{$download_id}.csv";
        echo $file_name;
    }


    

  	


	
}
