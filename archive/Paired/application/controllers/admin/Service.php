<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Service extends Home_Controller {

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
        $data['page_title'] = 'Service';  
        $data['page'] = 'Service';    
        $data['service'] = FALSE;
        $data['categories'] = $this->admin_model->get_categories($this->business->uid, 'service', 'all'); 
        $data['services'] = $this->admin_model->select_by_company('services');

        $data['limit'] = FALSE;
        $total = get_total_value('services');
        if (ckeck_plan_limit('services', $total) == FALSE) {
            $data['limit'] = TRUE;
        }

        $data['main_content'] = $this->load->view('admin/user/service',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function add()
    {   
        check_status();
        
        if($_POST)
        {   
            $id = $this->input->post('id', true);

            if(!empty($this->input->post('enable_booking'))){$enable_booking = $this->input->post('enable_booking', true);}
                else{$enable_booking = 0;}

            if ($enable_booking != 0) {
                $this->form_validation->set_rules('price', trans('price'), 'required');
                $this->form_validation->set_rules('duration', trans('duration'), 'required');

                if ($this->form_validation->run() === false) {
                    $this->session->set_flashdata('error', validation_errors());
                    redirect(base_url('admin/service'));
                    exit();
                } 
            }

            $data=array(
                'lang_id' => 1,
                'business_id' => $this->business->uid,
                'user_id' => user()->id,
                'category_id' => $this->input->post('category_id', true),
                'title' => $this->input->post('title', true),
                'slug' => str_slug($this->input->post('slug', true)),
                'details' => $this->input->post('details', true),
                'icon' => $this->input->post('icon', true),
                'order' => $this->input->post('order', true),
                'enable_booking' =>$enable_booking,
                'price' => $this->input->post('price', true),
                'duration_type' => $this->input->post('duration_type', true),
                'duration' => $this->input->post('duration', true),
                'content_type' => $this->input->post('content_type', true),
                'status' => $this->input->post('status', true),
            );
            $data = $this->security->xss_clean($data);


            if (!empty($id)) {
                $this->admin_model->edit_option($data, $id, 'services');
                $this->session->set_flashdata('msg', trans('updated-successfully')); 
            } else {
                $id = $this->admin_model->insert($data, 'services');
                $this->session->set_flashdata('msg', trans('inserted-successfully')); 
            }

            if($_FILES['photo']['name'] != ''){
                $up_load = $this->admin_model->upload_image('1200');
                $data_img = array(
                    'image' => $up_load['images'],
                    'thumb' => $up_load['thumb']
                );
                $data_img = $this->security->xss_clean($data_img);
                $this->admin_model->edit_option($data_img, $id, 'services');
            }
            
            redirect(base_url('admin/service'));
            

        }      
        
    }

    public function edit($id)
    {  
        $data = array();
        $data['page_title'] = 'Edit';  
        $data['page'] = 'Service';  
        $data['categories'] = $this->admin_model->get_categories($this->business->uid, 'service', 'all');
        $data['service'] = $this->admin_model->get_by_id($id, 'services');
        $data['main_content'] = $this->load->view('admin/user/service',$data,TRUE);
        $this->load->view('admin/index',$data);
    }
    
    public function active($id) 
    {
        $data = array(
            'status' => 1
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id,'services');
        $this->session->set_flashdata('msg', trans('activate-successfully')); 
        redirect(base_url('admin/service'));
    }

    public function deactive($id) 
    {
        $data = array(
            'status' => 0
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id,'services');
        $this->session->set_flashdata('msg', trans('deactivate-successfully')); 
        redirect(base_url('admin/service'));
    }

    public function delete($id)
    {
        $this->admin_model->delete($id,'services'); 
        echo json_encode(array('st' => 1));
    }

    public function appointment()
    {  
        $data = array();
        $data['page_title'] = 'Appointments';  
        $data['page'] = 'Appointments';     
        $data['appointments'] = $this->admin_model->get_appointments();
        $data['main_content'] = $this->load->view('admin/user/appointments',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function appointment_delete($id)
    {
        $this->admin_model->delete($id,'appointments'); 
        echo json_encode(array('st' => 1));
    }

    public function appointment_confirm($id) 
    {
        $data = array(
            'status' => 1,
            'confirm_date' => date('Y-m-d'),
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id,'appointments');
        $this->session->set_flashdata('msg', trans('appointment-confirm-successfully')); 
        redirect(base_url('admin/service/appointment'));
    }


    public function appointment_cancel($id) 
    {
        $data = array(
            'status' => 2,
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id,'appointments');
        $this->session->set_flashdata('msg', trans('appointment-cancel-successfully')); 
        redirect(base_url('admin/service/appointment'));
    }

}
    

