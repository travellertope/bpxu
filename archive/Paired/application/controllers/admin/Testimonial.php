<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Testimonial extends Home_Controller {

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
        $data['page_title'] = 'Testimonials';  
        $data['testimonial'] = FALSE;
        if (is_admin()) {
            $uid = 0; $type = 'admin';
        } else {
            $uid = $this->business->uid;
            $type = 'user';
        } 
        $data['testimonials'] = $this->admin_model->get_testimonials($uid, $type, 'all');
        $data['main_content'] = $this->load->view('admin/testimonial',$data,TRUE);
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
                'name' => $this->input->post('name', true),
                'designation' => $this->input->post('designation', true),
                'feedback' => $this->input->post('feedback', true)
            );
            $data = $this->security->xss_clean($data);
            if ($id != '') {
                $this->admin_model->edit_option($data, $id, 'testimonials');
                $this->session->set_flashdata('msg', trans('updated-successfully')); 
            } else {
                $id = $this->admin_model->insert($data, 'testimonials');
                $this->session->set_flashdata('msg', trans('inserted-successfully')); 
            }

            if($_FILES['photo']['name'] != ''){
                $up_load = $this->admin_model->upload_image('600');
                $data_img = array(
                    'image' => $up_load['images'],
                    'thumb' => $up_load['thumb']
                );
                $this->admin_model->edit_option($data_img, $id, 'testimonials');   
            }
            
            redirect(base_url('admin/testimonial'));

        }      
        
    }

    public function edit($id)
    {  
        $data = array();
        $data['page_title'] = 'Edit';   
        $data['testimonial'] = $this->admin_model->select_option($id, 'testimonials');
        $data['main_content'] = $this->load->view('admin/testimonial',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    
    public function active($id) 
    {
        $data = array(
            'status' => 1
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id,'testimonials');
        $this->session->set_flashdata('msg', trans('activate-successfully')); 
        redirect(base_url('admin/testimonial'));
    }

    public function deactive($id) 
    {
        $data = array(
            'status' => 0
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id,'testimonials');
        $this->session->set_flashdata('msg', trans('deactivate-successfully')); 
        redirect(base_url('admin/testimonial'));
    }

    public function delete($id)
    {
        $this->admin_model->delete($id,'testimonials'); 
        echo json_encode(array('st' => 1));
    }

}
	

