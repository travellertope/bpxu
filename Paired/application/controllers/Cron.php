<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cron extends Home_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    //cron job actions
    public function action()
    {   
        
        
        $recurr_sessions = $this->admin_model->get_recurr_session_by_date();
        if(!empty($recurr_sessions)){
            foreach ($recurr_sessions as $value) {

                $rdata = array(
                    'next_recur_date' => '1971-01-01',
                );

                $this->admin_model->edit_option($rdata, $value->id, 'session_booking');

                unset($value->id);
                $this->db->insert('session_booking', $value);
                $recurr_row_id = $this->db->insert_id();

                $session_repeat = get_by_id($value->session_id,'sessions')->session_repeat;
                $mentee = get_by_id($value->mentee_id,'users');
                $session_number = get_by_id($value->session_id,'sessions')->session_number;
                $session = get_by_id($value->session_id,'sessions');
                $date = new DateTime($value->next_recur_date);
                $date->modify('+'.$session_repeat.'day');
                $next_date = $date->format('Y-m-d');

                if(($session_number - 1) == $value->recurring_count){
                    $is_completed = 1;
                }else{
                    $is_completed = 0;;
                }

                $data = array(
                    'date' => $next_date,
                    'next_recur_date' => $next_date,
                    'recurring_count' => $value->recurring_count +1,
                    'is_completed' => $is_completed,
                );
                $data = $this->security->xss_clean($data);
                $this->admin_model->edit_option($data, $recurr_row_id, 'session_booking');

                $subject = get_email_by_slug('recurring-session-reminder')->subject;
                $body = get_email_by_slug('recurring-session-reminder')->body;
                $variables_data = [
                    'mentee_name'  =>$mentee->name,
                    'session_name' => $session->name,
                    'next_date' => my_date_show($next_date),
                    'time' => $value->time,
                ]; 

                $msg = preg_replace_callback('/{{(.*?)}}/', function ($matches) use ($variables_data) {
                    $key = trim($matches[1]);
                    return isset($variables_data[$key]) ? $variables_data[$key] : $matches[0]; 
                }, $body);
                $edata = array();
                $edata['subject'] = $subject;
                $edata['msg'] = $msg;
                $msg = $this->load->view('email_template/common', $edata, true);
                $this->email_model->send_email($mentee->email, $subject, $msg);



            }
        }


        $time_zone = $this->admin_model->get_by_id(settings()->time_zone, 'time_zone');
        $time_zone = $time_zone->name;


        //$reminder_time = settings()->booking_reminder_time; 
        //$reminder_time2 = settings()->second_booking_reminder_time;

        $reminder_times = [settings()->booking_reminder_time, settings()->second_booking_reminder_time]; 

        $now = new DateTime('now', new DateTimezone($time_zone));
        $current_time = $now->format('Y-m-d H:i');

        $bookings = $this->common_model->get_reminder_bookings($current_time, $reminder_times);

        if (!empty($bookings)) {
            
            foreach ($bookings as $booking) {
                $mentee = $this->common_model->get_by_id($booking->mentee_id, 'users');
                $mentor = $this->common_model->get_by_id($booking->user_id, 'users');
                $session = $this->common_model->get_by_id($booking->session_id, 'sessions');
                // send email to mentee with dynamic msg 

                $subject = get_email_by_slug('booking-reminder-mentee')->subject;
                $body = get_email_by_slug('booking-reminder-mentee')->body;
                $variables_data = [
                    'mentee_name'  =>$mentee->name,
                    'session_name' => $session->name,
                    'date' => my_date_show($booking->date),
                    'time' => $booking->time,
                ]; 

                $msg = preg_replace_callback('/{{(.*?)}}/', function ($matches) use ($variables_data) {
                    $key = trim($matches[1]);
                    return isset($variables_data[$key]) ? $variables_data[$key] : $matches[0]; 
                }, $body);


                $edata = array();
                $edata['subject'] = $subject;
                $edata['msg'] = $msg;
               
                $msg = $this->load->view('email_template/common', $edata, true);


                
                $this->email_model->send_email($mentee->email, $subject, $msg);

                // send email to mentor with dynamic msg 
                $subject = get_email_by_slug('booking-reminder-mentor')->subject;
                $body = get_email_by_slug('booking-reminder-mentor')->body;
                $variables_data = [
                    'mentor_name'  =>$mentor->name,
                    'session_name' => $session->name,
                    'date' => my_date_show($booking->date),
                    'time' => $booking->time,
                ]; 

                $msg = preg_replace_callback('/{{(.*?)}}/', function ($matches) use ($variables_data) {
                    $key = trim($matches[1]);
                    return isset($variables_data[$key]) ? $variables_data[$key] : $matches[0]; 
                }, $body);


                $edata = array();
                $edata['subject'] = $subject;
                $edata['msg'] = $msg;
               
                $msg = $this->load->view('email_template/common', $edata, true);
                $this->email_model->send_email($mentor->email, $subject, $msg);

                $reminder_data = array(
                    'is_sent_reminder' => 1
                );
                $reminder_data = $this->security->xss_clean($reminder_data);
                $this->common_model->update($reminder_data, $booking->id, 'session_booking');
            }
        }
        
    }

}