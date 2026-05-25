<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Time_zone extends Home_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!is_admin()) {
            redirect(base_url());
        }
    }


    public function index()
    {

        $data = array();
        $data['page_title'] = 'Time Zone';
        $data['page'] = 'Settings';
        $data['time_zone'] = FALSE;
        $data['time_zones'] = $this->admin_model->select_asc('time_zone');
        $data['main_content'] = $this->load->view('admin/time_zone',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function add()
    {   
        check_status();
        
        if($_POST)
        {   
            $id = $this->input->post('id', true);
    
            $data=array(
                'name' => $this->input->post('name', true),
            );
            $data = $this->security->xss_clean($data);

            if ($id != '') {
                $this->admin_model->edit_option($data, $id, 'time_zone');
                $this->session->set_flashdata('msg', trans('updated-successfully')); 
            } else {
                $id = $this->admin_model->insert($data, 'time_zone');
                $this->session->set_flashdata('msg', trans('inserted-successfully')); 
            }
            redirect(base_url('admin/time_zone'));

        }      
        
    }

    public function edit($id)
    {  
        $data = array();
        $data['page_title'] = 'Edit'; 
        $data['page'] = 'Settings';
        $data['time_zone'] = $this->admin_model->get_by_id($id, 'time_zone');
        $data['main_content'] = $this->load->view('admin/time_zone',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function delete($id)
    {
        $this->admin_model->delete($id,'time_zone'); 
        echo json_encode(array('st' => 1));
    }


}
    

