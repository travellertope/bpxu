<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pages extends Home_Controller {

    public function __construct()
    {
        parent::__construct();
        //check auth
        if (!is_admin() && !is_user()) {
            redirect(base_url());
        }
    }



    public function index()
    {
        $data = array();
        $data['page_title'] = 'Pages';  
        $data['page'] = FALSE;
        if (is_admin()) {
            $uid = 0; $type='admin';
        } else {
            $uid = $this->business->uid;
            $type='user';
        }
        $data['pages'] = $this->admin_model->get_pages($uid, $type, 'all');
        $data['main_content'] = $this->load->view('admin/pages',$data,TRUE);
        $this->load->view('admin/index',$data);
    }



    public function add()
    {	
        check_status();
        
        if($_POST)
        {   
            $id = $this->input->post('id', true);
            //validate inputs
            $this->form_validation->set_rules('title', 'Title', 'required');
            
            if ($this->form_validation->run() === false) {
                $this->session->set_flashdata('error', validation_errors());
                redirect(base_url('admin/pages'));
            } else {
                if (is_admin()) {
                    $uid = 0; $type='admin';
                } else {
                    $uid = $this->business->uid;
                    $type='user';
                }  

                $data=array(
                    'lang_id' => 1,
                    'business_id' => 0,
                    'title' => $this->input->post('title', true),
                    'slug' => str_slug(trim($this->input->post('slug', true))),
                    'type' => $type,
                    'details' => html_purify($this->input->post('details', true), 'enable'),
                    'status' => 1,
                    'created_at' => my_date_now()
                );
                $data = $this->security->xss_clean($data);
                //if id available info will be edited
                if ($id != '') {
                    $this->admin_model->edit_option($data, $id, 'pages');
                    $this->session->set_flashdata('msg', trans('updated-successfully')); 
                } else {
                    $id = $this->admin_model->insert($data, 'pages');
                    $this->session->set_flashdata('msg', trans('inserted-successfully')); 
                }
            }
            redirect(base_url('admin/pages'));

        }      
        
    }

    public function edit($id)
    {  
        $data = array();
        $data['page_title'] = 'Edit';   
        $data['page'] = $this->admin_model->select_option($id, 'pages');
        $data['main_content'] = $this->load->view('admin/pages',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    
    public function active($id) 
    {
        $data = array(
            'status' => 1
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id,'pages');
        $this->session->set_flashdata('msg', trans('activate-successfully')); 
        redirect(base_url('admin/pages'));
    }

    public function update_status($status, $id) 
    {
        $data = array(
            'status' => $status
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id,'pages');
        $this->session->set_flashdata('msg', trans('update-status-successfully')); 
        redirect(base_url('admin/pages'));
    }

    public function delete($id)
    {
        $this->admin_model->delete($id,'pages'); 
        echo json_encode(array('st' => 1));
    }

}
	

