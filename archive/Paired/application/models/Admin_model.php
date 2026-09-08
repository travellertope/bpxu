<?php
class Admin_model extends CI_Model {
    
    // insert function
	public function insert($data,$table){
        $data = $this->security->xss_clean($data);
        $this->db->insert($table,$data);        
        return $this->db->insert_id();
    }

    // edit function
    function edit_option($action, $id, $table){
        $action = $this->security->xss_clean($action);
        $this->db->where('id',$id);
        $this->db->update($table,$action);
        return;
    } 

    // edit function
    function edit_option_md5($action, $id, $table){
        $action = $this->security->xss_clean($action);
        $this->db->where('md5(id)', $id);
        $this->db->update($table,$action);
        return;
    } 

    // edit function
    function edit_user_md5($action, $id, $table){
        $action = $this->security->xss_clean($action);
        $this->db->where('md5(user_id)', $id);
        $this->db->update($table,$action);
        return;
    } 

    // edit function
    function edit_option_sess($action, $id, $table){
        $action = $this->security->xss_clean($action);
        $this->db->where('business_id', $id);
        $this->db->update($table,$action);
        return;
    }

    function update_gbooking_slot_number($action, $session_id, $time_slot, $table){
        $action = $this->security->xss_clean($action);
        $this->db->where('session_id', $session_id);
        $this->db->where('time', $time_slot);
        $this->db->update($table,$action);
        return;
    } 

    // update function
    function update($action,$id,$table){
        $action = $this->security->xss_clean($action);
        $this->db->where('id',$id);
        $this->db->update($table,$action);
    }

    // delete function
    function delete($id,$table){
        if (settings()->type == 'live') {
            $this->db->delete($table, array('id' => $id));
        }
        return;
    }

    function skill_delete($id,$table){
        if (settings()->type == 'live') {
            $this->db->delete($table, array('user_id' => $id));
        }
        return;
    }

    function delete_product_img($id,$table){
        if (settings()->type == 'live') {
            $this->db->delete($table, array('product_id' => $id));
        }
        return;
    }

    // delete function
    function delete_uid($id,$table){
        if (settings()->type == 'live') {
            $this->db->delete($table, array('uid' => $id));
        }
        return;
    }

    // delete days
    function delete_assaign_days($user_id, $table){
        $this->db->delete($table, array('user_id' => $user_id, 'session_id' => 0));
        return;
    }

    function delete_session_assaign_days($user_id, $session_id, $table){
        $this->db->delete($table, array('user_id' => $user_id, 'session_id' => $session_id));
        return;
    }

    // delete time
    function delete_assaign_time($user_id, $table){
        $this->db->delete($table, array('user_id' => $user_id, 'session_id' => 0));
        return;
    }

    function delete_session_assaign_time($user_id,$session_id, $table){
        $this->db->delete($table, array('user_id' => $user_id, 'session_id' => $session_id));
        return;
    }

    // delete staff days
    function delete_assaign_staff_days($staff_id, $table){
        $this->db->delete($table, array('staff_id' => $staff_id));
        return;
    }

    // delete staff time
    function delete_assaign_staff_time($staff_id, $table){
        $this->db->delete($table, array('staff_id' => $staff_id));
        return;
    }

    // delete tags
    function delete_assign_features($id, $table){
        $this->db->delete($table, array('package_id' => $id));
        return;
    }

    // delete tags
    function delete_staff_location($id, $table){
        $this->db->delete($table, array('staff_id' => $id));
        return;
    }


    // delete
    function delete_by_user($user_id, $table){
        $this->db->delete($table, array('user_id' => $user_id));
        return;
    }

    // get function
    function get_count($table)
    {
        $this->db->select();
        $this->db->from($table);
        $query = $this->db->get();
        $query = $query->num_rows();  
        return $query;
    }

    function get_count_mentees()
    {
        $this->db->select();
        $this->db->from('users');
        $this->db->where('role','mentee');
        $query = $this->db->get();
        $query = $query->num_rows();  
        return $query;
    }

    function get_count_mentors()
    {
        $this->db->select();
        $this->db->from('users');
        $this->db->where('role','user');
        $query = $this->db->get();
        $query = $query->num_rows();  
        return $query;
    }

    
    // get function
    function get($table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->order_by('id','DESC');
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }


    // select function
    function select($table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->order_by('id','ASC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    function select_by_company($table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('business_id',$this->business->uid);
        $this->db->order_by('id','ASC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    function get_admin_blogs($uid,$user_id)
    {
        $this->db->select();
        $this->db->from('blogs');
        $this->db->where('business_id',$uid);
        $this->db->where('user_id',$user_id);
        $this->db->order_by('id','ASC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    function get_workflows()
    {
        $this->db->select();
        $this->db->from('workflows');
        $this->db->where('status',1);
        $this->db->order_by('id','ASC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }


    // select function
    function get_contacts($uid)
    {
        $this->db->select();
        $this->db->from('contacts');
        $this->db->where('business_id',$uid);
        $this->db->order_by('id','ASC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    function get_skill_by_user($id)
    {
        $this->db->select();
        $this->db->from('users_skill');
        $this->db->where('user_id',$id);
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }

    function get_user_kyc($id)
    {
        $this->db->select();
        $this->db->from('kyc_verifications');
        $this->db->where('user_id',$id);
        $query = $this->db->get();
        $query = $query->row();
        return $query;
    }

    function get_all_kycs()
    {
        $this->db->select('k.*,u.name,u.email');
        $this->db->from('kyc_verifications as k');
        $this->db->join('users as u','u.id = k.user_id','LEFT');

        if(!empty($_GET['search']) && $_GET['search'] == 'pending'){
            $this->db->where('k.status', 0);
        }

        if(!empty($_GET['search']) && $_GET['search'] == 'approve'){
            $this->db->where('k.status', 1);
        }
        
        if(!empty($_GET['search']) && $_GET['search'] == 'reject'){
            $this->db->where('k.status', 2);
        }

        if(!empty($_GET['name'])){
            $this->db->like('u.name', $_GET['name']);
            $this->db->or_like('u.email', $_GET['name']);
        }

        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }

    // asc select function
    function select_asc($table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->order_by('id','ASC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    // select by id
    function select_option($id,$table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('id', $id);
        $query = $this->db->get();
        $query = $query->result_array();  
        return $query;
    } 

    // select by status
    function select_by_status($table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('status', 1);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    } 

    // select by status
    function select_order_by_name($table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('status', 1);
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    function select_by_user_id($id,$table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('user_id', $id);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }   

    // select by id
    function get_by_id($id,$table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('id', $id);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }

    function get_users($user_id)
    {
        $this->db->select();
        $this->db->from('users');
        $this->db->where('id', $user_id);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }


    function get_order_details_by_id($id)
    {
        $this->db->select();
        $this->db->from('product_order_lists');
        $this->db->where('order_id', $id);
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }








    /*
    *-------------------------------------------------------------------------------------------------
    * ADMIN PANEL QUERY START
    *-------------------------------------------------------------------------------------------------
    */



    //get report
    function get_admin_income_by_year()
    {
        $this->db->select('r.*');
        $this->db->select_sum('r.commission_amount', 'total');
        $this->db->from('payment_user r');
        $this->db->where("r.status !=", 'pending');
        $this->db->group_by("DATE_FORMAT(r.created_at,'%Y')");
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }

    //get report
    function get_admin_income_by_date($date)
    {
        $this->db->select('r.*');
        $this->db->select_sum('r.commission_amount', 'total');
        $this->db->from('payment_user r');
        $this->db->where("DATE_FORMAT(r.created_at,'%Y-%m')", $date);
        $this->db->where("r.status != ", 'pending');
        $query = $this->db->get();
        $query = $query->result();
        if (empty($query)) {
            return 0;
        } else {
            return $query[0]->total;
        }
    }


    function get_user_days($id,$session_id)
    {
        $this->db->select();
        $this->db->from('assaign_days');
        $this->db->where('user_id', $id);
        if ($session_id==0) {
            $this->db->where('session_id',0);
        }else{
            $this->db->where('session_id', $session_id);
        }
        $query = $this->db->get();
        $query = $query->result_array();  
        return $query;
    }

    function get_user_days_active($id)
    {
        $this->db->select();
        $this->db->from('assaign_days');
        $this->db->where('user_id', $id);
        $this->db->where('day !=', 0);
        $this->db->where('session_id',0);
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    function get_all_bookings($type, $id, $total, $limit, $offset)
    {
        
        $this->db->select('b.*, s.name as session_name, u.name as mentee_name, u.email as mentee_email');
        $this->db->from('session_booking as b');
        $this->db->join('sessions as s','s.id = b.session_id','LEFT');
        $this->db->join('users as u','u.id = b.mentee_id','LEFT');

        if($type == 'user'){
            $this->db->where('b.user_id', $id);
            if($_GET['search'] == 'all'){

                $this->db->where('b.user_id', $id);
                $this->db->where('s.type', 1);
            }
            if($_GET['search'] != 'recurring' && $_GET['search'] != 'upcoming'){
                $this->db->where('b.user_id', $id);
                $this->db->where('s.type', 1);
            }
        }

        if($type == 'mentee'){
            $this->db->where('b.mentee_id', $id);
            if($_GET['search'] == 'all'){
                    $this->db->where('b.mentee_id', $id);
                    $this->db->where('s.type', 1);
            }
            if($_GET['search'] != 'recurring' && $_GET['search'] != 'upcoming'){
                $this->db->where('b.mentee_id', $id);
                $this->db->where('s.type', 1);
            }
        }

        if(!empty($_GET['search']) && $_GET['search'] == 'pending'){
            $this->db->where('b.status', 0);
            $this->db->where('s.type', 1);
        }

        if(!empty($_GET['search']) && $_GET['search'] == 'completed'){
            $this->db->where('b.status', 3);
            $this->db->where('s.type', 1);
        }

        if(!empty($_GET['search']) && $_GET['search'] == 'upcoming'){
            $this->db->where('b.date < DATE_ADD(now(), INTERVAL 7 DAY) AND b.date > NOW()');
        }

        if(!empty($_GET['session']) && $_GET['session'] != 'all'){
            $this->db->where('b.session_id', $_GET['session']);
        }

        if(!empty($_GET['mentee']) && $_GET['mentee'] != 'all'){
            $this->db->where('b.mentee_id', $_GET['mentee']);
        }

        if(isset($_GET['status']) && $_GET['status'] != 'all'){
            $this->db->where('b.status', $_GET['status']);
        }

        if(!empty($_GET['mentor']) && $_GET['mentor'] != 'all'){
            $this->db->where('b.user_id', $_GET['mentor']);
        }

        if(isset($_GET['search_booking'])){
            $this->db->like('b.booking_number', $_GET['search_booking']);
        }

        if(!empty($_GET['search']) && $_GET['search'] == 'recurring'){
            $this->db->where('s.type', 2);
        }
        
        $this->db->order_by('b.id','DESC');

        if ($total == 1) {
            $query = $this->db->get();
            $query = $query->num_rows();  
        } else {
            $query = $this->db->get('', $limit, $offset);
            $query = $query->result();  
        }
        return $query;
    }


    function get_all_booking_calendars($id)
    {
        
        $this->db->select('b.*, s.name as session_name, u.name as mentee_name, u.email as mentee_email');
        $this->db->from('session_booking as b');
        $this->db->join('sessions as s','s.id = b.session_id','LEFT');
        $this->db->join('users as u','u.id = b.mentee_id','LEFT');

        $this->db->where('s.user_id', $id);
        
        $this->db->order_by('b.id','DESC');
       
        $query = $this->db->get('', $limit, $offset);
        $query = $query->result(); 
        return $query;
    }


    function count_booking($status)
    {
        $this->db->select('b.*');
        $this->db->from('session_booking as b');

        $this->db->join('sessions as s','s.id = b.session_id','LEFT');

        if($this->session->userdata('role') == 'user'){
            $this->db->where('b.user_id', $this->session->userdata('id'));
        }

        if($this->session->userdata('role') == 'mentee'){
            $this->db->where('mentee_id', $this->session->userdata('id'));
        }

        if($status != 'all' && $status != 'upcoming' && $status != 'recurring'){
            $this->db->where('b.status', $status);
            $this->db->where('s.type', 1);
        }

        if($status == 'recurring'){
            $this->db->where('s.type', 2);
        }

        if($status == 'upcoming'){
            $this->db->where('b.date < DATE_ADD(now(), INTERVAL 7 DAY) AND b.date > NOW()');
        }
        if($status != 'recurring' && $status != 'upcoming'){
            $this->db->where('s.type', 1);
        }
        $query = $this->db->get();
        $query = $query->num_rows();
        return $query;
    }


    function count_kyc($status)
    {
        $this->db->select();
        $this->db->from('kyc_verifications');

        

        if ($status != 'all') {
           $this->db->where('status', $status);
        }
        

        $query = $this->db->get();
        $query = $query->num_rows();
        return $query;
    }


    function get_single_booking($id)
    {
        $this->db->select();
        $this->db->from('session_booking');
        $this->db->where('booking_number', $id);
        
        $this->db->order_by('id','ASC');
        $query = $this->db->get();
        $query = $query->row();
        return $query;
    }

    function get_mentee_sessions($id)
    {
        $this->db->select('b.*');
        $this->db->from('session_booking as b');
        if ($this->session->userdata('role') == 'user') {
            $this->db->where('b.user_id', $this->session->userdata('id'));
        }
        $this->db->where('b.mentee_id', $id);
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }

    function get_mentor_mentees()
    {
        $this->db->select('b.*, u.name, u.country, u.phone, u.email, u.thumb, u.is_active');
        $this->db->from('session_booking as b');
        $this->db->join('users as u','u.id = b.mentee_id','LEFT');
        $this->db->where('b.user_id', $this->session->userdata('id'));

        if (isset($_GET['country']) && $_GET['country'] != 'all') {
            $this->db->where('u.country', $_GET['country']);
        }

        if (isset($_GET['search']) && $_GET['search'] != '') {
            $this->db->like('u.name', $_GET['search']);
        }
        
        $this->db->group_by('b.mentee_id');
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }

    function get_mentor_sessions($id)
    {
        $this->db->select('s.*');
        $this->db->from('sessions as s');
        $this->db->where('s.user_id', $id);
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }

    // get_payment
    function check_booking_payment($booking_id)
    {
        $this->db->select('*');
        $this->db->from('payment_user');
        $this->db->where('booking_id', $booking_id);
        $query = $this->db->get();
        $query = $query->row();
        return $query;
    }


    function get_discount($dcode, $session_id, $mentor_id)
    {
        $this->db->select();
        $this->db->from('coupons');
        $this->db->where('code', $dcode);
        $this->db->where('user_id', $mentor_id);
        $this->db->where('session_id', $session_id);
        $this->db->where('status', 1);
        
        $this->db->order_by('id','ASC');
        $query = $this->db->get();
        $query = $query->row();
        return $query;
    }

    function get_favourites()
    {
        $this->db->select();
        $this->db->from('favourite');

        
        $this->db->where('user_id', $this->session->userdata('id'));
        
        
        $this->db->order_by('id','ASC');
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }

    function check_discount_apply($code, $session_id, $user_id, $mentee_id)
    {
        $this->db->select();
        $this->db->from('coupon_apply');
        $this->db->where('code', $code);
        $this->db->where('session_id', $session_id);
        $this->db->where('user_id', $user_id);
        $this->db->where('mentee_id', $mentee_id);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }

    function check_coupon_mentee($session_id, $mentee_id)
    {
        $this->db->select();
        $this->db->from('coupon_apply');
        $this->db->where('session_id', $session_id);
        $this->db->where('mentee_id', $mentee_id);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }

    function check_discount_by_session($session_id)
    {
        $this->db->select();
        $this->db->from('coupons');
        $this->db->where('session_id', $session_id);
        $this->db->where('status', 1);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }

    function get_all_used_coupons_by_mentee($coupon_id)
    {
        $this->db->select();
        $this->db->from('coupon_apply');
        $this->db->where('coupon_id', $coupon_id);
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }


    // rating

     



    function get_user_working_times($id)
    {
        $this->db->select();
        $this->db->from('assign_time');
        $this->db->where('user_id', $id);
        $this->db->where('session_id',0);
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }
    

    //get report
    function get_users_packages()
    {
        $this->db->select('count(p.id) as total, k.name');
        $this->db->from('payment p');
        $this->db->join('package k', 'k.id = p.package_id', 'LEFT');
        $this->db->where("p.status !=", 'pending');
        $this->db->group_by("p.package_id");
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }


    // get testimonials
    function get_testimonials($business_id, $type, $status){
        $this->db->select();
        $this->db->from('testimonials');
        $this->db->where('type', $type);
        if ($business_id != 0) {
            $this->db->where('business_id', $business_id);
        }
        if ($status != 'all') {
            $this->db->where('status', $status);
        }
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    function get_time_by_days($day_id, $session_id, $user_id)
    {
        $this->db->select();
        $this->db->from('assign_time');
        $this->db->where('day_id', $day_id);
        $this->db->where('user_id', $user_id);
        if ($session_id ==0) {
            $this->db->where('session_id',0);
        }else{
            $this->db->where('session_id',$session_id);
        }
        $this->db->group_by('time');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }
    


    // get payment list
    function get_payment_lists($limit)
    {
        $this->db->select('p.*, k.name as package_name, k.slug, u.name as user_name, u.phone, u.address, u.email, u.thumb');
        $this->db->from('payment p');
        $this->db->join('package k', 'k.id = p.package_id', 'LEFT');
        $this->db->join('users u', 'u.id = p.user_id', 'LEFT');
        $this->db->where('p.amount != ', '0.00');
        $this->db->where('p.status != ', 'expired');
        $this->db->order_by('p.id', 'DESC');
        //$this->db->group_by('p.user_id');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }


    // get payment list
    function get_customer_payment_lists($limit)
    {
        $this->db->select('p.*, s.session_id');
        $this->db->from('payment_user p');
        $this->db->join('session_booking s', 's.id = p.booking_id', 'LEFT');
        if($this->session->userdata('role') == 'user'){
          $this->db->where('p.user_id', $this->session->userdata('id'));  
        }
        
        $this->db->where('p.amount != ', '0.00');
        $this->db->order_by('p.id', 'DESC');
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    function get_customer_payment_details($puid)
    {
        $this->db->select('p.*, s.session_id');
        $this->db->from('payment_user p');
        $this->db->join('session_booking s', 's.id = p.booking_id', 'LEFT');
        $this->db->where('p.puid', $puid);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }



    function count_users_by_status($type)
    {
        $this->db->select('count(p.id) as total');
        $this->db->from('payment p');
        $this->db->where('p.status', $type);
        $this->db->group_by("p.user_id");
        $query = $this->db->get();
        $query = $query->row();
        return $query;
    }


    //get packages
    function get_previous_payments($user_id)
    {
        $this->db->select();
        $this->db->from('payment p');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }

    //get category
    public function get_category($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('category');
        return $query->row();
    }

    //get category
    public function get_category_option($id, $table)
    {
        $this->db->where('id', $id);
        $query = $this->db->get($table);
        return $query->row();
    }


    // get_settings
    function get_settings()
    {
        $this->db->select('s.*, c.currency_code, c.currency_symbol, c.code');
        $this->db->from('settings s');
        $this->db->join('country c', 'c.id = s.country', 'LEFT');
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }


    // get_settings
    function get_currency_symbol($currency_code)
    {
        $this->db->select('*');
        $this->db->from('country');
        $this->db->where('currency_code', $currency_code);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }


    function get_font_by_slug($slug)
    {
        $this->db->select();
        $this->db->from('google_fonts');
        $this->db->where('slug', $slug);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }

    // select by id
    function select_option_md5($id,$table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where(md5('id'), $id);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    } 

    //get user by id
    public function get_user_by_slug($slug)
    {
        $this->db->where('slug', $slug);
        $query = $this->db->get('users');
        return $query->row();
    }


    // get faqs
    function get_faqs($business_id, $type, $status){
        $this->db->select();
        $this->db->from('faqs');
        $this->db->where('type', $type);
        if ($business_id != 0) {
            $this->db->where('business_id', $business_id);
        }
        if ($status != 'all') {
            $this->db->where('status', $status);
        }
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    } 


    // get pages
    function get_pages($business_id, $type, $status){
        $this->db->select();
        $this->db->from('pages');
        $this->db->where('type', $type);
        if ($business_id != 0) {
            $this->db->where('business_id', $business_id);
        }
        if ($status != 'all') {
            $this->db->where('status', $status);
        }
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    // get categories
    function get_category_by_slug($slug){
        $this->db->select();
        $this->db->from('categories');
        $this->db->where('slug', $slug);
        $query = $this->db->get();
        $query = $query->row();
        return $query;
    }

    // get categories
    function get_categories(){
        $this->db->select();
        $this->db->from('categories');
        $this->db->where('status', 1);
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }


    function get_subcategories(){
        $this->db->select();
        $this->db->from('categories');
        $this->db->where('status', 1);
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }  


    function get_subcategories_by_category()
    {
        $this->db->select();
        $this->db->from('categories');
        $this->db->where('status',1);
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    function get_skills_by_category($category_id)
    {
        $this->db->select();
        $this->db->from('skills');
        $this->db->where('category_id', $category_id);
        $this->db->where('status', 1);
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    function get_search_skills_by_category($category_id)
    {
        $this->db->select();
        $this->db->from('skills');
        $this->db->where('category_id', $category_id);
        $this->db->where('status', 1);
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }


    // get categories
    function get_site_categories($table){
        $this->db->select();
        $this->db->from($table);
        $this->db->where('status', 1);
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    function get_site_skills($table){
        $this->db->select();
        $this->db->from($table);
        $this->db->where('status', 1);
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }


    // get categories
    function get_admin_categories(){
        $this->db->select();
        $this->db->from('categories');
        //$this->db->where('status', 1);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }


    function get_admin_subcategories(){
        $this->db->select();
        $this->db->from('categories');
        $this->db->where('status',1);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }  


    function get_count_by_id($id,$table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('category_id', $id);
        $query = $this->db->get();
        $query = $query->num_rows();  
        return $query;
    }


   
    // get blog posts
    function get_blog_posts($total, $limit, $offset){
        $this->db->select('b.*');
        $this->db->select('c.slug as category_slug, c.name as category, u.role');
        $this->db->from('blog_posts b');
        $this->db->where('u.role', 'admin');
        $this->db->where('b.user_id', $this->session->userdata('id'));
        $this->db->join('blog_category c', 'c.id = b.category_id', 'LEFT');
        $this->db->join('users u', 'u.id = b.user_id', 'LEFT');
        $this->db->limit($limit);
        
        if ($total == 1) {
            $query = $this->db->get();
            $query = $query->num_rows();
            return $query;
        } else {
            $query = $this->db->get('', $limit, $offset);
            $query = $query->result();
            return $query;
        }
    } 
    

    //get posts categories
    function get_name_by_id($id,$table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('id', $id);
        $query = $this->db->get();
        $query = $query->row_array();  
        return $query;
    }

    //get category posts
    function get_category_posts($total, $limit, $offset, $id)
    {

        $this->db->select('p.*');
        $this->db->select('c.name as category, c.slug as category_slug');
        $this->db->from('blog_posts p');
        $this->db->join('blog_category as c', 'c.id = p.category_id', 'LEFT');
        $this->db->where('p.status', 1);
        $this->db->where('p.category_id', $id);
        
        $this->db->order_by('p.id', 'DESC');
        $this->db->limit($limit);
        
        if ($total == 1) {
            $query = $this->db->get();
            $query = $query->num_rows();
            return $query;
        } else {
            $query = $this->db->get('', $limit, $offset);
            $query = $query->result();
            return $query;
        }
    }


    //get category posts
    function count_posts_by_categories($id)
    {
        $this->db->select('count(p.id) as total');
        $this->db->from('blog_posts p');
        $this->db->where('p.status', 1);
        $this->db->where('p.category_id', $id);
        $query = $this->db->get();
        if($query->num_rows() == 1) {                 
            return $query->row();
        }else{
            return 0;
        }
    }


    // get_categories
    function get_blog_categories(){
        $this->db->select();
        $this->db->from('blog_category');
        $this->db->where('user_id', $this->session->userdata('id'));
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    } 

    //get latest users
    function get_latest_users(){
        //$this->active_langs();
        $this->db->select('u.*, p.status as payment_status,p.package_id, k.name as package');
        $this->db->from('users u');
        $this->db->join('payment p', 'p.user_id = u.id', 'LEFT');
        $this->db->join('package k', 'k.id = p.package_id', 'LEFT');
        $this->db->where('u.status', 1);
        $this->db->where('u.role', 'user');
        $this->db->group_by('u.id');
        $this->db->order_by('u.id','DESC');
        $this->db->limit(6);
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }

    function get_latest_bookings(){
        //$this->active_langs();
        $this->db->select('s.* , u.name, u.thumb, u.email');
        $this->db->from('session_booking s');

        if ($this->session->userdata('role') == 'user') {
            $this->db->join('users u', 'u.id = s.mentee_id', 'LEFT');
        }else{
            $this->db->join('users u', 'u.id = s.user_id', 'LEFT');
        }
        

        if ($this->session->userdata('role') == 'user') {
            $this->db->where('s.user_id', $this->session->userdata('id'));
        }else{
            $this->db->where('s.mentee_id', $this->session->userdata('id'));
            $this->db->where('s.date < DATE_ADD(now(), INTERVAL 7 DAY) AND s.date > NOW()');
        }
        
        $this->db->order_by('s.id','DESC');
        $this->db->limit(6);
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }

    // count user
    function get_user_total(){
        $this->db->select();
        $this->db->from('users');
        $this->db->where('role', 'user');
        $query = $this->db->get();
        $query = $query->num_rows();  
        return $query;
    }


    // get all posts
    function active_langs(){
        gets_active_langs();
    }

    // get all posts
    function get_latest_messages(){
        $this->db->select('c.*');
        $this->db->from('contacts c');
        $this->db->order_by('c.id','DESC');
        $this->db->limit(8);
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }

    //get tagfs
    function get_tags($post_id)
    {
        $this->db->select();
        $this->db->from('tags');
        $this->db->where('post_id', $post_id);
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }

    // delete tags
    function delete_tags($post_id, $table){
        $this->db->delete($table, array('post_id' => $post_id));
        return;
    }


    // get images by user
    function get_total_info(){
        $this->db->select('p.id');
        $this->db->select('(SELECT count(posts.id)
                            FROM posts 
                            WHERE (status = 1)
                            )
                            AS post',TRUE);
        
        $this->db->select('(SELECT count(users.id)
                            FROM users 
                            WHERE (status = 1)
                            )
                            AS user',TRUE);

        $this->db->from('posts p');
        $query = $this->db->get();
        $query = $query->row();
        return $query;
    }


    function get_admin_package_features()
    {
        $this->db->select('p.*');
        $this->db->from('package p');
        $this->db->order_by('p.id', 'ASC');
        $query = $this->db->get();
        $query = $query->result();  
        foreach ($query as $key => $value) {
            $this->db->select('a.*, f.name as feature_name');
            $this->db->from('feature_assaign a');
            $this->db->join('features f', 'f.id = a.feature_id', 'LEFT');
            $this->db->where('package_id',$value->id);
            $query2 = $this->db->get();
            $query2 = $query2->result();
            $query[$key]->features = $query2;
        }
        return $query;
    }


    function get_package_features()
    {
        $this->db->select('*');
        $this->db->from('package');
        $this->db->where('status', 1);
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get();
        $query = $query->result();  
        foreach ($query as $key => $value) {
            $this->db->select('a.*, f.name as feature_name');
            $this->db->from('feature_assaign a');
            $this->db->join('features f', 'f.id = a.feature_id', 'LEFT');
            $this->db->where('package_id',$value->id);
            $query2 = $this->db->get();
            $query2 = $query2->result();
            $query[$key]->features = $query2;
        }
        return $query;
    }
    
    function get_features()
    {
        if(get_user_info() == FALSE){$act = 0;}else{$act = 1;};
        $this->db->select('*');
        $this->db->from('features');
        if ($act == 0) {
            $this->db->where('slug !=', 'get-online-payments');
        }
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get();
        $query = $query->result(); 
        return $query;
    }


    function get_assign_package_features($package_id)
    {
        $this->db->select('*');
        $this->db->from('feature_assaign');
        $this->db->where('package_id', $package_id);
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get();
        $query = $query->result(); 
        return $query;
    }

    function check_assign_feature($feature_id, $package_id)
    {
        $this->db->select('*');
        $this->db->from('feature_assaign');
        $this->db->where('feature_id', $feature_id);
        $this->db->where('package_id', $package_id);
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }


    function get_total_user_by_package($package_id)
    {
        $this->db->select('*');
        $this->db->from('payment');
        $this->db->where('package_id', $package_id);
        $this->db->where('status !=', 'pending');
        $this->db->group_by('user_id');
        $query = $this->db->get();
        $query = $query->num_rows(); 
        return $query;
    }


    // get_payment
    function get_my_payment()
    {
        $this->db->select();
        $this->db->from('payment');
        $this->db->where('user_id', $this->session->userdata('id'));
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        $query = $query->row();
        return $query;
    }


    // get_payment
    function get_total_value($table, $date)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('user_id', $this->session->userdata('id'));
        //$this->db->where("DATE_FORMAT(created_at,'%Y-%m-%d') >=", $date);
        //$this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        $query = $query->num_rows();  
        return $query;
    }


    public function active_features($package_id){
        $this->db->select('f.*, s.name, s.slug');
        $this->db->from('feature_assaign f');
        $this->db->join('features s', 's.id = f.feature_id', 'LEFT');
        $this->db->where('f.package_id', $package_id);
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    // edit function
    function update_payment($action, $user_id, $table){
        $this->db->where('user_id', $user_id);
        $this->db->update($table,$action);
        return;
    }


    // get_payment
    function get_payment($payment_id)
    {
        $this->db->select();
        $this->db->from('payment');
        $this->db->where('puid', $payment_id);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }


    // get_payment
    function get_package_by_slug($slug)
    {
        $this->db->select();
        $this->db->from('package');
        $this->db->where('slug', $slug);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }


    // get plan coupons
    function get_plan_coupons($total, $limit, $offset){

        $this->db->select('c.*, p.name as plan_name');
        $this->db->from('plan_coupons as c');
        $this->db->join('package p', 'p.id = c.plan', 'LEFT');
        $this->db->group_by('c.uid');
        if ($total == 1) {
            $query = $this->db->get();
            $query = $query->num_rows();
            return $query;
        } else {
            $query = $this->db->get('', $limit, $offset);
            $query = $query->result();
            return $query;
        }
    } 


    // get_payment
    function count_by_uid($uid)
    {
        $this->db->select();
        $this->db->from('plan_coupons');
        $this->db->where('uid', $uid);
        $query = $this->db->get();
        $query = $query->num_rows();  
        return $query;
    }


    // select by function
    function get_by_user_id($table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('user_id', $this->session->userdata('id'));
        $this->db->order_by('id','DESC');
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }


    // get plan coupons
    function get_plan_coupons_by_uid($uid, $total, $limit, $offset){

        $this->db->select('c.*, p.name');
        $this->db->from('plan_coupons as c');
        $this->db->join('package p', 'p.id = c.plan', 'LEFT');
        $this->db->where('c.uid', $uid);
        if ($total == 1) {
            $query = $this->db->get();
            $query = $query->num_rows();
            return $query;
        } else {
            $query = $this->db->get('', $limit, $offset);
            $query = $query->result();
            return $query;
        }
    } 


    // get code
    function get_coupon_by_code($code){
        $this->db->select();
        $this->db->from('plan_coupons');
        $this->db->where('code', $code);
        $this->db->where('status', 1);
        $query = $this->db->get();
        $query = $query->row();
        return $query;
    } 


    // get code
    function get_coupon_code($code, $plan, $plan_type){
        $this->db->select();
        $this->db->from('plan_coupons');
        $this->db->where('code', $code);
        $this->db->where('plan', $plan);
        $this->db->where('plan_type', $plan_type);
        $this->db->where('status', 1);
        $query = $this->db->get();
        $query = $query->row();
        return $query;
    } 

    // check code
    function check_coupon_code_apply($id, $user_id){
        $this->db->select();
        $this->db->from('plan_coupons_apply');
        $this->db->where('coupon_id', $id);
        $this->db->where('user_id', $user_id);
        $query = $this->db->get();
        $query = $query->row();
        return $query;
    } 


    // get all users
    function get_all_users($total, $limit, $offset, $type){
        $this->db->select('u.*');
        $this->db->from('users u');

        if (isset($_GET['country']) && $_GET['country'] != 'all') {
            $this->db->where('u.country', $_GET['country']);
        }

        if (isset($_GET['category']) && $_GET['category'] != 'all') {
            $this->db->where('u.category', $_GET['category']);
        }

        if (isset($_GET['search']) && $_GET['search'] != '') {
            $this->db->like('u.name', $_GET['search']);
        }

        $this->db->where('u.role', 'user');
        $this->db->order_by('u.id','DESC');
        $this->db->group_by('u.id');
        $this->db->query('SET SQL_BIG_SELECTS=1');

        if ($total == 1) {
            $query = $this->db->get();
            $query = $query->num_rows();
            return $query;
        } else {

            $query = $this->db->get('', $limit, $offset);
            $query = $query->result();

            foreach ($query as $key => $value) {
                $this->db->select();
                $this->db->from('payment');
                $this->db->where('user_id', $value->id);
                $this->db->order_by('id','DESC');
                $this->db->limit(1);
                $query2 = $this->db->get();
                $query2 = $query2->row();
                $query[$key]->payment = $query2;
            }
            return $query;
        }
    }

      function get_all_mentees($total, $limit, $offset){
        $this->db->select('u.*, p.status as payment_status,p.package_id, k.name as package, b.name as currency_name, b.slug as company_slug');
        $this->db->from('users u');
        $this->db->join('payment p', 'p.user_id = u.id', 'LEFT');
        $this->db->join('package k', 'k.id = p.package_id', 'LEFT');
        $this->db->join('business b', 'b.user_id = u.id', 'LEFT');
        
        if (isset($_GET['sort']) && $_GET['sort'] != 'all') {
            $this->db->where('p.status', $_GET['sort']);
        }

        if (isset($_GET['package']) && $_GET['package'] != 'all') {
            $this->db->where('p.package_id', $_GET['package']);
        }

        if (isset($_GET['search']) && $_GET['search'] != '') {
            $this->db->like('u.name', $_GET['search']);
        }

        $this->db->where('u.role', 'mentee');
        $this->db->order_by('u.id','DESC');
        $this->db->group_by('u.id');
        $this->db->query('SET SQL_BIG_SELECTS=1');

        if ($total == 1) {
            $query = $this->db->get();
            $query = $query->num_rows();
            return $query;
        } else {

            $query = $this->db->get('', $limit, $offset);
            $query = $query->result();

            foreach ($query as $key => $value) {
                $this->db->select();
                $this->db->from('payment');
                $this->db->where('user_id', $value->id);
                $this->db->order_by('id','DESC');
                $this->db->limit(1);
                $query2 = $this->db->get();
                $query2 = $query2->row();
                $query[$key]->payment = $query2;
            }
            return $query;
        }
    }


    // image upload function with resize option
    function upload_image($max_size){
            
            // set upload path
            $config['upload_path']  = "./uploads/";
            $config['allowed_types']= 'gif|jpg|png|jpeg';
            $config['max_size']     = '92000';
            $config['max_width']    = '92000';
            $config['max_height']   = '92000';
            $config['remove_spaces'] = TRUE;
            $config['encrypt_name'] = TRUE;

            $this->load->library('upload', $config);

            if ($this->upload->do_upload("photo")) {

                
                $data = $this->upload->data();

                // set upload path
                $source             = "./uploads/".$data['file_name'] ;
                $destination_thumb  = "./uploads/thumbnail/" ;
                $destination_medium = "./uploads/medium/" ;
                $main_img = $data['file_name'];
                // Permission Configuration
                chmod($source, 0777) ;

                /* Resizing Processing */
                // Configuration Of Image Manipulation :: Static
                $this->load->library('image_lib') ;
                $img['image_library'] = 'GD2';
                $img['create_thumb']  = TRUE;
                $img['maintain_ratio']= TRUE;

                /// Limit Width Resize
                $limit_medium   = $max_size ;
                $limit_thumb    = 150;

                // Size Image Limit was using (LIMIT TOP)
                $limit_use  = $data['image_width'] > $data['image_height'] ? $data['image_width'] : $data['image_height'] ;

                // Percentase Resize
                if ($limit_use > $limit_medium || $limit_use > $limit_thumb) {
                    $percent_medium = $limit_medium/$limit_use ;
                    $percent_thumb  = $limit_thumb/$limit_use ;
                }

                //// Making THUMBNAIL ///////
                $img['width']  = $limit_use > $limit_thumb ?  $data['image_width'] * $percent_thumb : $data['image_width'] ;
                $img['height'] = $limit_use > $limit_thumb ?  $data['image_height'] * $percent_thumb : $data['image_height'] ;

                // Configuration Of Image Manipulation :: Dynamic
                $img['thumb_marker'] = '_thumb-'.floor($img['width']).'x'.floor($img['height']) ;
                $img['quality']      = ' 100%' ;
                $img['source_image'] = $source ;
                $img['new_image']    = $destination_thumb ;

                $thumb_nail = $data['raw_name']. $img['thumb_marker'].$data['file_ext'];
                // Do Resizing
                $this->image_lib->initialize($img);
                $this->image_lib->resize();
                $this->image_lib->clear() ;

                ////// Making MEDIUM /////////////
                $img['width']   = $limit_use > $limit_medium ?  $data['image_width'] * $percent_medium : $data['image_width'] ;
                $img['height']  = $limit_use > $limit_medium ?  $data['image_height'] * $percent_medium : $data['image_height'] ;

                // Configuration Of Image Manipulation :: Dynamic
                $img['thumb_marker'] = '_medium-'.floor($img['width']).'x'.floor($img['height']) ;
                $img['quality']      = '100%' ;
                $img['source_image'] = $source ;
                $img['new_image']    = $destination_medium ;

                $mid = $data['raw_name']. $img['thumb_marker'].$data['file_ext'];
                // Do Resizing
                $this->image_lib->initialize($img);
                $this->image_lib->resize();
                $this->image_lib->clear() ;

                // set upload path
                $images = 'uploads/medium/'.$mid;
                $thumb  = 'uploads/thumbnail/'.$thumb_nail;
                unlink($source) ;

                return array(
                    'images' => $images,
                    'thumb' => $thumb
                );
            }
            else {
                echo "Failed! to upload image" ;
            }
            
    }


    //multiple image upload with resize option
    public function do_upload($photo) {                   
        $config['upload_path']  = "./uploads/";
        $config['allowed_types']= 'gif|jpg|png|jpeg';
        $config['max_size']     = '20000';
        $config['max_width']    = '20000';
        $config['max_height']   = '20000';
        $config['remove_spaces'] = TRUE;
        $config['encrypt_name'] = TRUE;
 
        $this->load->library('upload', $config);                
        
            if ($this->upload->do_upload($photo)) {
                $data       = $this->upload->data(); 
                /* PATH */
                $source             = "./uploads/".$data['file_name'] ;
                $destination_thumb  = "./uploads/thumbnail/" ;
                $destination_medium = "./uploads/medium/" ;
                $destination_big    = "./uploads/big/" ;

                // Permission Configuration
                chmod($source, 0777) ;

                /* Resizing Processing */
                // Configuration Of Image Manipulation :: Static
                $this->load->library('image_lib') ;
                $img['image_library'] = 'GD2';
                $img['create_thumb']  = TRUE;
                $img['maintain_ratio']= TRUE;

                /// Limit Width Resize
                $limit_big   = 2000 ;
                $limit_medium    = 1000 ;
                $limit_thumb    = 200 ;

                // Size Image Limit was using (LIMIT TOP)
                $limit_use  = $data['image_width'] > $data['image_height'] ? $data['image_width'] : $data['image_height'] ;

                // Percentase Resize
                if ($limit_use > $limit_big || $limit_use > $limit_thumb || $limit_use > $limit_medium) {
                    $percent_big = $limit_big/$limit_use ;
                    $percent_medium  = $limit_medium/$limit_use ;
                    $percent_thumb  = $limit_thumb/$limit_use ;
                }

                //// Making THUMBNAIL ///////
                $img['width']  = $limit_use > $limit_thumb ?  $data['image_width'] * $percent_thumb : $data['image_width'] ;
                $img['height'] = $limit_use > $limit_thumb ?  $data['image_height'] * $percent_thumb : $data['image_height'] ;

                // Configuration Of Image Manipulation :: Dynamic
                $img['thumb_marker'] = '_thumb-'.floor($img['width']).'x'.floor($img['height']) ;
                $img['quality']      = '99%' ;
                $img['source_image'] = $source ;
                $img['new_image']    = $destination_thumb ;

                $thumb_nail = $data['raw_name']. $img['thumb_marker'].$data['file_ext'];
                // Do Resizing
                $this->image_lib->initialize($img);
                $this->image_lib->resize();
                $this->image_lib->clear() ;                 

                //// Making MEDIUM ///////
                $img['width']  = $limit_use > $limit_medium ?  $data['image_width'] * $percent_medium : $data['image_width'] ;
                $img['height'] = $limit_use > $limit_medium ?  $data['image_height'] * $percent_medium : $data['image_height'] ;

                // Configuration Of Image Manipulation :: Dynamic
                $img['thumb_marker'] = '_medium-'.floor($img['width']).'x'.floor($img['height']) ;
                $img['quality']      = '99%' ;
                $img['source_image'] = $source ;
                $img['new_image']    = $destination_medium ;

                $medium = $data['raw_name']. $img['thumb_marker'].$data['file_ext'];
                // Do Resizing
                $this->image_lib->initialize($img);
                $this->image_lib->resize();
                $this->image_lib->clear() ;               

                ////// Making BIG /////////////
                $img['width']   = $limit_use > $limit_big ?  $data['image_width'] * $percent_big : $data['image_width'] ;
                $img['height']  = $limit_use > $limit_big ?  $data['image_height'] * $percent_big : $data['image_height'] ;

                // Configuration Of Image Manipulation :: Dynamic
                $img['thumb_marker'] = '_big-'.floor($img['width']).'x'.floor($img['height']) ;
                $img['quality']      = '99%' ;
                $img['source_image'] = $source ;
                $img['new_image']    = $destination_big ;

                $album_picture = $data['raw_name']. $img['thumb_marker'].$data['file_ext'];
                // Do Resizing
                $this->image_lib->initialize($img);
                $this->image_lib->resize();
                $this->image_lib->clear() ;

                $data_image = array(
                    'thumb' => 'uploads/thumbnail/'.$thumb_nail,
                    'medium' => 'uploads/medium/'.$medium,
                    'big' => 'uploads/big/'.$album_picture
                );

                unlink($source) ;   
                return $data_image;   
    
            }
            else {
                return FALSE ;
            }
       
    }



    /*
    * language start
    */

    // get language
    function get_language()
    {
        $this->db->select();
        $this->db->from('language');
        $this->db->order_by('id','ASC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    // get language
    function get_language_values()
    {
        $this->db->select();
        $this->db->from('lang_values');
        $this->db->order_by('id','ASC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    // get language value pagination
    function get_lang_values($total, $limit, $offset)
    {
        $this->db->select('*');
        $this->db->from('lang_values');
        $this->db->order_by('id','DESC');
        
        if ($total == 1) {
            $query = $this->db->get();
            $query = $query->num_rows();
            return $query;
        } else {
            $query = $this->db->get('', $limit, $offset);
            $query = $query->result();
            return $query;
        }
    }


    // get language value pagination
    function get_lang_values_by_type($type)
    {
        $this->db->select('*');
        $this->db->from('lang_values');
        $this->db->where('type', $type);
        $this->db->order_by('id','DESC');
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }

    //check unique language keyword
    public function check_keyword($keyword)
    {
        $this->db->select('*');
        $this->db->from('lang_values');
        $this->db->where('keyword', $keyword); 
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1) {                 
            return 1;
        }else{
            return 0;
        }
    }

    //check unique language name
    public function check_language($name)
    {
        $this->db->select('*');
        $this->db->from('language');
        $this->db->where('name', $name); 
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1) {                 
            return 1;
        }else{
            return 0;
        }
    }

    /*
    * language end
    */

    /*
    *-------------------------------------------------------------------------------------------------
    * ADMIN PANEL QUERY END
    *-------------------------------------------------------------------------------------------------
    */


































    /*
    *-------------------------------------------------------------------------------------------------
    * USER PANEL QUERY START
    *-------------------------------------------------------------------------------------------------
    */



    // select by function
    


    // select by function
    



    // select by function
    

    

    // select by function
    function get_by_user($table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('user_id', $this->session->userdata('id'));
        $this->db->order_by('id','DESC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    // select by function
    function get_by_user_limit($table, $limit)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('user_id', $this->session->userdata('id'));
        $this->db->order_by('id','DESC');
        $this->db->limit($limit);
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }


    // select by function
    function select_by_user($table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('user_id', $this->session->userdata('id'));
        $this->db->order_by('id','DESC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    function get_site_fonts()
    {
        $this->db->select();
        $this->db->from('fonts');
        $this->db->where('user_id', 0);
        $this->db->order_by('id','DESC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    // get function
    function get_count_by_user($table)
    {
        $this->db->select();
        $this->db->from($table);
        if ($this->session->userdata('role') == 'user' && $table == 'session_booking') {
            $this->db->where('user_id', $this->session->userdata('id'));
        }
        if ($this->session->userdata('role') == 'mentee'  && $table == 'session_booking') {
            $this->db->where('mentee_id', $this->session->userdata('id'));
        }

        if ($this->session->userdata('role') == 'user'  && $table == 'sessions') {
            $this->db->where('user_id', $this->session->userdata('id'));
        }

        $query = $this->db->get();
        $query = $query->num_rows();  
        return $query;
    }



    function get_count_minute_by_user($id)
    {
        $this->db->select('*');
        $this->db->select_sum('duration', 'total');
        $this->db->from('session_booking');
        $this->db->where("user_id", $id);
        $this->db->where("status", 3);
        $query = $this->db->get();
        $query = $query->result();
        if (empty($query)) {
            return '0';
        } else {
            return $query[0]->total;
        }
    }


    function get_count_completed_sessions($id)
    {
        $this->db->select('*');
        $this->db->from('session_booking');
        $this->db->where("user_id", $id);
        $this->db->where("status", 3);
        $query = $this->db->get();
        $query = $query->num_rows(); 
        return $query;
    }

    function count_mentee_booking($mentee_id, $status)
    {
        $this->db->select('b.*');
        $this->db->from('session_booking as b');
        $this->db->join('sessions as s','s.id = b.session_id','LEFT');
        $this->db->where("b.user_id", $this->session->userdata('id'));
        $this->db->where("b.mentee_id", $mentee_id);
       
        $this->db->where("b.status", $status);;
        
        $query = $this->db->get();
        $query = $query->num_rows(); 
        return $query;
    }

    function count_session_booking($session_id)
    {
        $this->db->select('*');
        $this->db->from('session_booking');
        $this->db->where("session_id", $session_id);
        $query = $this->db->get();
        $query = $query->num_rows(); 
        return $query;
    }

    function count_mentee_recurring_booking($mentee_id, $type)
    {
        $this->db->select('b.*');
        $this->db->from('session_booking as b');
        $this->db->join('sessions as s','s.id = b.session_id','LEFT');
        $this->db->where("b.user_id", $this->session->userdata('id'));
        $this->db->where("b.mentee_id", $mentee_id);
       
        $this->db->where("s.type", $type);;
        
        $query = $this->db->get();
        $query = $query->num_rows(); 
        return $query;
    }

    // get function
    function get_count_by_user_id($table, $user_id)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('user_id', $this->session->userdata('id'));
        $query = $this->db->get();
        $query = $query->num_rows();  
        return $query;
    }

    function get_recurr_session_by_date()
    {
        $this->db->select();
        $this->db->from('session_booking');
        $this->db->where('next_recur_date', date('Y-m-d'));
        $this->db->where('is_completed', 0);
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    // get function
    function get_count_by_collection($table, $id)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('user_id', $this->session->userdata('id'));
        $this->db->where('collection_id', $id);
        $query = $this->db->get();
        $query = $query->num_rows();  
        return $query;
    }


    // select by function
    function check_data_by_user($table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('user_id', $this->session->userdata('id'));
        $query = $this->db->get();
        $query = $query->num_rows();  
        if($table == 'customers' && $query == 0) {
            $this->db->select();
            $this->db->from('appointments');
            $this->db->where('user_id', $this->session->userdata('id'));
            $query = $this->db->get();
            $query = $query->num_rows();
        }
        return $query;
    }


    // get business
    function get_business($uid)
    {
        $this->db->select('b.*, n.name as country_name, n.currency_name, n.currency_symbol, n.currency_code');
        $this->db->from('business b');
        if ($uid != 0) {
            $this->db->where('b.uid', $uid);
        }
        $this->db->where('b.user_id', $this->session->userdata('id'));
        $this->db->join('country n', 'n.id = b.country', 'LEFT');
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }


    // get business
    function get_business_uid($uid)
    {
        $this->db->select('b.*, n.name as country_name, n.currency_name, n.currency_symbol, n.currency_code');
        $this->db->from('business b');
        if ($uid != 0) {
            $this->db->where('b.uid', $uid);
        }
        $this->db->join('country n', 'n.id = b.country', 'LEFT');
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }


    function get_by_md5_id($id,$table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('md5(id)', $id);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }

    
    public function check_email($email)
    {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('email', $email); 
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1) {                 
            return $query->result();
        }else{
            return false;
        }
    }


    function check_session_slug($slug)
    {
        $this->db->select();
        $this->db->from('sessions');
        $this->db->where('slug', $slug);
        $query = $this->db->get();
        $query = $query->row();
        if (empty($query)) {
            return 0;
        }else{
            return 1; 
        }
        
    }


    function check_time($time, $date, $session_id, $mentor_id='')
    {
        
        $this->db->select();
        $this->db->from('session_booking');
        $this->db->where('date', $date);
        $this->db->where('time', $time);
        $this->db->where('status !=', 2);

        $this->db->where('session_id', $session_id);
        // added this staff line
        $this->db->where('user_id', $mentor_id);
        
        $query = $this->db->get();
        $query = $query->row();
        if (isset($query)) {
            return true;
        } else {
            return false;
        }
    }

    function count_session_time_slot($session_id, $date, $time_val)
    {
        
        $this->db->select();
        $this->db->from('session_booking');
        $this->db->where('date', $date);
        $this->db->where('time', $time_val);
        $this->db->where('status !=', 2);

        $this->db->where('session_id', $session_id);
        
        $query = $this->db->get();
        $query = $query->num_rows();
        return $query;
    }



    //get payment report
    function get_user_income_by_year()
    {
        $this->db->select('r.*');
        $this->db->select_sum('r.total_amount', 'total');
        $this->db->from('payment_user r');
        if ($this->session->userdata('role' == 'user')) {
            $this->db->where('s.user_id', $this->session->userdata('id'));
        }
        $this->db->group_by("DATE_FORMAT(r.created_at,'%Y')");
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }

    //get payment report
    function get_user_income_by_date($date)
    {
        $this->db->select('r.*');
        $this->db->select_sum('r.total_amount', 'total');
        $this->db->from('payment_user r');
        $this->db->where('r.user_id', $this->session->userdata('id'));
        $this->db->where("DATE_FORMAT(r.created_at,'%Y-%m')", $date);
        $query = $this->db->get();
        $query = $query->result();
        if (empty($query)) {
            return '0';
        } else {
            return $query[0]->total;
        }
    }


    // get user payment
    function get_user_payment_details($puid)
    {
        $this->db->select('p.*, k.name as package_name, k.price, k.monthly_price, k.slug, u.name as user_name, u.phone, u.address, u.email');
        $this->db->from('payment p');
        $this->db->join('package k', 'k.id = p.package_id', 'LEFT');
        $this->db->join('users u', 'u.id = p.user_id', 'LEFT');
        $this->db->where('p.puid', $puid);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }

    // get payment
    function get_users_payment_lists($user_id)
    {
        $this->db->select('p.*, k.name as package_name, k.slug, u.name as user_name, u.phone, u.address, u.email');
        $this->db->from('payment p');
        $this->db->join('package k', 'k.id = p.package_id', 'LEFT');
        $this->db->join('users u', 'u.id = p.user_id', 'LEFT');
        $this->db->where('p.user_id', $user_id);
        //$this->db->where('p.status', 'verified');
        $this->db->order_by('p.id', 'DESC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }


    //get user info
    function get_user_info()
    {
        $this->db->select('u.*');
        $this->db->from('users u');
        $this->db->where('u.id', $this->session->userdata('id'));
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }


    // get_payment
    function get_user_payment($user_id)
    {
        $this->db->select('p.*, k.name as package');
        $this->db->from('payment p');
        $this->db->join('package k', 'k.id = p.package_id', 'LEFT');
        $this->db->where('p.user_id', $user_id);
        $this->db->order_by('p.id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }

    function get_user_sessions($mentor_id)
    {
        $this->db->select();
        $this->db->from('sessions as s');
        $this->db->where('user_id', $mentor_id);
        $this->db->order_by('id','ASC');
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }

    function count_free_session($mentor_id)
    {
        $this->db->select();
        $this->db->from('sessions as s');
        $this->db->where('user_id', $mentor_id);
        $this->db->where('price', '0');
        $query = $this->db->get();
        $query = $query->num_rows();
        return $query;
    }


    function get_group_slots($mentor_id,$session_id)
    {
        $this->db->select();
        $this->db->from('assign_time ');
        $this->db->where('user_id', $mentor_id);
        $this->db->where('session_id', $session_id);
        //$this->db->limit(2);
        $this->db->order_by('id','ASC');
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }


    /*
    *
    * rating query start
    *
    */
    

    // select function
    function count_mentor_ratings($mentor_id)
    {
        $this->db->select('r.*');
        $this->db->from('reviews as r');
        $this->db->where('r.user_id', $mentor_id);
        $query = $this->db->get();
        $query = $query->num_rows();  
        return $query;
    }

    function get_all_ratings($mentor_id)
    {
        $this->db->select('r.*, u.name as mentee_name, u.thumb as mentee_thumb, u.designation, u.company');
        $this->db->from('reviews as r');
        $this->db->join('users as u', 'u.id = r.mentee_id', 'LEFT');
        $this->db->where('r.user_id', $mentor_id);
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }


    function get_all_ratings_by_session($session_id)
    {
        $this->db->select('r.*, u.name as mentee_name, u.thumb as mentee_thumb, u.designation, u.company');
        $this->db->from('reviews as r');
        $this->db->join('users as u', 'u.id = r.mentee_id', 'LEFT');
        $this->db->where('r.session_id', $session_id);
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }



    function get_ratings_info($session_id)
    {
        $this->db->select('p.*');
        $this->db->select('(SELECT count(reviews.session_id)
                            FROM reviews 
                            WHERE (session_id = '.$session_id.')
                            )
                            AS total_user',TRUE);

        $this->db->select('(SELECT sum(reviews.rating)
                            FROM reviews
                            WHERE (session_id = '.$session_id.')
                            )
                            AS total_point',TRUE);

        $this->db->from('reviews p');
        $query = $this->db->get();
        $query = $query->row();
        return $query;
    }


    function get_total_rating_user($session_id)
    {
        $this->db->select('p.*');
        $this->db->select('count(p.session_id) as total_session');
        $this->db->from('reviews p');
        $this->db->where('p.session_id', $session_id);
        $query = $this->db->get();
        $query = $query->row();
        return $query->total_session;
    }

    function get_total_ratings_by_user($service_id)
    {
        $this->db->select('p.*');
        $this->db->select_sum('p.rating', 'total_rating');
        $this->db->from('ratings p');
        $this->db->where('p.service_id', $service_id);
        $query = $this->db->get();
        $query = $query->row();
        return $query->total_rating;
    }


    function get_single_ratings($session_id)
    {
        $this->db->select('p.*');

        $this->db->select('(SELECT count(reviews.id)
                            FROM reviews 
                                WHERE (session_id = '.$session_id.')
                            )
                            AS total_user',TRUE);


        $this->db->select('(SELECT count(reviews.id)
                            FROM reviews 
                                WHERE (session_id = '.$session_id.'
                                AND
                                rating = 5)
                            )
                            AS five',TRUE);

        $this->db->select('(SELECT count(reviews.id)
                            FROM reviews 
                                WHERE (session_id = '.$session_id.'
                                AND
                                rating = 4)
                            )
                            AS four',TRUE);

        $this->db->select('(SELECT count(reviews.id)
                            FROM reviews 
                                WHERE (session_id = '.$session_id.'
                                AND
                                rating = 3)
                            )
                            AS three',TRUE);

        $this->db->select('(SELECT count(reviews.id)
                            FROM reviews 
                                WHERE (session_id = '.$session_id.'
                                AND
                                rating = 2)
                            )
                            AS two',TRUE);

        $this->db->select('(SELECT count(reviews.id)
                            FROM reviews 
                                WHERE (session_id = '.$session_id.'
                                AND
                                rating = 1)
                            )
                            AS one',TRUE);

        $this->db->from('reviews p');
        $query = $this->db->get();
        $query = $query->row();
        return $query;
    }


    function check_session_rating($booking_id)
    {
        $this->db->select('*');
        $this->db->from('reviews');
        $this->db->where('booking_id', $booking_id);
        $query = $this->db->get();
        $query = $query->row();
        return $query;
    }

    /*
    *
    * rating query end
    *
    */


    

    


    /*
    *-------------------------------------------------------------------------------------------------
    * USER PANEL QUERY END
    *-------------------------------------------------------------------------------------------------
    */

    
    // Referral model

    function get_by_referral_user($id)
    {
        $this->db->select();
        $this->db->from('referrals');
        $this->db->where('user_id', $id);
        $this->db->where('status', 0);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }

    function get_referral_settings()
    {
        $this->db->select();
        $this->db->from('referral_settings');
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }

    function get_single_user($id)
    {
        $this->db->select();
        $this->db->from('users');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }

    function get_by_referral_id($id)
    {
        $this->db->select();
        $this->db->from('users');
        $this->db->where('referral_id', $id);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }


    function get_referrals($id)
    {
        $this->db->select();
        $this->db->from('referrals');
        $this->db->where('referrar_id', $id);
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    function get_referral_payouts($id)
    {
        $this->db->select();
        $this->db->from('referral_payouts');
        $this->db->where('user_id', $id);
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    function get_payouts_all()
    {
        $this->db->select();
        $this->db->from('referral_payouts');
        $this->db->where('status', 0);
        if (isset($_GET['transaction_id'])) {
            $this->db->like('transaction_id', $_GET['transaction_id']);
        }
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }



    function get_payouts_by_status()
    {
        $this->db->select();
        $this->db->from('referral_payouts');
        $this->db->where('status', 1);
        if (isset($_GET['transaction_id'])) {
            $this->db->like('transaction_id', $_GET['transaction_id']);
        }
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    function get_by_md5($id,$table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('md5(id)', $id);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }

    function get_withdraw_balance_by_user($id)
    {
        $this->db->select_sum('amount');
        $this->db->from('referral_payouts');
        $this->db->where('user_id', $id);
        $this->db->where('status', 1);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }

    function get_total_earn_by_user($id)
    {
        $this->db->select_sum('commision_amount');
        $this->db->from('referrals');
        $this->db->where('referrar_id', $id);
        //$this->db->where('status', 1);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }


    // message query----------


    public function mgs_with(){

        $user_id = $this->session->userdata('id');

        $this->db->select('sq.profile_id as user_id');
        $this->db->select('sq.profile_name as name');
        $this->db->select('sq.is_active');
        $this->db->select('sq.thumb');
        $this->db->select('sq.mgs_time');
        $this->db->select('n.message');

        $this->db->from('messages as n');
        $this->db->from(" 

            (SELECT 
                u.id AS profile_id,
                u.name AS profile_name,
                u.thumb AS thumb,u.is_active AS is_active,
                MAX(m.mgs_time) AS mgs_time 
                FROM
                messages AS m,
                users AS u 
                WHERE 
                CASE
                WHEN m.mgs_from = '$user_id' 
                THEN m.mgs_to = u.id 
                WHEN m.mgs_to = '$user_id' 
                THEN m.mgs_from = u.id 
                END 
                GROUP BY u.id) AS sq 

            ");

        $this->db->where("


            sq.mgs_time = n.mgs_time 
            AND 
            CASE
            WHEN n.mgs_from = '$user_id' 
            THEN n.mgs_to = sq.profile_id 
            WHEN n.mgs_to = '$user_id' 
            THEN n.mgs_from = sq.profile_id 
            END 


            ");

        $this->db->order_by('sq.mgs_time','DESC');
        $this->db->query('SET SQL_BIG_SELECTS=1'); 
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }


    
    public function mgs_with_details($mgs_with){

        $user_id = md5($this->session->userdata('id'));

        $this->db->select('m.*');
        $this->db->select('u.name');
        $this->db->select('u.thumb');
        $this->db->from('messages as m');
        $this->db->join('users as u','u.id = m.mgs_from','RIGHT');


        $this->db->group_start();
        $this->db->where('md5(m.mgs_from)',$mgs_with);
        $this->db->or_where('md5(m.mgs_from)',$user_id);
        $this->db->group_end();  


        $this->db->group_start();
        $this->db->where('md5(m.mgs_to)',$user_id);
        $this->db->or_where('md5(m.mgs_to)',$mgs_with);
        $this->db->group_end();  


        $this->db->order_by('m.id','ASC');
        $this->db->query('SET SQL_BIG_SELECTS=1'); 
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    function get_user_id_md5($u_md5_id){
        $this->db->select();
        $this->db->from('users');
        $this->db->where('md5(id)', $u_md5_id);
        $this->db->limit(1);
        $this->db->query('SET SQL_BIG_SELECTS=1'); 
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }

    function get_all_cotacts($id,$query=''){
        $this->db->select('m.*, u.name, u.thumb, u.id as user_id, u.is_active');
        $this->db->from('messages as m');
        $this->db->where('mgs_from',$id);
        $this->db->or_where('mgs_to',$id);
        $this->db->group_by('mgs_to');
        $this->db->join('users as u','u.id = m.mgs_to','LEFT');
        if(!empty($query)){
            $this->db->like('u.name',$query);
        }
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    

     public function get_unseen_messages($user_id, $contact_id){

        $this->db->select('m.*');
        $this->db->from('messages as m');
        $this->db->where('md5(m.mgs_from)',$contact_id);
        $this->db->where('md5(m.mgs_to)',$user_id);
        $this->db->where('mgs_seen', 0);
        $this->db->query('SET SQL_BIG_SELECTS=1'); 
        $query = $this->db->get();
        $query = $query->num_rows();  
        return $query;
    }

    public function my_messages_make_seen($id){
        $this->db->where('md5(mgs_from)',$id);
        $this->db->where('mgs_to',$this->session->userdata('id'));
        $this->db->update('messages', array('mgs_seen' => 1));
    }

    // function get_msg_count($mgs_to,$mgs_from)
    // {
    //     $this->db->select();
    //     $this->db->from('messages');
    //     $this->db->where('mgs_to',$mgs_to);
    //     $this->db->where('mgs_from',$mgs_from);
    //     $query = $this->db->get();
    //     $query = $query->num_rows();  
    //     return $query;
    // }

    // End message query





    // Notifications Start

    function my_notifications(){
        $this->db->select('n.*');
        $this->db->select('u.name as name, u.thumb');
        $this->db->from('notifications as n');
        $this->db->join('users as u','u.id = n.action_id','LEFT');
        if($this->session->userdata('role') == 'admin'){
            $this->db->where('user_id', 0);
        }else{
            $this->db->where('user_id', $this->session->userdata('id')); 
        }
        $this->db->order_by('id','DESC');
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->limit(6); 
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    function get_all_notification($total, $limit, $offset){
        $this->db->select('n.*');
        $this->db->select('u.name as name, u.thumb');
        $this->db->from('notifications as n');
        $this->db->join('users as u','u.id = n.action_id','LEFT');
        if($this->session->userdata('role') == 'admin'){
            $this->db->where('user_id', 0);
        }else{
            $this->db->where('user_id', $this->session->userdata('id')); 
        }
        $this->db->order_by('id','DESC');
        $this->db->query('SET SQL_BIG_SELECTS=1'); 
        if ($total == 1) {
            $query = $this->db->get();
            $query = $query->num_rows();
            return $query;
        } else {
            $query = $this->db->get('', $limit, $offset);
            $query = $query->result();
            return $query;
        }
    }

    public function my_notifications_make_seen(){
        if($this->session->userdata('role') == 'admin'){
            $this->db->where('user_id', 0);
        }else{
            $this->db->where('user_id', $this->session->userdata('id')); 
        }
        $this->db->update('notifications', array('seen' => 1));
    }

    function count_unseen_notification(){
        $this->db->select('');
        $this->db->from('notifications');
        if($this->session->userdata('role') == 'admin'){
            $this->db->where('user_id', 0);
        }else{
            $this->db->where('user_id', $this->session->userdata('id')); 
        }
        $this->db->where('seen',0);
        $this->db->order_by('id','DESC');
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $query = $this->db->get();
        $query = $query->num_rows();  
        return $query;
    }


    // End notifications query

    // Start payout query

    function get_user_earnings($user_id)
    {
        $this->db->select('p.*');
        $this->db->select_sum('p.total_amount', 'net_income');
        $this->db->from('payment_user as p');
        $this->db->where('p.type', 'wallet');
        $this->db->where('p.user_id', $user_id);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }

    function get_user_withdraws($user_id)
    {
        $this->db->select('p.*');
        $this->db->select_sum('p.amount', 'net_income');
        $this->db->from('payouts as p');
        $this->db->where('p.status', 1);
        $this->db->where('p.user_id', $user_id);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }

    function get_total_payout_request()
    {
        $this->db->select('');
        $this->db->from('payouts as p');
        $this->db->where('p.status', 0);
        $query = $this->db->get();
        $query = $query->num_rows();
        return $query;
    }

    

    function get_payout_users()
    {
        $this->db->select('p.*, u.name as user_name, u.balance');
        $this->db->from('payment_user as p');
        $this->db->join('users as u', 'u.id = p.user_id', 'LEFT');
        $this->db->where('p.type', 'wallet');
        $this->db->group_by('p.user_id');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    } 


    
    function get_payouts($status, $user_id, $total, $limit, $offset){

        $this->db->select('p.*, u.name as user_name, u.thumb, u.balance');
        $this->db->from('payouts as p');
        $this->db->join('users as u', 'u.id = p.user_id', 'LEFT');
        if ($user_id != 0) {
            $this->db->where('p.user_id', $user_id);
        }
        if ($status != 2) {
            $this->db->where('p.status', $status);
        }
        if (isset($_GET['transaction_id'])) {
            $this->db->like('p.transaction_id', $_GET['transaction_id']);
        }
        $this->db->order_by('p.id', 'DESC');
        
        if ($total == 1) {
            $query = $this->db->get();
            $query = $query->num_rows();
            return $query;
        } else {
            $query = $this->db->get('', $limit, $offset);
            $query = $query->result();
            return $query;
        }
    }


    function get_skill_category($id)
    {
        $this->db->select();
        $this->db->from('skills');
        $this->db->where('category_id', $id);
        $this->db->where('status', 1);
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }



    function get_top_booked_sessions()
    {
        $this->db->select('s.id, s.name');
        $this->db->from('sessions s');
        if ($this->session->userdata('role') == 'user') {
            $this->db->where('s.user_id', $this->session->userdata('id'));
        }
        $query = $this->db->get();
        $query = $query->result();  
        foreach ($query as $key => $value) {
            $this->db->select('b.*');
            $this->db->from('session_booking b');
            $this->db->where('b.session_id',$value->id);
            $query2 = $this->db->get();
            $query2 = $query2->num_rows();
            $query[$key]->total = $query2;
        }
        return $query;
    }

    function get_top_mentees()
    {
        $this->db->select('s.id, s.mentee_id, u.name as mentee_name');
        $this->db->from('session_booking s');
        if ($this->session->userdata('role') == 'user') {
            $this->db->where('s.user_id', $this->session->userdata('id'));
        }
        $this->db->join('users as u', 'u.id = s.mentee_id', 'LEFT');
        $this->db->group_by('s.mentee_id');
        $query = $this->db->get();
        $query = $query->result();  
        foreach ($query as $key => $value) {
            $this->db->select('b.*');
            $this->db->from('session_booking b');
            $this->db->where('b.mentee_id',$value->mentee_id);
            if ($this->session->userdata('role') == 'user') {
                $this->db->where('b.user_id', $this->session->userdata('id'));
            }
            
            $query2 = $this->db->get();
            $query2 = $query2->num_rows();
            $query[$key]->total = $query2;
        }
        return $query;
    }



     function get_top_mentors()
    {
        $this->db->select('s.id, s.user_id, u.name as mentor_name');
        $this->db->from('session_booking s');
        $this->db->join('users as u', 'u.id = s.user_id', 'LEFT');
        $this->db->group_by('s.user_id');
        $query = $this->db->get();
        $query = $query->result();  
        foreach ($query as $key => $value) {
            $this->db->select('b.*');
            $this->db->from('session_booking b');
            $this->db->where('b.user_id',$value->user_id);
            
            $query2 = $this->db->get();
            $query2 = $query2->num_rows();
            $query[$key]->total = $query2;
        }
        return $query;
    }




    function get_top_countries()
    {
        $this->db->select('s.id, u.country, c.name as country_name');
        $this->db->from('session_booking s');
        if ($this->session->userdata('role') == 'user') {
            $this->db->where('s.user_id', $this->session->userdata('id'));
        }
        
        $this->db->join('users as u', 'u.id = s.mentee_id', 'LEFT');
        $this->db->join('country as c', 'c.id = u.country', 'LEFT');
        $this->db->group_by('u.country');
        $query = $this->db->get();
        $query = $query->result();  
        foreach ($query as $key => $value) {
            $this->db->select('s.*');
            $this->db->from('session_booking s');
            $this->db->join('users as u', 'u.id = s.mentee_id', 'LEFT');
            $this->db->where('u.country',$value->country);
            if ($this->session->userdata('role') == 'user') {
                $this->db->where('s.user_id', $this->session->userdata('id'));
            }
            $query2 = $this->db->get();
            $query2 = $query2->num_rows();
            $query[$key]->total = $query2;
        }
        return $query;
    }

    function get_all_brands()
    {
        $this->db->select();
        $this->db->from('brands');
        if($this->session->userdata('role') == 'admin'){
            $this->db->where('user_id',0);
        }else{
            $this->db->where('user_id', $this->session->userdata('id')); 
        }
       
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

/**
 * Author Shahin Alam
 * This function will check comming is already exist or not;
 */
public function Is_already_register($id)
 {
  $this->db->where('email', $id);
  $query = $this->db->get('users');
  if($query->num_rows() > 0)
  {
    $user = $query->row();
   $data = array(
        'id' => $user->id,
        'name' => $user->name,
        'slug' => $user->slug,
        'thumb' => $user->thumb,
        'email' =>$user->email,
        'role' =>$user->role,
        'parent' =>0,
        'logged_in' => TRUE,
    );
    $data = $this->security->xss_clean($data);
    $this->session->set_userdata($data);

    $data = array(
        'is_active' => 1,
        'last_active' => my_date_now(),
    );
    $data = $this->security->xss_clean($data);
    $this->admin_model->edit_option($data , $user->id,'users');


   return true;
  }
  else
  {
   return false;
  }
 }


/**
 * Author Shahin Alam
 *  This function will update the information if user is exist in database;
 */

public function Update_user_data($data, $id)
 {
  $this->db->where('google_auth_id', $id);
  $this->db->update('users', $data);
  
    $query = $this->db->get_where('users', array('google_auth_id' => $id));
    if ($query->num_rows() > 0) {
    $user = $query->row();
    $data = array(
        'id' => $user->id,
        'name' => $user->name,
        'slug' => $user->slug,
        'thumb' => $user->thumb,
        'email' =>$user->email,
        'role' =>$user->role,
        'parent' =>0,
        'logged_in' => TRUE,
    );
    $data = $this->security->xss_clean($data);
    $this->session->set_userdata($data);

    $data = array(
        'is_active' => 1,
        'last_active' => my_date_now(),
    );
    $data = $this->security->xss_clean($data);
    $this->admin_model->edit_option($data , $user->id,'users');
    }

return true;
 }

/**
 * Author Shahin Alam
 *  This function will store user if the user not exist in database;
 */

 public function Insert_user_data($data)
 {
  $this->db->insert('users', $data);
  $id = $this->db->insert_id();

    $query = $this->db->get_where('users', array('id' => $id));
    if ($query->num_rows() > 0) {
    $user = $query->row();
    $data = array(
        'id' => $user->id,
        'name' => $user->name,
        'slug' => $user->slug,
        'thumb' => $user->thumb,
        'email' =>$user->email,
        'role' =>$user->role,
        'parent' =>0,
        'logged_in' => TRUE,
        'status'=>1,
        'is_active'=>1,
        'last_active' => my_date_now()
    );
    $data = $this->security->xss_clean($data);
    $this->session->set_userdata($data);

    // $data = array(
    //     'is_active' => 1,
    //     'last_active' => my_date_now(),
    // );
    // $data = $this->security->xss_clean($data);
    // $this->admin_model->edit_option($data , $user->id,'users');
    return true;
    }
    return true;

 }

 function get_email_by_slug($slug){
    $this->db->select();
    $this->db->from('email_templates');
    $this->db->where('slug', $slug);
    $query = $this->db->get();
    $query = $query->row();  
    return $query;
}


}