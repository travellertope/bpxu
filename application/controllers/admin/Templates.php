<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Templates extends Home_Controller {

    public function __construct()
    {
        parent::__construct();
        //check auth
        if (!is_user()) {
            redirect(base_url());
        }
    }

    public function index(){
        $data = array();
        $data['page_title'] = 'Templates';  
        $data['page'] = 'Templates';
        
        if (check_feature_access('templates') == TRUE):
            $feature = TRUE;
        else:
            $feature = FALSE;
        endif;

        $data['categories'] = $this->admin_model->get_categories(1, $feature);
        $data['subcategories'] = $this->admin_model->get_subcategories(1, $feature);
        $data['main_content'] = $this->load->view('admin/user/templates',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

}