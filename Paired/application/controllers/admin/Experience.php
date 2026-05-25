<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Experience extends Home_Controller {

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
        $data['page_title'] = 'Experience';
        $data['experience'] = FALSE;
        $data['experiences'] = $this->admin_model->get_by_user('experiences');
        $data['main_content'] = $this->load->view('admin/user/experiences',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function add()
    {   
        check_status();
        
        if($_POST)
        {   
            $id = $this->input->post('id', true);
            if(!empty($this->input->post('is_present',true))){
                $is_present = $this->input->post('is_present',true);
            }else{
                $is_present = 0;
            }
            
            $data=array(
                'user_id' =>user()->id,
                'icon' => $this->input->post('icon',true),
                'title' => $this->input->post('title',true),
                'company' => $this->input->post('company',true),
                'start_date' => $this->input->post('start_date',true),
                'end_date' => $this->input->post('end_date',true),
                'is_present' => $is_present,
                'contribution' => $this->input->post('contribution',true),
                'status' => $this->input->post('status',true),
                'created_at' =>my_date_now()
            );

            $data = $this->security->xss_clean($data);

            if ($id != '') {
                $this->admin_model->edit_option($data, $id, 'experiences');
                $this->session->set_flashdata('msg', trans('updated-successfully')); 
            } else {
                $id = $this->admin_model->insert($data, 'experiences');
                $this->session->set_flashdata('msg', trans('inserted-successfully')); 
            }
            redirect(base_url('admin/experience'));

        }      
        
    }

    public function edit($id)
    {  
        $data = array();
        $data['page_title'] = 'Edit'; 
        $data['page'] = 'Experience'; 
        $data['experience'] = $this->admin_model->get_by_id($id, 'experiences');
        $data['main_content'] = $this->load->view('admin/user/experiences',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function delete($id)
    {
        $this->admin_model->delete($id,'experiences'); 
        echo json_encode(array('st' => 1));
    }


}
    

