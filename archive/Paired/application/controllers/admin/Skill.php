<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Skill extends Home_Controller {

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
        $data['page'] = 'Skill';
        $data['page_title'] = 'Skill';  
        $data['skill'] = FALSE;
        $data['categories'] = $this->admin_model->get_site_categories('categories');
        $data['skills'] = $this->admin_model->select_asc('skills');
        $data['main_content'] = $this->load->view('admin/skills',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function add()
    {   
        check_status();
        
        if($_POST)
        {   
            $id = $this->input->post('id', true);
            $data=array(
                'skill' => $this->input->post('skill', true),
                'category_id' => $this->input->post('category', true),
                'details' => $this->input->post('details', true),
                'status' => $this->input->post('status', true)
            );
            $data = $this->security->xss_clean($data);

            if ($id != '') {
                $this->admin_model->edit_option($data, $id, 'skills');
                $this->session->set_flashdata('msg', trans('updated-successfully')); 
            } else {
                $id = $this->admin_model->insert($data, 'skills');
                $this->session->set_flashdata('msg', trans('inserted-successfully')); 
            }
            redirect(base_url('admin/skill'));

        }      
        
    }

    public function edit($id)
    {  
        $data = array();
        $data['page'] = 'Skill';
        $data['page_title'] = 'Edit';
        $data['categories'] = $this->admin_model->get_admin_categories();   
        $data['skill'] = $this->admin_model->get_by_id($id, 'skills');
        $data['main_content'] = $this->load->view('admin/skills',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function delete($id)
    {
        $this->admin_model->delete($id,'skills'); 
        echo json_encode(array('st' => 1));
    }

}
    

