<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sessions extends Home_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!is_user() && !is_mentee() && !is_admin()) {
            redirect(base_url());
        }
        $this->load->library('zoom/zoom');
    }

    public function index()
    {
       
        $skills = explode(',', user()->skills);

        $data = array();
        $data['page_title'] = 'Session';
        $data['page'] = 'Session';
        $data['session'] = FALSE;
        $data['working_days'] = $this->admin_model->get_user_days_active(user()->id);
        $data['skills'] = $skills;
        $data['sessions'] = $this->admin_model->get_user_sessions(user()->id);
        $data['main_content'] = $this->load->view('admin/user/sessions',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function add()
    {   
        
        check_status();
        
        if($_POST)
        {   

            // $count_session = $this->admin_model->count_free_session(user()->id);
            // if ($count_session > 0 && $this->input->post('price') == 0) {
            //     $this->session->set_flashdata('error', trans('free-session-limit'));
            //     redirect(base_url('admin/sessions'));
            // }

            $check_slug = check_session_slug(str_slug($this->input->post('name', true)));
            $random_slug_code = random_string('numeric', 3);
            if($check_slug == 1){
                $slug = str_slug($this->input->post('name', true)).'-'.$random_slug_code; 
            }else{
                $slug = str_slug($this->input->post('name', true));
            }

            $id = $this->input->post('id', true);

            $is_default = 1;
            if (empty($this->input->post('is_default'))) {
                $is_default = 1;
            }else{
                $is_default = $this->input->post('is_default');
            }
            
            $data=array(
                'user_id' =>user()->id,
                'type' => $this->input->post('type',true),
                'name' => $this->input->post('name',true),
                'slug' => $slug,
                'uid' => random_string('numeric', 8),
                'details' => $this->input->post('details',true),
                'duration' => $this->input->post('duration',true),
                'price' => $this->input->post('price',true),
                'total_slot' => $this->input->post('total_slot',true),
                'slot_for' => $this->input->post('slot_for',true),
                'session_number' => $this->input->post('session_number',true),
                'session_repeat' => $this->input->post('session_repeat',true),
                'skill' => $this->input->post('skill',true),
                'allow_session' => $this->input->post('allow_session',true),
                'is_public' => $this->input->post('is_public',true),
                'enable_group_booking' => $this->input->post('enable_group_booking',true),
                'group_booking_slot' => $this->input->post('group_booking_slot',true),
                'is_default' =>$is_default,
                'intro_video' =>$this->input->post('intro_video',true),
                'status' => $this->input->post('status',true),
                'created_at' =>my_date_now()
            );
            $data = $this->security->xss_clean($data);
            //echo "<pre>"; print_r($data); exit();
            if ($id != '') {

                if ($is_default == 2) {
                    $this->admin_model->delete_session_assaign_days(user()->id, $id, 'assaign_days');
                    $this->admin_model->delete_session_assaign_time(user()->id, $id, 'assign_time');
                }
                

                $this->admin_model->edit_option($data, $id, 'sessions');
                $this->session->set_flashdata('msg', trans('updated-successfully')); 
            } else {
                $id = $this->admin_model->insert($data, 'sessions');
                $this->session->set_flashdata('msg', trans('inserted-successfully')); 
            }

            
            $session = $this->admin_model->get_by_id($id,'sessions');

            if($is_default == 2){
                for ($i=0; $i < 7; $i++) { 
                    if(empty($this->input->post("day_".$i))){
                        $day = 0;
                    }else{
                        $day = $this->input->post("day_".$i);
                    }
                    $data = array(
                        'user_id' => user()->id,
                        'session_id' => $id,
                        'day' => $day
                    );
                    $data = $this->security->xss_clean($data);
                    $this->admin_model->insert($data, 'assaign_days');

                    //insert times

                    $start = $this->input->post("start_time_".$i);
                    $end = $this->input->post("end_time_".$i);

                    if ($day != 0) {

                        $phpversion = phpversion();
                        if ($phpversion <= 8.0) {
                            for ($a=0; $a < count($start); $a++) { 
                                $time_data = array(
                                    'user_id' => user()->id,
                                    'session_id' => $id,
                                    'day_id' => $day,
                                    'time' => $start[$a].'-'.$end[$a],
                                    'start' => $start[$a],
                                    'end' => $end[$a],
                                    'person_per_slot' =>$session->group_booking_slot,
                                );
                                $time_data = $this->security->xss_clean($time_data);
                                $this->admin_model->insert($time_data, 'assign_time');
                            }
                        }else{
                            $time_count = isset($start) && is_array($start) ? count($start) : 0;
                            for ($a=0; $a < $time_count; $a++) {
                                $time_data = array(
                                    'user_id' => user()->id,
                                    'session_id' => $id,
                                    'day_id' => $day,
                                    'time' => $start[$a].'-'.$end[$a],
                                    'start' => $start[$a],
                                    'end' => $end[$a],
                                    'person_per_slot' =>$session->group_booking_slot,
                                );
                                $time_data = $this->security->xss_clean($time_data);
                                $this->admin_model->insert($time_data, 'assign_time');
                            }
                        }
                    }

                } 

            }

            if($_FILES['photo']['name'] != ''){
                $up_load = $this->admin_model->upload_image('1200');
                $data_img = array(
                    'image' => $up_load['images'],
                    'thumb' => $up_load['thumb']
                );
                $data_img = $this->security->xss_clean($data_img);
                $this->admin_model->edit_option($data_img, $id, 'sessions');
            }
            
            redirect(base_url('admin/sessions'));

        }      
        
    }
    

    public function edit($id)
    {  
        $skills = explode(',', user()->skills);
        $data = array();
        $data['page_title'] = 'Edit';
        $data['page'] = 'Session';
        $data['skills'] = $skills;
        $data['session'] = $this->admin_model->get_by_id($id, 'sessions');
        $data['working_days'] =$this->admin_model->get_user_days_active(user()->id);
        $data['my_days'] =$this->admin_model->get_user_days(user()->id,$id);
        $data['main_content'] = $this->load->view('admin/user/sessions',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function delete($id)
    {
        $this->admin_model->delete($id,'sessions'); 
        echo json_encode(array('st' => 1));
    }



    public function booking($session_id='')
    {
        $type = user()->role;
        
        $data = array();
        $data['page_title'] = 'Session Booking';
        $data['booking'] = FALSE;
        $data['type'] =  $type;
        $total_row = $this->admin_model->get_all_bookings($type, user()->id, $total=1, 0, 0);
        $config['total_rows'] = $total_row;
        $config['per_page'] = 10 ;
        $this->pagination->initialize($config);
        $page = $this->security->xss_clean($this->input->get('page'));
        if (empty($page)) {
            $page = 0;
        }
        if ($page != 0) {
            $page = $page - 1;
        }
        $data['bookings']=$this->admin_model->get_all_bookings($type, user()->id, $total=0, $config['per_page'], $page * $config['per_page']);
        $data['sessions'] = $this->common_model->get_user_sessions(user()->id);
        $data['mentees'] = $this->common_model->get_all_mentees();
        $data['user_mentees'] = $this->common_model->get_all_user_mentees(user()->id);
        $data['mentee_mentors'] = $this->common_model->get_all_mentee_mentors(user()->id);
        $data['main_content'] = $this->load->view('admin/user/booking',$data,TRUE);
        $this->load->view('admin/index',$data);
    }


    public function booking_payment($booking_id)
    {
        $data = array();
        $data['page_title'] = 'Booking Payment';
        $data['booking'] = $this->admin_model->get_by_md5_id($booking_id, 'session_booking');
        $mercado = $this->mercado_api_link($booking_id);
        $data['init'] = $mercado['init'];
        $data['main_content'] = $this->load->view('admin/user/payment',$data,TRUE);
        $this->load->view('admin/index',$data);
    }


    public function mercado_api_link($booking_id){

        $booking = $this->admin_model->get_by_md5_id($booking_id, 'session_booking');
        $user = $this->admin_model->get_by_id($booking->user_id, 'users');
        $this->session->set_userdata('booking_id', $booking->id);
      
        $mercado_token = settings()->mercado_token;
        $mercado_currency = settings()->mercado_currency;
        
        $coupon = check_coupon_mentee($booking->session_id, $booking->mentee_id);
        if(empty($coupon)){
          $session = $this->admin_model->get_by_id($booking->session_id, 'sessions');
          $totalCost = $booking->price;
        }else{
          $discount = $coupon->discount;
          $discount_amount = ($booking->price * $discount)/ 100 ;
          $totalCost = $booking->price - $discount_amount;
        }
       
        $data = [];
        MercadoPago\SDK::setAccessToken($mercado_token);
        $preference = new MercadoPago\Preference();
        // Create a preference item
        $item = new MercadoPago\Item();
        $item->title = 'Booking Payment - '.$booking->booking_number;
        $item->quantity = 1;
        $item->unit_price = $totalCost;
        $item->currency_id = $mercado_currency;
        $preference->items = array($item);
        $preference->back_urls = array(
            "success" => base_url("admin/payment/mercado"),
            "failure" => base_url("admin/payment/mercado"),
            "pending" => base_url("admin/payment/mercado")
        );
        $preference->auto_return = "approved";

        $preference->save();
        $data['f_id'] = $preference->id;
        $data['init'] = $preference->init_point;
        return $data;   
    }



    public function get_session_calendar($session_id)
    {
        $type = user()->role;
        $mentor_id = user()->id;

        $data = array();

        $data['session'] =  $this->common_model->get_single_session_by_id($session_id);
        
        if (isset($data['session']->is_default) && $data['session']->is_default == 2) {
            $my_days = $this->common_model->get_my_session_days($session_id);
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
            $my_days = $this->common_model->get_my_session_days_default($mentor_id);
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

        $holidays = json_decode(user()->holidays);


        foreach ($holidays as $value) {
            $date = $value;
            $dateTime = new DateTime($date);
            $formattedDate = $dateTime->format('Y-n-j');
            $holidays1[] = $formattedDate;
        }


        //echo "<pre>"; print_r($holidays1); exit();
        $data['holidays'] = $holidays1;
        $data['session_id'] = $data['session']->id;
        $data['page_title'] = 'Session Booking';
        $loaded=$this->load->view('admin/include/session_booking',$data,true);

        echo json_encode(array('st'=>1, 'loaded'=>$loaded));

        
    }


    public function booking_add()
    {   
        
        check_status();
        
        if($_POST)
        {   
            $id = $this->input->post('id', true);

            $session = $this->admin_model->get_by_id($this->input->post('session_id',true), 'sessions');
            $mentor = $this->admin_model->get_by_id($session->user_id, 'users');
            $mentee = $this->admin_model->get_by_id($this->input->post('mentee_id',true), 'users');
            $booking_number = random_string('numeric', 8);
            $booking_number = ltrim($booking_number, '0');
            
            $data=array(
                'user_id' =>user()->id,
                'session_id' => $this->input->post('session_id',true),
                'mentee_id' => $this->input->post('mentee_id',true),
                'booking_number' => $booking_number,
                'note' => $this->input->post('notes',true),
                'duration' => $session->duration,
                'price' => $session->price,
                'date' => $this->input->post('date',true),
                'time' => $this->input->post('time',true),
                'status' => 0,
                'created_at' =>my_date_now()
            );
            $data = $this->security->xss_clean($data);

            if ($id != '') {
                $this->admin_model->edit_option($data, $id, 'session_booking');
                $this->session->set_flashdata('msg', trans('updated-successfully')); 
            } else {
                $id = $this->admin_model->insert($data, 'session_booking');
                $this->session->set_flashdata('msg', trans('inserted-successfully')); 
            }

            $notify = array(
                'user_id' => $this->input->post('mentee_id',true),
                'action_id' =>$this->input->post('mentee_id',true),
                'content_id' =>$booking_number,
                'text' => "you have booked a session : " .'"'.$session->name.'" of ' .$mentor->name.'just now',
                'noti_type' => 2,
                'noti_time' => my_date_now()
            );
            $notify = $this->security->xss_clean($notify);
            $this->common_model->insert($notify, 'notifications');

            // $subject =trans('appointment-confirmation').' - '.$mentor->name;
            // $msg = "you have booked a session : " .'"'.$session->name.'" of ' .$mentor->name.'on '.$this->input->post('date', true).' at '.$this->input->post('time', true);
            // $edata = array();
            // $edata['subject'] = $subject;
            // $edata['message'] = $msg;
            // $message = $this->load->view('email_template/appointment', $edata, true);
            // $response = $this->email_model->send_email($mentee->email, $subject, $message);



            // send email with updated code dynamic value
            $booking_number_text = trans('booking-number').': #'.$booking_number;
            $subject = get_email_by_slug('session-booking-confirmation-mentee')->subject;
            $body = get_email_by_slug('session-booking-confirmation-mentee')->body;
            $variables_data = [
                'session_name' => $session->name,
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





            redirect(base_url('admin/sessions/booking'));

        }      
        
    }


    public function booking_edit($id)
    {  
        $data = array();
        $data['page_title'] = 'Edit';
        $data['page'] = 'Session Booking';
        $data['sessions'] = $this->common_model->get_user_sessions(user()->id);
        $data['mentees'] = $this->common_model->get_all_mentees(); 
        $data['booking'] = $this->admin_model->get_by_id($id, 'session_booking');
        $data['main_content'] = $this->load->view('admin/user/booking',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function booking_details($booking_id){
        
        $data = array();
        $data['page_title'] = 'Booking Details';
        $data['booking'] = FALSE;
        $data['booking'] = $this->admin_model->get_single_booking($booking_id);
        $data['session'] = $this->admin_model->get_by_id($data['booking']->session_id, 'sessions');
        $data['main_content'] = $this->load->view('admin/user/booking_details',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function status_update($status, $id) 
    {
        $data = array(
            'status' => $status
        );
        $this->admin_model->update($data, $id, 'session_booking');
        $booking = $this->admin_model->get_by_id($id,'session_booking');
        $session_name = $this->admin_model->get_by_id($booking->session_id,'sessions')->name;
        $mentor_name = $this->admin_model->get_by_id($booking->user_id,'users')->name;
        $mentee_email = $this->admin_model->get_by_id($booking->mentee_id,'users')->email;
        $mentee = $this->admin_model->get_by_id($booking->mentee_id,'users');
        
        
        if($status == 1){
            $text = $mentor_name .' approved your booked session : ' .'"'.$session_name.'"';
            $status = 'Approved';
        }
        if($status == 2){
            $text = $mentor_name .' rejected your booked session : ' .'"'.$session_name.'"';
            $status = 'Reject';
        }
        if($status == 3){
            $text = 'The session you have booked : ' .'"'.$session_name.'" '.'has been completed';
            $status = 'Complete';
        }

        $notify = array(
            'user_id' => $booking->mentee_id,
            'action_id' =>$booking->user_id,
            'content_id' =>$booking->booking_number,
            'text' => $text,
            'noti_type' => 3,
            'noti_time' => my_date_now()
        );
        $notify = $this->security->xss_clean($notify);
        $this->common_model->insert($notify, 'notifications');


        



         // send email with updated code dynamic value

        $subject = get_email_by_slug('session-booking-update-mentee')->subject;
        $body = get_email_by_slug('session-booking-update-mentee')->body;
        $variables_data = [
            'mentee_name'  =>$mentee->name,
            'session_name' => $session_name,
            'date' => $booking->date,
            'time' => $booking->time,
            'status_text' => $status,
        ]; 

        $msg = preg_replace_callback('/{{(.*?)}}/', function ($matches) use ($variables_data) {
            $key = trim($matches[1]);
            return isset($variables_data[$key]) ? $variables_data[$key] : $matches[0]; 
        }, $body);
        //echo "<pre>"; print_r($msg); exit();
        $edata = array();
        $edata['subject'] = $subject;
        $edata['msg'] = $msg;

        $msg = $this->load->view('email_template/common', $edata, true);
        $response = $this->email_model->send_email($mentee->email, $subject, $msg);



        echo json_encode(array('st' => 1));
    }


    public function booking_delete($id)
    {
        $this->admin_model->delete($id,'session_booking'); 
        echo json_encode(array('st' => 1));
    }

    public function booking_cancell($id) 
    {
        $data = array(
            'status' => 4
        );
        $this->admin_model->update($data, $id, 'session_booking');
        redirect($_SERVER['HTTP_REFERER']);
    }


    public function check_coupon($dcode, $booking_id) 
    {
        
        $booking = $this->admin_model->get_by_id($booking_id, 'session_booking');
        $session = $this->admin_model->get_by_id($booking->session_id, 'sessions');
        $coupon =  $this->admin_model->get_discount($dcode, $session->id, $session->user_id);


        if(empty($coupon)){
            echo json_encode(array('st' => 0, 'msg' => '<i class="fas fa-times-circle"></i> '.'Invalid Code')); exit();
        }else{

            if (date('Y-m-d') >= $coupon->start_date && date('Y-m-d') <= $coupon->end_date) {
                
                if ($coupon->usages_limit == 0) {
                    echo json_encode(array('st' => 0, 'msg' => '<i class="fas fa-times-circle"></i> '.'This coupon code has no limit')); exit();
                }

                if ($coupon->once_per_mentee == 1) {
                    $check = $this->admin_model->check_discount_apply($dcode, $session->id, $session->user_id, $booking->mentee_id);
                    if (isset($check)) {
                        echo json_encode(array('st' => 0, 'msg' => '<i class="fas fa-times-circle"></i> '.trans('already-applied-code'))); exit();
                    }
                }

                $price = $session->price;

                $data = array(
                    'code' => $dcode,
                    'coupon_id' => $coupon->id,
                    'discount' => $coupon->discount,
                    'session_id' => $session->id,
                    'user_id' => $session->user_id,
                    'session_id' => $booking->session_id,
                    'mentee_id' => $booking->mentee_id,
                    'created_at' => my_date_now()
                );
                $this->admin_model->insert($data, 'coupon_apply');

                //update coupon
                $discount_data = array(
                    'usages_limit' => $coupon->usages_limit - 1,
                    'used' => $coupon->used + 1
                );
                $this->admin_model->edit_option($discount_data, $coupon->id, 'coupons');
                echo json_encode(array('st' => 1, 'discount' => $coupon->discount, 'total_price' => $price, 'msg' => '<i class="fas fa-check-circle"></i> '.trans('coupon-applied-successfully')));

            }else{
                echo json_encode(array('st' => 0, 'msg' => '<i class="fas fa-times-circle"></i> '.trans('invalid-code')));
            }

        }
        
    }


    public function sync($booking_id)
    {  
        $this->session->set_userdata('booking_id', $booking_id);
        redirect(base_url('googlecalendar/login'));
    }


    public function add_review()
    {   
        
        check_status();
        
        if($_POST)
        {   
            $id = $this->input->post('booking_id',true);
            $booking = $this->common_model->get_by_id($id, 'session_booking');

            $data=array(
                'user_id' =>$booking->user_id,
                'session_id' =>$booking->session_id,
                'mentee_id' => $booking->mentee_id,
                'booking_id' => $id,
                'feedback' =>$this->input->post('feedback',true),
                'rating' => $this->input->post('rating',true),
                'created_at' =>my_date_now()
            );
            $data = $this->security->xss_clean($data);
            $id = $this->admin_model->insert($data, 'reviews');
            $this->session->set_flashdata('msg', trans('inserted-successfully'));

            $rating = $this->admin_model->get_by_id($id, 'reviews');
            $session_name = $this->admin_model->get_by_id($booking->session_id, 'sessions')->name;
            $mentee_name = $this->admin_model->get_by_id($booking->mentee_id, 'users')->name;
            // insert notification

            $notify = array(
                'user_id' => $booking->user_id,
                'action_id' =>$booking->mentee_id,
                'content_id' => 0,
                'text' => $mentee_name."gives ".$rating->rating ." star rating on your session : " .'"'.$session_name.'"',
                'noti_type' => 5,
                'noti_time' => my_date_now()
            );
            $notify = $this->security->xss_clean($notify);
            $this->common_model->insert($notify, 'notifications'); 
            
            redirect(base_url('admin/sessions/booking'));

        }      
        
    }


    public function add_meeting($id){

        if (settings()->zoom_api_user == 2) {
            $zoom_account_id = settings()->zoom_account_id; 
            $zoom_client_id = settings()->zoom_client_id; 
            $zoom_client_secret = settings()->zoom_client_secret; 
        }else{
            $zoom_account_id = user()->zoom_account_id; 
            $zoom_client_id = user()->zoom_client_id; 
            $zoom_client_secret = user()->zoom_client_secret; 
        }
        
        $appointment = $this->db->get_where('session_booking',['id'=>$id])->row();
        $mentee = $this->db->get_where('users',['id'=>$appointment->mentee_id])->row();
        $mentor = $this->db->get_where('users',['id'=>$appointment->user_id])->row();

        $time = explode('-', $appointment->time);
        $date = date('Y-m-d');

        if(count($time) && $appointment->date != null ){
            $time1 = $time[0];
            $time2 = $time[1];
        }

        $start_time = $date.' '.$time1;

        $agenda = 'Zoom Meeting Session with - '.$mentor->name;
        $duration = $appointment->duration;
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

        $mentees['attendees'] = array(
            0 => array('name'=> $mentee->name)
        );

        $invitation = $this->zoom->send_invitation($zoom_account_id, $zoom_client_id, $zoom_client_secret, $result->id, $mentees);

        $host_url = $result->start_url;
        $join_url = null;

        if(isset($invitation->attendees)){
            $join_url = $invitation->attendees[0]->join_url;
        }


        $this->db->where('id',$id);
        $this->db->update('session_booking',['join_url'=>$join_url,'host_url'=>$host_url,'zoom_password'=>$password]);

        $this->session->set_flashdata('msg','Meeting added successfully'); 
        redirect(base_url('admin/sessions/booking'));

    }



    public function zoom($id){
       
        $edit_data = array(
            'is_start' => 1
        );
        if ($id != 0) {
            $this->admin_model->edit_option($edit_data, $id, 'session_booking');
        }
        redirect($_SERVER['HTTP_REFERER']);
        
    }


    public function meet($id){
       
        $edit_data = array(
            'is_start' => 1
        );
        if ($id != 0) {
            $this->admin_model->edit_option($edit_data, $id, 'session_booking');
        }
        redirect($_SERVER['HTTP_REFERER']);
        
    }



    public function cancel_meeting($id)
    {
        $edit_data = array(
            'is_start' => 0
        );
        if ($id != 0) {
            $this->admin_model->edit_option($edit_data, $id, 'session_booking');
        }
        $this->session->set_flashdata('msg', trans('meeting-canceled-successfully')); 
        redirect($_SERVER['HTTP_REFERER']);
    }



    public function mentee_details($id)
    {
        $data = array();
        $data['page'] = 'Sessions';   
        $data['page_title'] = 'Details';   
        $data['mentee'] = $this->admin_model->get_by_id($id, 'users');
        $data['sessions'] = $this->admin_model->get_mentee_sessions($data['mentee']->id);
        $data['main_content'] = $this->load->view('admin/user/mentee_details',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function mentee_profile($id)
    {
        $data = array();
        $data['page'] = 'Sessions';   
        $data['page_title'] = 'Profile';   
        $data['mentee'] = $this->admin_model->get_by_id($id, 'users');
        $data['main_content'] = $this->load->view('admin/user/mentee_profile',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function booking_calendars()
    {
        $data = array();
        $data['page_title'] = 'Calendars';
        $data['bookings']=$this->admin_model->get_all_booking_calendars(user()->id);
        $data['main_content'] = $this->load->view('admin/user/calendars',$data,TRUE);
        $this->load->view('admin/index',$data);
    }


}
    

