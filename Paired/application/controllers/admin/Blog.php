<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Blog extends Home_Controller {

    public function __construct()
    {
        parent::__construct();
        //check auth
        if (!is_user() && !is_admin()) {
            redirect(base_url());
        }
    }

    public function index()
    {
        $data = array();
        $data['page_title'] = 'Blog';
        $data['page'] = 'Blog';    
        $data['blog'] = FALSE;

        if (is_admin()){
            $uid = 0;
        }else{
            $uid = 1;
        }
 
        $data['blogs'] = $this->admin_model->get_admin_blogs($uid,$this->session->userdata('id'));
        $data['main_content'] = $this->load->view('admin/user/blog',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function add()
    {   
        check_status();
        
        if($_POST)
        {   
            $id = $this->input->post('id', true);
            if (is_admin()) {$uid = 0;} else {$uid = 1;}
            $data=array(
                'lang_id' => 1,
                'business_id' =>$uid,
                'user_id' => user()->id,
                'category_id' => $this->input->post('category_id',true),
                'title' => $this->input->post('title',true),
                'slug' => str_slug($this->input->post('slug',true)),
                'details' => html_purify($this->input->post('details', true), 'enable'),
                'tags' => $this->input->post('tags',true),
                'meta_tags' => $this->input->post('meta_tags',true),
                'meta_desc' => $this->input->post('meta_desc',true),
                'total_views' => $this->input->post('total_views',true),
                'created_at' =>date('Y-m-d'),
                'status' => $this->input->post('status',true),
            );
            $data = $this->security->xss_clean($data);
            if (!empty($id)) {
                $this->admin_model->edit_option($data, $id, 'blogs');
                $this->session->set_flashdata('msg', trans('updated-successfully')); 
            } else {
                $id = $this->admin_model->insert($data, 'blogs');
                $this->session->set_flashdata('msg', trans('inserted-successfully')); 
            }

            if($_FILES['photo']['name'] != ''){
                $up_load = $this->admin_model->upload_image('1200');
                $data_img = array(
                    'image' => $up_load['images'],
                    'thumb' => $up_load['thumb']
                );
                $data_img = $this->security->xss_clean($data_img);
                $this->admin_model->edit_option($data_img, $id, 'blogs');
            }
            
            redirect(base_url('admin/blog'));

        }      
        
    }

    public function edit($id)
    {  
        $data = array();
        $data['page_title'] = 'Edit';  
        $data['page'] = 'Blog';
        $data['blog'] = $this->admin_model->get_by_id($id, 'blogs');
        $data['main_content'] = $this->load->view('admin/user/blog',$data,TRUE);
        $this->load->view('admin/index',$data);
    }
    
    public function active($id) 
    {
        $data = array(
            'status' => 1
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id,'blogs');
        $this->session->set_flashdata('msg', trans('activate-successfully')); 
        redirect(base_url('admin/blog'));
    }

    public function deactive($id) 
    {
        $data = array(
            'status' => 0
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id,'blogs');
        $this->session->set_flashdata('msg', trans('deactivate-successfully')); 
        redirect(base_url('admin/blog'));
    }

    public function delete($id)
    {
        $this->admin_model->delete($id,'blogs'); 
        echo json_encode(array('st' => 1));
    }

    

}
    

