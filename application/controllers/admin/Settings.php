<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

#[\AllowDynamicProperties]

class Settings extends Home_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    
    public function index()
    {
        if (!is_admin()) {
            redirect(base_url());
        }
        $data = array();
        $data['page_title'] = 'System Settings';
        $data['page'] = 'Settings';
        $data['settings'] = $this->admin_model->get('settings');
        $data['currencies'] = $this->admin_model->select_asc('country');
        $data['fonts'] = $this->admin_model->get_site_fonts();
        $data['time_zones'] = $this->admin_model->select_asc('time_zone');
        $data['main_content'] = $this->load->view('admin/settings', $data, TRUE);
        $this->load->view('admin/index', $data);
    }
    

    public function update(){

        check_status();

        if ($_POST) {

            $this->pwa_logo_upload();

            if(!empty($this->input->post('enable_multilingual', true))){$enable_multilingual = $this->input->post('enable_multilingual', true);}
            else{$enable_multilingual = 0;}

            if(!empty($this->input->post('enable_registration', true))){$enable_registration = $this->input->post('enable_registration', true);}
            else{$enable_registration = 0;}

            if(!empty($this->input->post('enable_email_verify', true))){$enable_email_verify = $this->input->post('enable_email_verify', true);}
            else{$enable_email_verify = 0;}

            if(!empty($this->input->post('enable_mentor_auto_approve', true))){$enable_mentor_auto_approve = $this->input->post('enable_mentor_auto_approve', true);}
            else{$enable_mentor_auto_approve = 0;}

            // if(!empty($this->input->post('enable_sms_verify', true))){$enable_sms = $this->input->post('enable_sms_verify', true);}
            // else{$enable_sms = 0;}

            if(!empty($this->input->post('enable_whatsapp_msg', true))){$enable_whatsapp_msg = $this->input->post('enable_whatsapp_msg', true);}
            else{$enable_whatsapp_msg = 0;}

            if(!empty($this->input->post('enable_captcha', true))){$enable_captcha = $this->input->post('enable_captcha', true);}
            else{$enable_captcha = 0;}

            if(!empty($this->input->post('enable_payment', true))){$enable_payment = $this->input->post('enable_payment', true);}
            else{$enable_payment = 0;}

            if(!empty($this->input->post('enable_blog', true))){$enable_blog = $this->input->post('enable_blog', true);}
            else{$enable_blog = 0;}

            if(!empty($this->input->post('enable_faq', true))){$enable_faq = $this->input->post('enable_faq', true);}
            else{$enable_faq = 0;}

            if(!empty($this->input->post('enable_users', true))){$enable_users = $this->input->post('enable_users', true);}
            else{$enable_users = 0;}

            if(!empty($this->input->post('enable_workflow', true))){$enable_workflow = $this->input->post('enable_workflow', true);}
            else{$enable_workflow = 0;}

            if(!empty($this->input->post('enable_feature', true))){$enable_feature = $this->input->post('enable_feature', true);}
            else{$enable_feature = 0;}

            if(!empty($this->input->post('enable_frontend', true))){$enable_frontend = $this->input->post('enable_frontend', true);}
            else{$enable_frontend = 0;}

            if(!empty($this->input->post('enable_lifetime', true))){$enable_lifetime = $this->input->post('enable_lifetime', true);}
            else{$enable_lifetime = 0;}

            if(!empty($this->input->post('enable_coupon', true))){$enable_coupon = $this->input->post('enable_coupon', true);}
            else{$enable_coupon = 0;}

            if(!empty($this->input->post('enable_animation', true))){$enable_animation = $this->input->post('enable_animation', true);}else{$enable_animation = 0;}

            if(!empty($this->input->post('enable_kyc', true))){$enable_kyc = $this->input->post('enable_kyc', true);}
            else{$enable_kyc = 0;}

            if(!empty($this->input->post('enable_pwa', true))){$enable_pwa = $this->input->post('enable_pwa', true);}
            else{$enable_pwa = 0;}

            
            $google_analytics = $this->input->post('google_analytics');
            $custom_css = $this->security->xss_clean($this->input->post('custom_css', true));
            $mail_password = $this->security->xss_clean($this->input->post('mail_password', true));

            $data = array(
                'site_name' => $this->input->post('site_name', true),
                'site_title' => $this->input->post('site_title', true),
                'site_title_mentor' => $this->input->post('site_title_mentor', true),
                'keywords' => $this->input->post('keywords', true),
                'description' => $this->input->post('description', true),
                'footer_about' => $this->input->post('footer_about', true),
                'admin_email' => $this->input->post('admin_email', true),
                'copyright' => $this->input->post('copyright', true),
                'time_zone' => $this->input->post('time_zone', true),
                'openai_key' => $this->input->post('openai_key', true),
                'openai_model' => $this->input->post('openai_model', true),
                'pagination_limit' => 0,
                'country' => $this->input->post('country', true),
                'trial_days' => $this->input->post('trial_days', true),
                'facebook' => $this->input->post('facebook', true),
                'twitter' => $this->input->post('twitter', true),
                'instagram' => $this->input->post('instagram', true),
                'linkedin' => $this->input->post('linkedin', true),
                'chart_style' => $this->input->post('chart_style', true),
                'curr_locate' => $this->input->post('curr_locate', true),
                'num_format' => $this->input->post('num_format', true),
                'zoom_api_user' => $this->input->post('zoom_api_user', true),
                'enable_multilingual' => $enable_multilingual,
                'enable_registration' => $enable_registration,
                'enable_captcha' => $enable_captcha,
                'enable_payment' => $enable_payment,
                'enable_blog' => $enable_blog,
                'enable_faq' => $enable_faq,
                'enable_users' => $enable_users,
                'enable_workflow' => $enable_workflow,
                'enable_feature' => $enable_feature,
                'enable_frontend' => $enable_frontend,
                'enable_lifetime' => $enable_lifetime,
                'enable_coupon' => $enable_coupon,
                'enable_kyc' => $enable_kyc,
                'enable_animation' => $enable_animation,
                'enable_email_verify' => $enable_email_verify,
                'enable_mentor_auto_approve' => $enable_mentor_auto_approve,
                //'enable_sms' => $enable_sms,
                'enable_pwa' => $enable_pwa,
                'enable_whatsapp_msg' => $enable_whatsapp_msg,
                'google_analytics' => base64_encode($google_analytics),
                'custom_css' => json_encode($custom_css),
                'site_color' => str_replace('#', '', $this->input->post('site_color', true)),
                'layout' => $this->input->post('layout', true),
                'front_layout' => $this->input->post('front_layout', true),
                'site_mode' => $this->input->post('site_mode', true),
                'site_font' => $this->input->post('font', true),
                'captcha_site_key' => $this->input->post('captcha_site_key', true),
                'captcha_secret_key' => $this->input->post('captcha_secret_key', true),
                'google_client_id' => trim($this->input->post('google_client_id', true)),
                'google_client_secret' => trim($this->input->post('google_client_secret', true)),
                'mail_protocol' => $this->input->post('mail_protocol', true),
                'mail_title' => $this->input->post('mail_title', true),
                'sender_mail' => $this->input->post('sender_mail', true),
                'mail_host' => $this->input->post('mail_host', true),
                'mail_port' => $this->input->post('mail_port', true),
                'mail_username' => $this->input->post('mail_username', true),
                'mail_password' => base64_encode($mail_password),
                'mail_encryption' => $this->input->post('mail_encryption', true),
                'twillo_account_sid' => $this->input->post('twillo_account_sid', true),
                'twillo_auth_token' => $this->input->post('twillo_auth_token', true),
                'twillo_number' => $this->input->post('twillo_number', true), 
                'ultramsg_instance_id' => $this->input->post('ultramsg_instance_id', true), 
                'ultramsg_token' => $this->input->post('ultramsg_token', true), 
                'zoom_account_id' => $this->input->post('zoom_account_id', true),
                'zoom_client_id' => $this->input->post('zoom_client_id', true),
                'zoom_client_secret' => $this->input->post('zoom_client_secret', true),
                'tax_name' => $this->input->post('tax_name', true),
                'tax_value' => $this->input->post('tax_value', true),
                'booking_date_type' => $this->input->post('booking_date_type', true),
                'booking_reminder_time' => $this->input->post('booking_reminder_time', true),
                'second_booking_reminder_time' => $this->input->post('second_booking_reminder_time', true),
            );

            $site_mode = array('site_mode' => $this->input->post('site_mode'),);
            $this->session->set_userdata($site_mode);
            
            // upload favicon image
            $data_img = $this->admin_model->do_upload('photo1');
            if($data_img){
                $data_img_1 = array(
                    'favicon' => $data_img['thumb']
                );
                $this->admin_model->edit_option($data_img_1, 1, 'settings'); 
             }

            // upload logo
            $data_img2 = $this->admin_model->do_upload('photo2');
            if($data_img2){
                $data_img_2 = array(
                    'logo' => $data_img2['medium']
                );            
                $this->admin_model->edit_option($data_img_2, 1, 'settings');
            }

            // upload home hero image
            $data_img3 = $this->admin_model->do_upload('photo3');
            if($data_img3){
                $data_img_3 = array(
                    'hero_img' => $data_img3['medium']
                );            
                $this->admin_model->edit_option($data_img_3, 1, 'settings');
            }

            $user_data = array(
                'email' => $this->input->post('admin_email', true)        
            );
            
            $user_data = $this->security->xss_clean($user_data);
            $this->admin_model->edit_option($user_data, user()->id, 'users');

            $batch_data = array(
                array(
                  'key' => 'enable_google',
                  'value' => !empty($this->input->post('enable_google'))?$this->input->post('enable_google'):'0'
                ),
                array(
                  'key' => 'enable_facebook',
                  'value' => !empty($this->input->post('enable_facebook'))?$this->input->post('enable_facebook'):'0'
                ),
                array(
                  'key' => 'google_client_id',
                  'value' => $this->input->post('google_client_id_log')
                ),
                array(
                  'key' => 'google_secret_key',
                  'value' => $this->input->post('google_secret_key')
                ),
                array(
                  'key' => 'google_redirect',
                  'value' => base_url('login')
                )
            );
            $this->db->update_batch('system_settings', $batch_data, 'key');

            //$data = $this->security->xss_clean($data);
            $this->admin_model->edit_option($data, 1, 'settings');
            $this->session->set_flashdata('msg', trans('updated-successfully')); 
            
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    
    public function pwa_logo_upload() {
        if (!empty($_FILES['pwa_logo']['name'])) {
            $config['upload_path']          = './uploads/files'; //file save path
            $config['allowed_types']        = 'jpg|png|jpeg';
            $config['max_size']             = 512;
            $config['encrypt_name']         = TRUE;

            $this->load->library('upload', $config);
            if ( ! $this->upload->do_upload('pwa_logo')){
                // Upload failed, display error
                $error = array('error' => $this->upload->display_errors());
                $this->session->set_flashdata('error', $error);
            } else {
                // Upload success, check image dimensions
                $upload_data = $this->upload->data();
                //echo "<pre>"; print_r($upload_data); exit();
                $file_path = $upload_data['full_path'];
                $file_name = $upload_data['file_name'];
                list($width, $height) = getimagesize($file_path);
                //echo $width.' = '.$height; exit();
                // Check dimensions here
                if ($width != 512 && $height != 512) {
                    unlink($file_path);
                    $error = "Image dimensions should not exceed 512 x 512 pixels.";
                    $this->session->set_flashdata('error', $error); 
                } else {
                    $cdata=array(
                        'pwa_logo' => 'uploads/files/'.$file_name
                    );
                    $cdata = $this->security->xss_clean($cdata);
                    $this->admin_model->edit_option($cdata, 1, 'settings');
                }
            }
        }
    }


    public function profile()
    {
        if (!is_user()) {
            redirect(base_url());
        }
        
        $data = array();
        $data['page'] = 'Settings';
        $data['page_title'] = 'Profile Settings';
        $data['user'] = $this->admin_model->get_users(user()->id);
        $data['countries'] = $this->admin_model->select_asc('country');
        $data['time_zones'] = $this->admin_model->select_asc('time_zone');
        $data['main_content'] = $this->load->view('admin/user/settings', $data, TRUE);
        $this->load->view('admin/index', $data);
    }

    public function license()
    {
        $data = array();
        $data['page'] = 'Settings';
        $data['page_title'] = 'License';
        $data['main_content'] = $this->load->view('admin/license', $data, TRUE);
        $this->load->view('admin/index', $data);
    }

    public function update_profile(){
        error_reporting(-1);
        ini_set('display_errors', 1);
        check_status();

        $id = $this->input->post('id');
        $data = array(
            'name' => $this->input->post('name', true),
            'email' => $this->input->post('email', true),
            'phone' => $this->input->post('phone', true),
            'gender' => $this->input->post('gender', true),
            'about_me' => $this->input->post('about', true),
            'mentorship_requirements' => $this->input->post('mentorship_requirements', true),
            'keywords' => $this->input->post('keywords', true),
            'description' => $this->input->post('description', true),
            'language' => $this->input->post('language', true),
            'country' => $this->input->post('country', true),
            'residence' => $this->input->post('residence', true),
            'time_zone' => $this->input->post('time_zone', true),
            'respond_in' => $this->input->post('respond_in', true),
            'respond_time' => $this->input->post('respond_time', true),
        );
  
        $data = $this->security->xss_clean($data);
        $this->admin_model->edit_option($data, $id, 'users');

        if($_FILES['photo']['name'] != ''){
            $up_load = $this->admin_model->upload_image('1200');
            $data_img = array(
                'image' => $up_load['images'],
                'thumb' => $up_load['thumb']
            );
            $data_img = $this->security->xss_clean($data_img);
            $this->admin_model->edit_option($data_img, $id, 'users');
        }

        $data_img = $this->admin_model->do_upload('photo1');
        if($data_img){
            $data_img = array(
                'cover' => $data_img['big']
            );            
            $this->admin_model->edit_option($data_img, $id, 'users');
        }

        $this->session->set_flashdata('msg', trans('updated-successfully')); 
        redirect(base_url('admin/settings/profile'));
    }

    public function mentorship()
    {
        if (!is_user()) {
            redirect(base_url());
        }
        $data = array();
        $data['page'] = 'Settings';
        $data['page_title'] = 'Mentorship Profile Settings';
        $data['user'] = $this->admin_model->get_users(user()->id);
        $data['countries'] = $this->admin_model->select_asc('country');
        $data['categories'] = $this->admin_model->get_site_categories('categories');
        $data['skills'] = $this->admin_model->get_site_skills('skills');
        $data['user_skills'] = $this->admin_model->get_skill_by_user(user()->id);
        $data['time_zones'] = $this->admin_model->select_asc('time_zone');
        $data['main_content'] = $this->load->view('admin/user/mentorship_profile', $data, TRUE);
        $this->load->view('admin/index', $data);
    }



    public function load_category_skill() 
    {
        $category_id = $this->input->post('id');

        $data = array();
        $data['skills'] = $this->admin_model->get_skill_category($category_id);
        $data['user_skills'] = $this->admin_model->get_skill_by_user(user()->id);
        $loaded=$this->load->view('admin/include/category_skill',$data,true);
        
        echo json_encode(array('st'=> 1, 'loaded'=> $loaded));
    }

    //set default language
    public function set_language()
    {
        check_status();

        if ($_POST) {

            if(!empty($this->input->post('enable_multilingual'))){$enable_multilingual = $this->input->post('enable_multilingual', true);}else{$enable_multilingual = 0;}

            $data = array(
                'enable_multilingual' => $enable_multilingual,
                'lang' => $this->input->post('language', true)
            );
            $data = $this->security->xss_clean($data);
            $this->admin_model->edit_option($data, 1, 'settings');
            $this->session->set_flashdata('msg', trans('updated-successfully')); 
            redirect(base_url('admin/language'));
        }
    }

    public function update_mentorship_profile(){

        check_status();

        $id = $this->input->post('id');


        $data = array(
            'intro_video' => $this->input->post('intro_video', true),
            'level' => $this->input->post('level', true),
            'experience_year' => $this->input->post('experience_year', true),
            'company' => $this->input->post('company', true),
            'designation' => $this->input->post('designation', true),
            'linkedin_profile' => $this->input->post('linkedin_profile', true),
            'facebook_profile' => $this->input->post('facebook_profile', true),
            'instagram_profile' => $this->input->post('instagram_profile', true),
            'x_profile' => $this->input->post('x_profile', true),
            'portfolio' => $this->input->post('portfolio', true),
            'skills' => $this->input->post('skills', true),
            //'expertise_industry' => $this->input->post('expertise_industry', true),
            'category' => $this->input->post('category', true),

            'bp_network' => $this->input->post('bp_network', true),
            'employment_status' => $this->input->post('employment_status', true),
            'mentorship_availability' => $this->input->post('mentorship_availability', true),
            'mentees_at_once' => $this->input->post('mentees_at_once', true),
        );
  
        $data = $this->security->xss_clean($data);
        $this->admin_model->edit_option($data, $id, 'users');

        $this->session->set_flashdata('msg', trans('updated-successfully')); 
        redirect(base_url('admin/settings/mentorship'));
    }


    public function schedule()
    {
        if (!is_user()) {
            redirect(base_url());
        }

        $data = array();
        $data['page'] = 'Settings';
        $data['page_title'] = 'Schedule';
        $data['user'] = $this->admin_model->get_users(user()->id);
        $data['my_days'] =$this->admin_model->get_user_days(user()->id,0);
        $data['main_content'] = $this->load->view('admin/user/schedule',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function online_meeting()
    {
        if (!is_user()) {
            redirect(base_url());
        }

        $data = array();
        $data['page'] = 'Settings';
        $data['page_title'] = 'Online Meeting';
        $data['main_content'] = $this->load->view('admin/user/online_meeting',$data,TRUE);
        $this->load->view('admin/index',$data);
    }


    public function zoom_api()
    {
        if (!is_user()) {
            redirect(base_url());
        }

        $data = array();
        $data['page'] = 'Settings';
        $data['page_title'] = 'Zoom Api';
        $data['main_content'] = $this->load->view('admin/user/zoom_api',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function set_interval() 
    {

        $data = array(
            'intervals' => $this->input->post('intervals',true),
        );
        
        $this->admin_model->edit_option($data, user()->id, 'users');
        $this->session->set_flashdata('msg', trans('updated-successfully')); 
        redirect(base_url('admin/settings/schedule'));
    }


    public function set()
    {   
        check_status();
        
        
        $this->admin_model->delete_assaign_days(user()->id, 'assaign_days');
        $this->admin_model->delete_assaign_time(user()->id, 'assign_time');

        if($_POST)
        {   

            for ($i=0; $i < 7; $i++) { 
                if(empty($this->input->post("day_".$i))){
                    $day = 0;
                }else{
                    $day = $this->input->post("day_".$i);
                }
                $data = array(
                    'user_id' => user()->id,
                    'day' => $day
                );
                $data = $this->security->xss_clean($data);
                $this->admin_model->insert($data, 'assaign_days');

                //insert times
                $start = $this->input->post("start_time_".$i);
                $end = $this->input->post("end_time_".$i);

                if ($day != 0) {
                    for ($a=0; $a < count($start); $a++) { 
                        $time_data = array(
                            'user_id' => user()->id,
                            'day_id' => $day,
                            'time' => $start[$a].'-'.$end[$a],
                            'start' => $start[$a],
                            'end' => $end[$a]
                        );
                        $time_data = $this->security->xss_clean($time_data);
                        $this->admin_model->insert($time_data, 'assign_time');
                    }
                }

            }

            $this->session->set_flashdata('msg', trans('schedule-assigned-successfully')); 
            redirect(base_url('admin/settings/schedule'));
        }      
        
    }

    public function delete_time($id)
    {
        $this->admin_model->delete($id,'assign_time'); 
        echo json_encode(array('st' => 1));
    }


    public function holidays()
    {
        if (isset($_GET['msg']) && $_GET['msg'] == 'success') {
            $this->session->set_flashdata('msg', trans('updated-successfully')); 
        }

        if (!is_user()) {
            redirect(base_url());
        }

        $holidays = json_decode(user()->holidays);

        foreach ($holidays as $value) {
            $date = $value;
            $dateTime = new DateTime($date);
            $formattedDate = $dateTime->format('Y-n-j');
            $holidays1[] = $formattedDate;
        }

        $data = array();
        $data['page'] = 'Holidays';
        $data['page_title'] = 'Holidays';
        $data['holidays'] = $holidays1;
        $data['user'] = $this->admin_model->get_users(user()->id);
        $data['main_content'] = $this->load->view('admin/user/holidays', $data, TRUE);
        $this->load->view('admin/index', $data);
    }
    
    public function add_holidays($date){
        $date = date_create($date);
        $date = date_format($date,"Y-m-d");
        $holidays = json_decode(user()->holidays, true);
      
        if (!empty($holidays)) {
            
            if (($key = array_search($date, $holidays)) !== false) {
                unset($holidays[$key]);
                $holidays = array_values($holidays);
            }else{
                array_push($holidays, $date);
            }
        } else {
            $holidays = array($date);
        }

        $data = array(
            'holidays' => json_encode($holidays)
        );
        $this->admin_model->edit_option($data, user()->id, 'users');

        $data['status'] = 1;
        die(json_encode($data));
    }

    public function mentee_profile()
    {
        if (!is_mentee()) {
            redirect(base_url());
        }
        $data = array();
        $data['page'] = 'Settings';
        $data['page_title'] = 'Mentee Profile';
        $data['user'] = $this->admin_model->get_users(user()->id);
        $data['countries'] = $this->admin_model->select_asc('country');
        $data['time_zones'] = $this->admin_model->select_asc('time_zone');
        $data['main_content'] = $this->load->view('admin/mentee/profile', $data, TRUE);
        $this->load->view('admin/index', $data);
    }

    public function update_mentee_profile(){
        
        $id = $this->input->post('id');

        $data = array(
            'name' => $this->input->post('name', true),
            'email' => $this->input->post('email', true),
            'phone' => $this->input->post('phone', true),
            'gender' => $this->input->post('gender', true),
            'country' => $this->input->post('country', true),
            'residence' => $this->input->post('residence', true),
            'mentorship_availability' => $this->input->post('mentorship_availability', true),
            'employment_status' => $this->input->post('employment_status', true),
            'membershhip_locale' => $this->input->post('membershhip_locale', true),
            'about_me' => $this->input->post('about_me', true),
            'career_goals' => $this->input->post('career_goals', true),
            'linkedin_profile' => $this->input->post('linkedin_profile', true),
            'experience_year' => $this->input->post('experience_year', true),
            'time_zone' => $this->input->post('time_zone', true),
        );
  
        $data = $this->security->xss_clean($data);
        $this->admin_model->edit_option($data, $id, 'users');

        if($_FILES['photo']['name'] != ''){
            $up_load = $this->admin_model->upload_image('1200');
            $data_img = array(
                'image' => $up_load['images'],
                'thumb' => $up_load['thumb']
            );
            $data_img = $this->security->xss_clean($data_img);
            $this->admin_model->edit_option($data_img, $id, 'users');
        }

        $this->session->set_flashdata('msg', trans('updated-successfully')); 
        redirect(base_url('admin/settings/mentee_profile'));
    }


    public function update_online_meeting(){

        $id = $this->input->post('id');

        $data = array(
            'meet_type' => $this->input->post('meet_type', true),
            'gmeet_url' => $this->input->post('gmeet_url', true)
        );
  
        $data = $this->security->xss_clean($data);
        $this->admin_model->edit_option($data, $id, 'users');

        $this->session->set_flashdata('msg', trans('updated-successfully')); 
        redirect(base_url('admin/settings/online_meeting'));
    }

    public function update_zoom_api(){

        $id = $this->input->post('id');

        $data = array(
            'zoom_account_id' => $this->input->post('zoom_account_id', true),
            'zoom_client_id' => $this->input->post('zoom_client_id', true),
            'zoom_client_secret' => $this->input->post('zoom_client_secret', true),
        );
  
        $data = $this->security->xss_clean($data);
        $this->admin_model->edit_option($data, $id, 'users');

        $this->session->set_flashdata('msg', trans('updated-successfully')); 
        redirect(base_url('admin/settings/zoom_api'));
    }


    public function test_zoom_connection(){

        if (settings()->zoom_api_user == 2) {
            $zoom_account_id = settings()->zoom_account_id; 
            $zoom_client_id = settings()->zoom_client_id; 
            $zoom_client_secret = settings()->zoom_client_secret; 
        }else{
            $zoom_account_id = user()->zoom_account_id; 
            $zoom_client_id = user()->zoom_client_id; 
            $zoom_client_secret = user()->zoom_client_secret; 
        }

        $zoom_account_id = settings()->zoom_account_id; 
        $zoom_client_id = settings()->zoom_client_id; 
        $zoom_client_secret = settings()->zoom_client_secret; 

        $this->load->library('zoom/zoom');
        $date = date('Y-m-d');
        $start_time = $date.' 10:00';

        $agenda = 'Zoom Meeting Test Connection';
        $duration = 60;
        $password = mt_rand(1111,9999);;

        $fields = array(
            'agenda'=>$agenda,
            'default_password'=>false,
            'duration'=>$duration, //minutes
            'password'=> $password,
            'start_time'=>$start_time,
            'waiting_room'=>true
        );

        $result = $this->zoom->get_meeting($zoom_account_id, $zoom_client_id, $zoom_client_secret, $fields);
        $result = json_decode($result);

        if (!empty($result->start_url)) {
            echo json_encode(array('st' => 1, 'msg' => '<i class="bi bi-check-circle"></i> Zoom api connected successfully'));
        }else{
            echo json_encode(array('st' => 2, 'msg' => '<i class="bi bi-x-circle"></i> '.$result->message));
        }

    }
    

    public function change_password()
    {
        $data = array();
        $data['page'] = 'Settings';
        $data['page_title'] = 'Change Password';
        $data['main_content'] = $this->load->view('admin/user/change_password', $data, TRUE);
        $this->load->view('admin/index', $data);
    }
    
    //change password
    public function change()
    {   
        check_status();

        if($_POST){
            
            $id = user()->id;
            $user = $this->admin_model->get_by_id($id, 'users');

            if(password_verify($this->input->post('old_pass', true), $user->password)){
                if ($this->input->post('new_pass', true) == $this->input->post('confirm_pass', true)) {
                    $data=array(
                        'password' => hash_password($this->input->post('new_pass', true))
                    );
                    $data = $this->security->xss_clean($data);
                    $this->admin_model->edit_option($data, $id, 'users');
                    echo json_encode(array('st'=>1));
                } else {
                    echo json_encode(array('st'=>2));
                }
            } else {
                echo json_encode(array('st'=>0));
            }
        }
    }


    public function update_visibility($status){

        if (!is_user()) {
            redirect(base_url());
        }

        $data = array(
            'visible_profile' => $status,
        );
  
        $data = $this->security->xss_clean($data);
        $this->admin_model->edit_option($data, user()->id, 'users');

        $this->session->set_flashdata('msg', trans('updated-successfully')); 
        redirect(base_url('admin/settings/profile'));
    }


}