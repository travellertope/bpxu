<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Controller extends Home_Controller {

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
        $data['page_title'] = 'Controllers';  
        $data['controller'] = FALSE; 
        $data['controllers'] = $this->admin_model->select('table_name');
        $data['main_content'] = $this->load->view('admin/user/view_page',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function add()
    {   
        check_status();
        
        if($_POST)
        {   
            $id = $this->input->post('id', true);
            $data=array(
                'lang_id' => 1,
                'title' => $this->input->post('title', true),
                'details' => $this->input->post('details', true)
            );
            $data = $this->security->xss_clean($data);

            if ($id != '') {
                $this->admin_model->edit_option($data, $id, 'table_name');
                $this->session->set_flashdata('msg', trans('updated-successfully')); 
            } else {
                $id = $this->admin_model->insert($data, 'table_name');
                $this->session->set_flashdata('msg', trans('inserted-successfully')); 
            }
            redirect(base_url('user/controller'));

        }      
        
    }

    public function edit($id)
    {  
        $data = array();
        $data['page_title'] = 'Edit';   
        $data['faq'] = $this->admin_model->get_by_id($id, 'table_name');
        $data['main_content'] = $this->load->view('admin/view_page',$data,TRUE);
        $this->load->view('admin/index',$data);
    }
    
    public function active($id) 
    {
        $data = array(
            'status' => 1
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id,'table_name');
        $this->session->set_flashdata('msg', trans('activate-successfully')); 
        redirect(base_url('user/controller'));
    }

    public function deactive($id) 
    {
        $data = array(
            'status' => 0
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id,'table_name');
        $this->session->set_flashdata('msg', trans('deactivate-successfully')); 
        redirect(base_url('user/controller'));
    }

    public function delete($id)
    {
        $this->admin_model->delete($id,'faqs'); 
        echo json_encode(array('st' => 1));
    }

}
    

