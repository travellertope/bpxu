<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Email_templates extends Home_Controller {

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
        $data['page'] = 'Settings';
        $data['page_title'] = 'Email Template';
        $data['templates'] = $this->admin_model->select_asc('email_templates');
        $data['template'] = $this->admin_model->get_email_by_slug('verification');
        $data['main_content'] = $this->load->view('admin/email_templates',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function template($slug='')
    {
        
        $data = array();
        $data['page'] = 'Settings';
        $data['page_title'] = 'Email Template';
        $data['templates'] = $this->admin_model->select_asc('email_templates');
        $data['template'] = $this->admin_model->get_email_by_slug($slug);
        $loaded=$this->load->view('admin/include/email_template',$data,true);
        echo json_encode(array('st'=> 1, 'loaded'=> $loaded));
    }


    public function add()
    {	
        if($_POST)
        {   
            //validate inputs

            $slug = $this->input->post('slug', true);
            //echo "<pre>"; print_r($slug); exit();
            $this->form_validation->set_rules('subject', 'Subject', 'required');
            $this->form_validation->set_rules('body', 'Body', 'required');

            if ($this->form_validation->run() === false) {
                $this->session->set_flashdata('error', validation_errors());
                redirect(base_url('admin/email_templates'));
            } else {
                $data=array(
                    'subject' => $this->input->post('subject', true),
                    'body' => $this->input->post('body', true),
                    'slug' => $slug,
                );
                $data = $this->security->xss_clean($data);
                $template = $this->admin_model->get_email_by_slug($slug);
                //echo "<pre>"; print_r( $template); exit();
                $this->admin_model->edit_option($data, $template->id, 'email_templates');
                $this->session->set_flashdata('msg', trans('updated-successfully')); 

            }
        }   
        
        redirect($_SERVER['HTTP_REFERER']);   
        
    }

}
	

