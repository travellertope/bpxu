<?php
defined('BASEPATH') OR exit('No direct script access allowed');



class Notifications extends Home_Controller {

    //load notification
	public function my() {
        $data = array();
        $data['notifications'] = $this->admin_model->my_notifications();
        $this->admin_model->my_notifications_make_seen();
        $noti = $this->load->view('admin/include/header_notifications',$data,TRUE);
        echo json_encode(array('st'=>1, 'noti' => $noti));
        
    }
	
    //get all notification
	public function all(){
		$data = array();

        $data = array();
        $this->load->library('pagination');
        $config['base_url'] = base_url('admin/notifications/all');
        $total_row = $this->admin_model->get_all_notification(1, 0, 0);
        $config['total_rows'] = $total_row;
        $config['per_page'] = 6;
        $this->pagination->initialize($config);
        $page = $this->security->xss_clean($this->input->get('page'));
        if (empty($page)) {
            $page = 0;
        }
        if ($page != 0) {
            $page = $page - 1;
        }
        
        $data['page_title'] = 'Blogs';
        $data['menu'] = TRUE;
        $data['notifications'] = $this->admin_model->get_all_notification(0 , $config['per_page'], $page * $config['per_page']);
        $data['main_content'] = $this->load->view('admin/user/all_notification',$data,TRUE);
        $this->load->view('admin/index',$data);


	}


}
