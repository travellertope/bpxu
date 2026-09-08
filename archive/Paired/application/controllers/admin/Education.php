<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Education extends Home_Controller {

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
        $data['page_title'] = 'Education';
        $data['education'] = FALSE;
        $data['educations'] = $this->admin_model->get_by_user('educations');
        $data['main_content'] = $this->load->view('admin/user/educations',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function add()
    {   
        check_status();
        
        if($_POST)
        {   
            $id = $this->input->post('id', true);
    
            $data=array(
                'user_id' => user()->id,
                'institute' => $this->input->post('institute',true),
                'degree' => $this->input->post('degree',true),
                'start_year' => $this->input->post('start_year',true),
                'end_year' => $this->input->post('end_year',true),
                'status' => $this->input->post('status',true),
                'created_at' =>my_date_now()
            );

            $data = $this->security->xss_clean($data);

            if ($id != '') {
                $this->admin_model->edit_option($data, $id, 'educations');
                $this->session->set_flashdata('msg', trans('updated-successfully')); 
            } else {
                $id = $this->admin_model->insert($data, 'educations');
                $this->session->set_flashdata('msg', trans('inserted-successfully')); 
            }
            redirect(base_url('admin/education'));

        }      
        
    }

    public function edit($id)
    {  
        $data = array();
        $data['page_title'] = 'Edit';
        $data['page'] = 'Education'; 
        $data['education'] = $this->admin_model->get_by_id($id, 'educations');
        $data['main_content'] = $this->load->view('admin/user/educations',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function delete($id)
    {
        $this->admin_model->delete($id,'educations'); 
        echo json_encode(array('st' => 1));
    }


}
    

