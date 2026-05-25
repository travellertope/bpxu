<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends Home_Controller {

    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {   
        check_frontend();

        if(!empty($_GET['ref'])){
           $this->session->set_userdata('ref',$_GET['ref']); 
        }
        
        $data = array();
        $data['page_title'] = 'Home';
        $data['menu'] = TRUE;
        $data['features'] = $this->common_model->select_orders('product_services');
        $data['countries'] = $this->common_model->select('country');
        $data['mentors'] = $this->common_model->get_slide_mentors();
        $data['random_mentor'] = $this->common_model->get_random_mentors();
        $data['serach_mentors'] = $this->common_model->get_mentors();
        $data['testimonials'] = $this->common_model->get_testimonials();
        $data['posts'] = $this->common_model->get_home_blog_posts();
        $data['workflows'] = $this->admin_model->get_workflows();
        $data['categories'] = $this->common_model->get_categories();
        $data['brands'] = $this->common_model->get_site_brands();
        $data['count_mentors'] = $this->common_model->get_mentors_count();
        $data['count_countries'] = $this->common_model->get_countries_count();
        $data['count_bookings'] = $this->common_model->get_bookings_count();
        $data['main_content'] = $this->load->view('home'.settings()->front_layout, $data, TRUE);
        $this->load->view('index', $data);
    }
    
    //language switch function
    public function switch_lang($language = "")
    {   
        $language = ($language != "") ? $language : "english";
        $site_lang = array('site_lang' => $language);
        $this->session->set_userdata($site_lang);
        redirect($_SERVER['HTTP_REFERER']);
    }

    // site features
    public function features()
    {   
        check_frontend();

        $data = array();
        $data['page_title'] = 'Features';
        $data['menu'] = TRUE;
        $data['features'] = $this->common_model->select('features');
        $data['main_content'] = $this->load->view('features', $data, TRUE);
        $this->load->view('index', $data);
    }

    //show all mentors
    public function mentors()
    {   

        $data = array();
        //initialize pagination
        $this->load->library('pagination');
        $config['base_url'] = base_url('home/mentors');
        $total_row = $this->common_model->get_all_mentors(1 , 0, 0);
        $config['total_rows'] = $total_row;
        $config['per_page'] = 12;
        $this->pagination->initialize($config);
        $page = $this->security->xss_clean($this->input->get('page'));
        if (empty($page)) {
            $page = 0;
        }
        if ($page != 0) {
            $page = $page - 1;
        }


        //echo $_GET['search_category']; exit();
        $data['page_title'] = 'Mentors';
        $data['menu'] = TRUE;
        $data['skills'] = $this->common_model->get_skills();
        $data['mentors'] = $this->common_model->get_all_mentors(0 , $config['per_page'], $page * $config['per_page']);
        $data['categories'] = $this->admin_model->get_site_categories('categories');
        $data['countries'] = $this->common_model->select('country');
        $data['main_content'] = $this->load->view('mentors', $data, TRUE);
        $this->load->view('index', $data);
    }

    public function load_search_skills() 
    {
        $category_id = $this->input->post('id');

        $data = array();
        $skills = $this->admin_model->get_search_skills_by_category($category_id);
      
        if (empty($skills)) {
            echo '<option value="0">'.trans('no-data-found').'</option>';
        }else{
            echo '<option value="">'.trans('all').'</option>';
            foreach ($skills as $skill) { 
                echo '<option value="'.$skill->id.'">'.$skill->skill.'</option>';
            }
        }
    }

    public function mentor_search()
    {   

        $data = array();
        $data['page_title'] = 'Mentors';
        $data['menu'] = TRUE;
        $data['skills'] = $this->common_model->get_skills();
        $data['mentors'] = $this->common_model->get_mentors();
        $loaded=$this->load->view('include/mentor_item',$data,true);
        if (!empty($data['mentors'])) {
            $st = 1;
        }else{
            $st = 0;
        }
        echo json_encode(array('st'=> $st, 'loaded'=> $loaded));
    }



    //pricing plans
    public function pricing()
    {   
        $data = array();
        $data['page_title'] = 'Pricing';
        $data['menu'] = TRUE;
        $data['packages'] = $this->admin_model->get_package_features();
        $data['features'] = $this->admin_model->get_features();
        $data['faqs'] = $this->common_model->get_faqs(0);
        $data['main_content'] = $this->load->view('price', $data, TRUE);
        $this->load->view('index', $data);
    }

    //faqs
    public function faqs()
    {   
        check_frontend();

        $data = array();
        $data['page_title'] = 'Faqs';
        $data['menu'] = TRUE;
        $data['faqs'] = $this->common_model->get_faqs(0);
        $data['main_content'] = $this->load->view('faqs', $data, TRUE);
        $this->load->view('index', $data);
    }


    public function mentor($slug)
    {   
        
        $data = array();
        $data['menu'] = TRUE;
        $data['page_title'] = 'Profile';
        $data['page'] = 'Mentor';

        $data['settings'] = $this->common_model->get('settings');
        $data['mentor'] = $this->common_model->get_mentor_by_slug($slug);

        $data['total_rating'] = $this->admin_model->count_mentor_ratings($data['mentor']->id);
        $data['experiences'] = $this->common_model->get_mentor_experiences($data['mentor']->id);
        $data['educations'] = $this->common_model->get_mentor_educations($data['mentor']->id);
        //$data['skills'] = $this->common_model->get_user_skills($data['mentor']->id);
        $data['sessions'] = $this->common_model->get_user_sessions($data['mentor']->id);
        $data['blogs'] = $this->admin_model->get_admin_blogs(1,$data['mentor']->id);
        $data['main_content'] = $this->load->view('mentor_profile', $data, TRUE);
        $this->load->view('index', $data);
    }


    public function load_session($id)
    {   
        $data = array();
        $data['session'] = $this->common_model->get_by_id($id, 'sessions');

        if (empty($data['session'])) {
            redirect(base_url('error-404'));
        }

        $data['mentor'] = $this->common_model->get_single_mentor($data['session']->user_id);
        
        if ($data['session']->is_default == 2) {
            $my_days = $this->common_model->get_my_session_days($data['session']->id);
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
            $data['my_days'] = $my_days;
        }else{
            $my_days = $this->common_model->get_my_session_days_default($data['mentor']->id);
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
            $data['my_days'] = $my_days;
        }
        
        $num_days = 40;
        $currentDate = new DateTime();

        for ($i = 0; $i < $num_days; $i++) {
            $nextDate = $currentDate->modify('+1 day');
            $date = new DateTime($nextDate->format('Y-m-d'));
            $next_days[] = $nextDate->format('Y-m-d');

        }

        $holidays = json_decode($data['mentor']->holidays);

        foreach ($holidays as $value) {
            while (($key = array_search($value, $next_days)) !== false) {
                unset($next_days[$key]);
            }
        }
        
        //echo "<pre>"; print_r($assign_days); exit();

        $data['assign_days'] = $assign_days;
        $data['next_days'] = $next_days;
        $data['num_days'] = $num_days;
        $data['menu'] = TRUE;
        $data['page_title'] = 'Booking';
        $data['settings'] = $this->common_model->get('settings');
        $data['time_zones'] = $this->admin_model->select_asc('time_zone');
        $data['sessions'] = $this->common_model->get_user_sessions($data['mentor']->id);
        $loaded = $this->load->view('include/booking_session',$data,true);
        echo json_encode(array('st'=> 1,'loaded'=> $loaded));
    } 


    public function send_message_mentor() 
    {
        
        if ($_POST) {

            $data = array(
                'mgs_to' => $this->input->post('mgs_to', true),
                'mgs_time' => my_date_now(),
                'message' => nl2br(strip_tags($this->input->post('message', true))),
                'mgs_from' => $this->input->post('mgs_from', true),
            );

            $data = $this->security->xss_clean($data);
            $this->common_model->insert($data, 'messages');
            $sender = $this->common_model->get_by_id($this->input->post('mgs_from', true), 'users');
            $reciever = $this->common_model->get_by_id($this->input->post('mgs_to', true), 'users');

            // send email with updated code dynamic value
            $subject = get_email_by_slug('sent-message-notification')->subject;
            $body = get_email_by_slug('sent-message-notification')->body;
            $variables_data = [
                'sender_name'  =>$sender->name,
                'sender_email' => $sender->email
            ]; 

            $msg = preg_replace_callback('/{{(.*?)}}/', function ($matches) use ($variables_data) {
                $key = trim($matches[1]);
                return isset($variables_data[$key]) ? $variables_data[$key] : $matches[0]; 
            }, $body);
            $edata = array();
            $edata['subject'] = $subject;
            $edata['msg'] = $msg;

            $msg = $this->load->view('email_template/common', $edata, true);
            
            $response = $this->email_model->send_email($reciever->email, $subject, $msg);
            
            //echo "<pre>"; print_r($response); exit();

            $notify = array(
                'user_id' =>$this->input->post('mgs_to', true),
                'action_id' => strip_tags($this->session->userdata('id')),
                'text' => $sender->name ." sent you a message",
                'content_id' => $this->input->post('mgs_to', true),
                'noti_type' => 6,
                'noti_time' => my_date_now()
            );
            notify_this($notify);

            redirect(base_url('admin/message'));
            
        }
    }


    public function session($slug)
    {  
       
        $data = array();
        $data['session'] = $this->common_model->get_single_session($slug);

        if (empty($data['session'])) {
            redirect(base_url('error-404'));
        }

        $data['mentor'] = $this->common_model->get_single_mentor($data['session']->user_id);
        
        if ($data['session']->is_default == 2) {
            $my_days = $this->common_model->get_my_session_days($data['session']->id);
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
            $data['my_days'] = $my_days;
        }else{
            $my_days = $this->common_model->get_my_session_days_default($data['mentor']->id);
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
            $data['my_days'] = $my_days;
        }
        
        $num_days = 30;
        $currentDate = new DateTime();

        for ($i = 0; $i < $num_days; $i++) {
            $nextDate = $currentDate->modify('+1 day');
            $date = new DateTime($nextDate->format('Y-m-d'));
            $next_days[] = $nextDate->format('Y-m-d');

        }

        $holidays = json_decode($data['mentor']->holidays);

        foreach ($holidays as $value) {
            while (($key = array_search($value, $next_days)) !== false) {
                unset($next_days[$key]);
            }
        }
        
        $data['assign_days'] = $assign_days;
        $data['next_days'] = $next_days;
        $data['day_ids'] = $day_ids;
        $data['num_days'] = $num_days;
        $data['menu'] = TRUE;
        $data['page_title'] = 'Booking';
        $data['settings'] = $this->common_model->get('settings');
        $data['time_zones'] = $this->admin_model->select_asc('time_zone');
        $data['sessions'] = $this->common_model->get_user_sessions($data['mentor']->id);
        $data['main_content'] = $this->load->view('booking', $data, TRUE);
        $this->load->view('index', $data);
    }


    public function get_time($date, $session_id, $time_zone='')
    {
        
        $day = date('l', strtotime($date));
        $day_id = get_day_id($day);
        $value = array();
        $session = $this->common_model->get_by_id($session_id, 'sessions');
        $interval = $session->duration;

        if ($session->is_default == 2) {
            $value['times'] = $this->common_model->get_timeslot_by_day($day_id, $session_id);
        }else{
            $value['times'] = $this->common_model->get_timeslot_by_day_default($day_id, $session->user_id);
        }
        
        $value['session_id'] = $session_id;
        $value['mentor_id'] = $session->user_id;
        $value['day_id'] = $day_id;
        $value['date'] = $date;

        $value['user_timezone'] = get_by_id($session->user_id, 'users')->time_zone;  

        if (empty($time_zone)) {
            $value['time_zone'] = get_by_id($session->user_id, 'users')->time_zone;  
        } else {
            $value['time_zone'] = $time_zone;
        }
     
        $value['interval'] = $interval;
        $data = array();
        $data['result'] = $this->load->view('include/time_loop', $value, TRUE);

        if (empty($value['times'])) {
            $data['status'] = 0;
        } else {
            $data['status'] = 1;
        }
        die(json_encode($data));
    }


    public function booking($slug, $uid)
    {  
        if($_POST){

            $sdata = array(
                'date' => $this->input->post('date', true),
                'time' => $this->input->post('time', true),
                'time_slot_id' => $this->input->post('time_slot_id', true),
                'convert_time_slot' => $this->input->post('convert_time_slot', true)
            );
            $this->session->set_userdata($sdata);
        }

        $data = array();
        $data['menu'] = TRUE;
        $data['page_title'] = 'Booking Session';
        $data['countries'] = $this->common_model->select('country');
        $data['time_zones'] = $this->common_model->select('time_zone');
        $data['session'] = $this->common_model->get_booked_session($uid);
        $data['mentor'] = $this->common_model->get_single_mentor($data['session']->user_id);
        $data['main_content'] = $this->load->view('booking_session', $data, TRUE);
        $this->load->view('index', $data);
    }


    public function session_booking($uid)
    {  
        $data = array();
        $session = $this->common_model->get_booked_session($uid);
        $mentor = $this->common_model->get_single_mentor($session->user_id);
        $type = $this->input->post('type',true);

        if($session->type == 2){

            $date = new DateTime($this->input->post('date'));
            $date->modify('+'.$session->session_repeat.'day');
            $next_date = $date->format('Y-m-d');

            $next_recur_date = $next_date;
            $recurring_count= 1 ;
            $is_recurring= 1 ;
            $is_completed = 0;
        }else{
            $next_recur_date = '';
            $recurring_count= 0 ;
            $is_completed = 0;
        }

        if($type == 'register'){
            $mail = $this->input->post('email',true);
            $check_email = $this->auth_model->check_duplicate_email($mail);
            if ($check_email){
                $msg = trans('email-exist');
                echo json_encode(array('st'=>0, 'msg' => $msg));
                exit();
            }

            $data = array(
                'name' =>$this->input->post('name', true),
                'user_name' => str_slug($this->input->post('name', true)), 
                'email' =>$this->input->post('email', true), 
                'password' =>hash_password($this->input->post('password', true)),
                'country' =>$this->input->post('country', true),
                'time_zone' =>$this->input->post('time_zone', true),
                'role' =>'mentee',
                'created_at' => my_date_now()
            );
            $data = $this->security->xss_clean($data);
            $mentee_id = $this->admin_model->insert($data, 'users');
            $mentee = $this->common_model->get_by_id($mentee_id,'users');
        }else{


            if (check_auth() == true) {
                $mentee = user();
                $mentee_id = $mentee->id;
            }else{
                $user_name = $this->input->post('user_name', true);
                $mentee = $this->common_model->check_customer_email($user_name);
                if ($mentee->status != 1) {
                    $msg = trans('your-account-is-deactive');
                    echo json_encode(array('st'=>0, 'msg' => $msg)); exit();
                }
                if (!empty($mentee)) {
                    $mentee_id = $mentee->id;
                } else {
                    $msg = trans('incorrect-username-or-password');
                    echo json_encode(array('st'=>0, 'msg' => $msg)); exit();
                }

                if(!password_verify($this->input->post('password', true), $mentee->password)){
                    $msg = trans('incorrect-username-or-password');
                    echo json_encode(array('st'=>0, 'msg' => $msg)); exit();
                }
            }
            
        }

        $data = array(
            'id' => $mentee->id,
            'name' => $mentee->name,
            'slug' => $mentee->slug,
            'thumb' => $mentee->thumb,
            'email' =>$mentee->email,
            'role' =>$mentee->role,
            'logged_in' => TRUE
        );
        $data = $this->security->xss_clean($data);
        $this->session->set_userdata($data);

        $code = random_string('numeric', 8);
        $code = ltrim($code, '0');
        $bdata = array(
            'mentee_id' => $mentee_id,
            'session_id' => $session->id,
            'user_id'=>$session->user_id,
            'booking_number'=>$code,
            'price' => get_by_id($session->id,'sessions')->price,
            'duration' => get_by_id($session->id,'sessions')->duration,
            'date' =>$this->input->post('date', true),
            'time' =>$this->input->post('time', true),
            'payment_status' => 0,
            'status' => 0,
            'is_recurring' => $is_recurring,
            'recurring_count' => $recurring_count,
            'next_recur_date' => $next_recur_date, 
            'is_completed' => $is_completed,
            'created_at' => my_date_now()
        );
        $bdata = $this->security->xss_clean($bdata);
        $booking_id = $this->admin_model->insert($bdata, 'session_booking');
        $time_id =$this->input->post('time_slot_id', true);
        $time = $this->admin_model->get_by_id($time_id, 'assign_time');

        if ($session->enable_group_booking == 1) {
            $gdata = array(
                'person_per_slot' => $time->person_per_slot - 1,
            );
            $this->admin_model->edit_option($gdata, $time_id, 'assign_time');
        }

        $single_booking = $this->admin_model->get_by_id($booking_id, 'session_booking');

        $session_name = $this->admin_model->get_by_id($single_booking->session_id, 'sessions')->name;
        $mentee_name = $this->admin_model->get_by_id($single_booking->mentee_id, 'users')->name;
        $mentor = $this->admin_model->get_by_id($single_booking->user_id, 'users');


        // insert notification
        $notify = array(
            'user_id' => $session->user_id,
            'action_id' =>$single_booking->mentee_id,
            'content_id' =>$single_booking->booking_number,
            'text' => $mentee_name." Just booked your session: " .'"'.$session_name.'"',
            'noti_type' => 2,
            'noti_time' => my_date_now()
        );
        $notify = $this->security->xss_clean($notify);
        $this->common_model->insert($notify, 'notifications');

        $notify = array(
            'user_id' => $single_booking->mentee_id,
            'action_id' =>$single_booking->mentee_id,
            'content_id' =>$single_booking->booking_number,
            'text' => "You have booked a session: " .'"'.$session_name.'" of ' .$mentor->name.' just now',
            'noti_type' => 2,
            'noti_time' => my_date_now()
        );
        $notify = $this->security->xss_clean($notify);
        $this->common_model->insert($notify, 'notifications');




        // send email with updated code dynamic value
        $booking_number_text = trans('booking-number').': #'.$code;
        $subject = get_email_by_slug('session-booking-confirmation-mentor')->subject;
        $body = get_email_by_slug('session-booking-confirmation-mentor')->body;
        $variables_data = [
            'mentee_name'  =>$mentee_name,
            'session_name' => $session_name,
            'date' => $this->input->post('date', true),
            'time' => $this->input->post('time', true),
            'booking_number' => $booking_number_text
        ]; 

        $msg = preg_replace_callback('/{{(.*?)}}/', function ($matches) use ($variables_data) {
            $key = trim($matches[1]);
            return isset($variables_data[$key]) ? $variables_data[$key] : $matches[0]; 
        }, $body);
        $edata = array();
        $edata['subject'] = $subject;
        $edata['msg'] = $msg;

        $msg = $this->load->view('email_template/common', $edata, true);
        $response = $this->email_model->send_email($mentor->email, $subject, $msg);




        // send email with updated code dynamic value
        $subject = get_email_by_slug('session-booking-confirmation-mentee')->subject;
        $body = get_email_by_slug('session-booking-confirmation-mentee')->body;
        $variables_data = [
            'session_name' => $session_name,
            'mentor_name'  =>$mentor->name,
            'date' => $this->input->post('date', true),
            'time' => $this->input->post('time', true),
            'booking_number' => $booking_number_text,
        ]; 

        $msg = preg_replace_callback('/{{(.*?)}}/', function ($matches) use ($variables_data) {
            $key = trim($matches[1]);
            return isset($variables_data[$key]) ? $variables_data[$key] : $matches[0]; 
        }, $body);

        $edata = array();
        $edata['subject'] = $subject;
        $edata['msg'] = $msg;

        $msg = $this->load->view('email_template/common', $edata, true);
        $response = $this->email_model->send_email($mentee->email, $subject, $msg);





        
        echo json_encode(array('st' => 1, 'url'=> base_url('admin/sessions/booking_details/'.$single_booking->booking_number)));
    }


    //purchase function
    public function purchase($payment_id)
    {   
        $data = array();
        $data['menu'] = TRUE;
        $data['payment'] = $this->common_model->get_payment($payment_id);
        $data['payment_id'] = $payment_id;  
        $data['package'] = $this->common_model->get_package_by_slug($data['payment']->package);
        $this->load->view('purchase', $data);
    }

    //send contact messages
    public function send_message()
    {     
        if ($_POST) {
            $data = array(
                'business_id' =>0,
                'name' => $this->input->post('name', true),
                'email' => $this->input->post('email', true),
                'message' => $this->input->post('message', true),
                'created_at' => my_date_now()
            );
            $data = $this->security->xss_clean($data);
            if (!$this->recaptcha_verify_request()) {
                $this->session->set_flashdata('error', trans('recaptcha-is-required')); 
            } else {
                $this->common_model->insert($data, 'contacts');
                $this->session->set_flashdata('msg', trans('send-successfully'));


                // $subject = 'Contact Message from '. $this->input->post('name', true);
                // $msg = $this->input->post('message', true) . '<br>'. 'Sender Name : '.$this->input->post('name', true).'<br>'. 'Sender Email : '.$this->input->post('email', true);

                // $response = $this->email_model->send_email(settings()->admin_email, $subject, $msg);



                // send email with updated code dynamic value

                $subject = get_email_by_slug('contact-submit-admin')->subject;
                $body = get_email_by_slug('contact-submit-admin')->body;
                $variables_data = [
                    'message'  =>$this->input->post('message', true),
                    'sender_name' => $this->settings->site_name,
                    'sender_email' => $this->input->post('email', true),
                ]; 

                $msg = preg_replace_callback('/{{(.*?)}}/', function ($matches) use ($variables_data) {
                    $key = trim($matches[1]);
                    return isset($variables_data[$key]) ? $variables_data[$key] : $matches[0]; 
                }, $body);

                $edata = array();
                $edata['subject'] = $subject;
                $edata['msg'] = $msg;

                $msg = $this->load->view('email_template/common', $edata, true);
                $response = $this->email_model->send_email(settings()->admin_email, $subject, $msg);



           
            }
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    //contact page
    public function contact()
    {   
        check_frontend();

        $data = array();
        $data['menu'] = TRUE;
        $data['page_title'] = 'Contact';
        $data['settings'] = $this->common_model->get('settings');
        $data['main_content'] = $this->load->view('contact', $data, TRUE);
        $this->load->view('index', $data);
    }

    //pages
    public function page($slug)
    {   
        check_frontend();

        $data = array();
        $data['page_title'] = 'Pages';
        $data['menu'] = TRUE;
        $data['page'] = $this->common_model->get_single_page($slug);
        if (empty($data['page'])) {
            redirect(base_url());
        }
        $data['page_name'] = $data['page']->title;
        $data['main_content'] = $this->load->view('page', $data, TRUE);
        $this->load->view('index', $data);
    }

    public function terms()
    {   
        $data = array();
        $data['page_title'] = 'Terms of Service';
        $data['menu'] = TRUE;
        $data['main_content'] = $this->load->view('terms', $data, TRUE);
        $this->load->view('index', $data);
    }

    //blog posts
    public function blogs()
    {   
        check_frontend();

        $data = array();
        $this->load->library('pagination');
        $config['base_url'] = base_url('blogs');
        $total_row = $this->common_model->get_site_blog_posts(1 , 0, 0);
        $config['total_rows'] = $total_row;
        $config['per_page'] = 9;
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
        $data['posts'] = $this->common_model->get_site_blog_posts(0 , $config['per_page'], $page * $config['per_page']);
        $data['categories'] = $this->common_model->get_blog_categories();
        $data['main_content'] = $this->load->view('blog_posts', $data, TRUE);
        $this->load->view('index', $data);
    }

    //category by blogs
    public function category($slug)
    {   
        check_frontend();

        $data = array();
        $slug = $this->security->xss_clean($slug);
        $category = $this->common_model->get_category_by_slug($slug);
        
        if (empty($category)) {
            redirect(base_url('blog'));
        }

        $this->load->library('pagination');
        $config['base_url'] = base_url('category/'.$slug);
        $total_row = $this->common_model->get_category_posts(1 , 0, 0, $category->id);
        $config['total_rows'] = $total_row;
        $config['per_page'] = 9;
        $this->pagination->initialize($config);
        $page = $this->security->xss_clean($this->input->get('page'));
        if (empty($page)) {
            $page = 0;
        }
        if ($page != 0) {
            $page = $page - 1;
        }
        
        $data['page_title'] = 'Category Posts';
        $data['menu'] = TRUE;
        $data['title'] = $category->name;
        $data['posts'] = $this->common_model->get_category_posts(0, $config['per_page'], $page * $config['per_page'], $category->id);
        $data['categories'] = $this->common_model->get_blog_categories();
        $data['main_content'] = $this->load->view('blog_posts', $data, TRUE);
        $this->load->view('index', $data);
    }

    //blog details
    public function post_details($slug)
    {   

        $data = array();
        $slug = $this->security->xss_clean($slug);
        $data['page_title'] = 'Post details';
        $data['menu'] = TRUE;
        $data['page'] = 'Post';
        $data['post'] = $this->common_model->get_post_details($slug);

        if (empty($data['post'])) {
            redirect(base_url());
        }
        $category_id = $data['post']->category_id;
        $post_id = $data['post']->id;
        $data['post_id'] = $post_id;

        $data['comments'] = $this->common_model->get_comments_by_post($data['post']->id);
        $data['total_comment'] = count($data['comments']);
        $data['tags'] = $this->common_model->get_post_tags($post_id);
        $data['main_content'] = $this->load->view('single_post', $data, TRUE);
        $this->load->view('index', $data);
    }

    public function post($mentor, $slug)
    {   

        $data = array();
        $slug = $this->security->xss_clean($slug);
        $data['page_title'] = 'Post';
        $data['menu'] = TRUE;
        $data['page'] = 'Post';
        $data['post'] = $this->common_model->get_mentor_post($slug);
        $data['mentor'] = $this->common_model->get_by_id($data['post']->user_id,'users');
        //echo "<pre>"; print_r($data['mentor']);exit();
        $data['main_content'] = $this->load->view('single_mentor_post', $data, TRUE);
        $this->load->view('index', $data);
    }

    //blog post comment
    public function send_comment($post_id)
    {     
        if ($_POST) {
            $data = array(
                'post_id' => $post_id,
                'name' => $this->input->post('name', true),
                'email' => $this->input->post('email', true),
                'message' => $this->input->post('message', true),
                'created_at' => my_date_now()
            );
            $data = $this->security->xss_clean($data);
            $this->common_model->insert($data, 'comments');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    

    public function demo()
    {  
        $this->load->view('demo');
    }

    //If url not found
    public function error_404()
    {
        $data['page_title'] = "Error 404";
        $data['description'] = "Error 404";
        $data['keywords'] = "error,404";
        $this->load->view('error_404');
    }


}