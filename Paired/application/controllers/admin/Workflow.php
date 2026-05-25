<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Workflow extends Home_Controller {

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
        $data['page_title'] = 'Workflow';      
        $data['page'] = 'workflow';
        $data['menu'] = TRUE; 
        $data['workflows'] = $this->admin_model->select('workflows');
        $data['main_content'] = $this->load->view('admin/workflow',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function add()
    {   
        check_status();
        
        if($_POST)
        {   
            $id = $this->input->post('id', true);
            $data=array(
                'title' => $this->input->post('title', true),
                'details' => $this->input->post('details', true),
                'status' => $this->input->post('status', true)
            );
            $data = $this->security->xss_clean($data);

            if (!empty($id)) {
                $this->admin_model->edit_option($data, $id, 'workflows');
                $this->session->set_flashdata('msg', trans('updated-successfully')); 
            } else {
                $id = $this->admin_model->insert($data, 'workflows');
                $this->session->set_flashdata('msg', trans('inserted-successfully')); 
            }

            if($_FILES['photo']['name'] != ''){
                $up_load = $this->admin_model->upload_image('1200');
                $data_img = array(
                    'image' => $up_load['images'],
                    'thumb' => $up_load['thumb']
                );
                $data_img = $this->security->xss_clean($data_img);
                $this->admin_model->edit_option($data_img, $id, 'workflows');
            }
            
            redirect(base_url('admin/workflow'));

        }      
        
    }

    public function edit($id)
    {  
        $data = array();
        $data['page_title'] = 'Edit';  
        $data['page'] = 'Workflow';     
        $data['workflow'] = $this->admin_model->get_by_id($id, 'workflows');
        $data['main_content'] = $this->load->view('admin/workflow',$data,TRUE);
        $this->load->view('admin/index',$data);
    }
    
    public function active($id) 
    {
        $data = array(
            'status' => 1
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id,'workflows');
        $this->session->set_flashdata('msg', trans('activate-successfully')); 
        redirect(base_url('admin/workflow'));
    }

    public function deactive($id) 
    {
        $data = array(
            'status' => 0
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id,'workflows');
        $this->session->set_flashdata('msg', trans('deactivate-successfully')); 
        redirect(base_url('admin/workflow'));
    }

    public function delete($id)
    {
        $this->admin_model->delete($id,'workflows'); 
        echo json_encode(array('st' => 1));
    }

}
	

