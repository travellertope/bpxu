<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profile extends Home_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    public function index($slug){
        $this->user($slug);
    }


    public function user($slug)
    {   
        
        $data = array();
        $slug = $this->security->xss_clean($slug);
        $data['user'] = $this->common_model->get_user_by_slug($slug);
        $user_id =  $data['user']->id;
        $data['my_days'] =$this->admin_model->get_my_days($user_id);

        $my_days = $this->admin_model->get_my_days($user_id);
        
        foreach ($my_days as $day) {
            if ($day['day'] != 0) {
                $myday[] = $day['day'];
            }
        }

        $days = "1,2,3,4,5,6,7";         
        $days = explode(',', $days);
        $assign_days = $myday;

        $match = array();
        $nomatch = array();

        foreach($days as $v){     
            if(in_array($v, $assign_days))
                $match[]=$v;
            else
                $nomatch[]=$v;
        }


        $data['not_available'] = $nomatch;
        $data['chambers'] = $this->common_model->get_my_chambers($user_id);
        $data['educations'] = $this->common_model->get_my_educations($user_id);
        $data['experiences'] = $this->common_model->get_my_experiences($user_id);
        $data['patients'] = $this->common_model->get_my_total_patients($user_id);
        $data['prescriptions'] = $this->common_model->get_my_total_prescriptions($user_id);
        $data['page_title'] = $data['user']->name;
        $data['page'] = 'Profile';
        $data['menu'] = FALSE;
        $data['slug'] = $slug;
        $data['main_content'] = $this->load->view('profile', $data, TRUE);
        $this->load->view('index', $data);
    }

    // not found page
    public function error_404()
    {
        $data['page_title'] = "Error 404";
        $data['description'] = "Error 404";
        $data['keywords'] = "error,404";
        $this->load->view('error_404');
    }


}