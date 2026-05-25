<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Favourite extends Home_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!is_user() && !is_mentee()) {
            redirect(base_url());
        }
    }

    public function index()
    {
        $data = array();
        $data['page_title'] = 'Favourite';  
        $data['faq'] = FALSE;
        $data['favourites'] = $this->admin_model->get_favourites();
        $data['main_content'] = $this->load->view('admin/user/favourites',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function add_favourite($favourite_id, $user_id){

        $favourite = $this->common_model->check_favourite($favourite_id, $user_id);

        if (empty($favourite)) {
            $data = array(
                'user_id' => $user_id,
                'favourite_id' => $favourite_id,
                'created_at' => my_date_now()
            );
            $data = $this->security->xss_clean($data);
            $this->common_model->insert($data, 'favourite');

            $user_name = $this->admin_model->get_by_id($user_id, 'users')->name;

            $notify = array(
                'user_id' => $favourite_id,
                'action_id' =>$user_id,
                'content_id' => 0,
                'text' => $user_name." added you to his favourite list",
                'noti_type' => 7,
                'noti_time' => my_date_now()
            );
            $notify = $this->security->xss_clean($notify);
            $this->common_model->insert($notify, 'notifications');

            $this->session->set_flashdata('msg', trans('updated-successfully'));
            echo json_encode(array('st'=>1));

        }else{
            $this->common_model->delete($favourite->id,'favourite');
            $this->session->set_flashdata('msg', trans('updated-successfully'));
            echo json_encode(array('st'=>2));
        }

    }

    public function delete($id)
    {
        $this->admin_model->delete($id,'faqs'); 
        echo json_encode(array('st' => 1));
    }

}
    

