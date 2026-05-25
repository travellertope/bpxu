<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payment extends Home_Controller {

    public function __construct()
    {
        parent::__construct();
    }


    public function index()
    {
        $data = array();
        $data['page_title'] = 'Payment';      
        $data['page'] = 'Settings'; 
        $payment = $this->admin_model->get_my_payment();
        $data['payment_id'] = $payment->puid;
        $data['my_payment'] = $payment;
        $data['package'] = $this->common_model->get_package_by_slug($payment->package);
        $data['main_content'] = $this->load->view('admin/payment',$data,TRUE);
        $this->load->view('admin/index',$data);
    }


    public function settings(){
        $data = array();
        $data['page_title'] = 'Payment Settings';      
        $data['page'] = 'Settings';   
        $data['packages'] = $this->admin_model->select_asc('package');
        $data['currencies'] = $this->admin_model->select_asc('country');
        $data['users'] = $this->common_model->get_users();
        $data['main_content'] = $this->load->view('admin/payment_settings',$data,TRUE);
        $this->load->view('admin/index',$data);
    }


    //update settings
    public function update(){

        if ($_POST) {
            
            if(!empty($this->input->post('paypal_payment', true))){$paypal_payment = $this->input->post('paypal_payment', true);}
            else{$paypal_payment = 0;}

            if(!empty($this->input->post('stripe_payment', true))){$stripe_payment = $this->input->post('stripe_payment', true);}
            else{$stripe_payment = 0;}

            if(!empty($this->input->post('razorpay_payment', true))){$razorpay_payment = $this->input->post('razorpay_payment', true);}
            else{$razorpay_payment = 0;}

            if(!empty($this->input->post('paystack_payment', true))){$paystack_payment = $this->input->post('paystack_payment', true);}
            else{$paystack_payment = 0;}

            if(!empty($this->input->post('flutterwave_payment', true))){$flutterwave_payment = $this->input->post('flutterwave_payment', true);}
            else{$flutterwave_payment = 0;}

            if(!empty($this->input->post('mercado_payment'))){$mercado_payment = $this->input->post('mercado_payment', true);}
            else{$mercado_payment = 0;}

            if(!empty($this->input->post('enable_offline_payment', true))){$enable_offline_payment = $this->input->post('enable_offline_payment', true);}
            else{$enable_offline_payment = 0;}

            
            $data = array(
                'country' => $this->input->post('country', true),
                'offline_details' => $this->input->post('offline_details'),
                'paypal_mode' => $this->input->post('paypal_mode', true),
                'paypal_email' => $this->input->post('paypal_email', true),
                'publish_key' => $this->input->post('publish_key', true),
                'secret_key' => $this->input->post('secret_key', true),
                'paystack_secret_key' => $this->input->post('paystack_secret_key', true),
                'paystack_public_key' => $this->input->post('paystack_public_key', true),
                'razorpay_key_id' => $this->input->post('razorpay_key_id', true),
                'razorpay_key_secret' => $this->input->post('razorpay_key_secret', true),
                'paypal_payment' => $paypal_payment,
                'stripe_payment' => $stripe_payment,
                'razorpay_payment' => $razorpay_payment,
                'paystack_payment' => $paystack_payment,
                'enable_offline_payment' => $enable_offline_payment,
                'flutterwave_payment' => $flutterwave_payment,
                'flutterwave_public_key' => $this->input->post('flutterwave_public_key', true),
                'flutterwave_secret_key' => $this->input->post('flutterwave_secret_key', true),
                'mercado_payment' => $mercado_payment,
                'mercado_api_key' => $this->input->post('mercado_api_key', true),
                'mercado_token' => $this->input->post('mercado_token', true),
                'mercado_currency' => $this->input->post('mercado_currency', true)
            );
            $data = $this->security->xss_clean($data);
            $this->admin_model->edit_option($data, 1, 'settings');
            $this->session->set_flashdata('msg', trans('updated-successfully'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }



    public function offline()
    {   
        if($_POST)
        {   
            $package = $this->admin_model->get_by_id($this->input->post('package', true), 'package');
            $payment = $this->admin_model->get_user_payment($this->input->post('user', true));

            if($this->input->post('billing_type', true) =='monthly'):
                $amount = round($package->monthly_price); 
                $expire_on = date('Y-m-d', strtotime('+1 month'));
            else:
                $amount = round($package->price); 
                $expire_on = date('Y-m-d', strtotime('+12 month'));
            endif;
            
            //validate inputs
            $this->form_validation->set_rules('user', trans('user'), 'required');
            $this->form_validation->set_rules('package', trans('package'), 'required');
            $this->form_validation->set_rules('status', trans('payment-status'), 'required');

            if ($this->form_validation->run() === false) {
                $this->session->set_flashdata('errors', validation_errors());
                redirect(base_url('admin/payment'));
            } else {

                $data=array(
                    'user_id' => $this->input->post('user', true),
                    'package_id' => $package->id,
                    'billing_type' => $this->input->post('billing_type', true),
                    'amount' => $amount,
                    'status' => $this->input->post('status', true),
                    'created_at' => my_date_now(),
                    'expire_on' => $expire_on
                );
                $data = $this->security->xss_clean($data);

                if (empty($payment)) {
                    $this->admin_model->insert($data, 'payment');
                } else {
                    $this->admin_model->update_payment($data, $this->input->post('user', true), 'payment');
                }

                $this->session->set_flashdata('msg', trans('inserted-successfully')); 
                redirect(base_url('admin/users'));

            }
        }      
        
    }


    public function approve_offline($id) 
    {
        $data = array(
            'status' => 'verified'
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id, 'payment');
        $this->session->set_flashdata('msg','Updated Successfully'); 
        redirect($_SERVER['HTTP_REFERER']);
    }


    public function receipt($puid)
    {
        //check auth
        if (!is_admin() && !is_user()) {
            redirect(base_url());
        }

        $data = array();
        $data['page_title'] = 'Payment Receipt'; 
        $data['user'] = $this->admin_model->get_user_payment_details($puid);

        if (!is_admin()) {
            if ($data['user']->user_id != $this->session->userdata('id')) {
                redirect(base_url());
            }
        }

        $this->load->view('admin/payment/payment_invoice_receipt',$data);
    }

    public function lists()
    {
        if (!is_user()) {
            redirect(base_url());
        }

        $data = array();
        $data['page_title'] = 'Payment list';
        $data['payments'] = $this->admin_model->get_users_payment_lists(user()->id);
        $data['main_content'] = $this->load->view('admin/payment/payment_invoice_lists',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    // public function transactions()
    // {
    //     //check auth
    //     if (!is_admin() && !is_user()) {
    //         redirect(base_url());
    //     }

    //     $data = array();
    //     $data['page_title'] = 'Transactions';
    //     $data['payments'] = $this->admin_model->get_payment_lists(0);
    //     $data['main_content'] = $this->load->view('admin/payment/transactions',$data,TRUE);
    //     $this->load->view('admin/index',$data);
    // }


    public function transactions()
    {
        //check auth
        if (!is_user() && !is_admin()) {
            redirect(base_url());
        }

        $data = array();
        $data['page_title'] = 'Transactions';
        $data['payments'] = $this->admin_model->get_customer_payment_lists(0);
        $data['main_content'] = $this->load->view('admin/payment/mentee_transactions',$data,TRUE);
        $this->load->view('admin/index',$data);
    }


    public function customer_receipt($puid)
    {
        //check auth
        if (!is_user() && !is_admin()) {
            redirect(base_url());
        }

        $data = array();
        $data['page_title'] = 'Payment Receipt'; 
        $data['user'] = $this->admin_model->get_customer_payment_details($puid);

        $this->load->view('admin/payment/mentee_payment_receipt',$data);
    }


    public function upgrade()
    {
        $data = array();
        $data['page_title'] = 'Upgrade';      
        $data['page'] = 'Payment'; 
        $payment = $this->admin_model->get_my_payment();
        $data['payment_id'] = $payment->puid;
        $data['package'] = $this->common_model->get_package_by_slug($payment->package);
        $data['main_content'] = $this->load->view('admin/upgrade',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    
    public function upgrade_operation() 
    {
        $data = array(
            'account_type' => 'pro'
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, user()->id, 'users');

        $pkg = $this->common_model->get_package_price('pro');
        $payment = $this->common_model->get_user_payment(user()->id);

        //create payment
        $pay_data=array(
            'package' => 'pro',
            'amount' => $pkg->price,
            'status' => 'pending',
            'created_at' => my_date_now()
        );
        $pay_data = $this->security->xss_clean($pay_data);
        $this->admin_model->update($pay_data, $payment->id, 'payment');

        if (get_settings()->enable_paypal == 1) {
            redirect(base_url('admin/payment'));
        } else {
            redirect(base_url('admin/profile'));
        }
        
    }

    public function deactive($id) 
    {
        $data = array(
            'status' => 0
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id,'testimonials');
        $this->session->set_flashdata('msg', trans('deactivate-successfully')); 
        redirect(base_url('admin/testimonial'));
    }

    public function delete($id)
    {
        $this->admin_model->delete($id,'testimonials'); 
        echo json_encode(array('st' => 1));
    }






    //******* User Payments *******//

    public function user()
    {
        //check auth
        if (!is_user()) {
            redirect(base_url());
        }
        
        $data = array();
        $data['page_title'] = 'Payment Settings'; 
        $data['page'] = 'Settings';
        $data['settings'] = $this->admin_model->get('settings');
        $data['currencies'] = $this->admin_model->select_asc('country');
        $data['packages'] = $this->admin_model->select_asc('package');
        $data['main_content'] = $this->load->view('admin/user/user_payment_settings',$data,TRUE);
        $this->load->view('admin/index',$data);
    }


    //update payment settings
    public function user_update(){
        //check auth
        if (!is_user()) {
            redirect(base_url());
        }
        
        if ($_POST) {
            
            if(!empty($this->input->post('paypal_payment', true))){$paypal_payment = $this->input->post('paypal_payment', true);}
            else{$paypal_payment = 0;}

            if(!empty($this->input->post('stripe_payment', true))){$stripe_payment = $this->input->post('stripe_payment', true);}
            else{$stripe_payment = 0;}

            if(!empty($this->input->post('razorpay_payment', true))){$razorpay_payment = $this->input->post('razorpay_payment', true);}
            else{$razorpay_payment = 0;}

            if(!empty($this->input->post('paystack_payment', true))){$paystack_payment = $this->input->post('paystack_payment', true);}
            else{$paystack_payment = 0;}

            if(!empty($this->input->post('flutterwave_payment', true))){$flutterwave_payment = $this->input->post('flutterwave_payment', true);}
            else{$flutterwave_payment = 0;}
            
            $country = $this->admin_model->get_by_id($this->input->post('country'), 'country');

            $data = array(
                'country' => 0,
                'currency' => 'USD',
                'paypal_mode' => $this->input->post('paypal_mode', true),
                'paypal_email' => $this->input->post('paypal_email', true),
                'publish_key' => $this->input->post('publish_key', true),
                'secret_key' => $this->input->post('secret_key', true),
                'razorpay_key_id' => $this->input->post('razorpay_key_id', true),
                'razorpay_key_secret' => $this->input->post('razorpay_key_secret', true),
                'paystack_secret_key' => $this->input->post('paystack_secret_key', true),
                'paystack_public_key' => $this->input->post('paystack_public_key', true),
                'paystack_payment' => $paystack_payment,
                'paypal_payment' => $paypal_payment,
                'stripe_payment' => $stripe_payment,
                'razorpay_payment' => $razorpay_payment, 
                'flutterwave_payment' => $flutterwave_payment,
                'flutterwave_public_key' => $this->input->post('flutterwave_public_key', true),
                'flutterwave_secret_key' => $this->input->post('flutterwave_secret_key', true)
            );
            $data = $this->security->xss_clean($data);
            $this->admin_model->edit_option($data, user()->id, 'users');
            $this->session->set_flashdata('msg', 'Updated Successfully'); 
            redirect($_SERVER['HTTP_REFERER']);
        }
    }


    // session payment
    public function record_payment($booking_id)
    {   
        $booking = $this->admin_model->get_by_id($booking_id, 'session_booking');
        $uid = random_string('numeric',5);
        
        $coupon = check_coupon_mentee($booking->session_id, $booking->mentee_id);
        if(empty($coupon)){
          $session = $this->admin_model->get_by_id($booking->session_id, 'sessions');
          $amount = $booking->price;
        }else{
          $discount = $coupon->discount;
          $discount_amount = ($booking->price * $discount)/ 100 ;
          $amount = $booking->price - $discount_amount;
        }
        
        $payment_method = 'offline';
        $type = 'wallet';
        $total_amount = get_commission($amount, settings()->commission_rate);
        $commission_amount = get_commission_rate($amount, settings()->commission_rate);
        $commission_rate = settings()->commission_rate;

        $pay_data = array(
            'user_id' => $booking->user_id,
            'customer_id' => $booking->mentee_id,
            'booking_id' => $booking->id,
            'puid' => $uid,
            'status' => 'verified',
            'amount' => $amount,
            'total_amount' => $total_amount,
            'commission_amount' => $commission_amount,
            'commission_rate' => $commission_rate,
            'payment_method' => $payment_method,
            'type' => $type,
            'created_at' => my_date_now()
        );
        $pay_data = $this->security->xss_clean($pay_data);
        $response = $this->common_model->insert($pay_data, 'payment_user');

        if ($response) {
            $balance = $total_amount * 100;
            $user_data = array(
                'balance' => $balance + user()->balance,
                'total_sales' => user()->total_sales + 1
            );
            $this->common_model->edit_option($user_data, user()->id, 'users');
        }

        $this->session->set_flashdata('msg', trans('inserted-successfully')); 
        redirect($_SERVER['HTTP_REFERER']);
    }











    //** ------ customer Payments ------ **//

    public function customer($amp_id){
        $data = array();
        $data['appointment'] = $this->admin_model->get_by_id($amp_id, 'appointments');
        $data['appointment_id'] = $data['appointment']->id;
        $data['user'] = $this->admin_model->get_by_id($data['appointment']->user_id, 'users');
        $data['main_content'] = $this->load->view('admin/user/patient_payment', $data, TRUE);
        $this->load->view('admin/index', $data);
    }


    public function mercado(){

        $booking = $this->admin_model->get_by_id($this->session->userdata('booking_id'), 'session_booking');
        $user = $this->admin_model->get_by_id($booking->user_id, 'users');

        $mercado_token = settings()->mercado_token;
        $access_token = $mercado_token;
        $respuesta = array(
            'Payment' => $_GET['payment_id'],
            'Status' => $_GET['status'],
            'MerchantOrder' => $_GET['merchant_order_id']        
        ); 
        MercadoPago\SDK::setAccessToken($access_token);
        $merchant_order = $_GET['payment_id'];

        $payment = MercadoPago\Payment::find_by_id($merchant_order);
        $merchant_order = MercadoPago\MerchantOrder::find_by_id($payment->order->id);

        //$merchant_order->payments
        redirect(base_url('admin/payment/payment_success/'.$booking->id.'/mercadopago'));

    }
    

    public function stripe_booking_payment()
    {
        $booking_id = $this->input->post('booking_id');
        $booking = $this->admin_model->get_by_id($booking_id, 'session_booking');
        $user = $this->admin_model->get_by_id($booking->user_id, 'users');
        $currency = get_currency_by_country(settings()->country)->currency_code;

        $coupon = check_coupon_mentee($booking->session_id, $booking->mentee_id);
        if(empty($coupon)){
          $session = $this->admin_model->get_by_id($booking->session_id, 'sessions');
          $amount = $booking->price;
        }else{
          $discount = $coupon->discount;
          $discount_amount = ($booking->price * $discount)/ 100 ;
          $amount = $booking->price - $discount_amount;
        }


        require_once('application/libraries/stripe-php/init.php');
        \Stripe\Stripe::setApiKey(settings()->secret_key);
        
        try {
            $customer = \Stripe\Customer::create(array(
                'name' => $user->name,
                'email' => $user->email,
                'source'  => $this->input->post('stripeToken')
            ));

            $charge = \Stripe\Charge::create ([
                "customer" => $customer,
                "amount" => $amount*100,
                "currency" => $currency,
                "description" => "Session payment ".get_settings()->site_name 
            ]);
            $chargeJson = $charge->jsonSerialize();
            
            $amount                  = $chargeJson['amount']/100;
            $balance_transaction     = $chargeJson['balance_transaction'];
            $currency                = $chargeJson['currency'];
            $status                  = $chargeJson['status'];
            $payment = 'success';
        }catch(Exception $e) { 
            $error = $e->getMessage(); 
            $this->session->set_flashdata('error', $error);
            $payment = 'failed';
        }

        if($payment == 'success'):
            redirect(base_url('admin/payment/payment_success/'.$booking->id.'/stripe'));
        else:
            redirect(base_url('admin/payment/payment_msg/error'.$booking->id));
        endif;
    }



     //payment success
    public function payment_success($booking_id, $payment_method='')
    {   

        if (settings()->type != 'live') {
            redirect($_SERVER['HTTP_REFERER']);
        }


        $booking = $this->admin_model->get_by_id($booking_id, 'session_booking');
        $user = $this->admin_model->get_by_id($booking->user_id, 'users');
        $uid = random_string('numeric',5);
        
        $coupon = check_coupon_mentee($booking->session_id, $booking->mentee_id);
        if(empty($coupon)){
          $session = $this->admin_model->get_by_id($booking->session_id, 'sessions');
          $amount = $booking->price;
        }else{
          $discount = $coupon->discount;
          $discount_amount = ($booking->price * $discount)/ 100 ;
          $amount = $booking->price - $discount_amount;
        }
        
        if (isset($payment_method) && $payment_method == 'stripe') {
            $payment_method = 'stripe';
        }else if(isset($payment_method) && $payment_method == 'razorpay'){
            $payment_method = 'razorpay';
        }else if(isset($payment_method) && $payment_method == 'paystack'){
            $payment_method = 'paystack';
        }else if(isset($payment_method) && $payment_method == 'flutterwave'){
            $payment_method = 'flutterwave';
        }else if(isset($payment_method) && $payment_method == 'mercadopago'){
            $payment_method = 'mercadopago';
        }else {
            $payment_method = 'paypal';
        }


        $total_amount = get_commission($amount, settings()->commission_rate);
        $commission_amount = get_commission_rate($amount, settings()->commission_rate);
        $commission_rate = settings()->commission_rate;

        $pay_data = array(
            'user_id' => $booking->user_id,
            'customer_id' => $booking->mentee_id,
            'booking_id' => $booking->id,
            'puid' => $uid,
            'status' => 'verified',
            'amount' => $amount,
            'total_amount' => $total_amount,
            'commission_amount' => $commission_amount,
            'commission_rate' => $commission_rate,
            'payment_method' => $payment_method,
            'type' => 'wallet',
            'created_at' => my_date_now()
        );
        $pay_data = $this->security->xss_clean($pay_data);
        $response = $this->common_model->insert($pay_data, 'payment_user');



        //affiliate code
        $referral_settings = $this->admin_model->get_referral_settings();

        if ($referral_settings->is_enable == 1) {
            $register_user = $this->admin_model->get_by_referral_user($user->id);
   
            $commision = $referral_settings->commision_rate;
            $commision_amount = ($commision * $amount) / 100; 

            $ref_data=array(
                'status' => 1,
                'amount' => $amount,
                'commision' => $commision,
                'commision_amount' => $commision_amount
            );
            $this->admin_model->edit_option($ref_data, $register_user->id, 'referrals');


            $user_mentor = $this->admin_model->get_by_referral_id($register_user->referrar_id);
            
            if (!empty($register_user)) {
                $user_id = $user_mentor->id;
                $ref_earn = $user_mentor->referral_earn;
                $update_balance = $ref_earn + $commision_amount;

                $earn_data = array(
                    'referral_earn' => $update_balance,
                );

                $earn_data = $this->security->xss_clean($earn_data);
                $this->admin_model->edit_option($earn_data, $user_id, 'users');
            }
        }

        //affiliate code


        // notify this
        $mentee = $this->admin_model->get_by_id($booking->mentee_id, 'users');
        $text_currency = $this->admin_model->get_by_id(settings()->country, 'country')->currency_symbol;
        $notify = array(
            'user_id' => $user->id,
            'action_id' => $booking->mentee_id,
            'content_id' => 0,
            'text' => $mentee->name." complete the payment of".' ' .$text_currency. $amount .' '. 'for booked session : '.$session->name,
            'noti_type' => 9,
            'noti_time' => my_date_now()
        );
        $notify = $this->security->xss_clean($notify);
        $this->common_model->insert($notify, 'notifications');

        $notify = array(
            'user_id' => 0,
            'action_id' => $booking->mentee_id,
            'content_id' => 0,
            'text' => $mentee->name." complete the payment of".' ' .$text_currency. $amount .' '. 'for booked session : '.$session->name.' of '.$user->name,
            'noti_type' => 9,
            'noti_time' => my_date_now()
        );
        $notify = $this->security->xss_clean($notify);
        $this->common_model->insert($notify, 'notifications');

        // notify End


        if ($response) {
            $balance = $total_amount * 100;
            $user_data = array(
                'balance' => $balance + $user->balance,
                'total_sales' => $user->total_sales + 1
            );
            $this->common_model->edit_option($user_data, $user->id, 'users');

            redirect(base_url('admin/payment/payment_msg/success/'.$booking->id));
        }else{
            redirect(base_url('admin/payment/payment_msg/error/'.$booking->id));
        }

    }


    public function offline_payment_customer($booking_id)
    {   
        $booking = $this->admin_model->get_by_md5_id($booking_id, 'session_booking');
        $user = $this->admin_model->get_by_id($booking->user_id, 'users');
        $uid = random_string('numeric',5);
        
        $coupon = check_coupon_mentee($booking->session_id, $booking->mentee_id);
        if(empty($coupon)){
          $session = $this->admin_model->get_by_id($booking->session_id, 'sessions');
          $amount = $booking->price;
        }else{
          $discount = $coupon->discount;
          $discount_amount = ($booking->price * $discount)/ 100 ;
          $amount = $booking->price - $discount_amount;
        }
    

        $file_name = 'proof_'.random_string('numeric',6).'.'.pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

        if (empty($_FILES['file']['name'])) {
            $this->session->set_flashdata('msg', trans('inserted-successfully')); 
            redirect($_SERVER['HTTP_REFERER']); exit();
        } else {
            $file_name = $file_name;
        }

        if (!empty($_FILES['file']['name'])) {
            $config['upload_path']          = './uploads/files'; //file save path
            $config['allowed_types']        = 'pdf|gif|jpg|png|JPG|GIF|PNG|jpeg|JPEG';
            $config['max_size']             = 10000;
            $config['file_name'] = $file_name;


            $this->load->library('upload', $config);
            if ( ! $this->upload->do_upload('file')){
                $error = array('error' => $this->upload->display_errors());
            }else{
                $data = array('upload_data' => $this->upload->data());
            }
        }

        $total_amount = get_commission($amount, settings()->commission_rate);
        $commission_amount = get_commission_rate($amount, settings()->commission_rate);
        $commission_rate = settings()->commission_rate;

        $pay_data = array(
            'user_id' => $booking->user_id,
            'customer_id' => $booking->mentee_id,
            'booking_id' => $booking->id,
            'puid' => $uid,
            'status' => 'pending',
            'amount' => $amount,
            'total_amount' => $total_amount,
            'commission_amount' => $commission_amount,
            'commission_rate' => $commission_rate,
            'payment_method' => 'offline',
            'proof' => $file_name,
            'created_at' => my_date_now()
        );
        $pay_data = $this->security->xss_clean($pay_data);
        $this->admin_model->insert($pay_data, 'payment_user');
        
        $this->session->set_flashdata('msg', trans('inserted-successfully')); 
        redirect(base_url('admin/sessions/booking'));

    }

    public function approve_offline_customer($id) 
    {
        $data = array(
            'status' => 'verified'
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id, 'payment_user');
        $this->session->set_flashdata('msg','Updated Successfully'); 
        redirect($_SERVER['HTTP_REFERER']);
    }



    //payment cancel
    public function payment_msg($msg, $id='')
    {   
        $data = array();
        $data['pay_status'] = $msg;
        $data['booking_id'] = $id;
        $data['main_content'] = $this->load->view('admin/user/payment_msg',$data,TRUE);
        $this->load->view('admin/index',$data);
    }



    //payment cancel
    public function offline_payment($amp_id)
    {   
        $appointment = $this->admin_model->get_by_id($amp_id, 'appointments');
        $user = $this->admin_model->get_by_id($appointment->user_id, 'users');
        $amount = evisit_settings($user->id)->price;
        $uid = random_string('numeric',5);
        $payment_method = 'offline';
        
        $pay_data = array(
            'user_id' => $user->id,
            'patient_id' => $appointment->patient_id,
            'appointment_id' => $appointment->id,
            'puid' => $uid,
            'status' => 'verified',
            'amount' => $amount,
            'payment_method' => $payment_method,
            'created_at' => my_date_now()
        );
        $pay_data = $this->security->xss_clean($pay_data);
        $response = $this->common_model->insert($pay_data, 'payment_user');
        $this->session->set_flashdata('msg', trans('inserted-successfully')); 
        redirect($_SERVER['HTTP_REFERER']);
    }


    public function success_msg(){
        $data = array();
        $data['success_msg'] = 'Success';
        $data['main_content'] = $this->load->view('admin/user/payment_user_msg',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

}
	

