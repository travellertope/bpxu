<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Font extends Home_Controller {

    public function __construct()
    {
        parent::__construct();
        //check auth
        if (!is_admin()) {
            redirect(base_url());
        }
    }


    public function index()
    {
        $data = array();
        $data['page_title'] = 'Font'; 
        $data['page'] = 'Settings';
        $data['font'] = FALSE;
        $data['fonts'] = $this->admin_model->get_site_fonts();
        $data['main_content'] = $this->load->view('admin/user/font',$data,TRUE);
        $this->load->view('admin/index',$data);
    }


    public function add()
    {	
        check_status();
        
        if($_POST)
        {   
            $id = $this->input->post('id', true);

                $data=array(
                    'user_id' => 0,
                    'name' => $this->input->post('name', true),
                    'slug' => str_slug($this->input->post('name', true)),
                );
                $data = $this->security->xss_clean($data);
                
                //if id available info will be edited
                if ($id != '') {
                    $this->admin_model->edit_option($data, $id, 'fonts');
                    $this->session->set_flashdata('msg', trans('updated-successfully')); 
                } else {
                    $id = $this->admin_model->insert($data, 'fonts');
                    $this->session->set_flashdata('msg', trans('inserted-successfully')); 
                }

                redirect(base_url('admin/font'));

           
        }      
        
    }

    public function edit($id)
    {  
        $data = array();
        $data['page_title'] = 'Edit';   
        $data['page'] = 'Settings';
        $data['font'] = $this->admin_model->select_option($id, 'fonts');
        $data['main_content'] = $this->load->view('admin/user/font',$data,TRUE);
        $this->load->view('admin/index',$data);
    }


    public function delete($id)
    {
        $this->admin_model->delete($id,'fonts'); 
        echo json_encode(array('st' => 1));
    }


}
	

