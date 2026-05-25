<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Category extends Home_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!is_admin() && !is_user()) {
            redirect(base_url());
        }
    }


    public function index()
    {

        $data = array();
        $data['page'] = 'Skill';
        $data['page_title'] = 'Category';
        $data['category'] = FALSE;
        $data['categories'] = $this->admin_model->get_admin_categories();
        $data['main_content'] = $this->load->view('admin/categories',$data,TRUE);
        $this->load->view('admin/index',$data);
    }



    public function add()
    {   
        check_status();
        
        if($_POST)
        {   
            $id = $this->input->post('id', true);
    
            $data=array(
                'icon' => $this->input->post('icon', true),
                'name' => $this->input->post('name', true),
                'slug' => str_slug(trim($this->input->post('name', true))),
                'details' => $this->input->post('details',true),
                'status' => $this->input->post('status',true)
            );
            $data = $this->security->xss_clean($data);

            if ($id != '') {
                $this->admin_model->edit_option($data, $id, 'categories');
                $this->session->set_flashdata('msg', trans('updated-successfully')); 
            } else {
                $id = $this->admin_model->insert($data, 'categories');
                $this->session->set_flashdata('msg', trans('inserted-successfully')); 
            }
            redirect(base_url('admin/category'));

        }      
        
    }
    

    public function edit($id)
    {  
        $data = array();
        $data['page'] = 'Skill';
        $data['page_title'] = 'Edit'; 
        $data['category'] = $this->admin_model->select_option($id, 'categories');
        $data['main_content'] = $this->load->view('admin/categories',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    
    public function active($id) 
    {
        $data = array(
            'status' => 1
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id,'categories');
        $this->session->set_flashdata('msg', trans('activate-successfully')); 
        redirect(base_url('admin/pages'));
    }

    public function deactive($id) 
    {
        $data = array(
            'status' => 0
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id,'categories');
        $this->session->set_flashdata('msg', trans('deactivate-successfully')); 
        redirect(base_url('admin/pages'));
    }

    public function delete($id)
    {
        $this->admin_model->delete($id,'categories'); 
        echo json_encode(array('st' => 1));
    }



    public function sub()
    {
        $data = array();
        $data['page_title'] = 'Subcategory';  
        $data['subcategory'] = FALSE;
        $data['categories'] = $this->admin_model->get_admin_categories('all', true);
        $data['subcategories'] = $this->admin_model->get_admin_subcategories('all', true);
        $data['main_content'] = $this->load->view('admin/subcategories',$data,TRUE);
        $this->load->view('admin/index',$data); 
    }


    public function sub_add()
    {   
        check_status();
        
        if($_POST)
        {   
            $id = $this->input->post('id', true);
            $data=array(
                'type' => 0,
                'name' => $this->input->post('name', true),
                'slug' => str_slug(trim($this->input->post('name', true))),
                'parent_id' =>$this->input->post('category_id', true),
                'details' => $this->input->post('details', true),
                'status' => $this->input->post('status', true)
            );
            $data = $this->security->xss_clean($data);

           
            if ($id != '') {
                $this->admin_model->edit_option($data, $id, 'categories');
                $this->session->set_flashdata('msg', trans('updated-successfully')); 
            } else {
                $id = $this->admin_model->insert($data, 'categories');
                $this->session->set_flashdata('msg', trans('inserted-successfully')); 
            }
            redirect(base_url('admin/category/sub'));

        }      
        
    }

    public function sub_edit($id)
    {  
        $data = array();
        $data['page_title'] = 'Edit';
        $data['categories'] = $this->admin_model->get_admin_categories('all', true);   
        $data['subcategory'] = $this->admin_model->select_option($id, 'categories');
        $data['main_content'] = $this->load->view('admin/subcategories',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    
    public function sub_active($id) 
    {
        $data = array(
            'status' => 1
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id,'categories');
        $this->session->set_flashdata('msg', trans('activate-successfully')); 
        redirect(base_url('admin/pages'));
    }

    public function sub_deactive($id) 
    {
        $data = array(
            'status' => 0
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id,'categories');
        $this->session->set_flashdata('msg', trans('deactivate-successfully')); 
        redirect(base_url('admin/pages'));
    }

    public function sub_delete($id)
    {
        $this->admin_model->delete($id,'categories'); 
        echo json_encode(array('st' => 1));
    }


}
    

