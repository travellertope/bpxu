<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends Home_Controller {

	public function __construct()
    {
        parent::__construct();
        if (!is_admin()) {
            redirect(base_url());
        }
    }


    public function index()
    {
        $this->all_users('all');
    }

    public function all_users($type)
    {

        $data = array();
        $this->load->library('pagination');
        $config['base_url'] = base_url('admin/users/all_users/'.$type);
        $total_row = $this->admin_model->get_all_users(1 , 0, 0, $type);
        $config['total_rows'] = $total_row;
        $config['per_page'] = 15;
        $this->pagination->initialize($config);
        
        $page = $this->security->xss_clean($this->input->get('page'));
        if (empty($page)) {
            $page = 0;
        }
        if ($page != 0) {
            $page = $page - 1;
        }

        $data['page_title'] = 'Users';
        $data['countries'] = $this->admin_model->select('country');
        $data['categories'] = $this->admin_model->select('categories');
        $data['users'] = $this->admin_model->get_all_users(0 , $config['per_page'], $page * $config['per_page'], $type);
        $data['main_content'] = $this->load->view('admin/users', $data, TRUE);
        $this->load->view('admin/index', $data);
    }

    public function mentor_details($id)
    {
        $data = array();
        $data['page'] = 'Users';   
        $data['page_title'] = 'Mentor Details';   
        $data['mentor'] = $this->admin_model->get_by_id($id, 'users');
        $data['sessions'] = $this->admin_model->get_mentor_sessions($data['mentor']->id);
        //echo '<pre>'; print_r($data['sessions']); exit();
        $data['main_content'] = $this->load->view('admin/user/mentor_details',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function mentees()
    {
        $data = array();
        $this->load->library('pagination');
        $config['base_url'] = base_url('admin/users/mentee');
        $total_row = $this->admin_model->get_all_mentees(1 , 0, 0);
        $config['total_rows'] = $total_row;
        $config['per_page'] = 15;
        $this->pagination->initialize($config);
        
        $page = $this->security->xss_clean($this->input->get('page'));
        if (empty($page)) {
            $page = 0;
        }
        if ($page != 0) {
            $page = $page - 1;
        }

        $data['page_title'] = 'Mentee';
        $data['packages'] = $this->admin_model->select('package');
        $data['users'] = $this->admin_model->get_all_mentees(0 , $config['per_page'], $page * $config['per_page']);
        $data['main_content'] = $this->load->view('admin/mentees', $data, TRUE);
        $this->load->view('admin/index', $data);
    }


    public function status_action($type, $id) 
    {

        $user = $this->admin_model->get_by_id($id, 'users');

        if ($user->role == 'user') {
            $url = base_url('admin/users');
        }else{
            $url = base_url('admin/mentee/all');
        }

        $data = array(
            'status' => $type
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id,'users');

        if($type == 1):

            if ($user->role == 'user') {

                // $data = array();
                // $data['name'] = $user->name;
                // $message = $this->load->view('email_template/approved', $data, true);
                // $this->email_model->send_email($user->email, $data['name'], $message);


                // send email with updated code dynamic value

                $subject = get_email_by_slug('mentors-profile-approved-confirmation')->subject;
                $body = get_email_by_slug('mentors-profile-approved-confirmation')->body;
                $variables_data = [
                    'name'  =>$user->name,
                    'site_name' => settings()->site_name,
                    'admin_email' => settings()->admin_email,
                ]; 

                $msg = preg_replace_callback('/{{(.*?)}}/', function ($matches) use ($variables_data) {
                    $key = trim($matches[1]);
                    return isset($variables_data[$key]) ? $variables_data[$key] : $matches[0]; 
                }, $body);

                $edata = array();
                $edata['subject'] = $subject;
                $edata['msg'] = $msg;

                $msg = $this->load->view('email_template/common', $edata, true);
                $response = $this->email_model->send_email($user->email, $subject, $msg);


            }

            $this->session->set_flashdata('msg', trans('activate-successfully')); 
        else:
            $this->session->set_flashdata('msg', trans('deactivate-successfully')); 
        endif;

        redirect($url);
    }

    public function change_account($id) 
    {
        $data = array(
            'account_type' => $this->input->post('type', false)
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->edit_option($data, $id, 'users');
        $this->session->set_flashdata('msg', trans('updated-successfully')); 
        redirect(base_url('admin/users'));
    }


    public function delete($user_id)
    {
        check_status();
        $this->admin_model->delete($user_id,'users'); 
        echo json_encode(array('st' => 1));
        
    }


}