<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $global_data['settings'] = $this->common_model->get_settings();
        $this->settings = $global_data['settings'];

        $global_data['selected_lang'] = $this->settings->lang;
        $this->selected_lang = $global_data['selected_lang'];
        $this->lang->load('website', $global_data['settings']->lang_slug);

        $active_business = $this->session->userdata('active_business');
        if (empty($active_business)) {
            $global_data['business'] = $this->admin_model->get_business(0);
        } else {
            $global_data['business'] = $this->admin_model->get_business($active_business);
        }
        $this->business = $global_data['business'];
        $this->load->vars($global_data);
        $this->db->query("SET sql_mode=''");

        if ($this->settings->version == '1.01') {
            $this->db->query("ALTER TABLE `documents` ADD `language` VARCHAR(155) NULL DEFAULT NULL AFTER `prompt`;");
            
            $this->db->query("INSERT INTO `lang_values` (`type`, `label`, `keyword`, `english`) VALUES
            ('user', 'Prompt', 'prompt', 'Prompt'),
            ('user', 'Placeholder text', 'placeholder', 'Placeholder text');");

            $data = array(
                'version' => '1.1'
            );
            $this->admin_model->edit_option($data, 1, 'settings');
        }
        

    }

}


class Home_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $global_data['settings'] = $this->common_model->get_settings();
        $this->settings = $global_data['settings'];

        if (get_lang() == '') {
            $this->lang->load('website', $this->settings->lang_slug);
        }else{
            $this->lang->load('website', get_lang());
        }
        $this->load->vars($global_data);

        if (empty($this->session->userdata('site_mode'))) {
            $site_mode = array('site_mode' => settings()->site_mode);
            $this->session->set_userdata($site_mode);
        }
    }

    //verify recaptcha
    public function recaptcha_verify_request()
    {
        if ($this->settings->enable_captcha == 0) {
            return true;
        }

        $this->load->library('recaptcha');
        $recaptcha = $this->input->post('g-recaptcha-response');
        if (!empty($recaptcha)) {
            $response = $this->recaptcha->verifyResponse($recaptcha);
            if (isset($response['success']) && $response['success'] === true) {
                return true;
            }
        }
        return false;
    }

}