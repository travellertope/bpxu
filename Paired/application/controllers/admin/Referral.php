<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Referral extends Home_Controller {

    public function __construct()
    {
        parent::__construct();
        //check auth
        if (!is_user() && !is_admin()) {
            redirect(base_url());
        }
    }


    public function settings()
    {
        $data = array();
        $data['page_title'] = 'Referral_Settings';      
        $data['page'] = 'Affiliate';   
        $data['referral_settings'] = FALSE;    
        $data['settings'] = $this->admin_model->get_referral_settings();
        $data['main_content'] = $this->load->view('admin/referral/settings',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

     public function update_settings(){

        check_status();

        $id = $this->input->post('id');

        
        $data = array(
            'is_enable' => $this->input->post('enable_referral', true),
            'referral_policy' => $this->input->post('referral_policy', true),
            'commision_rate' => $this->input->post('commision_rate', true),
            'minimum_payout' => $this->input->post('minimum_payout', true),
            'payment_method' => $this->input->post('payment_method', true),
            'referral_guideline' => $this->input->post('referral_guideline', true),
        );
  
        $data = $this->security->xss_clean($data);
        $this->admin_model->edit_option($data, $id, 'referral_settings');
        $this->session->set_flashdata('msg', trans('updated-successfully')); 
        redirect(base_url('admin/referral/settings'));
    }

    public function user()
    {
        if (affiliate_settings()->is_enable != 1) {
            redirect(base_url('admin/dashboard/user'));
        }

        if (empty(user()->referral_id)) {
            $data = array(
                'referral_id' => substr(random_string('alnum', 5).mt_rand(), 0, 10)
            );
            $this->admin_model->edit_option($data, user()->id, 'users');
        }

        $data = array();
        $data['page_title'] = 'Home';      
        $data['page'] = 'Affiliate';   
        $data['user'] = FALSE;    
        $data['settings'] = $this->admin_model->get_referral_settings();    
        $data['user'] = $this->admin_model->get_single_user(user()->id);    
        $data['referrals'] = $this->admin_model->get_referrals(user()->referral_id);
        $data['withdraws'] = $this->admin_model->get_withdraw_balance_by_user(user()->id);
        $data['earns'] = $this->admin_model->get_total_earn_by_user(user()->referral_id);
        //echo "<pre>"; print_r($data['earns']); exit();
        $data['main_content'] = $this->load->view('admin/referral/home',$data,TRUE);
        $this->load->view('admin/index',$data);
    }


    public function my_referrals()
    {
        $data = array();
        $data['page_title'] = 'Referral';      
        $data['page'] = 'Affiliate';   
        $data['referrals'] = FALSE;    
        $data['referrals'] = $this->admin_model->get_referrals(user()->referral_id);
        $data['main_content'] = $this->load->view('admin/referral/my_referrals',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function referral_delete($id)
    {
        $this->admin_model->delete($id,'referrals'); 
        echo json_encode(array('st' => 1));
    }

    public function payouts()
    {
        $data = array();
        $data['page_title'] = 'Payouts';      
        $data['page'] = 'Affiliate';   
        $data['referrals'] = FALSE;    
        $data['settings'] = $this->admin_model->get_referral_settings();    
        $data['referrals'] = $this->admin_model->get_referrals(user()->referral_id);
        $data['payouts'] = $this->admin_model->get_referral_payouts(user()->id);
        //echo "<pre>"; print_r($data['payouts']); exit();
        $data['main_content'] = $this->load->view('admin/referral/payouts',$data,TRUE);
        $this->load->view('admin/index',$data);
    }


     public function add_payouts()
    {   
        if($_POST)
        {   
            $settings = $this->admin_model->get_referral_settings();
            $id = $this->input->post('id', true);
            $payout_amount = $this->input->post('amount' , true);
            if ($payout_amount >= $settings->minimum_payout && $payout_amount <= user()->referral_earn) {
                $amount = $payout_amount ;
            } else {
               $this->session->set_flashdata('error', 'You do not have sufficient balance');
               redirect(base_url('admin/referral/payouts'));
            }

                $data=array(
                    'user_id' => user()->id,
                    'transaction_id' => random_string('alnum', 10),
                    'amount' => $amount,
                    'payout_method' => $this->input->post('payment_method', true),
                    'method_details' => $this->input->post('method_details', true),
                    'status' => 0,
                    'created_at' => my_date_now(),
                );
                $data = $this->security->xss_clean($data);
                $this->admin_model->insert($data, 'referral_payouts');
                $this->session->set_flashdata('msg', trans('inserted-successfully'));

                redirect(base_url('admin/referral/payouts'));

        }
    }


    public function payout_delete($id)
    {
        $this->admin_model->delete($id,'referral_payouts'); 
        echo json_encode(array('st' => 1));
    }

    public function payout_request()
    {
        $data = array();
        $data['page_title'] = 'Payout Request';      
        $data['page'] = 'Affiliate';   
        $data['payouts'] = FALSE;
        $data['payouts'] = $this->admin_model->get_payouts_all();
        $data['main_content'] = $this->load->view('admin/referral/payout_request',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function payout_complete($id) 
    {

        $payout = $this->admin_model->get_by_md5($id,'referral_payouts');
        $user = $this->admin_model->get_by_id($payout->user_id,'users');

        if (!is_admin()) {
            redirect(base_url());
        }
        
        check_status();

        $data = array(
            'status' => 1
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->edit_option_md5($data, $id,'referral_payouts');

        $settings = $this->admin_model->get_referral_settings();
        $total_balance= $user->referral_earn;
        $remaining_balace = $total_balance - $payout->amount;

        $refdata = array(
            'referral_earn' => $remaining_balace,
        );
        $refdata = $this->security->xss_clean($refdata);
        $this->admin_model->edit_option($refdata, $user->id,'users');
        $this->session->set_flashdata('msg', trans('updated-successfully'));

        redirect(base_url('admin/referral/payout_request'));
    }

    public function completed_payout()
    {
        $data = array();
        $data['page_title'] = 'Completed Payout';      
        $data['page'] = 'Affiliate';   
        $data['payouts'] = FALSE;
        $data['payouts'] = $this->admin_model->get_payouts_by_status();
        $data['main_content'] = $this->load->view('admin/referral/completed_payout',$data,TRUE);
        $this->load->view('admin/index',$data);
    }      
        

}
	

