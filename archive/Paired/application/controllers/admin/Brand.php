<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Brand extends Home_Controller {

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
        $data['page_title'] = 'Brand';     
        $data['page'] = 'Brand';   
        $data['brand'] = FALSE;
        $data['brands'] = $this->admin_model->get_all_brands($this->business->uid);
        $data['main_content'] = $this->load->view('admin/user/brand',$data,TRUE);
        $this->load->view('admin/index',$data);
    }


    public function add()
    {	
        check_status();
        
        if($_POST)
        {   
            $id = $this->input->post('id', true);
            //validate inputs

            if(user()->role == 'admin'){
                $user_id = 0;
            }else{
                $user_id = user()->id;    
            }
            $data=array(
                'user_id' => $user_id,
                'name' => $this->input->post('name', true),
                'link' => $this->input->post('link', true),
                'status' => $this->input->post('status', true)
            );
            $data = $this->security->xss_clean($data);
            
            //if id available info will be edited
            if ($id != '') {
                $this->admin_model->edit_option($data, $id, 'brands');
                $this->session->set_flashdata('msg', trans('updated-successfully')); 
            } else {
                $id = $this->admin_model->insert($data, 'brands');
                $this->session->set_flashdata('msg', trans('inserted-successfully')); 
            }


            //upload image
            $data_img = $this->admin_model->do_upload('photo');
            if($data_img){
                $data_img = array(
                    'logo' => $data_img['thumb']
                );
                $this->admin_model->edit_option($data_img, $id, 'brands'); 
             }

            redirect(base_url('admin/brand'));

           
        }      
        
    }

    public function edit($id)
    {  
        $data = array();
        $data['page_title'] = 'Edit';   
        $data['brand'] = $this->admin_model->select_option($id, 'brands');
        $data['main_content'] = $this->load->view('admin/user/brand',$data,TRUE);
        $this->load->view('admin/index',$data);
    }


    public function delete($id)
    {
        $this->admin_model->delete($id,'brands'); 
        echo json_encode(array('st' => 1));
    }


}
	

