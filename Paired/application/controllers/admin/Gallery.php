<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gallery extends Home_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!is_user()) {
            redirect(base_url());
        }
    }

    public function index()
    {
        $data = array();
        $data['page_title'] = 'Gallery';  
        $data['page'] = 'Gallery';    
        $data['gallery'] = FALSE; 
        $data['galleries'] = $this->admin_model->select_by_company('galleries');
        $data['main_content'] = $this->load->view('admin/user/gallery',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function add()
    {   
        check_status();
        
        if($_POST)
        {   
            $id = $this->input->post('id', true);
            $data=array(
                'lang_id' => 1,
                'business_id' => $this->business->uid,
                'user_id' => user()->id,
                'title' => $this->input->post('title', true),
                'status' => $this->input->post('status', true),
            );
            $data = $this->security->xss_clean($data);


            if (!empty($id)) {
                $this->admin_model->edit_option($data, $id, 'galleries');
                $this->session->set_flashdata('msg', trans('updated-successfully')); 
            } else {
                $id = $this->admin_model->insert($data, 'galleries');
                $this->session->set_flashdata('msg', trans('inserted-successfully')); 
            }

            if($_FILES['photo']['name'] != ''){
                $up_load = $this->admin_model->upload_image('1200');
                $data_img = array(
                    'image' => $up_load['images'],
                    'thumb' => $up_load['thumb']
                );
                $data_img = $this->security->xss_clean($data_img);
                $this->admin_model->edit_option($data_img, $id, 'galleries');
            }
            
            redirect(base_url('admin/gallery'));

        }      
        
    }

    public function edit($id)
    {  
        $data = array();
        $data['page_title'] = 'Edit';  
        $data['page'] = 'Gallery';     
        $data['gallery'] = $this->admin_model->get_by_id($id, 'galleries');
        $data['main_content'] = $this->load->view('admin/user/gallery',$data,TRUE);
        $this->load->view('admin/index',$data);
    }
    
    public function active($id) 
    {
        $data = array(
            'status' => 1
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id,'galleries');
        $this->session->set_flashdata('msg', trans('activate-successfully')); 
        redirect(base_url('admin/gallery'));
    }

    public function deactive($id) 
    {
        $data = array(
            'status' => 0
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id,'galleries');
        $this->session->set_flashdata('msg', trans('deactivate-successfully')); 
        redirect(base_url('admin/gallery'));
    }

    public function delete($id)
    {
        $this->admin_model->delete($id,'galleries'); 
        echo json_encode(array('st' => 1));
    }

}
    

