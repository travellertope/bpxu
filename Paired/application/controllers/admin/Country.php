<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Country extends Home_Controller {

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
        $data['page_title'] = 'Country';
        $data['page'] = 'Settings';
        $data['country'] = FALSE;
        $data['countries'] = $this->admin_model->select_asc('country');
        $data['main_content'] = $this->load->view('admin/countries',$data,TRUE);
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
                'code' => $this->input->post('country_code', true),
                'dial_code' => $this->input->post('dial_code', true),
                'currency_name' => $this->input->post('currency_name', true),
                'currency_symbol' => $this->input->post('currency_symbol', true),
                'currency_code' => $this->input->post('currency_code', true)
            );

            $data = $this->security->xss_clean($data);

            if ($id != '') {
                $this->admin_model->edit_option($data, $id, 'country');
                $this->session->set_flashdata('msg', trans('updated-successfully')); 
            } else {
                $id = $this->admin_model->insert($data, 'country');
                $this->session->set_flashdata('msg', trans('inserted-successfully')); 
            }
            redirect(base_url('admin/country'));

        }      
        
    }

    public function edit($id)
    {  
        $data = array();
        $data['page_title'] = 'Edit'; 
        $data['page'] = 'Settings';
        $data['country'] = $this->admin_model->get_by_id($id, 'country');
        $data['main_content'] = $this->load->view('admin/countries',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function delete($id)
    {
        $this->admin_model->delete($id,'country'); 
        echo json_encode(array('st' => 1));
    }


}
    

