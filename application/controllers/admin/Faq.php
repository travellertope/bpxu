<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Faq extends Home_Controller {

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
        $data['page_title'] = 'Faqs';  
        $data['faq'] = FALSE;
        if (is_admin()) {
            $uid = 0; $type = 'admin';
        } else {
            $uid = $this->business->uid;
            $type = 'user';
        } 
        $data['faqs'] = $this->admin_model->get_faqs($uid, $type, 'all');
        $data['main_content'] = $this->load->view('admin/faqs',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function add()
    {   
        check_status();
        
        if($_POST)
        {   
            $id = $this->input->post('id', true);
            if (is_admin()) {
            $uid = 0; $type = 'admin';
            } else {
                $uid = $this->business->uid;
                $type = 'user';
            } 
            $data=array(
                'lang_id' => 1,
                'business_id' => $this->business->uid,
                'type' => $type,
                'title' => $this->input->post('title', true),
                'details' => $this->input->post('details', true)
            );
            $data = $this->security->xss_clean($data);

            if ($id != '') {
                $this->admin_model->edit_option($data, $id, 'faqs');
                $this->session->set_flashdata('msg', trans('updated-successfully')); 
            } else {
                $id = $this->admin_model->insert($data, 'faqs');
                $this->session->set_flashdata('msg', trans('inserted-successfully')); 
            }
            redirect(base_url('admin/faq'));

        }      
        
    }

    public function edit($id)
    {  
        $data = array();
        $data['page_title'] = 'Edit';   
        $data['faq'] = $this->admin_model->select_option($id, 'faqs');
        $data['main_content'] = $this->load->view('admin/faqs',$data,TRUE);
        $this->load->view('admin/index',$data);
    }
    
    public function active($id) 
    {
        $data = array(
            'status' => 1
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id,'faqs');
        $this->session->set_flashdata('msg', trans('activate-successfully')); 
        redirect(base_url('admin/pages'));
    }

    public function deactive($id) 
    {
        $data = array(
            'status' => 0
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id,'faqs');
        $this->session->set_flashdata('msg', trans('deactivate-successfully')); 
        redirect(base_url('admin/pages'));
    }

    public function delete($id)
    {
        $this->admin_model->delete($id,'faqs'); 
        echo json_encode(array('st' => 1));
    }

}
    

